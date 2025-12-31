<x-layouts.app title="My Proposals">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">My Proposals</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">Track your submitted proposals</p>
                </div>
                <a href="{{ route('seller.jobs.available') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
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

            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Total Proposals</p>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white mt-1">{{ $proposals->total() }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Pending</p>
                    <p class="text-2xl font-bold text-warning-600 dark:text-warning-400 mt-1">{{ $pendingCount ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Shortlisted</p>
                    <p class="text-2xl font-bold text-info-600 dark:text-info-400 mt-1">{{ $shortlistedCount ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Accepted</p>
                    <p class="text-2xl font-bold text-success-600 dark:text-success-400 mt-1">{{ $acceptedCount ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Success Rate</p>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white mt-1">{{ $successRate ?? 0 }}%</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-2 mb-6">
                <a href="{{ route('seller.proposals.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request('status') ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                    All
                </a>
                <a href="{{ route('seller.proposals.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'pending' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                    Pending
                </a>
                <a href="{{ route('seller.proposals.index', ['status' => 'shortlisted']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'shortlisted' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                    Shortlisted
                </a>
                <a href="{{ route('seller.proposals.index', ['status' => 'accepted']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'accepted' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                    Accepted
                </a>
                <a href="{{ route('seller.proposals.index', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'rejected' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                    Rejected
                </a>
            </div>

            <!-- Proposals List -->
            <div class="space-y-4">
                @forelse($proposals as $proposal)
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-start gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                        <a href="{{ route('seller.jobs.show', $proposal->jobPosting) }}">{{ $proposal->jobPosting->title ?? 'Job Deleted' }}</a>
                                    </h3>
                                    <x-status-badge :status="$proposal->status" size="sm" />
                                </div>
                                <p class="text-sm text-surface-500 dark:text-surface-400 mb-3">
                                    Submitted {{ $proposal->created_at->diffForHumans() }} &bull; Client: {{ $proposal->jobPosting->client->name ?? 'Unknown' }}
                                </p>

                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <p class="text-xs text-surface-500 dark:text-surface-400">Your Bid</p>
                                        <p class="font-semibold text-surface-900 dark:text-white">${{ number_format($proposal->proposed_price, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-surface-500 dark:text-surface-400">Duration</p>
                                        <p class="font-semibold text-surface-900 dark:text-white">{{ $proposal->proposed_duration }} days</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-surface-500 dark:text-surface-400">Job Budget</p>
                                        <p class="font-semibold text-surface-900 dark:text-white">${{ number_format($proposal->jobPosting->budget_min ?? 0) }} - ${{ number_format($proposal->jobPosting->budget_max ?? 0) }}</p>
                                    </div>
                                </div>

                                <div class="text-sm text-surface-600 dark:text-surface-400 line-clamp-2">
                                    {{ $proposal->cover_letter }}
                                </div>
                            </div>

                            <div class="flex lg:flex-col items-center lg:items-end gap-3">
                                <a href="{{ route('seller.jobs.show', $proposal->jobPosting) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                    View Job
                                </a>
                                @if($proposal->status === 'pending')
                                    <form action="{{ route('seller.proposals.withdraw', $proposal) }}" method="POST" onsubmit="return confirm('Are you sure you want to withdraw this proposal?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-danger-600 hover:text-danger-700 dark:text-danger-400 dark:hover:text-danger-300">Withdraw</button>
                                    </form>
                                @endif
                                @if($proposal->status === 'accepted' && $proposal->contract)
                                    <a href="{{ route('seller.contracts.show', $proposal->contract) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-success-600 hover:bg-success-700 text-white font-medium rounded-lg transition-colors">
                                        View Contract
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-12 text-center">
                        <svg class="w-16 h-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No proposals yet</h3>
                        <p class="text-surface-600 dark:text-surface-400 mb-6">Start applying to jobs to see your proposals here.</p>
                        <a href="{{ route('seller.jobs.available') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Find Jobs
                        </a>
                    </div>
                @endforelse
            </div>

            @if($proposals->hasPages())
                <div class="mt-6">
                    {{ $proposals->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
