<x-layouts.app title="My Purchases - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">My Purchases</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Download your purchased products</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar -->
                <aside class="lg:col-span-1">
                    <nav class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('purchases') }}" class="flex items-center gap-3 px-4 py-3 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium border-l-4 border-primary-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            My Purchases
                        </a>
                        <a href="{{ route('wishlist') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            Wishlist
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                        </a>
                    </nav>
                </aside>

                <!-- Main Content -->
                <div class="lg:col-span-3">
                    @if($orders->count() > 0)
                        <div class="space-y-6">
                            @foreach($orders as $order)
                                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                                    <!-- Order Header -->
                                    <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 bg-surface-50 dark:bg-surface-800 border-b border-surface-200 dark:border-surface-700">
                                        <div class="flex items-center gap-6">
                                            <div>
                                                <p class="text-xs text-surface-500 dark:text-surface-400">Order Number</p>
                                                <p class="font-semibold text-surface-900 dark:text-white">{{ $order->order_number }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-surface-500 dark:text-surface-400">Date</p>
                                                <p class="font-medium text-surface-900 dark:text-white">{{ $order->created_at->format('M d, Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-surface-500 dark:text-surface-400">Total</p>
                                                <p class="font-semibold text-surface-900 dark:text-white">${{ number_format($order->total, 2) }}</p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $order->status === 'completed' ? 'bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400' : 'bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>

                                    <!-- Order Items -->
                                    <div class="divide-y divide-surface-200 dark:divide-surface-700">
                                        @foreach($order->items as $item)
                                            <div class="flex items-center gap-4 px-6 py-4">
                                                <div class="w-20 h-20 rounded-lg overflow-hidden bg-surface-100 dark:bg-surface-700 shrink-0">
                                                    @if($item->product && $item->product->thumbnail)
                                                        <img src="{{ $item->product->thumbnail_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h3 class="font-medium text-surface-900 dark:text-white">{{ $item->product_name }}</h3>
                                                    <p class="text-sm text-surface-500 dark:text-surface-400">License: {{ ucfirst($item->license_type) }}</p>
                                                    @if($item->license_key)
                                                        <p class="text-xs text-surface-400 mt-1">Key: {{ $item->license_key }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-semibold text-surface-900 dark:text-white">${{ number_format($item->price, 2) }}</p>
                                                    @if($order->status === 'completed' && $item->product)
                                                        <a href="{{ route('download', $item) }}" class="inline-flex items-center gap-1 mt-2 text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                            </svg>
                                                            Download
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-6 py-16 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-2">No purchases yet</h3>
                            <p class="text-surface-600 dark:text-surface-400 mb-6">Start exploring our collection of premium digital assets</p>
                            <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-lg bg-primary-600 px-6 py-3 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                                Browse Products
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
