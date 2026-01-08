<?php

namespace App\Http\Controllers;

use App\Filament\Admin\Pages\CommissionSettings;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Wallet;
use App\Models\Currency;
use App\Exceptions\Wallet\DuplicateTransactionException;
use App\Exceptions\Wallet\InsufficientBalanceException;
use App\Exceptions\Wallet\WalletException;
use App\Exceptions\Wallet\WalletFrozenException;
use App\Exceptions\Wallet\WalletInactiveException;
use App\Services\CurrencyService;
use App\Services\FraudDetectionService;
use App\Services\LicenseService;
use App\Services\PayoneerService;
use App\Services\PayPalService;
use App\Services\PushNotificationService;
use App\Services\StripeService;
use App\Services\TaxService;
use App\Services\WalletService;
use App\Services\WebhookProtectionService;
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
    protected WalletService $walletService;
    protected WebhookProtectionService $webhookProtection;

    public function __construct(
        StripeService $stripeService,
        PayoneerService $payoneerService,
        PayPalService $paypalService,
        TaxService $taxService,
        FraudDetectionService $fraudService,
        WalletService $walletService,
        WebhookProtectionService $webhookProtection
    ) {
        $this->stripeService = $stripeService;
        $this->payoneerService = $payoneerService;
        $this->paypalService = $paypalService;
        $this->taxService = $taxService;
        $this->fraudService = $fraudService;
        $this->walletService = $walletService;
        $this->webhookProtection = $webhookProtection;
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

        // Apply coupon discount if present in session
        $couponCode = null;
        $couponDiscount = 0;
        $couponData = session('coupon');
        if ($couponData && isset($couponData['id'])) {
            $coupon = \App\Models\Coupon::find($couponData['id']);
            if ($coupon && $coupon->isValid()) {
                // Re-check user usage limit before applying
                if (auth()->check() && $coupon->max_uses_per_user) {
                    $userUsage = \App\Models\CouponUsage::where('coupon_id', $coupon->id)
                        ->where('user_id', auth()->id())
                        ->count();
                    if ($userUsage < $coupon->max_uses_per_user) {
                        $couponDiscount = $coupon->calculateDiscount($total);
                        $couponCode = $coupon->code;
                    }
                } else {
                    $couponDiscount = $coupon->calculateDiscount($total);
                    $couponCode = $coupon->code;
                }
            }
        }
        $subtotalAfterDiscount = max(0, $total - $couponDiscount);

        // Calculate tax if enabled (on discounted subtotal)
        $taxData = $this->taxService->calculateTotals($subtotalAfterDiscount, 0, $billingState);
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
                'discount' => $couponDiscount,
                'coupon_code' => $couponCode,
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
     * Process Wallet checkout using WalletService with hold/capture pattern.
     */
    protected function processWalletCheckout(Request $request, Order $order, array $orderItems, float $total)
    {
        $isAjax = $request->expectsJson() || $request->ajax();

        // Generate idempotency key to prevent duplicate transactions
        $idempotencyKey = $this->walletService->generateCheckoutIdempotencyKey(
            $order,
            Auth::id() ?? 0,
            $total
        );

        Log::info('Checkout: Processing wallet payment', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'amount' => $total,
            'user_id' => Auth::id(),
            'idempotency_key' => $idempotencyKey,
        ]);

        // Authentication check
        if (!Auth::check()) {
            return $this->walletCheckoutError(
                $order,
                'Please log in to use wallet payment.',
                401,
                $isAjax
            );
        }

        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();

        try {
            // Validate wallet state
            $this->walletService->validateWallet($wallet);

            // Check if full wallet payment is possible
            if (!$this->walletService->canPurchase($wallet, $total)) {
                // Check for partial payment option
                if ($request->boolean('allow_partial_wallet')) {
                    return $this->processPartialWalletCheckout(
                        $request,
                        $order,
                        $orderItems,
                        $total,
                        $wallet
                    );
                }

                throw new InsufficientBalanceException(
                    $total,
                    $this->walletService->getAvailableBalance($wallet),
                    ['wallet_id' => $wallet->id]
                );
            }

            // Use hold/capture pattern for full wallet payment
            $hold = $this->walletService->holdFunds(
                wallet: $wallet,
                amount: $total,
                idempotencyKey: $idempotencyKey,
                description: 'Purchase: Order #' . $order->order_number,
                holdable: $order,
                metadata: ['order_items' => count($orderItems)]
            );

            // Store hold reference on order
            $order->update(['wallet_hold_id' => $hold->id]);

            // Capture immediately for direct checkout
            $transaction = $this->walletService->captureFunds(
                hold: $hold,
                description: 'Purchase: Order #' . $order->order_number,
                transactionable: $order
            );

            // Update order with transaction reference
            $order->update(['wallet_transaction_id' => $transaction->id]);

            // Store order items in session for completeOrder
            session()->put('pending_order_items', $orderItems);

            // Complete the order
            $this->completeOrder($order, 'wallet_' . $transaction->reference);

            Log::info('Checkout: Wallet payment successful', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'transaction_ref' => $transaction->reference,
                'hold_id' => $hold->id,
            ]);

            // Clear cart
            session()->forget(['cart', 'pending_order_items']);

            $redirectUrl = route('checkout.success') . '?order=' . $order->id;

            if ($isAjax) {
                return response()->json(['redirect' => $redirectUrl]);
            }
            return view('pages.checkout-success', compact('order'));

        } catch (InsufficientBalanceException $e) {
            Log::warning('Checkout: Wallet payment failed - insufficient balance', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'context' => $e->getContext(),
            ]);
            return $this->walletCheckoutError(
                $order,
                'Insufficient wallet balance. Please add funds or choose another payment method.',
                422,
                $isAjax
            );

        } catch (WalletFrozenException $e) {
            Log::warning('Checkout: Wallet payment failed - wallet frozen', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return $this->walletCheckoutError(
                $order,
                'Your wallet is frozen. Please contact support.',
                403,
                $isAjax
            );

        } catch (WalletInactiveException $e) {
            Log::warning('Checkout: Wallet payment failed - wallet inactive', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return $this->walletCheckoutError(
                $order,
                'Your wallet is not active. Please contact support.',
                403,
                $isAjax
            );

        } catch (DuplicateTransactionException $e) {
            Log::warning('Checkout: Duplicate transaction detected', [
                'order_id' => $order->id,
                'idempotency_key' => $idempotencyKey,
            ]);
            // Return success - transaction was already processed
            $redirectUrl = route('checkout.success') . '?order=' . $order->id;
            if ($isAjax) {
                return response()->json(['redirect' => $redirectUrl]);
            }
            return redirect($redirectUrl);

        } catch (WalletException $e) {
            Log::error('Checkout: Wallet payment error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'error_code' => $e->getErrorCode(),
                'context' => $e->getContext(),
            ]);
            return $this->walletCheckoutError(
                $order,
                'Payment failed. Please try again.',
                422,
                $isAjax
            );

        } catch (\Exception $e) {
            Log::error('Checkout: Wallet payment unexpected error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->walletCheckoutError(
                $order,
                'Payment failed. Please try again.',
                422,
                $isAjax
            );
        }
    }

    /**
     * Process partial wallet checkout (wallet + secondary payment).
     */
    protected function processPartialWalletCheckout(
        Request $request,
        Order $order,
        array $orderItems,
        float $total,
        Wallet $wallet
    ) {
        $isAjax = $request->expectsJson() || $request->ajax();
        $secondaryMethod = $request->input('secondary_payment_method', 'stripe');

        Log::info('Checkout: Processing partial wallet payment', [
            'order_id' => $order->id,
            'total' => $total,
            'secondary_method' => $secondaryMethod,
        ]);

        // Calculate split
        $split = $this->walletService->calculatePartialPayment($wallet, $total);

        if ($split['wallet_amount'] <= 0) {
            // No wallet balance, redirect to secondary payment
            Log::info('Checkout: No wallet balance for partial payment, using full secondary', [
                'order_id' => $order->id,
            ]);
            $order->update([
                'payment_method' => $secondaryMethod,
            ]);

            // Store order items and redirect to secondary payment
            session()->put('pending_order_items', $orderItems);

            if ($secondaryMethod === 'paypal') {
                return $this->processPayPalCheckout($request, $order, $orderItems, [
                    'tax_amount' => $order->tax_amount ?? 0,
                ]);
            }
            return $this->processStripeCheckout($request, $order, $orderItems, [
                'tax_amount' => $order->tax_amount ?? 0,
            ]);
        }

        $idempotencyKey = 'partial_hold_' . $order->order_number;

        try {
            // Hold wallet funds
            $hold = $this->walletService->holdFunds(
                wallet: $wallet,
                amount: $split['wallet_amount'],
                idempotencyKey: $idempotencyKey,
                description: 'Partial payment: Order #' . $order->order_number,
                holdable: $order,
                metadata: [
                    'total_amount' => $total,
                    'remaining_amount' => $split['remaining_amount'],
                    'secondary_method' => $secondaryMethod,
                ],
                expirationMinutes: 30
            );

            // Update order with partial payment info
            $order->update([
                'wallet_amount' => $split['wallet_amount'],
                'wallet_hold_id' => $hold->id,
                'payment_method' => 'wallet_' . $secondaryMethod,
                'secondary_payment_method' => $secondaryMethod,
            ]);

            Log::info('Checkout: Wallet hold created for partial payment', [
                'order_id' => $order->id,
                'hold_id' => $hold->id,
                'wallet_amount' => $split['wallet_amount'],
                'remaining_amount' => $split['remaining_amount'],
            ]);

            // Store order items
            session()->put('pending_order_items', $orderItems);

            // Create modified order items for secondary payment (only remaining amount)
            // For now, we'll pass the remaining amount in the tax data
            $taxData = [
                'tax_amount' => $order->tax_amount ?? 0,
                'partial_wallet_amount' => $split['wallet_amount'],
            ];

            // Update order total to reflect only the remaining amount for secondary payment
            $order->update([
                'total' => $split['remaining_amount'] + ($order->tax_amount ?? 0),
            ]);

            // Redirect to secondary payment for remaining amount
            if ($secondaryMethod === 'paypal') {
                return $this->processPayPalCheckout($request, $order, $orderItems, $taxData);
            }
            return $this->processStripeCheckout($request, $order, $orderItems, $taxData);

        } catch (WalletException $e) {
            // If wallet hold fails, proceed with full secondary payment
            Log::warning('Checkout: Partial wallet hold failed, using full secondary payment', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            $order->update([
                'payment_method' => $secondaryMethod,
                'wallet_amount' => null,
                'wallet_hold_id' => null,
            ]);

            session()->put('pending_order_items', $orderItems);

            if ($secondaryMethod === 'paypal') {
                return $this->processPayPalCheckout($request, $order, $orderItems, [
                    'tax_amount' => $order->tax_amount ?? 0,
                ]);
            }
            return $this->processStripeCheckout($request, $order, $orderItems, [
                'tax_amount' => $order->tax_amount ?? 0,
            ]);
        }
    }

    /**
     * Helper to handle wallet checkout errors consistently.
     */
    protected function walletCheckoutError(Order $order, string $message, int $code, bool $isAjax)
    {
        $order->update(['status' => 'failed']);

        if ($isAjax) {
            return response()->json(['message' => $message], $code);
        }

        $route = $code === 401 ? 'login' : 'checkout';
        return redirect()->route($route)->with('error', $message);
    }

    /**
     * Process PayPal checkout
     */
    protected function processPayPalCheckout(Request $request, Order $order, array $orderItems, array $taxData)
    {
        $isAjax = $request->expectsJson() || $request->ajax();

        Log::info('Checkout: Processing PayPal payment', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'amount' => $order->total,
            'user_id' => Auth::id(),
        ]);

        if (!$this->paypalService->isConfigured()) {
            $order->update(['status' => 'failed']);
            Log::warning('Checkout: PayPal not configured', ['order_id' => $order->id]);
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

        if (!$paypalOrder || !isset($paypalOrder['approval_url'])) {
            $order->update(['status' => 'failed']);
            Log::error('Checkout: Failed to create PayPal order', [
                'order_id' => $order->id,
                'paypal_response' => $paypalOrder,
            ]);
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

        Log::info('Checkout: PayPal order created, redirecting', [
            'order_id' => $order->id,
            'paypal_order_id' => $paypalOrder['id'],
        ]);

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

        // Check if Stripe is configured
        if (!$this->stripeService->isConfigured()) {
            $order->update(['status' => 'failed']);
            if ($isAjax) {
                return response()->json(['message' => 'Stripe is not configured. Please contact support.'], 422);
            }
            return redirect()->back()->with('error', 'Stripe is not configured. Please contact support.');
        }

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
        $orderId = $request->query('order');

        // Handle wallet/direct payment success (order ID provided)
        if ($orderId && !$sessionId) {
            Log::info('Checkout: Success page - wallet/direct payment', [
                'order_id' => $orderId,
                'user_id' => auth()->id(),
            ]);

            $order = Order::where('id', $orderId)
                ->where('status', 'completed')
                ->first();

            if (!$order) {
                Log::warning('Checkout: Success page - order not found or not completed', [
                    'order_id' => $orderId,
                ]);
                return redirect()->route('products.index')->with('error', 'Order not found.');
            }

            // Verify ownership - if order has a user_id, check authorization
            // Note: SameSite=strict cookies may not be sent on cross-site redirects from payment providers
            if ($order->user_id) {
                if (auth()->check()) {
                    // User is authenticated - verify they own this order
                    if ((int) $order->user_id !== auth()->id()) {
                        Log::warning('Checkout: Success page - unauthorized access', [
                            'order_id' => $orderId,
                            'order_user_id' => $order->user_id,
                            'current_user_id' => auth()->id(),
                        ]);
                        return redirect()->route('products.index')->with('error', 'Unauthorized access.');
                    }
                } else {
                    // User is not authenticated (session lost due to SameSite=strict)
                    // Redirect to login with success message
                    Log::info('Checkout: Success page - session lost, redirecting to login', [
                        'order_id' => $orderId,
                        'order_number' => $order->order_number,
                    ]);
                    return redirect()->route('login')->with('success', 'Your order #' . $order->order_number . ' has been completed! Please log in to view your order details.');
                }
            }

            // Clear cart
            session()->forget('cart');
            session()->forget('pending_order_items');

            return view('pages.checkout-success', compact('order'));
        }

        // Handle Stripe payment success (session_id provided)
        if (!$sessionId) {
            return redirect()->route('products.index');
        }

        Log::info('Checkout: Success page - Stripe payment', [
            'session_id' => $sessionId,
            'user_id' => auth()->id(),
        ]);

        try {
            // Retrieve the session from Stripe
            $session = StripeSession::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                Log::warning('Checkout: Stripe payment not completed', [
                    'session_id' => $sessionId,
                    'payment_status' => $session->payment_status,
                ]);
                return redirect()->route('checkout')->with('error', 'Payment was not completed.');
            }

            // Find the order
            $order = Order::where('stripe_session_id', $sessionId)->first();

            if (!$order) {
                Log::warning('Checkout: Order not found for Stripe session', ['session_id' => $sessionId]);
                return redirect()->route('products.index')->with('error', 'Order not found.');
            }

            // If order is still pending, complete it
            if ($order->status === 'pending') {
                $this->completeOrder($order, $session->payment_intent);
            }

            Log::info('Checkout: Stripe payment successful', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_intent' => $session->payment_intent,
            ]);

            // Clear cart
            session()->forget('cart');
            session()->forget('pending_order_items');

            return view('pages.checkout-success', compact('order'));

        } catch (\Exception $e) {
            Log::error('Checkout: Stripe success error', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);
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
        Log::info('Checkout: PayPal success callback', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'user_id' => auth()->id(),
            'token' => $request->query('token'),
        ]);

        // Verify ownership - if order has a user_id, check authorization
        // Note: SameSite=strict cookies may not be sent on cross-site redirects from payment providers
        if ($order->user_id) {
            if (auth()->check()) {
                // User is authenticated - verify they own this order
                if ((int) $order->user_id !== auth()->id()) {
                    Log::warning('Checkout: PayPal success - unauthorized access', [
                        'order_id' => $order->id,
                        'order_user_id' => $order->user_id,
                        'current_user_id' => auth()->id(),
                    ]);
                    return redirect()->route('products.index')->with('error', 'Unauthorized access.');
                }
            } else {
                // User is not authenticated (session lost due to SameSite=strict)
                // Still capture the payment but redirect to login with success message
                Log::info('Checkout: PayPal success - session lost, will redirect to login after capture', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]);
            }
        }

        $paypalOrderId = $request->query('token') ?? $order->paypal_order_id;

        if (!$paypalOrderId) {
            Log::warning('Checkout: PayPal success - no order ID', ['order_id' => $order->id]);
            return redirect()->route('checkout')->with('error', 'Invalid PayPal order.');
        }

        try {
            // Capture the payment
            $captureResult = $this->paypalService->captureOrder($paypalOrderId);

            if ($captureResult && $captureResult['status'] === 'COMPLETED') {
                // Handle partial wallet payment: capture wallet hold if exists
                if ($order->wallet_hold_id) {
                    try {
                        $hold = $this->walletService->findHold($order->wallet_hold_id);
                        if ($hold->canCapture()) {
                            $walletTransaction = $this->walletService->captureFunds(
                                hold: $hold,
                                description: 'Partial payment captured: Order #' . $order->order_number,
                                transactionable: $order
                            );

                            $order->update([
                                'wallet_transaction_id' => $walletTransaction->id,
                            ]);

                            Log::info('Checkout: Wallet hold captured for PayPal partial payment', [
                                'order_id' => $order->id,
                                'hold_id' => $hold->id,
                                'transaction_id' => $walletTransaction->id,
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Checkout: Failed to capture wallet hold for PayPal payment', [
                            'order_id' => $order->id,
                            'hold_id' => $order->wallet_hold_id,
                            'error' => $e->getMessage(),
                        ]);
                        // Continue with order completion even if wallet capture fails
                    }
                }

                // Payment successful
                if ($order->status === 'pending') {
                    $this->completeOrder($order, 'paypal_' . ($captureResult['capture_id'] ?? $paypalOrderId));
                }

                Log::info('Checkout: PayPal payment successful', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'capture_id' => $captureResult['capture_id'] ?? null,
                ]);

                // Clear cart if session exists
                session()->forget('cart');
                session()->forget('pending_order_items');

                // If user's session was lost (SameSite=strict), redirect to login
                if ($order->user_id && !auth()->check()) {
                    return redirect()->route('login')->with('success', 'Your order #' . $order->order_number . ' has been completed! Please log in to view your order details.');
                }

                return view('pages.checkout-success', compact('order'));
            }

            // Payment not completed - release wallet hold if exists
            if ($order->wallet_hold_id) {
                try {
                    $hold = $this->walletService->findHold($order->wallet_hold_id);
                    $this->walletService->releaseFunds($hold, 'PayPal payment not completed');
                    Log::info('Checkout: Wallet hold released - PayPal payment not completed', [
                        'order_id' => $order->id,
                        'hold_id' => $hold->id,
                    ]);
                } catch (\Exception $releaseError) {
                    Log::error('Checkout: Failed to release wallet hold', [
                        'order_id' => $order->id,
                        'error' => $releaseError->getMessage(),
                    ]);
                }
            }

            Log::warning('Checkout: PayPal payment not completed', [
                'order_id' => $order->id,
                'capture_status' => $captureResult['status'] ?? 'unknown',
            ]);
            return redirect()->route('checkout')->with('error', 'Payment was not completed. Please try again.');

        } catch (\Exception $e) {
            // Release wallet hold on error
            if ($order->wallet_hold_id) {
                try {
                    $hold = $this->walletService->findHold($order->wallet_hold_id);
                    $this->walletService->releaseFunds($hold, 'PayPal payment error');
                } catch (\Exception $releaseError) {
                    Log::error('Checkout: Failed to release wallet hold on PayPal error', [
                        'order_id' => $order->id,
                        'error' => $releaseError->getMessage(),
                    ]);
                }
            }

            Log::error('Checkout: PayPal success callback error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('products.index')->with('error', 'Error processing payment confirmation.');
        }
    }

    /**
     * Handle Payoneer success callback
     */
    public function payoneerSuccess(Request $request, Order $order)
    {
        Log::info('Checkout: Payoneer success callback', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'user_id' => auth()->id(),
        ]);

        // Verify ownership - if order has a user_id, check authorization
        // Note: SameSite=strict cookies may not be sent on cross-site redirects from payment providers
        if ($order->user_id) {
            if (auth()->check()) {
                // User is authenticated - verify they own this order
                if ((int) $order->user_id !== auth()->id()) {
                    Log::warning('Checkout: Payoneer success - unauthorized access', [
                        'order_id' => $order->id,
                        'order_user_id' => $order->user_id,
                        'current_user_id' => auth()->id(),
                    ]);
                    return redirect()->route('products.index')->with('error', 'Unauthorized access.');
                }
            } else {
                // User is not authenticated (session lost due to SameSite=strict)
                // Still process the payment but redirect to login with success message
                Log::info('Checkout: Payoneer success - session lost, will redirect to login after processing', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]);
            }
        }

        try {
            // Get the transaction status from Payoneer
            $status = $this->payoneerService->getCheckoutStatus($order->order_number);

            if ($status && isset($status['status']) && in_array($status['status'], ['CHARGED', 'PAID', 'COMPLETED'])) {
                // Payment successful
                if ($order->status === 'pending') {
                    $this->completeOrder($order, $status['transactionId'] ?? null);
                }

                Log::info('Checkout: Payoneer payment successful', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'transaction_id' => $status['transactionId'] ?? null,
                ]);

                // Clear cart if session exists
                session()->forget('cart');
                session()->forget('pending_order_items');

                // If user's session was lost (SameSite=strict), redirect to login
                if ($order->user_id && !auth()->check()) {
                    return redirect()->route('login')->with('success', 'Your order #' . $order->order_number . ' has been completed! Please log in to view your order details.');
                }

                return view('pages.checkout-success', compact('order'));
            }

            // Payment not confirmed yet - check if still processing
            if ($status && isset($status['status']) && in_array($status['status'], ['PENDING', 'PROCESSING'])) {
                Log::info('Checkout: Payoneer payment processing', [
                    'order_id' => $order->id,
                    'status' => $status['status'],
                ]);

                // If user's session was lost (SameSite=strict), redirect to login
                if ($order->user_id && !auth()->check()) {
                    return redirect()->route('login')->with('success', 'Your order #' . $order->order_number . ' is being processed. Please log in to check your order status.');
                }

                return view('pages.checkout-success', compact('order'))
                    ->with('warning', 'Your payment is being processed. You will receive confirmation shortly.');
            }

            // Payment failed or was declined
            Log::warning('Checkout: Payoneer payment not completed', [
                'order_id' => $order->id,
                'status' => $status['status'] ?? 'unknown',
            ]);
            return redirect()->route('checkout')->with('error', 'Payment was not completed. Please try again.');

        } catch (\Exception $e) {
            Log::error('Checkout: Payoneer success callback error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('products.index')->with('error', 'Error processing payment confirmation.');
        }
    }

    /**
     * Handle Payoneer webhook
     */
    public function payoneerWebhook(Request $request)
    {
        $payload = $request->getContent();

        // Verify webhook signature - REQUIRED for security
        $signature = $request->header('X-Payoneer-Signature', '');
        if (!$this->payoneerService->verifyWebhook($payload, $signature)) {
            Log::warning('Payoneer webhook rejected: Invalid signature', [
                'ip' => $request->ip(),
            ]);
            return response('Invalid signature', 401);
        }

        // Replay attack prevention
        $validation = $this->webhookProtection->validatePayoneerWebhook($request);
        if (!$validation['valid']) {
            Log::warning('Payoneer webhook replay detected', [
                'error' => $validation['error'],
                'ip' => $request->ip(),
            ]);
            return response($validation['error'], 400);
        }

        $webhookRecord = $validation['webhook'];

        $data = json_decode($payload, true);
        if (!$data) {
            Log::warning('Payoneer webhook rejected: Invalid JSON payload');
            $webhookRecord?->markFailed();
            return response('Invalid payload', 400);
        }

        $transactionId = $data['transactionId'] ?? null;
        $status = $data['status'] ?? null;
        $reference = $data['reference'] ?? $data['payment']['reference'] ?? null;

        // Update webhook record with event type
        if ($webhookRecord && $status) {
            $webhookRecord->update(['event_type' => 'payment.' . strtolower($status)]);
        }

        Log::info('Payoneer webhook received', [
            'transactionId' => $transactionId,
            'status' => $status,
            'reference' => $reference,
        ]);

        if (!$reference && !$transactionId) {
            $webhookRecord?->markFailed();
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
                    // Capture wallet hold if this was a partial wallet + Payoneer payment
                    if ($order->wallet_hold_id) {
                        try {
                            $hold = $this->walletService->findHold($order->wallet_hold_id);
                            if ($hold->canCapture()) {
                                $walletTransaction = $this->walletService->captureFunds(
                                    hold: $hold,
                                    description: 'Partial payment captured: Order #' . $order->order_number,
                                    transactionable: $order
                                );

                                $order->update([
                                    'wallet_transaction_id' => $walletTransaction->id,
                                ]);

                                Log::info('Payoneer webhook: Wallet hold captured for partial payment', [
                                    'order_id' => $order->id,
                                    'hold_id' => $hold->id,
                                    'transaction_id' => $walletTransaction->id,
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('Payoneer webhook: Failed to capture wallet hold', [
                                'order_id' => $order->id,
                                'hold_id' => $order->wallet_hold_id,
                                'error' => $e->getMessage(),
                            ]);
                            // Continue with order completion even if wallet capture fails
                            // The hold will expire and be released automatically
                        }
                    }

                    $this->completeOrder($order, $transactionId);
                }
                break;

            case 'FAILED':
            case 'DECLINED':
            case 'CANCELED':
                $order->update(['status' => 'failed']);

                // Release wallet hold if payment was partial wallet + Payoneer
                if ($order->wallet_hold_id) {
                    try {
                        $hold = $this->walletService->findHold($order->wallet_hold_id);
                        if ($hold->canRelease()) {
                            $this->walletService->releaseFunds($hold, 'Payoneer payment ' . strtolower($status));
                            Log::info('Payoneer webhook: Wallet hold released on payment failure', [
                                'order_id' => $order->id,
                                'hold_id' => $hold->id,
                                'reason' => $status,
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Payoneer webhook: Failed to release wallet hold', [
                            'order_id' => $order->id,
                            'hold_id' => $order->wallet_hold_id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                Log::info('Payoneer order marked as failed: ' . $order->order_number);
                break;

            default:
                Log::info('Unhandled Payoneer status: ' . $status);
        }

        // Mark webhook as successfully processed
        $webhookRecord?->markCompleted();

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

        // Replay attack prevention: validate event hasn't been processed
        $validation = $this->webhookProtection->validateStripeEvent($event, $request);
        if (!$validation['valid']) {
            Log::warning('Stripe webhook replay detected', [
                'event_id' => $event->id,
                'event_type' => $event->type,
                'error' => $validation['error'],
                'ip' => $request->ip(),
            ]);
            return response($validation['error'], 400);
        }

        $webhookRecord = $validation['webhook'];

        try {
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

            // Mark webhook as successfully processed
            $webhookRecord?->markCompleted();

            return response('Webhook handled', 200);

        } catch (\Exception $e) {
            // Mark webhook as failed but don't delete (allow investigation)
            $webhookRecord?->markFailed();

            Log::error('Stripe webhook processing error', [
                'event_id' => $event->id,
                'event_type' => $event->type,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function handleCheckoutCompleted($session)
    {
        // Check if this is a wallet deposit (based on metadata)
        if (isset($session->metadata->type) && $session->metadata->type === 'wallet_deposit') {
            $this->handleWalletDepositWebhook($session);
            return;
        }

        $order = Order::where('stripe_session_id', $session->id)->first();

        if (!$order) {
            Log::error('Order not found for session: ' . $session->id);
            return;
        }

        if ($order->status === 'pending') {
            // Handle partial wallet payment: capture wallet hold if exists
            if ($order->wallet_hold_id) {
                try {
                    $hold = $this->walletService->findHold($order->wallet_hold_id);
                    if ($hold->canCapture()) {
                        $walletTransaction = $this->walletService->captureFunds(
                            hold: $hold,
                            description: 'Partial payment captured: Order #' . $order->order_number,
                            transactionable: $order
                        );

                        $order->update([
                            'wallet_transaction_id' => $walletTransaction->id,
                        ]);

                        Log::info('Checkout: Wallet hold captured for partial payment', [
                            'order_id' => $order->id,
                            'hold_id' => $hold->id,
                            'transaction_id' => $walletTransaction->id,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Checkout: Failed to capture wallet hold', [
                        'order_id' => $order->id,
                        'hold_id' => $order->wallet_hold_id,
                        'error' => $e->getMessage(),
                    ]);
                    // Continue with order completion even if wallet capture fails
                }
            }

            $this->completeOrder($order, $session->payment_intent);
        }
    }

    protected function handlePaymentFailed($paymentIntent)
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                // Release wallet hold if exists
                if ($order->wallet_hold_id) {
                    try {
                        $hold = $this->walletService->findHold($order->wallet_hold_id);
                        $this->walletService->releaseFunds($hold, 'Secondary payment failed');

                        Log::info('Checkout: Wallet hold released due to secondary payment failure', [
                            'order_id' => $order->id,
                            'hold_id' => $hold->id,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Checkout: Failed to release wallet hold', [
                            'order_id' => $order->id,
                            'hold_id' => $order->wallet_hold_id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                $order->update(['status' => 'failed']);
                Log::info('Order marked as failed: ' . $orderId);
            }
        }
    }

    /**
     * Handle wallet deposit via webhook (fallback for redirect failures).
     * This ensures deposits are credited even if user closes browser after payment.
     */
    protected function handleWalletDepositWebhook($session): void
    {
        $sessionId = $session->id;
        $userId = $session->metadata->user_id ?? null;
        $walletId = $session->metadata->wallet_id ?? null;
        $amount = (float) ($session->metadata->amount ?? 0);

        if (!$userId || !$amount) {
            Log::error('Wallet deposit webhook: Missing required metadata', [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'amount' => $amount,
            ]);
            return;
        }

        // Check if already processed (idempotency)
        $existingTransaction = \App\Models\WalletTransaction::where('payment_id', $sessionId)->first();
        if ($existingTransaction) {
            Log::info('Wallet deposit already processed via webhook', [
                'session_id' => $sessionId,
                'transaction_id' => $existingTransaction->id,
            ]);
            return;
        }

        // Find or create wallet
        $wallet = \App\Models\Wallet::where('user_id', $userId)->first();
        if (!$wallet) {
            $user = \App\Models\User::find($userId);
            if ($user) {
                $wallet = $user->getOrCreateWallet();
            }
        }

        if (!$wallet) {
            Log::error('Wallet deposit webhook: Wallet not found', [
                'session_id' => $sessionId,
                'user_id' => $userId,
            ]);
            return;
        }

        try {
            // Process the deposit
            $wallet->deposit(
                amount: $amount,
                description: 'Wallet deposit via Stripe (webhook)',
                paymentMethod: 'stripe',
                paymentId: $sessionId
            );

            Log::info('Wallet deposit processed via webhook', [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'amount' => $amount,
            ]);
        } catch (\Exception $e) {
            Log::error('Wallet deposit webhook failed: ' . $e->getMessage(), [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'amount' => $amount,
            ]);
        }
    }

    protected function completeOrder(Order $order, $paymentIntentId = null)
    {
        try {
            DB::beginTransaction();

            // Lock order to prevent race conditions (concurrent webhooks, page refreshes)
            $order = Order::where('id', $order->id)->lockForUpdate()->first();

            // Check if order was already completed by another process
            if ($order->status !== 'pending') {
                DB::rollBack();
                Log::info('Checkout: Order already processed, skipping', [
                    'order_id' => $order->id,
                    'status' => $order->status,
                ]);
                return;
            }

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

            // Record coupon usage if a coupon was applied (with locking to prevent race conditions)
            if ($order->coupon_code) {
                // Lock the coupon row to prevent concurrent usage
                $coupon = \App\Models\Coupon::where('code', $order->coupon_code)
                    ->lockForUpdate()
                    ->first();

                if ($coupon) {
                    // Check if coupon has already been used for this order (idempotency)
                    $existingUsage = \App\Models\CouponUsage::where('coupon_id', $coupon->id)
                        ->where('order_id', $order->id)
                        ->exists();

                    if (!$existingUsage) {
                        // Verify usage limit hasn't been exceeded
                        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                            Log::warning('Coupon usage limit exceeded during order completion', [
                                'coupon_code' => $order->coupon_code,
                                'order_id' => $order->id,
                            ]);
                        } else {
                            // Create usage record
                            \App\Models\CouponUsage::create([
                                'coupon_id' => $coupon->id,
                                'user_id' => $order->user_id,
                                'order_id' => $order->id,
                                'discount_amount' => $order->discount ?? 0,
                            ]);
                            // Increment global usage counter
                            $coupon->increment('used_count');
                            Log::info('Coupon usage recorded', [
                                'coupon_code' => $order->coupon_code,
                                'order_id' => $order->id,
                            ]);
                        }
                    }
                }
            }

            // Process referral commission if user was referred
            try {
                $order->user->processReferralPurchaseCommission($order);
            } catch (\Exception $e) {
                Log::error('Failed to process referral commission: ' . $e->getMessage());
            }

            // Clear coupon from session after successful order
            session()->forget('coupon');

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
