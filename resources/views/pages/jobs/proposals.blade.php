<x-layouts.app title="Proposals for {{ $jobPosting->title }}">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('jobs.my-jobs') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to My Jobs
                </a>
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Proposals</h1>
                        <p class="mt-1 text-surface-600 dark:text-surface-400">{{ $jobPosting->title }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1.5 rounded-full text-sm font-medium {{ $jobPosting->status === 'open' ? 'bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-300' : 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400' }}">
                            {{ ucfirst($jobPosting->status) }}
                        </span>
                        <span class="text-surface-500 dark:text-surface-400">
                            {{ $jobPosting->proposals->count() }} proposal{{ $jobPosting->proposals->count() !== 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-success-50 dark:bg-success-900/30 border border-success-200 dark:border-success-800 rounded-lg text-success-700 dark:text-success-300">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-danger-50 dark:bg-danger-900/30 border border-danger-200 dark:border-danger-800 rounded-lg text-danger-700 dark:text-danger-300">
                    {{ session('error') }}
                </div>
            @endif

            @if($jobPosting->proposals->count() > 0)
                <div class="space-y-6">
                    @foreach($jobPosting->proposals as $proposal)
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                            <div class="p-6">
                                <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                                    <!-- Seller Info -->
                                    <div class="flex items-start gap-4 flex-1">
                                        <div class="w-14 h-14 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center overflow-hidden flex-shrink-0">
                                            @if($proposal->seller?->logo)
                                                <img src="{{ Storage::url($proposal->seller->logo) }}" alt="" class="w-14 h-14 rounded-full object-cover">
                                            @else
                                                <span class="text-xl font-medium text-primary-700 dark:text-primary-300">
                                                    {{ strtoupper(substr($proposal->seller->store_name ?? 'S', 0, 1)) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <a href="{{ route('sellers.show', $proposal->seller) }}" class="font-semibold text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                                    {{ $proposal->seller->store_name }}
                                                </a>
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $proposal->status === 'pending' ? 'bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-300' : ($proposal->status === 'accepted' ? 'bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-300' : 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400') }}">
                                                    {{ ucfirst($proposal->status) }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">
                                                Level {{ $proposal->seller->level ?? 1 }} Seller
                                                @if($proposal->seller->total_sales > 0)
                                                    &middot; {{ format_price($proposal->seller->total_sales) }} earned
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Bid Amount -->
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ format_price($proposal->bid_amount) }}</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">
                                            {{ $proposal->estimated_duration }} day{{ $proposal->estimated_duration != 1 ? 's' : '' }} delivery
                                        </p>
                                    </div>
                                </div>

                                <!-- Cover Letter -->
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Cover Letter</h4>
                                    <p class="text-surface-600 dark:text-surface-400 whitespace-pre-line">{{ $proposal->cover_letter }}</p>
                                </div>

                                <!-- Milestones -->
                                @if($proposal->milestones && count($proposal->milestones) > 0)
                                    <div class="mt-4">
                                        <h4 class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Proposed Milestones</h4>
                                        <div class="space-y-2">
                                            @foreach($proposal->milestones as $index => $milestone)
                                                <div class="flex items-center justify-between p-3 bg-surface-50 dark:bg-surface-900/50 rounded-lg">
                                                    <div>
                                                        <span class="text-sm font-medium text-surface-900 dark:text-white">{{ $milestone['title'] ?? "Milestone " . ($index + 1) }}</span>
                                                        @if(isset($milestone['description']))
                                                            <p class="text-xs text-surface-500 dark:text-surface-400 mt-0.5">{{ $milestone['description'] }}</p>
                                                        @endif
                                                    </div>
                                                    <span class="text-sm font-semibold text-surface-900 dark:text-white">{{ format_price($milestone['amount'] ?? 0) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Submitted At -->
                                <p class="mt-4 text-sm text-surface-500 dark:text-surface-400">
                                    Submitted {{ $proposal->created_at->diffForHumans() }}
                                </p>

                                <!-- Actions -->
                                @if($proposal->status === 'pending' && $jobPosting->status === 'open')
                                    <div class="mt-6 pt-4 border-t border-surface-200 dark:border-surface-700 flex flex-wrap gap-3">
                                        <form action="{{ route('jobs.accept-proposal', [$jobPosting, $proposal]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-success-600 hover:bg-success-700 text-white font-medium rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Accept Proposal
                                            </button>
                                        </form>
                                        <form action="{{ route('jobs.reject-proposal', [$jobPosting, $proposal]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to reject this proposal?')">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 border border-danger-300 dark:border-danger-700 text-danger-600 dark:text-danger-400 rounded-lg hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Reject
                                            </button>
                                        </form>
                                        <a href="{{ route('conversations.create') }}?seller={{ $proposal->seller_id }}" class="inline-flex items-center gap-2 px-4 py-2 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            Message
                                        </a>
                                    </div>
                                @elseif($proposal->status === 'accepted')
                                    <div class="mt-6 pt-4 border-t border-surface-200 dark:border-surface-700">
                                        <a href="{{ route('contracts.show', $jobPosting->contract) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                            View Contract
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-surface-400 dark:text-surface-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No proposals yet</h3>
                    <p class="text-surface-500 dark:text-surface-400 max-w-md mx-auto">
                        Your job posting is live. Freelancers will be able to submit proposals soon. Make sure your job description is clear and detailed to attract quality proposals.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
