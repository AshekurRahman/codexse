<x-layouts.app title="Checkout - Codexse">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Checkout</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Complete your purchase securely</p>
            </div>

            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Checkout Form -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Contact Information -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-6">Contact Information</h2>
                            <div class="space-y-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Email Address</label>
                                    <input type="email" id="email" name="email" value="{{ auth()->user()->email ?? old('email') }}" required
                                        class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 px-4 py-3 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                </div>
                                <div>
                                    <label for="name" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Full Name</label>
                                    <input type="text" id="name" name="name" value="{{ auth()->user()->name ?? old('name') }}" required
                                        class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 px-4 py-3 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-6">Payment Method</h2>
                            <div class="space-y-3">
                                <label class="flex items-center gap-4 p-4 rounded-xl border-2 border-primary-500 bg-primary-50 dark:bg-primary-900/20 cursor-pointer">
                                    <input type="radio" name="payment_method" value="stripe" checked class="h-5 w-5 text-primary-600 focus:ring-primary-500">
                                    <div class="flex-1">
                                        <span class="font-medium text-surface-900 dark:text-white">Credit Card</span>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">Pay securely with Stripe</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <div class="w-10 h-6 bg-white rounded border flex items-center justify-center">
                                            <span class="text-xs font-bold text-blue-600">VISA</span>
                                        </div>
                                        <div class="w-10 h-6 bg-white rounded border flex items-center justify-center">
                                            <span class="text-xs font-bold text-red-500">MC</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="flex items-center gap-4 p-4 rounded-xl border-2 border-surface-200 dark:border-surface-700 hover:border-primary-300 dark:hover:border-primary-700 cursor-pointer transition-colors">
                                    <input type="radio" name="payment_method" value="paypal" class="h-5 w-5 text-primary-600 focus:ring-primary-500">
                                    <div class="flex-1">
                                        <span class="font-medium text-surface-900 dark:text-white">PayPal</span>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">Pay with your PayPal account</p>
                                    </div>
                                    <div class="w-16 h-6 bg-white rounded border flex items-center justify-center">
                                        <span class="text-xs font-bold text-blue-700">PayPal</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-6">Order Notes (Optional)</h2>
                            <textarea name="notes" rows="3" placeholder="Any special instructions..."
                                class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 px-4 py-3 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"></textarea>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6 sticky top-24">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-6">Order Summary</h2>

                            <!-- Cart Items -->
                            <div class="space-y-4 mb-6">
                                @foreach($products as $product)
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
                                            <p class="text-sm text-surface-500 dark:text-surface-400">${{ number_format($product->current_price, 2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="border-surface-200 dark:border-surface-700 mb-6">

                            <div class="space-y-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-surface-600 dark:text-surface-400">Subtotal</span>
                                    <span class="font-medium text-surface-900 dark:text-white">${{ number_format($total, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-surface-600 dark:text-surface-400">Discount</span>
                                    <span class="font-medium text-green-500">-$0.00</span>
                                </div>
                                <hr class="border-surface-200 dark:border-surface-700">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-surface-900 dark:text-white">Total</span>
                                    <span class="text-2xl font-bold text-surface-900 dark:text-white">${{ number_format($total, 2) }}</span>
                                </div>
                            </div>

                            <button type="submit" class="mt-6 w-full inline-flex items-center justify-center rounded-xl bg-primary-600 px-6 py-4 text-base font-semibold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 hover:shadow-xl transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Complete Purchase
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
</x-layouts.app>
