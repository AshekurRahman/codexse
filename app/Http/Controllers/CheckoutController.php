<?php

namespace App\Http\Controllers;

use App\Filament\Admin\Pages\CommissionSettings;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Wallet;
use App\Models\Currency;
use App\Services\CurrencyService;
use App\Services\FraudDetectionService;
use App\Services\LicenseService;
use App\Services\PayoneerService;
use App\Services\PayPalService;
use App\Services\PushNotificationService;
use App\Services\StripeService;
use App\Services\TaxService;
use App\Notifications\ProductPurchaseConfirmation;
use App\Notifications\NewSaleNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Webhook;

class CheckoutController extends Controller
{
    protected StripeService $stripeService;
    protected PayoneerService $payoneerService;
    protected PayPalService $paypalService;
    protected TaxService $taxService;
    protected FraudDetectionService $fraudService;

    public function __construct(
        StripeService $stripeService,
        PayoneerService $payoneerService,
        PayPalService $paypalService,
        TaxService $taxService,
        FraudDetectionService $fraudService
    ) {
        $this->stripeService = $stripeService;
        $this->payoneerService = $payoneerService;
        $this->paypalService = $paypalService;
        $this->taxService = $taxService;
        $this->fraudService = $fraudService;
    }

    public function index()
    {
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

        // Get wallet info for logged-in users
        $wallet = null;
        $canPayWithWallet = false;
        if (Auth::check()) {
            $wallet = Auth::user()->getOrCreateWallet();
            $canPayWithWallet = $wallet->canPurchase($total);
        }

        // Get currency info for display
        $baseCurrency = Currency::getDefault();
        $userCurrency = current_currency();
        $showCurrencyNote = $baseCurrency && $userCurrency && $baseCurrency->code !== $userCurrency->code;

        // Tax calculation support
        $taxEnabled = $this->taxService->isEnabled();
        $usStates = $this->taxService->getUsStates();
        $savedState = session('billing_state');
        $taxData = null;

        if ($taxEnabled && $savedState) {
            $taxData = $this->taxService->calculateTotals($total, 0, $savedState);
        }

        // Check if payment gateways are configured
        $stripeConfigured = $this->stripeService->isConfigured();
        $paypalConfigured = $this->paypalService->isConfigured();
        $payoneerConfigured = $this->payoneerService->isConfigured();

        return view('pages.checkout', compact(
            'products',
            'cart',
            'total',
            'wallet',
            'canPayWithWallet',
            'baseCurrency',
            'userCurrency',
            'showCurrencyNote',
            'taxEnabled',
            'usStates',
            'savedState',
            'taxData',
            'stripeConfigured',
            'paypalConfigured',
            'payoneerConfigured'
        ));
    }

