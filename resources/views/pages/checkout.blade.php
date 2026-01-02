<x-layouts.app title="Checkout - Codexse">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Checkout</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Complete your purchase securely</p>
            </div>

            <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Checkout Form -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Global Error Alert -->
                        <div id="form-errors" class="hidden rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-semibold text-red-800 dark:text-red-200">Please fix the following errors:</h3>
                                    <ul id="error-list" class="mt-2 text-sm text-red-700 dark:text-red-300 list-disc list-inside space-y-1"></ul>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4 sm:p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-6">Contact Information</h2>
                            <div class="space-y-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Email Address</label>
                                    <input type="email" id="email" name="email" value="{{ auth()->user()->email ?? old('email') }}"
                                        class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 px-4 py-3 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-colors"
                                        data-validate="required|email">
                                    <p id="email-error" class="mt-1 text-sm text-red-500 hidden"></p>
                                </div>
                                <div>
                                    <label for="name" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Full Name</label>
                                    <input type="text" id="name" name="name" value="{{ auth()->user()->name ?? old('name') }}"
                                        class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 px-4 py-3 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-colors"
                                        data-validate="required">
                                    <p id="name-error" class="mt-1 text-sm text-red-500 hidden"></p>
                                </div>

                                @if($taxEnabled)
                                <div>
                                    <label for="billing_state" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                        US State
                                        <span class="text-surface-400 font-normal">(for tax calculation)</span>
                                    </label>
                                    <select id="billing_state" name="billing_state"
                                        class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 px-4 py-3 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                        <option value="">Select your state (optional)</option>
                                        @foreach($usStates as $code => $name)
                                            <option value="{{ $code }}" {{ $savedState === $code ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">
                                        Tax will be calculated based on your state's tax rate
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Payment Method -->
                        @php
                            $defaultPayment = 'wallet';
                            if (!($canPayWithWallet ?? false)) {
                                if (($stripeConfigured ?? false) && \App\Models\Setting::get('stripe_enabled', true)) {
                                    $defaultPayment = 'stripe';
                                } elseif (($paypalConfigured ?? false) && \App\Models\Setting::get('paypal_enabled', false)) {
                                    $defaultPayment = 'paypal';
                                } elseif (($payoneerConfigured ?? false) && \App\Models\Setting::get('payoneer_enabled', false)) {
                                    $defaultPayment = 'payoneer';
                                } else {
                                    $defaultPayment = '';
                                }
                            }
                        @endphp
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4 sm:p-6" x-data="{ selectedMethod: '{{ $defaultPayment }}' }">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4 sm:mb-6">Payment Method</h2>
                            <div class="space-y-3">
                                {{-- Wallet Payment Option --}}
                                @auth
                                    @if(isset($wallet))
                                    <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-colors {{ !$canPayWithWallet ? 'opacity-60' : '' }}"
                                        :class="selectedMethod === 'wallet' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-surface-200 dark:border-surface-700 {{ $canPayWithWallet ? 'hover:border-primary-300 dark:hover:border-primary-700' : '' }}'">
                                        <input type="radio" name="payment_method" value="wallet" x-model="selectedMethod" {{ !$canPayWithWallet ? 'disabled' : '' }} class="h-5 w-5 text-primary-600 focus:ring-primary-500">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-surface-900 dark:text-white">Wallet Balance</span>
                                                @if($canPayWithWallet)
                                                    <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Recommended</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-surface-500 dark:text-surface-400">
                                                Available: <span class="font-semibold {{ $canPayWithWallet ? 'text-green-600' : 'text-red-500' }}">{{ $wallet->formatted_balance }}</span>
                                                @if(!$canPayWithWallet)
                                                    <span class="text-red-500">- Insufficient balance</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-indigo-600 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                        </div>
                                    </label>
                                    @if(!$canPayWithWallet)
                                        <div class="flex items-center justify-between px-4 py-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                                            <span class="text-sm text-amber-700 dark:text-amber-400">Need more funds?</span>
                                            <a href="{{ route('wallet.deposit') }}" class="text-sm font-medium text-amber-700 dark:text-amber-400 hover:underline">Add funds to wallet</a>
                                        </div>
                                    @endif
                                    @endif
                                @endauth

                                @if(\App\Models\Setting::get('stripe_enabled', true) && ($stripeConfigured ?? false))
                                <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-colors"
                                    :class="selectedMethod === 'stripe' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-surface-200 dark:border-surface-700 hover:border-primary-300 dark:hover:border-primary-700'">
                                    <input type="radio" name="payment_method" value="stripe" x-model="selectedMethod" class="h-5 w-5 text-primary-600 focus:ring-primary-500">
                                    <div class="flex-1">
                                        <span class="font-medium text-surface-900 dark:text-white">Credit Card</span>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">Pay securely with Stripe</p>
                                    </div>
                                    <div class="flex gap-1.5">
                                        <img src="{{ asset('images/cards/visa.svg') }}" alt="Visa" class="h-6 w-auto">
                                        <img src="{{ asset('images/cards/mastercard.svg') }}" alt="Mastercard" class="h-6 w-auto">
                                    </div>
                                </label>

                                <!-- Stripe Checkout Info -->
                                <div x-show="selectedMethod === 'stripe'" x-transition class="mt-4 p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-surface-900 dark:text-white">Secure Stripe Checkout</h4>
                                            <p class="mt-1 text-sm text-surface-600 dark:text-surface-400">
                                                You'll be redirected to Stripe's secure payment page to enter your card details.
                                            </p>
                                            <div class="mt-3 flex items-center gap-2 flex-wrap">
                                                <img src="{{ asset('images/cards/visa.svg') }}" alt="Visa" class="h-8 w-auto">
                                                <img src="{{ asset('images/cards/mastercard.svg') }}" alt="Mastercard" class="h-8 w-auto">
                                                <img src="{{ asset('images/cards/amex.svg') }}" alt="American Express" class="h-8 w-auto">
                                                <img src="{{ asset('images/cards/discover.svg') }}" alt="Discover" class="h-8 w-auto">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if(\App\Models\Setting::get('paypal_enabled', false) && ($paypalConfigured ?? false))
                                <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-colors"
                                    :class="selectedMethod === 'paypal' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-surface-200 dark:border-surface-700 hover:border-primary-300 dark:hover:border-primary-700'">
                                    <input type="radio" name="payment_method" value="paypal" x-model="selectedMethod" class="h-5 w-5 text-primary-600 focus:ring-primary-500">
                                    <div class="flex-1">
                                        <span class="font-medium text-surface-900 dark:text-white">PayPal</span>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">Pay with your PayPal account</p>
                                    </div>
                                    <div class="w-14 h-8 bg-[#003087] rounded flex items-center justify-center" title="PayPal">
                                        <span class="text-[10px] font-bold text-white italic">Pay<span class="text-[#009CDE]">Pal</span></span>
                                    </div>
                                </label>

                                <!-- PayPal Info -->
                                <div x-show="selectedMethod === 'paypal'" x-transition class="mt-4 p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-surface-900 dark:text-white">PayPal Checkout</h4>
                                            <p class="mt-1 text-sm text-surface-600 dark:text-surface-400">
                                                You'll be redirected to PayPal to complete your payment securely.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if(\App\Models\Setting::get('payoneer_enabled', false) && ($payoneerConfigured ?? false))
                                <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-colors"
                                    :class="selectedMethod === 'payoneer' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-surface-200 dark:border-surface-700 hover:border-primary-300 dark:hover:border-primary-700'">
                                    <input type="radio" name="payment_method" value="payoneer" x-model="selectedMethod" class="h-5 w-5 text-primary-600 focus:ring-primary-500">
                                    <div class="flex-1">
                                        <span class="font-medium text-surface-900 dark:text-white">Payoneer</span>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">Pay with local payment methods, cards, or bank transfer</p>
                                    </div>
                                    <div class="w-20 h-8 bg-gradient-to-r from-orange-500 to-orange-600 rounded flex items-center justify-center">
                                        <span class="text-xs font-bold text-white">Payoneer</span>
                                    </div>
                                </label>

                                <!-- Payoneer Info -->
                                <div x-show="selectedMethod === 'payoneer'" x-transition class="mt-4 p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-surface-900 dark:text-white">Payoneer Checkout</h4>
                                            <p class="mt-1 text-sm text-surface-600 dark:text-surface-400">
                                                Pay using local payment methods, credit cards, or bank transfers worldwide.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- No Payment Methods Warning --}}
                                @if(empty($defaultPayment) && !($canPayWithWallet ?? false))
                                <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <div>
                                            <p class="font-medium text-red-700 dark:text-red-300">No payment methods available</p>
                                            <p class="text-sm text-red-600 dark:text-red-400 mt-1">Please contact support or try again later.</p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4 sm:p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4 sm:mb-6">Order Notes (Optional)</h2>
                            <textarea name="notes" rows="3" placeholder="Any special instructions..."
                                class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 px-4 py-3 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"></textarea>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4 sm:p-6 lg:sticky lg:top-24">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-6">Order Summary</h2>

                            <!-- Cart Items -->
                            <div class="space-y-4 mb-6">
                                @foreach($cart as $cartKey => $cartItem)
                                    @php
                                        $productId = is_array($cartItem) ? ($cartItem['product_id'] ?? $cartKey) : $cartKey;
                                        $product = $products->firstWhere('id', $productId);
                                        if (!$product) continue;
                                        $itemPrice = is_array($cartItem) ? ($cartItem['price'] ?? $product->current_price) : $product->current_price;
                                        $variationName = is_array($cartItem) ? ($cartItem['variation_name'] ?? null) : null;
                                    @endphp
                                    <div class="flex gap-3">
                                        <div class="w-16 h-16 rounded-lg overflow-hidden bg-surface-100 dark:bg-surface-700 shrink-0">
                                            @if($product->thumbnail)
                                                <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-medium text-surface-900 dark:text-white truncate">{{ $product->name }}</h3>
                                            @if($variationName)
                                                <p class="text-xs text-surface-400 dark:text-surface-500">{{ $variationName }}</p>
                                            @endif
                                            <p class="text-sm text-surface-500 dark:text-surface-400">{{ format_price($itemPrice) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="border-surface-200 dark:border-surface-700 mb-6">

                            <div class="space-y-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-surface-600 dark:text-surface-400">Subtotal</span>
                                    <span class="font-medium text-surface-900 dark:text-white">{{ format_price($total) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-surface-600 dark:text-surface-400">Discount</span>
                                    <span class="font-medium text-green-500">-{{ format_price(0) }}</span>
                                </div>

                                @if($taxEnabled)
                                <div class="flex items-center justify-between text-sm" id="tax-row">
                                    <span class="text-surface-600 dark:text-surface-400">
                                        {{ \App\Models\Setting::get('tax_label', 'Sales Tax') }}
                                        @if($taxData && $taxData['tax_rate'] > 0)
                                            <span class="text-xs">({{ number_format($taxData['tax_rate'], 2) }}%)</span>
                                        @endif
                                    </span>
                                    <span class="font-medium text-surface-900 dark:text-white" id="tax-amount">
                                        @if($taxData && $taxData['tax_amount'] > 0)
                                            {{ format_price($taxData['tax_amount']) }}
                                        @else
                                            <span class="text-surface-400">--</span>
                                        @endif
                                    </span>
                                </div>
                                @endif

                                <hr class="border-surface-200 dark:border-surface-700">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-surface-900 dark:text-white">Total</span>
                                    <div class="text-right">
                                        @php
                                            $displayTotal = $taxData ? $taxData['total'] : $total;
                                        @endphp
                                        <span class="text-2xl font-bold text-surface-900 dark:text-white" id="order-total">{{ format_price($displayTotal) }}</span>
                                        @if(isset($showCurrencyNote) && $showCurrencyNote && isset($baseCurrency))
                                            <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                                                Payment: {{ $baseCurrency->format($displayTotal) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if(isset($showCurrencyNote) && $showCurrencyNote && isset($baseCurrency))
                            <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                                <p class="text-xs text-blue-700 dark:text-blue-300 flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Payment will be processed in {{ $baseCurrency->code }} ({{ $baseCurrency->symbol }}{{ number_format($total, 2) }})</span>
                                </p>
                            </div>
                            @endif

                            <button type="submit" id="submit-btn" class="mt-6 w-full inline-flex items-center justify-center rounded-xl bg-primary-600 px-6 py-4 text-base font-semibold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg id="submit-icon" xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <svg id="submit-spinner" class="hidden animate-spin mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span id="submit-text">Complete Purchase</span>
                            </button>

                            <p class="mt-4 text-xs text-center text-surface-500 dark:text-surface-400">
                                By completing your purchase, you agree to our
                                <a href="{{ route('terms') }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">Terms of Service</a>
                                and
                                <a href="{{ route('privacy') }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">Privacy Policy</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('checkout-form');
            const submitBtn = document.getElementById('submit-btn');
            const submitIcon = document.getElementById('submit-icon');
            const submitSpinner = document.getElementById('submit-spinner');
            const submitText = document.getElementById('submit-text');
            const formErrors = document.getElementById('form-errors');
            const errorList = document.getElementById('error-list');

            // Validation rules
            const validators = {
                required: (value) => value.trim() !== '' ? null : 'This field is required',
                email: (value) => /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/.test(value) ? null : 'Please enter a valid email address',
            };

            // Clear field error
            function clearFieldError(field) {
                const errorEl = document.getElementById(`${field.name}-error`);
                if (errorEl) {
                    errorEl.classList.add('hidden');
                    errorEl.textContent = '';
                }
                field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                field.classList.add('border-surface-200', 'dark:border-surface-700', 'focus:border-primary-500', 'focus:ring-primary-500');
            }

            // Show field error
            function showFieldError(field, message) {
                const errorEl = document.getElementById(`${field.name}-error`);
                if (errorEl) {
                    errorEl.textContent = message;
                    errorEl.classList.remove('hidden');
                }
                field.classList.remove('border-surface-200', 'dark:border-surface-700', 'focus:border-primary-500', 'focus:ring-primary-500');
                field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            }

            // Validate single field
            function validateField(field) {
                const rules = field.dataset.validate?.split('|') || [];
                const value = field.value;

                for (const rule of rules) {
                    if (validators[rule]) {
                        const error = validators[rule](value);
                        if (error) {
                            return error;
                        }
                    }
                }
                return null;
            }

            // Validate all fields
            function validateForm() {
                const fields = form.querySelectorAll('[data-validate]');
                const errors = [];

                fields.forEach(field => {
                    clearFieldError(field);
                    const error = validateField(field);
                    if (error) {
                        showFieldError(field, error);
                        const label = field.closest('div').querySelector('label');
                        const fieldName = label ? label.textContent.trim().replace('*', '') : field.name;
                        errors.push(`${fieldName}: ${error}`);
                    }
                });

                // Check payment method
                const paymentMethod = form.querySelector('input[name="payment_method"]:checked');
                if (!paymentMethod) {
                    errors.push('Please select a payment method');
                }

                return errors;
            }

            // Show global errors
            function showGlobalErrors(errors) {
                if (errors.length > 0) {
                    errorList.innerHTML = errors.map(e => `<li>${e}</li>`).join('');
                    formErrors.classList.remove('hidden');
                    formErrors.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    formErrors.classList.add('hidden');
                    errorList.innerHTML = '';
                }
            }

            // Set loading state
            function setLoading(loading) {
                submitBtn.disabled = loading;
                submitIcon.classList.toggle('hidden', loading);
                submitSpinner.classList.toggle('hidden', !loading);
                submitText.textContent = loading ? 'Processing...' : 'Complete Purchase';
            }

            // Real-time validation on blur
            form.querySelectorAll('[data-validate]').forEach(field => {
                field.addEventListener('blur', function() {
                    clearFieldError(this);
                    const error = validateField(this);
                    if (error) {
                        showFieldError(this, error);
                    }
                });

                field.addEventListener('input', function() {
                    if (this.classList.contains('border-red-500')) {
                        clearFieldError(this);
                        const error = validateField(this);
                        if (error) {
                            showFieldError(this, error);
                        }
                    }
                });
            });

            // Form submission with AJAX validation
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Client-side validation
                const errors = validateForm();
                if (errors.length > 0) {
                    showGlobalErrors(errors);
                    return;
                }

                showGlobalErrors([]);
                setLoading(true);

                try {
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok) {
                        // Success - redirect
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else if (data.url) {
                            window.location.href = data.url;
                        } else {
                            // Fallback to form submission
                            form.submit();
                        }
                    } else {
                        // Server validation errors
                        if (data.errors) {
                            const serverErrors = [];
                            for (const [field, messages] of Object.entries(data.errors)) {
                                const fieldEl = form.querySelector(`[name="${field}"]`);
                                if (fieldEl) {
                                    showFieldError(fieldEl, messages[0]);
                                }
                                serverErrors.push(...messages);
                            }
                            showGlobalErrors(serverErrors);
                        } else if (data.message) {
                            showGlobalErrors([data.message]);
                        } else {
                            showGlobalErrors(['An error occurred. Please try again.']);
                        }
                        setLoading(false);
                    }
                } catch (error) {
                    console.error('Checkout error:', error);
                    // If AJAX fails, submit form normally
                    form.submit();
                }
            });

            @if($taxEnabled)
            // Tax calculation via AJAX
            const stateSelect = document.getElementById('billing_state');
            const taxRow = document.getElementById('tax-row');
            const taxAmount = document.getElementById('tax-amount');
            const orderTotal = document.getElementById('order-total');

            if (stateSelect) {
                stateSelect.addEventListener('change', async function() {
                    const state = this.value;

                    // Show loading state
                    if (taxAmount) {
                        taxAmount.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>';
                    }

                    try {
                        const response = await fetch('{{ route("checkout.calculate-tax") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ state: state })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Update tax display
                            if (taxRow) {
                                const taxLabel = taxRow.querySelector('span:first-child');
                                if (taxLabel) {
                                    let labelText = data.tax_label;
                                    if (data.tax_rate > 0) {
                                        labelText += ` <span class="text-xs">(${data.tax_rate.toFixed(2)}%)</span>`;
                                    }
                                    taxLabel.innerHTML = labelText;
                                }
                            }

                            if (taxAmount) {
                                if (data.tax_amount > 0) {
                                    taxAmount.innerHTML = data.tax_amount_formatted;
                                    taxAmount.classList.remove('text-surface-400');
                                    taxAmount.classList.add('text-surface-900', 'dark:text-white');
                                } else {
                                    taxAmount.innerHTML = '<span class="text-surface-400">--</span>';
                                }
                            }

                            // Update total
                            if (orderTotal) {
                                orderTotal.textContent = data.total_formatted;
                            }
                        }
                    } catch (error) {
                        console.error('Error calculating tax:', error);
                        if (taxAmount) {
                            taxAmount.innerHTML = '<span class="text-surface-400">--</span>';
                        }
                    }
                });
            }
            @endif
        });
    </script>
</x-layouts.app>
