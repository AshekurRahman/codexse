<x-layouts.app title="My Service Orders - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">My Freelance Service Orders</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Track progress, communicate with freelancers, and manage all your service orders in one place. Your payments are protected by our secure escrow system.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar -->
                <aside class="lg:col-span-1">
                    <nav class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-b border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Overview</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>

                        <div class="px-4 py-3 bg-surface-50 dark:bg-surface-700/50 border-y border-surface-200 dark:border-surface-700">
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Services</p>
                        </div>
                        <a href="{{ route('service-orders.index') }}" class="flex items-center gap-3 px-4 py-3 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium border-l-4 border-primary-500">
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
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 border-l-4 border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                        </a>
                    </nav>

                    <!-- Browse Services -->
                    <div class="mt-6">
                        <a href="{{ route('services.index') }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Browse Services
                        </a>
                    </div>
                </aside>

                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <!-- Filter Tabs -->
                    <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
                        <a href="{{ route('service-orders.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('status') ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }} transition-colors whitespace-nowrap">
                            All Orders
                        </a>
                        <a href="{{ route('service-orders.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'pending' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }} transition-colors whitespace-nowrap">
                            Pending
                        </a>
                        <a href="{{ route('service-orders.index', ['status' => 'in_progress']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'in_progress' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }} transition-colors whitespace-nowrap">
                            In Progress
                        </a>
                        <a href="{{ route('service-orders.index', ['status' => 'delivered']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'delivered' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }} transition-colors whitespace-nowrap">
                            Delivered
                        </a>
                        <a href="{{ route('service-orders.index', ['status' => 'completed']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'completed' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }} transition-colors whitespace-nowrap">
                            Completed
                        </a>
                    </div>

                    @if(isset($orders) && $orders->count() > 0)
                        <div class="space-y-4">
                            @foreach($orders as $order)
                                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden hover:shadow-lg transition-shadow">
                                    <div class="p-6">
                                        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                                            <!-- Order Info -->
                                            <div class="flex items-start gap-4 flex-1">
                                                @if($order->service && $order->service->thumbnail)
                                                    <img src="{{ asset('storage/' . $order->service->thumbnail) }}" alt="{{ $order->service->name }}" class="w-20 h-14 rounded-lg object-cover">
                                                @else
                                                    <div class="w-20 h-14 rounded-lg bg-surface-100 dark:bg-surface-700 flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="text-xs text-surface-500 dark:text-surface-400">{{ $order->order_number }}</span>
                                                        <x-status-badge :status="$order->status" />
                                                    </div>
                                                    <h3 class="font-semibold text-surface-900 dark:text-white truncate">{{ $order->title }}</h3>
                                                    <p class="text-sm text-surface-600 dark:text-surface-400">
                                                        by <a href="{{ route('sellers.show', $order->seller) }}" class="hover:text-primary-600">{{ $order->seller->store_name ?? $order->seller->user->name }}</a>
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Order Meta -->
                                            <div class="flex flex-wrap items-center gap-6 lg:gap-8">
                                                <div class="text-center">
                                                    <p class="text-xs text-surface-500 dark:text-surface-400">Price</p>
                                                    <p class="font-semibold text-surface-900 dark:text-white">${{ number_format($order->price, 2) }}</p>
                                                </div>
                                                @if($order->due_at)
                                                    <div class="text-center">
                                                        <p class="text-xs text-surface-500 dark:text-surface-400">Due Date</p>
                                                        <p class="font-semibold {{ $order->due_at->isPast() ? 'text-danger-600 dark:text-danger-400' : 'text-surface-900 dark:text-white' }}">
                                                            {{ $order->due_at->format('M d, Y') }}
                                                        </p>
                                                    </div>
                                                @endif
                                                <div class="text-center">
                                                    <p class="text-xs text-surface-500 dark:text-surface-400">Ordered</p>
                                                    <p class="font-semibold text-surface-900 dark:text-white">{{ $order->created_at->format('M d, Y') }}</p>
                                                </div>
                                                <a href="{{ route('service-orders.show', $order) }}"
                                                    class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                                                    View Order
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($orders->hasPages())
                            <div class="mt-8">
                                {{ $orders->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-16 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700">
                            <div class="w-16 h-16 rounded-full bg-surface-100 dark:bg-surface-700 flex items-center justify-center mx-auto mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-2">No Service Orders Yet</h3>
                            <p class="text-surface-500 dark:text-surface-400 mb-6 max-w-md mx-auto">You haven't ordered any freelance services yet. Browse our marketplace to find expert freelancers for your projects.</p>
                            <a href="{{ route('services.index') }}" class="inline-flex items-center rounded-lg bg-primary-600 px-6 py-3 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Explore Freelance Services
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
