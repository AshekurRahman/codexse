<x-layouts.app title="Shopping Cart - Codexse">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Shopping Cart</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">{{ count($products) }} items in your cart</p>
            </div>

            @if(count($products) > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2 space-y-4">
                        @foreach($products as $product)
                            <div class="flex gap-4 p-4 rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800">
                                <!-- Product Image -->
                                <div class="w-24 h-24 rounded-xl overflow-hidden bg-surface-100 dark:bg-surface-700 shrink-0">
                                    @if($product->thumbnail)
                                        <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-surface-900 dark:text-white truncate">{{ $product->name }}</h3>
                                    <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">{{ $product->seller->store_name ?? 'Unknown Seller' }}</p>
                                    <div class="mt-3 flex items-center justify-between">
                                        <span class="text-lg font-bold text-surface-900 dark:text-white">${{ number_format($product->current_price, 2) }}</span>
                                        <form action="{{ route('cart.remove', $product->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-500 hover:text-red-600 font-medium flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6 sticky top-24">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-6">Order Summary</h2>

                            <!-- Coupon Code -->
                            <div class="mb-6">
                                @if(session('coupon'))
                                    <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800">
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            <span class="text-sm font-medium text-green-700 dark:text-green-300">{{ session('coupon.code') }}</span>
                                        </div>
                                        <form action="{{ route('coupon.remove') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-red-500 hover:text-red-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <form action="{{ route('coupon.apply') }}" method="POST" class="flex gap-2">
                                        @csrf
                                        <input type="text" name="code" placeholder="Enter coupon code"
                                            class="flex-1 rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-4 py-2.5 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-primary-500"
                                            value="{{ old('code') }}">
                                        <button type="submit" class="rounded-xl bg-surface-900 dark:bg-surface-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-surface-800 dark:hover:bg-surface-500 transition-colors">
                                            Apply
                                        </button>
                                    </form>
                                    @error('code')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                @endif
                                @if(session('coupon_error'))
                                    <p class="mt-2 text-sm text-red-500">{{ session('coupon_error') }}</p>
                                @endif
                                @if(session('coupon_success'))
                                    <p class="mt-2 text-sm text-green-500">{{ session('coupon_success') }}</p>
                                @endif
                            </div>

                            @php
                                $discount = session('coupon.discount', 0);
                                $finalTotal = $total - $discount;
                            @endphp

                            <div class="space-y-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-surface-600 dark:text-surface-400">Subtotal ({{ count($products) }} items)</span>
                                    <span class="font-medium text-surface-900 dark:text-white">${{ number_format($total, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-surface-600 dark:text-surface-400">Discount</span>
                                    <span class="font-medium text-green-500">-${{ number_format($discount, 2) }}</span>
                                </div>
                                <hr class="border-surface-200 dark:border-surface-700">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-surface-900 dark:text-white">Total</span>
                                    <span class="text-2xl font-bold text-surface-900 dark:text-white">${{ number_format($finalTotal, 2) }}</span>
                                </div>
                            </div>

                            <a href="{{ route('checkout') }}" class="mt-6 w-full inline-flex items-center justify-center rounded-xl bg-primary-600 px-6 py-4 text-base font-semibold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 hover:shadow-xl transition-all">
                                Proceed to Checkout
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>

                            <a href="{{ route('products.index') }}" class="mt-3 w-full inline-flex items-center justify-center rounded-xl border-2 border-surface-200 dark:border-surface-700 px-6 py-3 text-sm font-semibold text-surface-700 dark:text-surface-300 hover:border-primary-500 transition-all">
                                Continue Shopping
                            </a>

                            <!-- Security Badges -->
                            <div class="mt-6 pt-6 border-t border-surface-200 dark:border-surface-700">
                                <div class="flex items-center justify-center gap-4 text-surface-400">
                                    <div class="flex items-center gap-1 text-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Secure Checkout
                                    </div>
                                    <div class="flex items-center gap-1 text-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Safe Payment
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty Cart -->
                <div class="text-center py-16 bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700">
                    <div class="w-20 h-20 rounded-full bg-surface-100 dark:bg-surface-700 flex items-center justify-center mx-auto mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-2">Your cart is empty</h2>
                    <p class="text-surface-500 dark:text-surface-400 mb-6 max-w-md mx-auto">Looks like you haven't added any products to your cart yet. Start browsing our collection of premium digital assets.</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 hover:shadow-xl transition-all">
                        Browse Products
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
