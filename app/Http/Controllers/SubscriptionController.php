<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPlan;
use App\Services\WebhookProtectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription as StripeSubscription;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Webhook;

class SubscriptionController extends Controller
{
    protected WebhookProtectionService $webhookProtection;

    public function __construct(WebhookProtectionService $webhookProtection)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->webhookProtection = $webhookProtection;
    }

    /**
     * Display available subscription plans.
     */
    public function index(Request $request)
    {
        $query = SubscriptionPlan::query()
            ->active()
            ->with(['product', 'service', 'seller'])
            ->orderBy('sort_order')
            ->orderBy('price');

        if ($request->filled('type')) {
            if ($request->type === 'product') {
                $query->whereNotNull('product_id');
            } elseif ($request->type === 'service') {
                $query->whereNotNull('service_id');
            }
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        $plans = $query->get();

        // Group plans by product/service
        $groupedPlans = $plans->groupBy(function ($plan) {
            if ($plan->product_id) {
                return 'product_' . $plan->product_id;
            }
            if ($plan->service_id) {
                return 'service_' . $plan->service_id;
            }
            return 'standalone';
        });

        return view('pages.subscriptions.index', [
            'plans' => $plans,
            'groupedPlans' => $groupedPlans,
        ]);
    }

    /**
     * Show subscription plan details.
     */
    public function show(SubscriptionPlan $plan)
    {
        $plan->load(['product', 'service', 'seller']);

        $userSubscription = null;
        if (Auth::check()) {
            $userSubscription = Auth::user()
                ->subscriptions()
                ->where('subscription_plan_id', $plan->id)
                ->whereIn('status', ['active', 'trialing', 'past_due'])
                ->first();
        }

        return view('pages.subscriptions.show', [
            'plan' => $plan,
            'userSubscription' => $userSubscription,
        ]);
    }

    /**
     * Show checkout page for subscription.
     */
    public function checkout(SubscriptionPlan $plan)
    {
        $user = Auth::user();

        // Check if user already has active subscription
        if (!$plan->canSubscribe($user)) {
            return redirect()->route('subscriptions.manage')
                ->with('error', 'You already have an active subscription to this plan.');
        }

        $plan->load(['product', 'service', 'seller']);

        return view('pages.subscriptions.checkout', [
            'plan' => $plan,
        ]);
    }

    /**
     * Create Stripe checkout session for subscription.
     */
    public function createCheckoutSession(Request $request, SubscriptionPlan $plan)
    {
        $user = Auth::user();

        if (!$plan->canSubscribe($user)) {
            return response()->json(['error' => 'You already have an active subscription.'], 400);
        }

        // Create or get Stripe customer
        $stripeCustomerId = $user->stripe_id;
        if (!$stripeCustomerId) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ]);
            $user->update(['stripe_id' => $customer->id]);
            $stripeCustomerId = $customer->id;
        }

        // Create checkout session
        $sessionParams = [
            'customer' => $stripeCustomerId,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('subscriptions.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('subscriptions.checkout', $plan),
            'metadata' => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ],
            'subscription_data' => [
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                ],
            ],
        ];

        // Add trial if plan has trial days
        if ($plan->trial_days > 0) {
            $sessionParams['subscription_data']['trial_period_days'] = $plan->trial_days;
        }

        $session = StripeCheckoutSession::create($sessionParams);

        return response()->json(['sessionId' => $session->id]);
    }

    /**
     * Handle successful subscription checkout.
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('subscriptions.index')
                ->with('error', 'Invalid checkout session.');
        }

        try {
            $session = StripeCheckoutSession::retrieve([
                'id' => $sessionId,
                'expand' => ['subscription', 'subscription.latest_invoice'],
            ]);

            $user = Auth::user();
            $planId = $session->metadata->plan_id;
            $plan = SubscriptionPlan::findOrFail($planId);

            // Create local subscription record
            $subscription = Subscription::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'subscription_plan_id' => $plan->id,
                    'stripe_subscription_id' => $session->subscription->id,
                ],
                [
                    'seller_id' => $plan->seller_id,
                    'stripe_customer_id' => $session->customer,
                    'status' => $session->subscription->status,
                    'trial_ends_at' => $session->subscription->trial_end
                        ? \Carbon\Carbon::createFromTimestamp($session->subscription->trial_end)
                        : null,
                    'current_period_start' => \Carbon\Carbon::createFromTimestamp($session->subscription->current_period_start),
                    'current_period_end' => \Carbon\Carbon::createFromTimestamp($session->subscription->current_period_end),
                ]
            );

            // Create invoice record if available
            if ($session->subscription->latest_invoice) {
                $invoice = $session->subscription->latest_invoice;
                SubscriptionInvoice::updateOrCreate(
                    ['stripe_invoice_id' => $invoice->id],
                    [
                        'subscription_id' => $subscription->id,
                        'user_id' => $user->id,
                        'amount' => $invoice->subtotal / 100,
                        'tax' => ($invoice->tax ?? 0) / 100,
                        'total' => $invoice->total / 100,
                        'currency' => $invoice->currency,
                        'status' => $invoice->status,
                        'paid_at' => $invoice->status === 'paid' ? now() : null,
                        'pdf_url' => $invoice->invoice_pdf ?? null,
                    ]
                );
            }

            return redirect()->route('subscriptions.manage')
                ->with('success', 'Your subscription has been activated!');

        } catch (\Exception $e) {
            return redirect()->route('subscriptions.index')
                ->with('error', 'Failed to process subscription. Please contact support.');
        }
    }

    /**
     * Manage user subscriptions.
     */
    public function manage()
    {
        $user = Auth::user();

        $activeSubscriptions = $user->subscriptions()
            ->with(['plan.product', 'plan.service', 'plan.seller'])
            ->whereIn('status', ['active', 'trialing', 'past_due', 'paused'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pastSubscriptions = $user->subscriptions()
            ->with(['plan.product', 'plan.service'])
            ->whereIn('status', ['canceled', 'expired', 'incomplete_expired'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.subscriptions.manage', [
            'activeSubscriptions' => $activeSubscriptions,
            'pastSubscriptions' => $pastSubscriptions,
        ]);
    }

    /**
     * Show subscription details.
     */
    public function showSubscription(Subscription $subscription)
    {
        $this->authorize('view', $subscription);

        $subscription->load(['plan.product', 'plan.service', 'invoices', 'usage']);

        return view('pages.subscriptions.subscription-show', [
            'subscription' => $subscription,
        ]);
    }

    /**
     * Cancel subscription.
     */
    public function cancel(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $immediately = $request->boolean('immediately', false);

        try {
            if ($subscription->stripe_subscription_id) {
                $stripeSubscription = StripeSubscription::retrieve($subscription->stripe_subscription_id);

                if ($immediately) {
                    $stripeSubscription->cancel();
                } else {
                    $stripeSubscription->update($subscription->stripe_subscription_id, [
                        'cancel_at_period_end' => true,
                    ]);
                }
            }

            $subscription->cancel($immediately);

            $message = $immediately
                ? 'Your subscription has been canceled.'
                : 'Your subscription will be canceled at the end of the billing period.';

            return redirect()->route('subscriptions.manage')
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel subscription. Please try again.');
        }
    }

    /**
     * Resume a canceled subscription.
     */
    public function resume(Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        if (!$subscription->cancel_at_period_end) {
            return back()->with('error', 'This subscription cannot be resumed.');
        }

        try {
            if ($subscription->stripe_subscription_id) {
                StripeSubscription::update($subscription->stripe_subscription_id, [
                    'cancel_at_period_end' => false,
                ]);
            }

            $subscription->resume();

            return redirect()->route('subscriptions.manage')
                ->with('success', 'Your subscription has been resumed.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to resume subscription. Please try again.');
        }
    }

    /**
     * Pause subscription.
     */
    public function pause(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $resumeAt = $request->filled('resume_at')
            ? \Carbon\Carbon::parse($request->resume_at)
            : null;

        try {
            if ($subscription->stripe_subscription_id) {
                StripeSubscription::update($subscription->stripe_subscription_id, [
                    'pause_collection' => [
                        'behavior' => 'void',
                        'resumes_at' => $resumeAt?->timestamp,
                    ],
                ]);
            }

            $subscription->pause($resumeAt);

            return redirect()->route('subscriptions.manage')
                ->with('success', 'Your subscription has been paused.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to pause subscription. Please try again.');
        }
    }

    /**
     * Get subscription invoices.
     */
    public function invoices(Subscription $subscription)
    {
        $this->authorize('view', $subscription);

        $invoices = $subscription->invoices()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.subscriptions.invoices', [
            'subscription' => $subscription,
            'invoices' => $invoices,
        ]);
    }

    /**
     * Update payment method via Stripe billing portal.
     */
    public function billingPortal()
    {
        $user = Auth::user();

        if (!$user->stripe_id) {
            return redirect()->route('subscriptions.manage')
                ->with('error', 'No billing information found.');
        }

        try {
            $session = \Stripe\BillingPortal\Session::create([
                'customer' => $user->stripe_id,
                'return_url' => route('subscriptions.manage'),
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to access billing portal.');
        }
    }

    /**
     * Handle Stripe webhook events.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\Exception $e) {
            Log::error('Subscription webhook: Invalid signature', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Invalid webhook signature'], 400);
        }

        // Replay attack prevention: validate event hasn't been processed
        $validation = $this->webhookProtection->validateStripeEvent($event, $request);
        if (!$validation['valid']) {
            Log::warning('Subscription webhook replay detected', [
                'event_id' => $event->id,
                'event_type' => $event->type,
                'error' => $validation['error'],
                'ip' => $request->ip(),
            ]);
            return response()->json(['error' => $validation['error']], 400);
        }

        $webhookRecord = $validation['webhook'];

        try {
            switch ($event->type) {
                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event->data->object);
                    break;

                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event->data->object);
                    break;

                case 'invoice.paid':
                    $this->handleInvoicePaid($event->data->object);
                    break;

                case 'invoice.payment_failed':
                    $this->handleInvoicePaymentFailed($event->data->object);
                    break;
            }

            // Mark webhook as successfully processed
            $webhookRecord?->markCompleted();

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            $webhookRecord?->markFailed();

            Log::error('Subscription webhook processing error', [
                'event_id' => $event->id,
                'event_type' => $event->type,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function handleSubscriptionUpdated($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            return;
        }

        $subscription->update([
            'status' => $stripeSubscription->status,
            'current_period_start' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start),
            'current_period_end' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
            'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end,
            'canceled_at' => $stripeSubscription->canceled_at
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->canceled_at)
                : null,
            'ended_at' => $stripeSubscription->ended_at
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->ended_at)
                : null,
        ]);

        // Reset usage on period renewal
        if ($stripeSubscription->current_period_start > $subscription->getOriginal('current_period_start')?->timestamp) {
            $subscription->resetUsage();
        }
    }

    protected function handleSubscriptionDeleted($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            return;
        }

        $subscription->update([
            'status' => 'canceled',
            'ended_at' => now(),
        ]);
    }

    protected function handleInvoicePaid($invoice)
    {
        $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();

        if (!$subscription) {
            return;
        }

        SubscriptionInvoice::updateOrCreate(
            ['stripe_invoice_id' => $invoice->id],
            [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'amount' => $invoice->subtotal / 100,
                'tax' => ($invoice->tax ?? 0) / 100,
                'total' => $invoice->total / 100,
                'currency' => $invoice->currency,
                'status' => 'paid',
                'paid_at' => now(),
                'pdf_url' => $invoice->invoice_pdf ?? null,
            ]
        );
    }

    protected function handleInvoicePaymentFailed($invoice)
    {
        $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();

        if (!$subscription) {
            return;
        }

        $subscription->update(['status' => 'past_due']);
    }
}
