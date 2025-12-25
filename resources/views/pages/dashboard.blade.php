<x-layouts.app title="Dashboard - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Welcome back, {{ auth()->user()->name }}!</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Manage your purchases and account settings</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar -->
                <aside class="lg:col-span-1">
                    <nav class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium border-l-4 border-primary-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('purchases') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
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
                <div class="lg:col-span-3 space-y-6">
                    <!-- Stats -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $totalOrders ?? 0 }}</p>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Total Orders</p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-100 dark:bg-success-900/30 text-success-600 dark:text-success-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $activeLicenses ?? 0 }}</p>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Active Licenses</p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-info-100 dark:bg-info-900/30 text-info-600 dark:text-info-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $totalDownloads ?? 0 }}</p>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Downloads</p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-danger-100 dark:bg-danger-900/30 text-danger-600 dark:text-danger-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $wishlistCount ?? 0 }}</p>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Wishlist Items</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Purchases -->
                    <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-surface-200 dark:border-surface-700">
                            <h2 class="font-semibold text-surface-900 dark:text-white">Recent Purchases</h2>
                            <a href="{{ route('purchases') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium">View All</a>
                        </div>
                        @if(isset($recentOrders) && $recentOrders->count() > 0)
                            <div class="divide-y divide-surface-200 dark:divide-surface-700">
                                @foreach($recentOrders as $order)
                                    <div class="px-6 py-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium text-surface-900 dark:text-white">{{ $order->order_number }}</p>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">{{ $order->created_at->format('M d, Y') }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-surface-900 dark:text-white">${{ number_format($order->total, 2) }}</p>
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $order->status === 'completed' ? 'bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400' : 'bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400' }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-6 py-12 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <p class="text-surface-600 dark:text-surface-400 mb-4">No purchases yet</p>
                                <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                                    Browse Products
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
