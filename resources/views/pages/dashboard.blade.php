<x-layouts.app title="Dashboard - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Welcome back, {{ auth()->user()->name }}!</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Your central hub for managing purchases, freelance services, contracts, and projects. Track your orders, communicate with freelancers, and grow your business.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar -->
                <aside class="lg:col-span-1">
                    <nav class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-b border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Overview</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium border-l-4 border-primary-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Products</p>
                        </div>
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

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Services</p>
                        </div>
                        <a href="{{ route('service-orders.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Service Orders
                        </a>
                        <a href="{{ route('quotes.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Quote Requests
                        </a>

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Jobs</p>
                        </div>
                        <a href="{{ route('jobs.my-jobs') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            My Job Posts
                        </a>
                        <a href="{{ route('contracts.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Contracts
                        </a>

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Communication</p>
                        </div>
                        <a href="{{ route('conversations.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Messages
                        </a>

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Account</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                        </a>

                        @if(!auth()->user()->seller)
                            <a href="{{ route('become-seller') }}" class="flex items-center gap-3 px-4 py-3 text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 border-l-4 border-transparent transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                Become a Seller
                            </a>
                        @endif
                    </nav>
                </aside>

                <!-- Main Content -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Stats -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $totalOrders ?? 0 }}</p>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Product Orders</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-accent-100 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $serviceOrders ?? 0 }}</p>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Service Orders</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-info-100 dark:bg-info-900/30 text-info-600 dark:text-info-400 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $activeContracts ?? 0 }}</p>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Active Contracts</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success-100 dark:bg-success-900/30 text-success-600 dark:text-success-400 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $activeLicenses ?? 0 }}</p>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Active Licenses</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning-100 dark:bg-warning-900/30 text-warning-600 dark:text-warning-400 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $jobPosts ?? 0 }}</p>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Job Posts</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-danger-100 dark:bg-danger-900/30 text-danger-600 dark:text-danger-400 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $wishlistCount ?? 0 }}</p>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Wishlist</p>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <a href="{{ route('services.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 hover:border-primary-500 dark:hover:border-primary-500 transition-colors group">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Browse Services</span>
                        </a>
                        <a href="{{ route('jobs.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 hover:border-primary-500 dark:hover:border-primary-500 transition-colors group">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent-100 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Find Work</span>
                        </a>
                        <a href="{{ route('jobs.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 hover:border-primary-500 dark:hover:border-primary-500 transition-colors group">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-100 dark:bg-success-900/30 text-success-600 dark:text-success-400 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Post a Job</span>
                        </a>
                        <a href="{{ route('products.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 hover:border-primary-500 dark:hover:border-primary-500 transition-colors group">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-info-100 dark:bg-info-900/30 text-info-600 dark:text-info-400 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Browse Products</span>
                        </a>
                    </div>

                    <!-- Recent Activity Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Recent Purchases -->
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-700">
                                <h2 class="font-semibold text-surface-900 dark:text-white">Recent Purchases</h2>
                                <a href="{{ route('purchases') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium">View All</a>
                            </div>
                            @if(isset($recentOrders) && $recentOrders->count() > 0)
                                <div class="divide-y divide-surface-200 dark:divide-surface-700">
                                    @foreach($recentOrders->take(3) as $order)
                                        <div class="px-5 py-3">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="font-medium text-surface-900 dark:text-white text-sm">{{ $order->order_number }}</p>
                                                    <p class="text-xs text-surface-500 dark:text-surface-400">{{ $order->created_at->format('M d, Y') }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-semibold text-surface-900 dark:text-white text-sm">${{ number_format($order->total, 2) }}</p>
                                                    <x-status-badge :status="$order->status" size="sm" />
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="px-5 py-8 text-center">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">No purchases yet</p>
                                </div>
                            @endif
                        </div>

                        <!-- Recent Service Orders -->
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-700">
                                <h2 class="font-semibold text-surface-900 dark:text-white">Service Orders</h2>
                                <a href="{{ route('service-orders.index') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium">View All</a>
                            </div>
                            @if(isset($recentServiceOrders) && $recentServiceOrders->count() > 0)
                                <div class="divide-y divide-surface-200 dark:divide-surface-700">
                                    @foreach($recentServiceOrders->take(3) as $order)
                                        <div class="px-5 py-3">
                                            <div class="flex items-center justify-between">
                                                <div class="min-w-0 flex-1">
                                                    <p class="font-medium text-surface-900 dark:text-white text-sm truncate">{{ $order->title }}</p>
                                                    <p class="text-xs text-surface-500 dark:text-surface-400">{{ $order->created_at->format('M d, Y') }}</p>
                                                </div>
                                                <div class="text-right ml-4">
                                                    <p class="font-semibold text-surface-900 dark:text-white text-sm">${{ number_format($order->price, 2) }}</p>
                                                    <x-status-badge :status="$order->status" size="sm" />
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="px-5 py-8 text-center">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">No service orders yet</p>
                                </div>
                            @endif
                        </div>

                        <!-- Active Contracts -->
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-700">
                                <h2 class="font-semibold text-surface-900 dark:text-white">Active Contracts</h2>
                                <a href="{{ route('contracts.index') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium">View All</a>
                            </div>
                            @if(isset($recentContracts) && $recentContracts->count() > 0)
                                <div class="divide-y divide-surface-200 dark:divide-surface-700">
                                    @foreach($recentContracts->take(3) as $contract)
                                        <div class="px-5 py-3">
                                            <div class="flex items-center justify-between">
                                                <div class="min-w-0 flex-1">
                                                    <p class="font-medium text-surface-900 dark:text-white text-sm truncate">{{ $contract->title }}</p>
                                                    <p class="text-xs text-surface-500 dark:text-surface-400">{{ $contract->created_at->format('M d, Y') }}</p>
                                                </div>
                                                <div class="text-right ml-4">
                                                    <p class="font-semibold text-surface-900 dark:text-white text-sm">${{ number_format($contract->total_amount, 2) }}</p>
                                                    <x-status-badge :status="$contract->status" size="sm" />
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="px-5 py-8 text-center">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">No active contracts</p>
                                </div>
                            @endif
                        </div>

                        <!-- My Job Posts -->
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-700">
                                <h2 class="font-semibold text-surface-900 dark:text-white">My Job Posts</h2>
                                <a href="{{ route('jobs.my-jobs') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium">View All</a>
                            </div>
                            @if(isset($recentJobPosts) && $recentJobPosts->count() > 0)
                                <div class="divide-y divide-surface-200 dark:divide-surface-700">
                                    @foreach($recentJobPosts->take(3) as $job)
                                        <div class="px-5 py-3">
                                            <div class="flex items-center justify-between">
                                                <div class="min-w-0 flex-1">
                                                    <p class="font-medium text-surface-900 dark:text-white text-sm truncate">{{ $job->title }}</p>
                                                    <p class="text-xs text-surface-500 dark:text-surface-400">{{ $job->proposals_count ?? 0 }} proposals</p>
                                                </div>
                                                <div class="text-right ml-4">
                                                    <p class="font-semibold text-surface-900 dark:text-white text-sm">{{ $job->budget_range }}</p>
                                                    <x-status-badge :status="$job->status" size="sm" />
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="px-5 py-8 text-center">
                                    <p class="text-sm text-surface-500 dark:text-surface-400 mb-3">No job posts yet</p>
                                    <a href="{{ route('jobs.create') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium">Post a Job</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
