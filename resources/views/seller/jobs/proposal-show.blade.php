<x-layouts.app title="Proposal Details">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('seller.proposals.index') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to My Proposals
                </a>
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Proposal Details</h1>
                        <p class="text-surface-600 dark:text-surface-400 mt-1">for "{{ $proposal->jobPosting->title ?? 'Job Deleted' }}"</p>
                    </div>
                    <span class="px-3 py-1 text-sm font-medium rounded-lg
                        @if($proposal->status === 'pending') bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-300
                        @elseif($proposal->status === 'accepted') bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-300
                        @elseif($proposal->status === 'rejected') bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-300
                        @else bg-surface-100 text-surface-700 dark:bg-surface-700 dark:text-surface-300 @endif">
                        {{ ucfirst($proposal->status) }}
                    </span>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-success-50 dark:bg-success-900/30 border border-success-200 dark:border-success-800 rounded-lg text-success-700 dark:text-success-300">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 p-4 bg-info-50 dark:bg-info-900/30 border border-info-200 dark:border-info-800 rounded-lg text-info-700 dark:text-info-300">
                    {{ session('info') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-danger-50 dark:bg-danger-900/30 border border-danger-200 dark:border-danger-800 rounded-lg text-danger-700 dark:text-danger-300">
                    {{ session('error') }}
                </div>
            @endif

            <div class="space-y-6">
                <!-- Proposal Overview -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Proposal Overview</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Proposed Price</p>
                            <p class="text-xl font-bold text-surface-900 dark:text-white">{{ format_price($proposal->proposed_price) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Timeline</p>
                            <p class="text-xl font-bold text-surface-900 dark:text-white">{{ $proposal->proposed_duration }} {{ ucfirst($proposal->duration_type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Submitted</p>
                            <p class="text-xl font-bold text-surface-900 dark:text-white">{{ $proposal->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Cover Letter -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Cover Letter</h2>
                    <div class="prose prose-sm dark:prose-invert max-w-none text-surface-600 dark:text-surface-400">
                        {!! nl2br(e($proposal->cover_letter)) !!}
                    </div>
                </div>

                <!-- Milestones -->
                @if($proposal->milestones && count($proposal->milestones) > 0)
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Proposed Milestones</h2>
                        <div class="space-y-4">
                            @foreach($proposal->milestones as $index => $milestone)
                                <div class="flex items-start gap-4 p-4 bg-surface-50 dark:bg-surface-700/50 rounded-lg">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <h3 class="font-medium text-surface-900 dark:text-white">{{ $milestone['title'] }}</h3>
                                            <span class="font-semibold text-surface-900 dark:text-white">{{ format_price($milestone['amount']) }}</span>
                                        </div>
                                        @if(!empty($milestone['description']))
                                            <p class="text-sm text-surface-600 dark:text-surface-400 mt-1">{{ $milestone['description'] }}</p>
                                        @endif
                                        @if(!empty($milestone['due_date']))
                                            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Due: {{ \Carbon\Carbon::parse($milestone['due_date'])->format('M d, Y') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Attachments -->
                @if($proposal->attachments && count($proposal->attachments) > 0)
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Attachments</h2>
                        <div class="space-y-2">
                            @foreach($proposal->attachments as $attachment)
                                <div class="flex items-center justify-between p-3 bg-surface-50 dark:bg-surface-700/50 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                        <span class="text-sm text-surface-700 dark:text-surface-300">{{ $attachment['name'] }}</span>
                                    </div>
                                    <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
                                        Download
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Job Details -->
                @if($proposal->jobPosting)
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Job Details</h2>

                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Client</p>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $proposal->jobPosting->client->name ?? 'Unknown' }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Budget</p>
                                <p class="font-medium text-surface-900 dark:text-white">
                                    @if($proposal->jobPosting->budget_min && $proposal->jobPosting->budget_max)
                                        {{ format_price($proposal->jobPosting->budget_min) }} - {{ format_price($proposal->jobPosting->budget_max) }}
                                    @elseif($proposal->jobPosting->budget_min)
                                        From {{ format_price($proposal->jobPosting->budget_min) }}
                                    @elseif($proposal->jobPosting->budget_max)
                                        Up to {{ format_price($proposal->jobPosting->budget_max) }}
                                    @else
                                        Not specified
                                    @endif
                                </p>
                            </div>

                            <div>
                                <a href="{{ route('seller.jobs.show', $proposal->jobPosting) }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium text-sm">
                                    View Full Job Posting &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex items-center justify-between gap-4">
                    @if($proposal->status === 'pending')
                        <form action="{{ route('seller.proposals.withdraw', $proposal) }}" method="POST" onsubmit="return confirm('Are you sure you want to withdraw this proposal?');">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-danger-600 hover:text-danger-700 dark:text-danger-400 font-medium">
                                Withdraw Proposal
                            </button>
                        </form>
                        <a href="{{ route('seller.proposals.edit', $proposal) }}" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                            Edit Proposal
                        </a>
                    @elseif($proposal->status === 'accepted' && $proposal->contract)
                        <div></div>
                        <a href="{{ route('seller.contracts.show', $proposal->contract) }}" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                            View Contract
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
