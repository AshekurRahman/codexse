<x-layouts.app title="My Quote Requests">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Custom Quote Requests</h1>
                    <p class="mt-1 text-surface-600 dark:text-surface-400">Track all your custom quote requests. Freelancers will review your requirements and send personalized pricing.</p>
                </div>

                <!-- Status Filter -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-surface-500 dark:text-surface-400">Filter:</span>
                    <select onchange="window.location.href = this.value" class="rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-1.5 text-sm text-surface-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        <option value="{{ route('quotes.index') }}" {{ !request('status') ? 'selected' : '' }}>All Requests</option>
                        <option value="{{ route('quotes.index', ['status' => 'pending']) }}" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="{{ route('quotes.index', ['status' => 'quoted']) }}" {{ request('status') == 'quoted' ? 'selected' : '' }}>Quoted</option>
                        <option value="{{ route('quotes.index', ['status' => 'accepted']) }}" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="{{ route('quotes.index', ['status' => 'rejected']) }}" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="{{ route('quotes.index', ['status' => 'expired']) }}" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
            </div>

            @if($quoteRequests->count() > 0)
                <div class="space-y-4">
                    @foreach($quoteRequests as $request)
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <div class="flex flex-col lg:flex-row lg:items-start gap-4">
                                <!-- Service Info -->
                                <div class="flex items-start gap-4 flex-1">
                                    @if($request->service->thumbnail)
                                        <img src="{{ asset('storage/' . $request->service->thumbnail) }}" alt="{{ $request->service->name }}" class="w-16 h-16 rounded-lg object-cover">
                                    @else
                                        <div class="w-16 h-16 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="font-semibold text-surface-900 dark:text-white truncate">{{ $request->title }}</h3>
                                            <x-status-badge :status="$request->status" />
                                        </div>
                                        <p class="text-sm text-surface-500 dark:text-surface-400 mb-2">
                                            For: <a href="{{ route('services.show', $request->service) }}" class="hover:text-primary-600">{{ $request->service->name }}</a>
                                            by {{ $request->seller->store_name }}
                                        </p>
                                        <p class="text-sm text-surface-600 dark:text-surface-400 line-clamp-2">{{ $request->description }}</p>

                                        <!-- Budget & Date -->
                                        <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-surface-500 dark:text-surface-400">
                                            @if($request->budget_min || $request->budget_max)
                                                <span class="flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Budget: {{ format_price($request->budget_min ?? 0) }} - {{ format_price($request->budget_max ?? 0) }}
                                                </span>
                                            @endif
                                            <span class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Requested {{ $request->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        <!-- Quote Info -->
                                        @if($request->quote)
                                            <div class="mt-4 p-3 rounded-lg bg-surface-50 dark:bg-surface-700/50 border border-surface-200 dark:border-surface-600">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-sm font-medium text-surface-900 dark:text-white">
                                                        Quoted: {{ format_price($request->quote->price) }}
                                                    </span>
                                                    <span class="text-sm text-surface-500 dark:text-surface-400">
                                                        {{ $request->quote->delivery_days }} day{{ $request->quote->delivery_days != 1 ? 's' : '' }} delivery
                                                    </span>
                                                </div>
                                                @if($request->quote->expires_at)
                                                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                                                        Expires: {{ $request->quote->expires_at->format('M d, Y') }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex lg:flex-col items-center gap-2 shrink-0">
                                    <a href="{{ route('quotes.show', $request) }}" class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                                        View Details
                                    </a>
                                    @if($request->quote && $request->quote->status === 'pending')
                                        <a href="{{ route('quotes.show', $request) }}" class="inline-flex items-center justify-center rounded-lg border border-green-500 px-4 py-2 text-sm font-medium text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors">
                                            Review Quote
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($quoteRequests->hasPages())
                    <div class="mt-8">
                        {{ $quoteRequests->withQueryString()->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-16 bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700">
                    <div class="w-20 h-20 rounded-full bg-surface-100 dark:bg-surface-700 flex items-center justify-center mx-auto mb-6">
                        <svg class="h-10 w-10 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-2">No Quote Requests Yet</h3>
                    <p class="text-surface-500 dark:text-surface-400 mb-6 max-w-md mx-auto">Need a custom solution? Browse freelance services and request personalized quotes tailored to your specific project requirements.</p>
                    <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-6 py-3 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Browse Freelance Services
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
