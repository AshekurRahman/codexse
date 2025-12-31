<x-layouts.app title="Dashboard - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Welcome back, {{ auth()->user()->name }}!</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Your central hub for managing purchases, services, contracts, and projects.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar -->
                <aside class="lg:col-span-1">
                    <nav class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden sticky top-24">
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
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Account</p>
                        </div>
                        <a href="{{ route('conversations.index') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Messages
                        </a>
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
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-medium text-success-600 dark:text-success-400 bg-success-100 dark:bg-success-900/30 px-2 py-1 rounded-full">Products</span>
                            </div>
                            <p class="text-3xl font-bold text-surface-900 dark:text-white">{{ $totalOrders ?? 0 }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Total Orders</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent-100 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-medium text-accent-600 dark:text-accent-400 bg-accent-100 dark:bg-accent-900/30 px-2 py-1 rounded-full">Services</span>
                            </div>
                            <p class="text-3xl font-bold text-surface-900 dark:text-white">{{ $serviceOrders ?? 0 }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Service Orders</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-info-100 dark:bg-info-900/30 text-info-600 dark:text-info-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-medium text-info-600 dark:text-info-400 bg-info-100 dark:bg-info-900/30 px-2 py-1 rounded-full">Active</span>
                            </div>
                            <p class="text-3xl font-bold text-surface-900 dark:text-white">{{ $activeContracts ?? 0 }}</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Contracts</p>
                        </div>
                    </div>

                    <!-- Spending Overview -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-gradient-to-br from-primary-500 to-primary-700 p-5 text-white">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-primary-100">Product Spending</p>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-3xl font-bold">${{ number_format($totalSpent ?? 0, 2) }}</p>
                            <p class="text-sm text-primary-200 mt-1">Lifetime total</p>
                        </div>
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-gradient-to-br from-accent-500 to-accent-700 p-5 text-white">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-accent-100">Service Spending</p>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-3xl font-bold">${{ number_format($totalServiceSpent ?? 0, 2) }}</p>
                            <p class="text-sm text-accent-200 mt-1">Lifetime total</p>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Activity Breakdown Donut Chart -->
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Activity Overview</h3>
                            <div class="flex items-center justify-center">
                                <div class="relative" style="width: 200px; height: 200px;">
                                    <canvas id="activityChart"></canvas>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ ($totalOrders ?? 0) + ($serviceOrders ?? 0) + ($activeContracts ?? 0) + ($jobPosts ?? 0) }}</p>
                                            <p class="text-xs text-surface-500 dark:text-surface-400">Total</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 mt-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-primary-500"></span>
                                    <span class="text-sm text-surface-600 dark:text-surface-400">Products ({{ $totalOrders ?? 0 }})</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-accent-500"></span>
                                    <span class="text-sm text-surface-600 dark:text-surface-400">Services ({{ $serviceOrders ?? 0 }})</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-info-500"></span>
                                    <span class="text-sm text-surface-600 dark:text-surface-400">Contracts ({{ $activeContracts ?? 0 }})</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-warning-500"></span>
                                    <span class="text-sm text-surface-600 dark:text-surface-400">Jobs ({{ $jobPosts ?? 0 }})</span>
                                </div>
                            </div>
                        </div>

                        <!-- Order Status Pie Chart -->
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Order Status</h3>
                            @if(!empty($orderStatusData))
                                <div class="flex items-center justify-center">
                                    <div style="width: 200px; height: 200px;">
                                        <canvas id="orderStatusChart"></canvas>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mt-4">
                                    @foreach($orderStatusData as $status => $count)
                                        <div class="flex items-center gap-2">
                                            <span class="w-3 h-3 rounded-full
                                                @if($status === 'completed') bg-success-500
                                                @elseif($status === 'pending') bg-warning-500
                                                @elseif($status === 'processing') bg-info-500
                                                @elseif($status === 'cancelled' || $status === 'refunded') bg-danger-500
                                                @else bg-surface-400
                                                @endif"></span>
                                            <span class="text-sm text-surface-600 dark:text-surface-400 capitalize">{{ str_replace('_', ' ', $status) }} ({{ $count }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center h-48 text-surface-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    <p class="text-sm">No order data yet</p>
                                </div>
                            @endif
                        </div>

                        <!-- Service Status Pie Chart -->
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Service Orders Status</h3>
                            @if(!empty($serviceStatusData))
                                <div class="flex items-center justify-center">
                                    <div style="width: 200px; height: 200px;">
                                        <canvas id="serviceStatusChart"></canvas>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mt-4">
                                    @foreach($serviceStatusData as $status => $count)
                                        <div class="flex items-center gap-2">
                                            <span class="w-3 h-3 rounded-full
                                                @if($status === 'completed' || $status === 'delivered') bg-success-500
                                                @elseif($status === 'pending') bg-warning-500
                                                @elseif($status === 'in_progress') bg-info-500
                                                @elseif($status === 'cancelled') bg-danger-500
                                                @else bg-surface-400
                                                @endif"></span>
                                            <span class="text-sm text-surface-600 dark:text-surface-400 capitalize">{{ str_replace('_', ' ', $status) }} ({{ $count }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center h-48 text-surface-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm">No service orders yet</p>
                                </div>
                            @endif
                        </div>

                        <!-- Contract Status Pie Chart -->
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Contract Status</h3>
                            @if(!empty($contractStatusData))
                                <div class="flex items-center justify-center">
                                    <div style="width: 200px; height: 200px;">
                                        <canvas id="contractStatusChart"></canvas>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mt-4">
                                    @foreach($contractStatusData as $status => $count)
                                        <div class="flex items-center gap-2">
                                            <span class="w-3 h-3 rounded-full
                                                @if($status === 'completed') bg-success-500
                                                @elseif($status === 'pending') bg-warning-500
                                                @elseif($status === 'active' || $status === 'in_progress') bg-info-500
                                                @elseif($status === 'cancelled') bg-danger-500
                                                @else bg-surface-400
                                                @endif"></span>
                                            <span class="text-sm text-surface-600 dark:text-surface-400 capitalize">{{ str_replace('_', ' ', $status) }} ({{ $count }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center h-48 text-surface-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-sm">No contracts yet</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <a href="{{ route('services.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 hover:border-primary-500 dark:hover:border-primary-500 hover:shadow-lg transition-all group">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Browse Services</span>
                        </a>
                        <a href="{{ route('jobs.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 hover:border-primary-500 dark:hover:border-primary-500 hover:shadow-lg transition-all group">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent-100 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Find Work</span>
                        </a>
                        <a href="{{ route('jobs.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 hover:border-primary-500 dark:hover:border-primary-500 hover:shadow-lg transition-all group">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-100 dark:bg-success-900/30 text-success-600 dark:text-success-400 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Post a Job</span>
                        </a>
                        <a href="{{ route('products.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 hover:border-primary-500 dark:hover:border-primary-500 hover:shadow-lg transition-all group">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-info-100 dark:bg-info-900/30 text-info-600 dark:text-info-400 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Browse Products</span>
                        </a>
                    </div>

                    <!-- Recent Activity -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Recent Purchases -->
                        <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-700">
                                <h2 class="font-semibold text-surface-900 dark:text-white">Recent Purchases</h2>
                                <a href="{{ route('purchases') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium">View All</a>
                            </div>
                            @if(isset($recentOrders) && $recentOrders->count() > 0)
                                <div class="divide-y divide-surface-200 dark:divide-surface-700">
                                    @foreach($recentOrders->take(4) as $order)
                                        <div class="px-5 py-3 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
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
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-surface-300 dark:text-surface-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">No purchases yet</p>
                                    <a href="{{ route('products.index') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium mt-2 inline-block">Browse Products</a>
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
                                    @foreach($recentServiceOrders->take(4) as $order)
                                        <div class="px-5 py-3 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
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
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-surface-300 dark:text-surface-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">No service orders yet</p>
                                    <a href="{{ route('services.index') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium mt-2 inline-block">Browse Services</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#94a3b8' : '#64748b';

            // Color palette
            const colors = {
                primary: '#6366f1',
                accent: '#06b6d4',
                info: '#0ea5e9',
                warning: '#f59e0b',
                success: '#10b981',
                danger: '#f43f5e',
                surface: '#94a3b8'
            };

            // Activity Donut Chart
            const activityCtx = document.getElementById('activityChart');
            if (activityCtx) {
                new Chart(activityCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Products', 'Services', 'Contracts', 'Jobs'],
                        datasets: [{
                            data: [
                                {{ $activityBreakdown['products'] ?? 0 }},
                                {{ $activityBreakdown['services'] ?? 0 }},
                                {{ $activityBreakdown['contracts'] ?? 0 }},
                                {{ $activityBreakdown['jobs'] ?? 0 }}
                            ],
                            backgroundColor: [colors.primary, colors.accent, colors.info, colors.warning],
                            borderWidth: 0,
                            cutout: '70%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }

            // Order Status Pie Chart
            @if(!empty($orderStatusData))
            const orderStatusCtx = document.getElementById('orderStatusChart');
            if (orderStatusCtx) {
                const orderStatuses = @json(array_keys($orderStatusData));
                const orderCounts = @json(array_values($orderStatusData));
                const orderColors = orderStatuses.map(status => {
                    if (status === 'completed') return colors.success;
                    if (status === 'pending') return colors.warning;
                    if (status === 'processing') return colors.info;
                    if (status === 'cancelled' || status === 'refunded') return colors.danger;
                    return colors.surface;
                });

                new Chart(orderStatusCtx, {
                    type: 'pie',
                    data: {
                        labels: orderStatuses.map(s => s.charAt(0).toUpperCase() + s.slice(1).replace('_', ' ')),
                        datasets: [{
                            data: orderCounts,
                            backgroundColor: orderColors,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
            @endif

            // Service Status Pie Chart
            @if(!empty($serviceStatusData))
            const serviceStatusCtx = document.getElementById('serviceStatusChart');
            if (serviceStatusCtx) {
                const serviceStatuses = @json(array_keys($serviceStatusData));
                const serviceCounts = @json(array_values($serviceStatusData));
                const serviceColors = serviceStatuses.map(status => {
                    if (status === 'completed' || status === 'delivered') return colors.success;
                    if (status === 'pending') return colors.warning;
                    if (status === 'in_progress') return colors.info;
                    if (status === 'cancelled') return colors.danger;
                    return colors.surface;
                });

                new Chart(serviceStatusCtx, {
                    type: 'pie',
                    data: {
                        labels: serviceStatuses.map(s => s.charAt(0).toUpperCase() + s.slice(1).replace('_', ' ')),
                        datasets: [{
                            data: serviceCounts,
                            backgroundColor: serviceColors,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
            @endif

            // Contract Status Pie Chart
            @if(!empty($contractStatusData))
            const contractStatusCtx = document.getElementById('contractStatusChart');
            if (contractStatusCtx) {
                const contractStatuses = @json(array_keys($contractStatusData));
                const contractCounts = @json(array_values($contractStatusData));
                const contractColors = contractStatuses.map(status => {
                    if (status === 'completed') return colors.success;
                    if (status === 'pending') return colors.warning;
                    if (status === 'active' || status === 'in_progress') return colors.info;
                    if (status === 'cancelled') return colors.danger;
                    return colors.surface;
                });

                new Chart(contractStatusCtx, {
                    type: 'pie',
                    data: {
                        labels: contractStatuses.map(s => s.charAt(0).toUpperCase() + s.slice(1).replace('_', ' ')),
                        datasets: [{
                            data: contractCounts,
                            backgroundColor: contractColors,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
            @endif
        });
    </script>
    @endpush
</x-layouts.app>
