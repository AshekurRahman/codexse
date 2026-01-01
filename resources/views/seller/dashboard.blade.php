<x-layouts.app title="Seller Dashboard - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Seller Dashboard</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Welcome back, {{ $seller->store_name ?? auth()->user()->name }}! Manage your products, services, contracts and track your earnings all in one place.</p>
            </div>

            <!-- Vacation Mode Banner -->
            @if($seller->isOnVacation())
                <div class="mb-6 p-4 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-xl">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-warning-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-warning-800 dark:text-warning-300">Vacation Mode Active</h3>
                            <p class="text-sm text-warning-700 dark:text-warning-400 mt-1">
                                Your store is currently in vacation mode. Customers can see your products but cannot make purchases.
                                @if($seller->vacation_ends_at)
                                    Vacation ends {{ $seller->vacation_ends_at->diffForHumans() }}.
                                @endif
                            </p>
                        </div>
                        <button type="button" onclick="document.getElementById('vacation-modal').classList.remove('hidden')" class="flex-shrink-0 text-warning-600 hover:text-warning-800 dark:text-warning-400 dark:hover:text-warning-200 font-medium text-sm">
                            Manage
                        </button>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar -->
                <aside class="lg:col-span-1">
                    <nav class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-b border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Overview</p>
                        </div>
                        <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('seller.dashboard') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium border-l-4 border-primary-500' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent' }} transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('seller.analytics.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('seller.analytics.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium border-l-4 border-primary-500' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent' }} transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Analytics
                        </a>

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Products</p>
                        </div>
                        <a href="{{ route('seller.products.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            My Products
                        </a>
                        <a href="{{ route('seller.orders.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Product Orders
                        </a>

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Services</p>
                        </div>
                        <a href="{{ route('seller.services.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            My Services
                        </a>
                        <a href="{{ route('seller.service-orders.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Service Orders
                        </a>
                        <a href="{{ route('seller.quotes.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Quote Requests
                        </a>

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Jobs & Contracts</p>
                        </div>
                        <a href="{{ route('seller.jobs.available') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Find Jobs
                        </a>
                        <a href="{{ route('seller.proposals.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            My Proposals
                        </a>
                        <a href="{{ route('seller.contracts.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Contracts
                        </a>

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Finances</p>
                        </div>
                        <a href="{{ route('seller.payouts.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Payouts
                        </a>
                        <a href="{{ route('seller.licenses.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Licenses
                        </a>
                        <a href="{{ route('wallet.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Wallet
                            @if(isset($wallet) && $wallet->balance > 0)
                                <span class="ml-auto text-xs font-medium text-success-600 dark:text-success-400">{{ format_price($wallet->balance) }}</span>
                            @endif
                        </a>

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">More</p>
                        </div>
                        <a href="{{ route('seller.reviews.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            Reviews
                        </a>
                        <a href="{{ route('conversations.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Messages
                        </a>
                        <button type="button" onclick="document.getElementById('vacation-modal').classList.remove('hidden')" class="w-full flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors {{ $seller->isOnVacation() ? 'text-warning-600 dark:text-warning-400' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Vacation Mode
                            @if($seller->isOnVacation())
                                <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400">ON</span>
                            @endif
                        </button>
                    </nav>
                </aside>

                <!-- Main Content -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Quick Actions -->
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('seller.products.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 hover:border-primary-500 dark:hover:border-primary-500 transition-colors group">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Add Product</span>
                        </a>
                        <a href="{{ route('seller.services.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 hover:border-primary-500 dark:hover:border-primary-500 transition-colors group">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent-100 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Add Service</span>
                        </a>
                        <a href="{{ route('seller.jobs.available') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 hover:border-primary-500 dark:hover:border-primary-500 transition-colors group">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-info-100 dark:bg-info-900/30 text-info-600 dark:text-info-400 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Find Jobs</span>
                        </a>
                        <a href="{{ route('seller.analytics.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 hover:border-primary-500 dark:hover:border-primary-500 transition-colors group">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-100 dark:bg-success-900/30 text-success-600 dark:text-success-400 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">View Analytics</span>
                        </a>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ number_format($stats['total_products'] ?? 0) }}</p>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Products</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-accent-100 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ number_format($stats['total_services'] ?? 0) }}</p>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Services</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success-100 dark:bg-success-900/30 text-success-600 dark:text-success-400 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ format_price($stats['total_sales'] ?? 0) }}</p>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Total Sales</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-info-100 dark:bg-info-900/30 text-info-600 dark:text-info-400 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ number_format($stats['active_contracts'] ?? 0) }}</p>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Active Contracts</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning-100 dark:bg-warning-900/30 text-warning-600 dark:text-warning-400 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ format_price($stats['pending_payouts'] ?? 0) }}</p>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Balance</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-danger-100 dark:bg-danger-900/30 text-danger-600 dark:text-danger-400 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ number_format($stats['average_rating'] ?? 0, 1) }}</p>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Avg Rating</p>
                        </div>
                    </div>

                    <!-- Recent Activity Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Recent Product Orders -->
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-700">
                                <h2 class="font-semibold text-surface-900 dark:text-white">Recent Product Orders</h2>
                                <a href="{{ route('seller.orders.index') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium">View All</a>
                            </div>
                            @if(isset($recentOrders) && $recentOrders->count() > 0)
                                <div class="divide-y divide-surface-200 dark:divide-surface-700">
                                    @foreach($recentOrders->take(3) as $orderItem)
                                        <div class="px-5 py-3 flex items-center justify-between gap-4">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-10 h-10 rounded-lg bg-surface-100 dark:bg-surface-700 flex items-center justify-center overflow-hidden shrink-0">
                                                    @if($orderItem->product && $orderItem->product->thumbnail)
                                                        <img src="{{ $orderItem->product->thumbnail_url }}" alt="" class="w-full h-full object-cover">
                                                    @else
                                                        <svg class="w-5 h-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-medium text-surface-900 dark:text-white truncate text-sm">{{ $orderItem->product_name }}</p>
                                                    <p class="text-xs text-surface-500 dark:text-surface-400">{{ $orderItem->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                            <p class="font-semibold text-surface-900 dark:text-white text-sm">{{ format_price($orderItem->seller_amount ?? 0) }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="px-5 py-8 text-center">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">No product orders yet</p>
                                </div>
                            @endif
                        </div>

                        <!-- Recent Service Orders -->
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-700">
                                <h2 class="font-semibold text-surface-900 dark:text-white">Recent Service Orders</h2>
                                <a href="{{ route('seller.service-orders.index') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium">View All</a>
                            </div>
                            @if(isset($recentServiceOrders) && $recentServiceOrders->count() > 0)
                                <div class="divide-y divide-surface-200 dark:divide-surface-700">
                                    @foreach($recentServiceOrders->take(3) as $order)
                                        <div class="px-5 py-3">
                                            <div class="flex items-center justify-between">
                                                <div class="min-w-0 flex-1">
                                                    <p class="font-medium text-surface-900 dark:text-white text-sm truncate">{{ $order->title }}</p>
                                                    <p class="text-xs text-surface-500 dark:text-surface-400">{{ $order->created_at->diffForHumans() }}</p>
                                                </div>
                                                <div class="text-right ml-4">
                                                    <p class="font-semibold text-surface-900 dark:text-white text-sm">{{ format_price($order->price) }}</p>
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
                                <a href="{{ route('seller.contracts.index') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium">View All</a>
                            </div>
                            @if(isset($activeContracts) && $activeContracts->count() > 0)
                                <div class="divide-y divide-surface-200 dark:divide-surface-700">
                                    @foreach($activeContracts->take(3) as $contract)
                                        <div class="px-5 py-3">
                                            <div class="flex items-center justify-between">
                                                <div class="min-w-0 flex-1">
                                                    <p class="font-medium text-surface-900 dark:text-white text-sm truncate">{{ $contract->title ?? $contract->jobPosting->title ?? 'Contract' }}</p>
                                                    <p class="text-xs text-surface-500 dark:text-surface-400">{{ $contract->client->name ?? 'Client' }}</p>
                                                </div>
                                                <div class="text-right ml-4">
                                                    <p class="font-semibold text-surface-900 dark:text-white text-sm">{{ format_price($contract->total_amount) }}</p>
                                                    <x-status-badge :status="$contract->status" size="sm" />
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="px-5 py-8 text-center">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">No active contracts</p>
                                    <a href="{{ route('seller.jobs.available') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium mt-2 inline-block">Find Jobs</a>
                                </div>
                            @endif
                        </div>

                        <!-- Quote Requests -->
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-700">
                                <h2 class="font-semibold text-surface-900 dark:text-white">Quote Requests</h2>
                                <a href="{{ route('seller.quotes.index') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium">View All</a>
                            </div>
                            @if(isset($quoteRequests) && $quoteRequests->count() > 0)
                                <div class="divide-y divide-surface-200 dark:divide-surface-700">
                                    @foreach($quoteRequests->take(3) as $quote)
                                        <div class="px-5 py-3">
                                            <div class="flex items-center justify-between">
                                                <div class="min-w-0 flex-1">
                                                    <p class="font-medium text-surface-900 dark:text-white text-sm truncate">{{ $quote->title }}</p>
                                                    <p class="text-xs text-surface-500 dark:text-surface-400">{{ $quote->buyer->name ?? 'Buyer' }} &middot; {{ $quote->created_at->diffForHumans() }}</p>
                                                </div>
                                                <x-status-badge :status="$quote->status" size="sm" />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="px-5 py-8 text-center">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">No quote requests</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vacation Mode Modal -->
    <div id="vacation-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="vacation-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-surface-900/75 transition-opacity" onclick="document.getElementById('vacation-modal').classList.add('hidden')"></div>

            <div class="relative bg-white dark:bg-surface-800 rounded-2xl shadow-xl transform transition-all sm:max-w-lg sm:w-full mx-auto">
                <form action="{{ route('seller.vacation.update') }}" method="POST">
                    @csrf
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-surface-900 dark:text-white" id="vacation-modal-title">Vacation Mode</h3>
                            <button type="button" onclick="document.getElementById('vacation-modal').classList.add('hidden')" class="text-surface-400 hover:text-surface-600 dark:hover:text-surface-300">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- Toggle -->
                            <div class="flex items-center justify-between p-4 bg-surface-50 dark:bg-surface-700/50 rounded-xl">
                                <div>
                                    <p class="font-medium text-surface-900 dark:text-white">Enable Vacation Mode</p>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Temporarily pause your store</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_on_vacation" value="1" {{ $seller->isOnVacation() ? 'checked' : '' }} class="sr-only peer" id="vacation-toggle">
                                    <div class="w-11 h-6 bg-surface-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-surface-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-surface-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-surface-600 peer-checked:bg-primary-600"></div>
                                </label>
                            </div>

                            <!-- Message -->
                            <div>
                                <label for="vacation_message" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Vacation Message (Optional)</label>
                                <textarea name="vacation_message" id="vacation_message" rows="3" class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-surface-900 dark:text-white placeholder-surface-400 focus:ring-2 focus:ring-primary-500" placeholder="Let customers know when you'll be back...">{{ $seller->vacation_message }}</textarea>
                            </div>

                            <!-- End Date -->
                            <div>
                                <label for="vacation_ends_at" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Return Date (Optional)</label>
                                <input type="date" name="vacation_ends_at" id="vacation_ends_at" value="{{ $seller->vacation_ends_at?->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg text-surface-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Leave empty for indefinite vacation</p>
                            </div>

                            <!-- Auto Reply -->
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="vacation_auto_reply" id="vacation_auto_reply" value="1" {{ $seller->vacation_auto_reply ? 'checked' : '' }} class="w-4 h-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                                <label for="vacation_auto_reply" class="text-sm text-surface-700 dark:text-surface-300">Auto-reply to messages with vacation notice</label>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-surface-50 dark:bg-surface-700/50 rounded-b-2xl flex items-center justify-end gap-3">
                        <button type="button" onclick="document.getElementById('vacation-modal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-700 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
