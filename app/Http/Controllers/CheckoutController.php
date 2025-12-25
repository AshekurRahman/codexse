<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\LicenseService;
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

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
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

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->with('seller')->get();

        // Calculate total using cart snapshot prices
        $total = 0;
        foreach ($products as $product) {
            $cartItem = $cart[$product->id] ?? [];
            $price = is_array($cartItem) ? ($cartItem['price'] ?? $product->current_price) : $product->current_price;
            $total += $price;
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
            'payment_method' => 'required|in:stripe,paypal',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->with('seller')->get();

        // Check all products are still available
        if ($products->count() !== count($productIds)) {
            return redirect()->route('cart.index')->with('error', 'Some products are no longer available.');
        }

        // Build line items for Stripe
        $lineItems = [];
        $total = 0;
        $orderItems = [];

        foreach ($products as $product) {
            $cartItem = $cart[$product->id] ?? [];
            $price = is_array($cartItem) ? ($cartItem['price'] ?? $product->current_price) : $product->current_price;
            $licenseType = is_array($cartItem) ? ($cartItem['license_type'] ?? 'regular') : 'regular';
            $total += $price;

            $lineItems[] = [
                'price_data' => [
                    'currency' => $this->stripeService->getCurrency(),
                    'product_data' => [
                        'name' => $product->name,
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
                'product_name' => $product->name,
                'license_type' => $licenseType,
                'price' => $price,
            ];
        }

        try {
            // Create pending order first
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'email' => $request->input('email'),
                'subtotal' => $total,
                'total' => $total,
                'status' => 'pending',
                'payment_method' => 'stripe',
                'notes' => $request->input('notes'),
            ]);

            // Store order items temporarily in session for webhook
            session()->put('pending_order_items', $orderItems);

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

        } catch (\Exception $e) {
            Log::error('Stripe checkout error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment processing failed. Please try again.');
        }
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
                // Fallback: reconstruct from cart
                $productIds = array_keys($cart);
                $products = Product::whereIn('id', $productIds)->get();

                foreach ($products as $product) {
                    $cartItem = $cart[$product->id] ?? [];
                    $price = is_array($cartItem) ? ($cartItem['price'] ?? $product->current_price) : $product->current_price;
                    $licenseType = is_array($cartItem) ? ($cartItem['license_type'] ?? 'regular') : 'regular';

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'seller_id' => $product->seller_id,
                        'product_name' => $product->name,
                        'license_type' => $licenseType,
                        'price' => $price,
                    ];
                }
            }

            // Create order items if they don't exist
            if ($order->items->isEmpty() && !empty($orderItems)) {
                $licenseService = app(LicenseService::class);

                foreach ($orderItems as $item) {
                    $product = Product::find($item['product_id']);
                    if (!$product) continue;

                    $commissionRate = $product->seller->commission_rate ?? 0.30;
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

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing order: ' . $e->getMessage());
            throw $e;
        }
    }
}
