<?php

namespace App\Http\Controllers;

use App\Filament\Admin\Pages\CommissionSettings;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Services\LicenseService;
use App\Services\PayoneerService;
use App\Services\PushNotificationService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Webhook;

class CheckoutController extends Controller
{
    protected StripeService $stripeService;
    protected PayoneerService $payoneerService;

    public function __construct(StripeService $stripeService, PayoneerService $payoneerService)
    {
        $this->stripeService = $stripeService;
        $this->payoneerService = $payoneerService;
    }

    public function index()
    {
        // Check if Stripe is configured
        if (!$this->stripeService->isConfigured()) {
            return redirect()->route('cart.index')->with('error', 'Payment system is not configured. Please contact support.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Extract product IDs from cart items (handles both simple and variation cart keys)
        $productIds = collect($cart)->map(function ($item, $key) {
            return is_array($item) ? ($item['product_id'] ?? $key) : $key;
        })->unique()->values()->toArray();

        $products = Product::whereIn('id', $productIds)->with('seller')->get();

        // Calculate total using cart snapshot prices
        $total = 0;
        foreach ($cart as $cartKey => $cartItem) {
            $productId = is_array($cartItem) ? ($cartItem['product_id'] ?? $cartKey) : $cartKey;
            $product = $products->firstWhere('id', $productId);
            if ($product) {
                $price = is_array($cartItem) ? ($cartItem['price'] ?? $product->current_price) : $product->current_price;
                $total += $price;
            }
        }

        return view('pages.checkout', compact('products', 'cart', 'total'));
    }

    public function process(Request $request)
    {
        // Check if Stripe is configured
        if (!$this->stripeService->isConfigured()) {
            return redirect()->route('cart.index')->with('error', 'Payment system is not configured. Please contact support.');
        }

        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'payment_method' => 'required|in:stripe,paypal,payoneer',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Extract product IDs from cart items (handles both simple and variation cart keys)
        $productIds = collect($cart)->map(function ($item, $key) {
            return is_array($item) ? ($item['product_id'] ?? $key) : $key;
        })->unique()->values()->toArray();

        $products = Product::whereIn('id', $productIds)->with('seller')->get();

        // Check all products are still available
        if ($products->count() !== count($productIds)) {
            return redirect()->route('cart.index')->with('error', 'Some products are no longer available.');
        }

        // Build line items for Stripe
        $lineItems = [];
        $total = 0;
        $orderItems = [];

        foreach ($cart as $cartKey => $cartItem) {
            $productId = is_array($cartItem) ? ($cartItem['product_id'] ?? $cartKey) : $cartKey;
            $product = $products->firstWhere('id', $productId);
            if (!$product) continue;

            $price = is_array($cartItem) ? ($cartItem['price'] ?? $product->current_price) : $product->current_price;
            $licenseType = is_array($cartItem) ? ($cartItem['license_type'] ?? 'regular') : 'regular';
            $variationName = is_array($cartItem) ? ($cartItem['variation_name'] ?? null) : null;
            $total += $price;

            $productName = $variationName ? $product->name . ' - ' . $variationName : $product->name;

            $lineItems[] = [
                'price_data' => [
                    'currency' => $this->stripeService->getCurrency(),
                    'product_data' => [
                        'name' => $productName,
                        'description' => $product->category->name ?? 'Digital Product',
                        'images' => $product->thumbnail_url ? [$product->thumbnail_url] : [],
                        'metadata' => [
                            'product_id' => $product->id,
                        ],
                    ],
                    'unit_amount' => (int) ($price * 100), // Convert to cents
                ],
                'quantity' => 1,
            ];

            $orderItems[] = [
                'product_id' => $product->id,
                'seller_id' => $product->seller_id,
                'product_name' => $productName,
                'license_type' => $licenseType,
                'price' => $price,
                'variation_id' => is_array($cartItem) ? ($cartItem['variation_id'] ?? null) : null,
            ];
        }

        $paymentMethod = $request->input('payment_method');

        try {
            // Create pending order first
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'email' => $request->input('email'),
                'subtotal' => $total,
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'notes' => $request->input('notes'),
            ]);

            // Store order items temporarily in session for webhook
            session()->put('pending_order_items', $orderItems);

            // Process based on payment method
            switch ($paymentMethod) {
                case 'payoneer':
                    return $this->processPayoneerCheckout($request, $order, $products, $total);

                case 'paypal':
                    // PayPal implementation placeholder
                    return redirect()->back()->with('error', 'PayPal payment is coming soon.');

                case 'stripe':
                default:
                    return $this->processStripeCheckout($request, $order, $lineItems);
            }

        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Process Stripe checkout
     */
    protected function processStripeCheckout(Request $request, Order $order, array $lineItems)
    {
        // Create Stripe Checkout Session
        $checkoutSession = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'customer_email' => $request->input('email'),
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'metadata' => [
                'order_id' => $order->id,
                'user_id' => auth()->id(),
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'order_id' => $order->id,
                ],
            ],
        ]);

