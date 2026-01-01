<x-layouts.app title="Find Jobs">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Find Jobs</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">Browse available job opportunities</p>
                </div>
                <a href="{{ route('seller.proposals.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    My Proposals
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4 mb-6">
                <form action="{{ route('seller.jobs.available') }}" method="GET" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search jobs..." class="w-full px-4 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 text-surface-900 dark:text-white placeholder-surface-400">
                    </div>
                    <select name="category" class="px-4 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 text-surface-900 dark:text-white">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <select name="budget_type" class="px-4 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 text-surface-900 dark:text-white">
                        <option value="">All Budget Types</option>
                        <option value="fixed" {{ request('budget_type') === 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                        <option value="hourly" {{ request('budget_type') === 'hourly' ? 'selected' : '' }}>Hourly Rate</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">Search</button>
                    @if(request()->hasAny(['search', 'category', 'budget_type']))
                        <a href="{{ route('seller.jobs.available') }}" class="px-4 py-2 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">Clear</a>
                    @endif
                </form>
            </div>

            <!-- Jobs Grid -->
            <div class="space-y-4">
                @forelse($jobs as $job)
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-start gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                        <a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a>
                                    </h3>
                                    @if($job->is_urgent)
                                        <span class="px-2 py-0.5 text-xs font-medium bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-300 rounded">Urgent</span>
                                    @endif
                                </div>
                                <p class="text-surface-600 dark:text-surface-400 line-clamp-2 mb-4">{{ $job->description }}</p>

                                <div class="flex flex-wrap items-center gap-4 text-sm text-surface-500 dark:text-surface-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ format_price($job->budget_min) }} - {{ format_price($job->budget_max) }}
                                        <span class="text-xs">({{ ucfirst($job->budget_type) }})</span>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        {{ $job->category->name ?? 'Uncategorized' }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Posted {{ $job->created_at->diffForHumans() }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        {{ $job->proposals_count ?? 0 }} proposals
                                    </span>
                                </div>

                                @if($job->skills && count($job->skills) > 0)
                                    <div class="flex flex-wrap gap-2 mt-4">
                                        @foreach(array_slice($job->skills, 0, 5) as $skill)
                                            <span class="px-2 py-1 text-xs bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-400 rounded">{{ $skill }}</span>
                                        @endforeach
                                        @if(count($job->skills) > 5)
                                            <span class="px-2 py-1 text-xs bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-400 rounded">+{{ count($job->skills) - 5 }} more</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="flex lg:flex-col items-center lg:items-end gap-3">
                                @php
                                    $hasProposal = $job->proposals->where('seller_id', auth()->user()->seller?->id)->first();
                                @endphp
                                @if($hasProposal)
                                    <span class="px-3 py-1.5 text-sm font-medium bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-300 rounded-lg">Applied</span>
                                @else
                                    <a href="{{ route('jobs.show', $job) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                        Submit Proposal
                                    </a>
                                @endif

                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-surface-100 dark:bg-surface-700 flex items-center justify-center">
                                        <span class="text-xs font-medium text-surface-600 dark:text-surface-400">{{ strtoupper(substr($job->client->name ?? 'U', 0, 1)) }}</span>
                                    </div>
                                    <span class="text-sm text-surface-600 dark:text-surface-400">{{ $job->client->name ?? 'Unknown' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-12 text-center">
                        <svg class="w-16 h-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No jobs available</h3>
                        <p class="text-surface-600 dark:text-surface-400">Check back later for new opportunities.</p>
                    </div>
                @endforelse
            </div>

            @if($jobs->hasPages())
                <div class="mt-6">
                    {{ $jobs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
