<x-layouts.app title="Quote Requests">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Quote Requests</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">Custom quote requests from buyers</p>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-success-50 dark:bg-success-900/30 border border-success-200 dark:border-success-800 rounded-lg text-success-700 dark:text-success-300">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Total Requests</p>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white mt-1">{{ $quoteRequests->total() }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Pending</p>
                    <p class="text-2xl font-bold text-warning-600 dark:text-warning-400 mt-1">{{ $pendingCount ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Quoted</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400 mt-1">{{ $quotedCount ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Accepted</p>
                    <p class="text-2xl font-bold text-success-600 dark:text-success-400 mt-1">{{ $acceptedCount ?? 0 }}</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-2 mb-6">
                <a href="{{ route('seller.quotes.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request('status') ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                    All
                </a>
                <a href="{{ route('seller.quotes.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'pending' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                    Pending
                </a>
                <a href="{{ route('seller.quotes.index', ['status' => 'quoted']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'quoted' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                    Quoted
                </a>
                <a href="{{ route('seller.quotes.index', ['status' => 'accepted']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'accepted' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                    Accepted
                </a>
                <a href="{{ route('seller.quotes.index', ['status' => 'expired']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'expired' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                    Expired
                </a>
            </div>

            <!-- Quote Requests -->
            <div class="space-y-4">
                @forelse($quoteRequests as $request)
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                                        <span class="text-lg font-medium text-primary-700 dark:text-primary-300">{{ strtoupper(substr($request->buyer->name ?? 'U', 0, 1)) }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 mb-1">
                                            <h3 class="font-semibold text-surface-900 dark:text-white">{{ $request->title }}</h3>
                                            <x-status-badge :status="$request->status" size="sm" />
                                        </div>
                                        <p class="text-sm text-surface-500 dark:text-surface-400 mb-2">
                                            From {{ $request->buyer->name ?? 'Unknown' }} &bull; {{ $request->created_at->diffForHumans() }}
                                        </p>
                                        <p class="text-surface-600 dark:text-surface-400 line-clamp-2">{{ $request->description }}</p>
                                        <div class="flex flex-wrap items-center gap-4 mt-3 text-sm">
                                            <span class="text-surface-500 dark:text-surface-400">
                                                Budget: <span class="font-medium text-surface-900 dark:text-white">${{ number_format($request->budget_min) }} - ${{ number_format($request->budget_max) }}</span>
                                            </span>
                                            @if($request->service)
                                                <span class="text-surface-500 dark:text-surface-400">
                                                    Service: <span class="font-medium text-surface-900 dark:text-white">{{ $request->service->name }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 lg:flex-col lg:items-end">
                                <a href="{{ route('seller.quotes.show', $request) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                    @if($request->status === 'pending')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Send Quote
                                    @else
                                        View Details
                                    @endif
                                </a>
                                @if($request->quote)
                                    <div class="text-right mt-2">
                                        <p class="text-sm text-surface-500 dark:text-surface-400">Your quote</p>
                                        <p class="font-semibold text-surface-900 dark:text-white">${{ number_format($request->quote->price, 2) }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-12 text-center">
                        <svg class="w-16 h-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No quote requests</h3>
                        <p class="text-surface-600 dark:text-surface-400">Quote requests from buyers will appear here.</p>
                    </div>
                @endforelse
            </div>

            @if($quoteRequests->hasPages())
                <div class="mt-6">
                    {{ $quoteRequests->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