        // Store Stripe session ID in order
        $order->update([
            'stripe_session_id' => $checkoutSession->id,
        ]);

        // Redirect to Stripe Checkout
        return redirect($checkoutSession->url);
    }

    /**
     * Process Payoneer checkout
     */
    protected function processPayoneerCheckout(Request $request, Order $order, $products, float $total)
    {
        if (!$this->payoneerService->isConfigured()) {
            return redirect()->back()->with('error', 'Payoneer is not configured. Please contact support.');
        }

        // Split name into first and last name
        $nameParts = explode(' ', $request->input('name'), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        // Prepare items for Payoneer
        $items = [];
        foreach ($products as $product) {
            $items[] = [
                'name' => $product->name,
                'price' => $product->current_price,
                'quantity' => 1,
            ];
        }

        // Create Payoneer checkout session
        $checkoutData = [
            'transaction_id' => $order->order_number,
            'amount' => $total,
            'currency' => Setting::get('stripe_currency', 'usd'),
            'email' => $request->input('email'),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'reference' => $order->order_number,
            'return_url' => route('checkout.payoneer.success', ['order' => $order->id]),
            'cancel_url' => route('checkout.cancel'),
            'webhook_url' => route('payoneer.webhook'),
            'items' => $items,
        ];

        $session = $this->payoneerService->createCheckoutSession($checkoutData);

        if (!$session) {
            $order->update(['status' => 'failed']);
            return redirect()->back()->with('error', 'Failed to create Payoneer checkout session. Please try again.');
        }

        // Store Payoneer session data in order
        $order->update([
            'payoneer_transaction_id' => $session['transactionId'] ?? $order->order_number,
        ]);

        // Get redirect URL and redirect
        $redirectUrl = $this->payoneerService->getCheckoutUrl($session);

        if ($redirectUrl) {
            return redirect($redirectUrl);
        }

        // If no redirect URL, show error
        return redirect()->back()->with('error', 'Unable to redirect to Payoneer. Please try again.');
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('products.index');
        }

        try {
            // Retrieve the session from Stripe
            $session = StripeSession::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                return redirect()->route('checkout.index')->with('error', 'Payment was not completed.');
            }

            // Find the order
            $order = Order::where('stripe_session_id', $sessionId)->first();

            if (!$order) {
                return redirect()->route('products.index')->with('error', 'Order not found.');
            }

            // If order is still pending, complete it
            if ($order->status === 'pending') {
                $this->completeOrder($order, $session->payment_intent);
            }

            // Clear cart
            session()->forget('cart');
            session()->forget('pending_order_items');

            return view('pages.checkout-success', compact('order'));

        } catch (\Exception $e) {
            Log::error('Stripe success error: ' . $e->getMessage());
            return redirect()->route('products.index')->with('error', 'Error processing payment confirmation.');
        }
    }

    public function cancel()
    {
        return redirect()->route('checkout.index')->with('error', 'Payment was cancelled. Please try again.');
    }

    /**
     * Handle Payoneer success callback
     */
    public function payoneerSuccess(Request $request, Order $order)
    {
        // Verify the order belongs to the current user
        if ($order->user_id !== auth()->id()) {
            return redirect()->route('products.index')->with('error', 'Unauthorized access.');
        }

        try {
            // Get the transaction status from Payoneer
            $status = $this->payoneerService->getCheckoutStatus($order->order_number);

            if ($status && isset($status['status']) && in_array($status['status'], ['CHARGED', 'PAID', 'COMPLETED'])) {
                // Payment successful
                if ($order->status === 'pending') {
                    $this->completeOrder($order, $status['transactionId'] ?? null);
                }

                // Clear cart
                session()->forget('cart');
                session()->forget('pending_order_items');

                return view('pages.checkout-success', compact('order'));
            }

            // Payment not confirmed yet - check if still processing
            if ($status && isset($status['status']) && in_array($status['status'], ['PENDING', 'PROCESSING'])) {
                return view('pages.checkout-success', compact('order'))
                    ->with('warning', 'Your payment is being processed. You will receive confirmation shortly.');
            }

            // Payment failed or was declined
            return redirect()->route('checkout.index')->with('error', 'Payment was not completed. Please try again.');

        } catch (\Exception $e) {
            Log::error('Payoneer success callback error: ' . $e->getMessage());
            return redirect()->route('products.index')->with('error', 'Error processing payment confirmation.');
        }
    }

    /**
     * Handle Payoneer webhook
     */
    public function payoneerWebhook(Request $request)
    {
        $payload = $request->all();

        // Verify webhook signature (if Payoneer provides one)
        $signature = $request->header('X-Payoneer-Signature', '');
        if (!$this->payoneerService->verifyWebhook(json_encode($payload), $signature)) {
            Log::warning('Payoneer webhook signature verification failed');
            // Continue processing anyway since verification is placeholder
        }

        $transactionId = $payload['transactionId'] ?? null;
        $status = $payload['status'] ?? null;
        $reference = $payload['reference'] ?? $payload['payment']['reference'] ?? null;

        Log::info('Payoneer webhook received', [
            'transactionId' => $transactionId,
            'status' => $status,
            'reference' => $reference,
        ]);

        if (!$reference && !$transactionId) {
            return response('Missing transaction reference', 400);
        }

        // Find the order by order_number (reference) or payoneer_transaction_id
        $order = Order::where('order_number', $reference)
            ->orWhere('payoneer_transaction_id', $transactionId)
            ->first();

        if (!$order) {
            Log::error('Payoneer webhook: Order not found', [
                'reference' => $reference,
                'transactionId' => $transactionId,
            ]);
            return response('Order not found', 404);
        }

        // Handle the status
        switch (strtoupper($status)) {
            case 'CHARGED':
            case 'PAID':
            case 'COMPLETED':
                if ($order->status === 'pending') {
                    $this->completeOrder($order, $transactionId);
                }
                break;

            case 'FAILED':
            case 'DECLINED':
            case 'CANCELED':
                $order->update(['status' => 'failed']);
                Log::info('Payoneer order marked as failed: ' . $order->order_number);
                break;

            default:
                Log::info('Unhandled Payoneer status: ' . $status);
        }

        return response('Webhook handled', 200);
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = $this->stripeService->getWebhookSecret();

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook error: Invalid payload');
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe webhook error: Invalid signature');
            return response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutCompleted($session);
                break;

            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                Log::info('Payment succeeded: ' . $paymentIntent->id);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentFailed($paymentIntent);
                break;

            default:
                Log::info('Unhandled Stripe event: ' . $event->type);
        }

        return response('Webhook handled', 200);
    }

    protected function handleCheckoutCompleted($session)
    {
        $order = Order::where('stripe_session_id', $session->id)->first();

        if (!$order) {
            Log::error('Order not found for session: ' . $session->id);
            return;
        }

        if ($order->status === 'pending') {
            $this->completeOrder($order, $session->payment_intent);
        }
    }

    protected function handlePaymentFailed($paymentIntent)
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->update(['status' => 'failed']);
                Log::info('Order marked as failed: ' . $orderId);
            }
        }
    }

    protected function completeOrder(Order $order, $paymentIntentId = null)
    {
        try {
            DB::beginTransaction();

            // Get products from cart (stored in session during checkout)
            $cart = session()->get('cart', []);
            $orderItems = session()->get('pending_order_items', []);

            // If no items in session, try to get from order items relation
            if (empty($orderItems) && $order->items->isEmpty()) {
                // Fallback: reconstruct from cart (handles both simple and variation cart keys)
                $productIds = collect($cart)->map(function ($item, $key) {
                    return is_array($item) ? ($item['product_id'] ?? $key) : $key;
                })->unique()->values()->toArray();
                $products = Product::whereIn('id', $productIds)->get();

                foreach ($cart as $cartKey => $cartItem) {
                    $productId = is_array($cartItem) ? ($cartItem['product_id'] ?? $cartKey) : $cartKey;
                    $product = $products->firstWhere('id', $productId);
                    if (!$product) continue;

                    $price = is_array($cartItem) ? ($cartItem['price'] ?? $product->current_price) : $product->current_price;
                    $licenseType = is_array($cartItem) ? ($cartItem['license_type'] ?? 'regular') : 'regular';
                    $variationName = is_array($cartItem) ? ($cartItem['variation_name'] ?? null) : null;
                    $productName = $variationName ? $product->name . ' - ' . $variationName : $product->name;

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'seller_id' => $product->seller_id,
                        'product_name' => $productName,
                        'license_type' => $licenseType,
                        'price' => $price,
                        'variation_id' => is_array($cartItem) ? ($cartItem['variation_id'] ?? null) : null,
                    ];
                }
            }

            // Create order items if they don't exist
            if ($order->items->isEmpty() && !empty($orderItems)) {
                $licenseService = app(LicenseService::class);

                foreach ($orderItems as $item) {
                    $product = Product::find($item['product_id']);
                    if (!$product) continue;

                    $commissionRate = CommissionSettings::getCommissionRateForSeller($product->seller);
                    $platformFee = $item['price'] * $commissionRate;
                    $sellerAmount = $item['price'] - $platformFee;

                    // Create order item (license_key generated automatically by model)
                    $orderItem = $order->items()->create([
                        'product_id' => $item['product_id'],
                        'seller_id' => $item['seller_id'],
                        'product_name' => $item['product_name'],
                        'license_type' => $item['license_type'],
                        'price' => $item['price'],
                        'seller_amount' => $sellerAmount,
                        'platform_fee' => $platformFee,
                        'download_limit' => 5,
                    ]);

                    // Create License record for tracking activations
                    $licenseService->createForOrderItem($orderItem);

                    // Update seller balance
                    $product->seller->increment('available_balance', $sellerAmount);
                    $product->seller->increment('total_earnings', $sellerAmount);
                    $product->seller->increment('total_sales', $item['price']);

                    // Increment product downloads count
                    $product->increment('downloads_count');
                }
            }

            // Update order status
            $order->update([
                'status' => 'completed',
                'stripe_payment_intent_id' => $paymentIntentId,
                'paid_at' => now(),
            ]);

            DB::commit();

            Log::info('Order completed: ' . $order->order_number);

            // Send push notifications
            $this->sendOrderNotifications($order);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing order: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function sendOrderNotifications(Order $order): void
    {
        try {
            $pushService = app(PushNotificationService::class);

            // Notify buyer about their order
            $pushService->notifyOrderUpdate($order->user, [
                'order_id' => $order->order_number,
                'status' => 'Order Confirmed',
                'url' => route('purchases'),
            ]);

            // Notify each seller about their sale
            $order->load('items.seller.user', 'items.product');

            $sellerNotifications = [];
            foreach ($order->items as $item) {
                $sellerId = $item->seller_id;

                if (!isset($sellerNotifications[$sellerId])) {
                    $sellerNotifications[$sellerId] = [
                        'seller' => $item->seller,
                        'items' => [],
                        'total' => 0,
                    ];
                }

                $sellerNotifications[$sellerId]['items'][] = $item;
                $sellerNotifications[$sellerId]['total'] += $item->seller_amount;
            }

            foreach ($sellerNotifications as $notification) {
                if ($notification['seller'] && $notification['seller']->user) {
                    $productName = count($notification['items']) > 1
                        ? $notification['items'][0]->product_name . ' +' . (count($notification['items']) - 1) . ' more'
                        : $notification['items'][0]->product_name;

                    $pushService->notifyNewSale($notification['seller']->user, [
                        'order_id' => $order->order_number,
                        'product_name' => $productName,
                        'amount' => '$' . number_format($notification['total'], 2),
                        'url' => '/seller/orders',
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send order notifications: ' . $e->getMessage());
        }
    }
}
