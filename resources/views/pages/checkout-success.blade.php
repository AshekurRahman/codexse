<x-layouts.app title="Order Complete - Codexse">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-16">
            <!-- Success Animation -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-green-100 dark:bg-green-900/30 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-surface-900 dark:text-white mb-4">Thank you for your purchase!</h1>
                <p class="text-lg text-surface-600 dark:text-surface-400">Your order has been confirmed and is being processed.</p>
            </div>

            <!-- Order Details -->
            <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-sm text-surface-500 dark:text-surface-400">Order Number</p>
                        <p class="text-xl font-bold text-surface-900 dark:text-white">{{ $order->order_number }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-4 py-2 text-sm font-medium text-green-700 dark:text-green-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Completed
                    </span>
                </div>

                <hr class="border-surface-200 dark:border-surface-700 mb-6">

                <!-- Order Items -->
                <div class="space-y-4 mb-6">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-lg overflow-hidden bg-surface-100 dark:bg-surface-700 shrink-0">
                                @if($item->product && $item->product->thumbnail)
                                    <img src="{{ asset('storage/' . $item->product->thumbnail) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-surface-900 dark:text-white">{{ $item->product_name }}</h3>
                                <p class="text-sm text-surface-500 dark:text-surface-400">License: {{ ucfirst($item->license_type) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-surface-900 dark:text-white">${{ number_format($item->price, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <hr class="border-surface-200 dark:border-surface-700 mb-6">

                <!-- Order Total -->
                <div class="flex items-center justify-between">
                    <span class="text-lg font-semibold text-surface-900 dark:text-white">Total Paid</span>
                    <span class="text-2xl font-bold text-surface-900 dark:text-white">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="rounded-2xl border border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-900/20 p-6 mb-8">
                <h3 class="font-semibold text-primary-900 dark:text-primary-100 mb-4">What's next?</h3>
                <ul class="space-y-3 text-sm text-primary-700 dark:text-primary-300">
                    <li class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>A confirmation email has been sent to <strong>{{ $order->email }}</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>You can download your files from your <a href="{{ route('purchases') }}" class="font-medium underline">Purchases</a> page</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Need help? Contact our <a href="{{ route('contact') }}" class="font-medium underline">support team</a></span>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('purchases') }}" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl bg-primary-600 px-8 py-4 text-base font-semibold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 hover:shadow-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Files
                </a>
                <a href="{{ route('products.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl border-2 border-surface-200 dark:border-surface-700 px-8 py-4 text-base font-semibold text-surface-700 dark:text-surface-300 hover:border-primary-500 transition-all">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
