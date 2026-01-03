<x-layouts.app title="My Proposals">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">My Proposals</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">Track your submitted proposals</p>
                </div>
                <a href="{{ route('seller.jobs.available') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Find Jobs
                </a>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-success-50 dark:bg-success-900/30 border border-success-200 dark:border-success-800 rounded-lg text-success-700 dark:text-success-300">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4 mb-6">
                <form action="{{ route('seller.proposals.index') }}" method="GET" class="flex flex-wrap gap-4">
                    <select name="status" onchange="this.form.submit()" class="rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                    </select>
                </form>
            </div>

            <!-- Proposals List -->
            @if($proposals->count() > 0)
                <div class="space-y-4">
                    @foreach($proposals as $proposal)
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <a href="{{ route('seller.proposals.show', $proposal) }}" class="text-lg font-semibold text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                            {{ $proposal->jobPosting->title ?? 'Job Deleted' }}
                                        </a>
                                        <span class="px-2 py-0.5 text-xs font-medium rounded
                                            @if($proposal->status === 'pending') bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-300
                                            @elseif($proposal->status === 'accepted') bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-300
                                            @elseif($proposal->status === 'rejected') bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-300
                                            @else bg-surface-100 text-surface-700 dark:bg-surface-700 dark:text-surface-300 @endif">
                                            {{ ucfirst($proposal->status) }}
                                        </span>
                                    </div>

                                    <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">
                                        Client: {{ $proposal->jobPosting->client->name ?? 'Unknown' }} &bull; Submitted {{ $proposal->created_at->diffForHumans() }}
                                    </p>

                                    <p class="text-surface-600 dark:text-surface-400 mt-3 line-clamp-2">
                                        {{ Str::limit($proposal->cover_letter, 150) }}
                                    </p>
                                </div>

                                <div class="flex flex-col items-end gap-3 lg:min-w-[180px]">
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-surface-900 dark:text-white">{{ format_price($proposal->proposed_price) }}</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ $proposal->proposed_duration }} {{ $proposal->duration_type }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('seller.proposals.show', $proposal) }}" class="px-4 py-2 bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-200 dark:hover:bg-surface-600 transition-colors text-sm font-medium">
                                            View Details
                                        </a>
                                        @if($proposal->status === 'accepted' && $proposal->contract)
                                            <a href="{{ route('seller.contracts.show', $proposal->contract) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors text-sm font-medium">
                                                View Contract
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $proposals->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-12 text-center">
                    <svg class="w-16 h-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-2">No proposals yet</h3>
                    <p class="text-surface-600 dark:text-surface-400 mb-6">Start applying to jobs to get your first contract!</p>
                    <a href="{{ route('seller.jobs.available') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        Browse Available Jobs
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