    /**
     * Calculate tax via AJAX (no page reload)
     */
    public function calculateTax(Request $request)
    {
        $request->validate([
            'state' => 'nullable|string|max:2',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        // Extract product IDs and calculate total
        $productIds = collect($cart)->map(function ($item, $key) {
            return is_array($item) ? ($item['product_id'] ?? $key) : $key;
        })->unique()->values()->toArray();

        $products = Product::whereIn('id', $productIds)->get();

        $total = 0;
        foreach ($cart as $cartKey => $cartItem) {
            $productId = is_array($cartItem) ? ($cartItem['product_id'] ?? $cartKey) : $cartKey;
            $product = $products->firstWhere('id', $productId);
            if ($product) {
                $price = is_array($cartItem) ? ($cartItem['price'] ?? $product->current_price) : $product->current_price;
                $total += $price;
            }
        }

        $stateCode = $request->input('state');

        // Save state to session
        if ($stateCode) {
            session(['billing_state' => $stateCode]);
        } else {
            session()->forget('billing_state');
        }

        // Calculate tax
        $taxData = $this->taxService->calculateTotals($total, 0, $stateCode);
        $stateName = $stateCode ? config("tax.states.{$stateCode}", $stateCode) : null;

        return response()->json([
            'success' => true,
            'tax_enabled' => $this->taxService->isEnabled(),
            'tax_label' => Setting::get('tax_label', 'Sales Tax'),
            'tax_rate' => $taxData['tax_rate'],
            'tax_amount' => $taxData['tax_amount'],
            'tax_amount_formatted' => format_price($taxData['tax_amount']),
            'subtotal' => $total,
            'subtotal_formatted' => format_price($total),
            'total' => $taxData['total'],
            'total_formatted' => format_price($taxData['total']),
            'state_code' => $stateCode,
            'state_name' => $stateName,
        ]);
    }

    public function process(Request $request)
    {
        $isAjax = $request->expectsJson() || $request->ajax();

        // Check if Stripe is configured
        if (!$this->stripeService->isConfigured()) {
            if ($isAjax) {
                return response()->json(['message' => 'Payment system is not configured. Please contact support.'], 422);
            }
            return redirect()->route('cart.index')->with('error', 'Payment system is not configured. Please contact support.');
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'payment_method' => 'required|in:stripe,paypal,payoneer,wallet',
            'billing_state' => 'nullable|string|max:2',
        ]);

        // Store billing state in session for future reference
        if ($request->filled('billing_state')) {
            session(['billing_state' => $request->input('billing_state')]);
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            if ($isAjax) {
                return response()->json(['message' => 'Your cart is empty.'], 422);
            }
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Extract product IDs from cart items (handles both simple and variation cart keys)
        $productIds = collect($cart)->map(function ($item, $key) {
            return is_array($item) ? ($item['product_id'] ?? $key) : $key;
        })->unique()->values()->toArray();

        $products = Product::whereIn('id', $productIds)->with('seller')->get();

        // Check all products are still available
        if ($products->count() !== count($productIds)) {
            if ($isAjax) {
                return response()->json(['message' => 'Some products are no longer available.'], 422);
            }
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
                        'description' => $product->category?->name ?? 'Digital Product',
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
        $billingState = $request->input('billing_state');

        // Calculate tax if enabled
        $taxData = $this->taxService->calculateTotals($total, 0, $billingState);
        $orderTotal = $taxData['total'];

        // Fraud detection check
        $fraudResult = $this->fraudService->analyze(
            user: Auth::user(),
            amount: $orderTotal,
            paymentMethod: $paymentMethod,
            request: $request
        );

        // Block transaction if fraud detected
        if ($fraudResult->shouldBlock) {
            Log::warning('Checkout blocked by fraud detection', [
                'user_id' => Auth::id(),
                'amount' => $orderTotal,
                'risk_score' => $fraudResult->riskScore,
                'ip' => $request->ip(),
            ]);

            if ($isAjax) {
                return response()->json([
                    'message' => 'Your transaction could not be processed. Please contact support if you believe this is an error.',
                    'reference' => $fraudResult->alert?->alert_number,
                ], 403);
            }
            return redirect()->route('cart.index')
                ->with('error', 'Your transaction could not be processed. Please contact support if you believe this is an error.');
        }

        try {
            // Create pending order first
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'email' => $request->input('email'),
                'subtotal' => $total,
                'tax_rate' => $taxData['tax_rate'],
                'tax_amount' => $taxData['tax_amount'],
                'billing_state' => $billingState,
                'total' => $orderTotal,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'notes' => $request->input('notes'),
                'ip_address' => $request->ip(),
                'fraud_score' => $fraudResult->riskScore > 0 ? $fraudResult->riskScore : null,
            ]);

            // Link fraud alert to order if created
            if ($fraudResult->alert) {
                $fraudResult->alert->update([
                    'alertable_type' => Order::class,
                    'alertable_id' => $order->id,
                ]);
            }

            // Store order items temporarily in session for webhook
            session()->put('pending_order_items', $orderItems);

            // Process based on payment method
            switch ($paymentMethod) {
                case 'wallet':
                    return $this->processWalletCheckout($request, $order, $orderItems, $orderTotal);

                case 'payoneer':
                    return $this->processPayoneerCheckout($request, $order, $products, $orderTotal, $taxData);

                case 'paypal':
                    return $this->processPayPalCheckout($request, $order, $orderItems, $taxData);

                case 'stripe':
                default:
                    return $this->processStripeCheckout($request, $order, $lineItems, $taxData);
            }

        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Process Wallet checkout
     */
    protected function processWalletCheckout(Request $request, Order $order, array $orderItems, float $total)
    {
        $isAjax = $request->expectsJson() || $request->ajax();

        if (!Auth::check()) {
            $order->update(['status' => 'failed']);
            if ($isAjax) {
                return response()->json(['message' => 'Please log in to use wallet payment.'], 401);
            }
            return redirect()->route('login')->with('error', 'Please log in to use wallet payment.');
        }

        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();

        // Verify wallet can make this purchase
        if (!$wallet->canPurchase($total)) {
            $order->update(['status' => 'failed']);
            if ($isAjax) {
                return response()->json(['message' => 'Insufficient wallet balance. Please add funds or choose another payment method.'], 422);
            }
            return redirect()->route('checkout.index')->with('error', 'Insufficient wallet balance. Please add funds or choose another payment method.');
        }

        try {
            DB::beginTransaction();

            // Deduct from wallet
            $transaction = $wallet->purchase(
                amount: $total,
                description: 'Purchase: Order #' . $order->order_number,
                transactionable: $order
            );

            // Store order items in session for completeOrder
            session()->put('pending_order_items', $orderItems);

            // Complete the order immediately
            $this->completeOrder($order, 'wallet_' . $transaction->reference);

            DB::commit();

            // Clear cart
            session()->forget('cart');
            session()->forget('pending_order_items');

            if ($isAjax) {
                return response()->json(['redirect' => route('checkout.success') . '?order=' . $order->id]);
            }
            return view('pages.checkout-success', compact('order'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Wallet checkout error: ' . $e->getMessage());
            $order->update(['status' => 'failed']);
            if ($isAjax) {
                return response()->json(['message' => 'Payment failed. Please try again.'], 422);
            }
            return redirect()->route('checkout.index')->with('error', 'Payment failed. Please try again.');
        }
    }

    /**
     * Process PayPal checkout
     */
    protected function processPayPalCheckout(Request $request, Order $order, array $orderItems, array $taxData)
    {
        $isAjax = $request->expectsJson() || $request->ajax();

        if (!$this->paypalService->isConfigured()) {
            $order->update(['status' => 'failed']);
            if ($isAjax) {
                return response()->json(['message' => 'PayPal is not configured. Please contact support.'], 422);
            }
            return redirect()->back()->with('error', 'PayPal is not configured. Please contact support.');
        }

        // Prepare items for PayPal
        $items = [];
        foreach ($orderItems as $item) {
            $items[] = [
                'name' => $item['product_name'],
                'price' => $item['price'],
            ];
        }

        // Create PayPal order
        $paypalOrder = $this->paypalService->createOrder([
            'order_number' => $order->order_number,
            'subtotal' => $order->subtotal,
            'tax_amount' => $taxData['tax_amount'] ?? 0,
            'total' => $order->total,
            'items' => $items,
            'return_url' => route('checkout.paypal.success', ['order' => $order->id]),
            'cancel_url' => route('checkout.cancel'),
        ]);

        if (!$paypalOrder || !$paypalOrder['approval_url']) {
            $order->update(['status' => 'failed']);
            if ($isAjax) {
                return response()->json(['message' => 'Failed to create PayPal order. Please try again.'], 422);
            }
            return redirect()->back()->with('error', 'Failed to create PayPal order. Please try again.');
        }

        // Store PayPal order ID
        $order->update([
            'paypal_order_id' => $paypalOrder['id'],
        ]);

        // Store order items in session
        session()->put('pending_order_items', $orderItems);

        // Redirect to PayPal
        if ($isAjax) {
            return response()->json(['redirect' => $paypalOrder['approval_url']]);
        }
        return redirect($paypalOrder['approval_url']);
    }

    /**
     * Process Stripe checkout
     */
    protected function processStripeCheckout(Request $request, Order $order, array $lineItems, array $taxData)
    {
        $isAjax = $request->expectsJson() || $request->ajax();

        // Add tax as a separate line item if applicable
        if ($taxData['tax_amount'] > 0) {
            $taxLabel = $this->taxService->getLabel();
            $stateCode = $taxData['state_code'] ?? '';
            $taxName = $stateCode ? "{$taxLabel} ({$stateCode})" : $taxLabel;

            $lineItems[] = [
                'price_data' => [
                    'currency' => $this->stripeService->getCurrency(),
                    'product_data' => [
                        'name' => $taxName,
                        'description' => number_format($taxData['tax_rate'], 2) . '% tax',
                    ],
                    'unit_amount' => (int) ($taxData['tax_amount'] * 100),
                ],
                'quantity' => 1,
            ];
        }

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
        if ($isAjax) {
            return response()->json(['redirect' => $checkoutSession->url]);
        }
        return redirect($checkoutSession->url);
    }

    /**
     * Process Payoneer checkout
     */
    protected function processPayoneerCheckout(Request $request, Order $order, $products, float $total, array $taxData)
    {
        if (!$this->payoneerService->isConfigured()) {
            return redirect()->back()->with('error', 'Payoneer is not configured. Please contact support.');
        }

        // Split name into first and last name
        $nameParts = explode(' ', $request->input('name'), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        // Get base currency for payment processing (always process in USD)
        $baseCurrency = Currency::getDefault();
        $paymentCurrency = $baseCurrency ? $baseCurrency->code : 'USD';

        // Prepare items for Payoneer (prices are stored in base currency)
        $items = [];
        $cart = session()->get('cart', []);
        foreach ($cart as $cartKey => $cartItem) {
            $productId = is_array($cartItem) ? ($cartItem['product_id'] ?? $cartKey) : $cartKey;
            $product = $products->firstWhere('id', $productId);
            if (!$product) continue;

            $price = is_array($cartItem) ? ($cartItem['price'] ?? $product->current_price) : $product->current_price;
            $variationName = is_array($cartItem) ? ($cartItem['variation_name'] ?? null) : null;
            $productName = $variationName ? $product->name . ' - ' . $variationName : $product->name;

            $items[] = [
                'name' => $productName,
                'price' => $price,
                'quantity' => 1,
            ];
        }

        // Add tax as a separate line item if applicable
        if ($taxData['tax_amount'] > 0) {
            $taxLabel = $this->taxService->getLabel();
            $stateCode = $taxData['state_code'] ?? '';
            $taxName = $stateCode ? "{$taxLabel} ({$stateCode})" : $taxLabel;

            $items[] = [
                'name' => $taxName,
                'price' => $taxData['tax_amount'],
                'quantity' => 1,
            ];
        }

        // Create Payoneer checkout session
        $checkoutData = [
            'transaction_id' => $order->order_number,
            'amount' => $total,
            'currency' => $paymentCurrency,
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
        return redirect()->route('checkout')->with('error', 'Payment was cancelled. Please try again.');
    }

    /**
     * Handle PayPal success callback
     */
    public function paypalSuccess(Request $request, Order $order)
    {
        // Verify the order belongs to the current user (if logged in)
        if (auth()->check() && $order->user_id && $order->user_id !== auth()->id()) {
            return redirect()->route('products.index')->with('error', 'Unauthorized access.');
        }

        $paypalOrderId = $request->query('token') ?? $order->paypal_order_id;

        if (!$paypalOrderId) {
            return redirect()->route('checkout.index')->with('error', 'Invalid PayPal order.');
        }

        try {
            // Capture the payment
            $captureResult = $this->paypalService->captureOrder($paypalOrderId);

            if ($captureResult && $captureResult['status'] === 'COMPLETED') {
                // Payment successful
                if ($order->status === 'pending') {
                    $this->completeOrder($order, 'paypal_' . ($captureResult['capture_id'] ?? $paypalOrderId));
                }

                // Clear cart
                session()->forget('cart');
                session()->forget('pending_order_items');

                return view('pages.checkout-success', compact('order'));
            }

            // Payment not completed
            return redirect()->route('checkout.index')->with('error', 'Payment was not completed. Please try again.');

        } catch (\Exception $e) {
            Log::error('PayPal success callback error: ' . $e->getMessage());
            return redirect()->route('products.index')->with('error', 'Error processing payment confirmation.');
        }
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

                    $seller = $product->seller;
                    $commissionRate = CommissionSettings::getCommissionRateForSeller($seller);
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

                    // Credit seller's wallet (only if seller and user exist)
                    if ($seller && $seller->user) {
                        $sellerWallet = $seller->user->getOrCreateWallet();
                        $sellerWallet->deposit(
                            amount: $sellerAmount,
                            description: 'Sale: ' . $product->name,
                            paymentMethod: 'product_sale',
                            paymentId: $order->order_number,
                            transactionable: $orderItem
                        );
                        $seller->increment('total_earnings', $sellerAmount);
                        $seller->increment('total_sales', $item['price']);
                    }

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

            // Load order with relationships
            $order->load('items.seller.user', 'items.product', 'user');

            // Send purchase confirmation email to buyer
            try {
                $order->user->notify(new ProductPurchaseConfirmation($order));
            } catch (\Exception $e) {
                Log::error('Failed to send purchase confirmation email: ' . $e->getMessage());
            }

            // Notify buyer about their order (push notification)
            $pushService->notifyOrderUpdate($order->user, [
                'order_id' => $order->order_number,
                'status' => 'Order Confirmed',
                'url' => route('purchases'),
            ]);

            // Group items by seller for notifications
            $sellerNotifications = [];
            foreach ($order->items as $item) {
                $sellerId = $item->seller_id;

                if (!isset($sellerNotifications[$sellerId])) {
                    $sellerNotifications[$sellerId] = [
                        'seller' => $item->seller,
                        'items' => collect(),
                        'total' => 0,
                    ];
                }

                $sellerNotifications[$sellerId]['items']->push($item);
                $sellerNotifications[$sellerId]['total'] += $item->seller_amount;
            }

            // Send notifications to each seller
            foreach ($sellerNotifications as $notification) {
                if ($notification['seller'] && $notification['seller']->user) {
                    $sellerUser = $notification['seller']->user;

                    // Send email notification to seller
                    try {
                        $sellerUser->notify(new NewSaleNotification(
                            $order,
                            $notification['items'],
                            $notification['total']
                        ));
                    } catch (\Exception $e) {
                        Log::error('Failed to send new sale email to seller: ' . $e->getMessage());
                    }

                    // Send push notification to seller
                    $productName = $notification['items']->count() > 1
                        ? $notification['items']->first()->product_name . ' +' . ($notification['items']->count() - 1) . ' more'
                        : $notification['items']->first()->product_name;

                    $pushService->notifyNewSale($sellerUser, [
                        'order_id' => $order->order_number,
                        'product_name' => $productName,
                        'amount' => format_price($notification['total']),
                        'url' => '/seller/orders',
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send order notifications: ' . $e->getMessage());
        }
    }
}
