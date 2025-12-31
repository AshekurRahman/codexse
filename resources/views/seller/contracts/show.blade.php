<x-layouts.app title="Contract #{{ $contract->contract_number }}">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('seller.contracts.index') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Contracts
                </a>
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Contract #{{ $contract->contract_number }}</h1>
                            <x-status-badge :status="$contract->status" />
                        </div>
                        <p class="text-surface-600 dark:text-surface-400 mt-1">{{ $contract->jobPosting->title ?? 'Contract Details' }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($contract->conversation)
                            <a href="{{ route('conversations.show', $contract->conversation) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Message Client
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-success-50 dark:bg-success-900/30 border border-success-200 dark:border-success-800 rounded-lg text-success-700 dark:text-success-300">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Milestones -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Milestones</h2>

                        @if($contract->milestones && $contract->milestones->count() > 0)
                            <div class="space-y-4">
                                @foreach($contract->milestones as $index => $milestone)
                                    <div class="border border-surface-200 dark:border-surface-700 rounded-lg p-4 {{ $milestone->status === 'in_progress' ? 'bg-primary-50 dark:bg-primary-900/10 border-primary-200 dark:border-primary-800' : '' }}">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span class="w-6 h-6 rounded-full {{ $milestone->status === 'approved' ? 'bg-success-100 text-success-600' : ($milestone->status === 'in_progress' ? 'bg-primary-100 text-primary-600' : 'bg-surface-100 text-surface-500') }} flex items-center justify-center text-sm font-medium">
                                                        @if($milestone->status === 'approved')
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        @else
                                                            {{ $index + 1 }}
                                                        @endif
                                                    </span>
                                                    <h3 class="font-medium text-surface-900 dark:text-white">{{ $milestone->title }}</h3>
                                                    <x-status-badge :status="$milestone->status" size="sm" />
                                                </div>
                                                <p class="text-sm text-surface-600 dark:text-surface-400 ml-9 mb-3">{{ $milestone->description ?? 'No description' }}</p>
                                                <div class="flex items-center gap-4 ml-9 text-sm text-surface-500 dark:text-surface-400">
                                                    <span class="font-medium text-surface-900 dark:text-white">${{ number_format($milestone->amount, 2) }}</span>
                                                    @if($milestone->due_date)
                                                        <span>Due: {{ $milestone->due_date->format('M d, Y') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex flex-col gap-2">
                                                @if($milestone->status === 'in_progress')
                                                    <a href="{{ route('seller.milestones.submit', $milestone) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                        </svg>
                                                        Submit Work
                                                    </a>
                                                @elseif($milestone->status === 'pending_review')
                                                    <span class="text-xs text-info-600 dark:text-info-400">Awaiting client review</span>
                                                @elseif($milestone->status === 'revision_requested')
                                                    <a href="{{ route('seller.milestones.submit', $milestone) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-warning-600 hover:bg-warning-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                        Resubmit
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                        @if($milestone->status === 'revision_requested' && $milestone->revision_notes)
                                            <div class="mt-3 ml-9 p-3 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-lg">
                                                <p class="text-sm font-medium text-warning-700 dark:text-warning-300">Revision Requested:</p>
                                                <p class="text-sm text-warning-600 dark:text-warning-400 mt-1">{{ $milestone->revision_notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-surface-500 dark:text-surface-400">No milestones defined for this contract.</p>
                        @endif
                    </div>

                    <!-- Activity Timeline -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Activity</h2>
                        <div class="space-y-4">
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-3 h-3 rounded-full bg-success-500"></div>
                                    <div class="flex-1 w-0.5 bg-surface-200 dark:bg-surface-700"></div>
                                </div>
                                <div class="pb-4">
                                    <p class="font-medium text-surface-900 dark:text-white">Contract Created</p>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">{{ $contract->created_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                            @if($contract->started_at)
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-3 h-3 rounded-full bg-primary-500"></div>
                                        <div class="flex-1 w-0.5 bg-surface-200 dark:bg-surface-700"></div>
                                    </div>
                                    <div class="pb-4">
                                        <p class="font-medium text-surface-900 dark:text-white">Work Started</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ $contract->started_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                            @foreach($contract->milestones->where('status', 'approved') as $milestone)
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-3 h-3 rounded-full bg-success-500"></div>
                                        <div class="flex-1 w-0.5 bg-surface-200 dark:bg-surface-700"></div>
                                    </div>
                                    <div class="pb-4">
                                        <p class="font-medium text-surface-900 dark:text-white">{{ $milestone->title }} Approved</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ $milestone->approved_at?->format('M d, Y \a\t g:i A') ?? 'Recently' }}</p>
                                    </div>
                                </div>
                            @endforeach
                            @if($contract->completed_at)
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-3 h-3 rounded-full bg-success-500"></div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-surface-900 dark:text-white">Contract Completed</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ $contract->completed_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Contract Summary -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Contract Summary</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Total Value</span>
                                <span class="font-semibold text-surface-900 dark:text-white">${{ number_format($contract->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Payment Type</span>
                                <span class="text-surface-900 dark:text-white">{{ ucfirst($contract->payment_type) }}</span>
                            </div>
                            @if($contract->milestones)
                                @php
                                    $releasedAmount = $contract->milestones->where('status', 'approved')->sum('amount');
                                    $pendingAmount = $contract->total_amount - $releasedAmount;
                                @endphp
                                <div class="pt-3 border-t border-surface-200 dark:border-surface-700">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-surface-600 dark:text-surface-400">Released</span>
                                        <span class="text-success-600 dark:text-success-400">${{ number_format($releasedAmount, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-surface-600 dark:text-surface-400">In Escrow</span>
                                        <span class="text-surface-900 dark:text-white">${{ number_format($pendingAmount, 2) }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Client Info -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Client</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                <span class="text-lg font-medium text-primary-700 dark:text-primary-300">{{ strtoupper(substr($contract->client->name ?? 'U', 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $contract->client->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Member since {{ $contract->client?->created_at?->format('M Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contract Dates -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Timeline</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Started</span>
                                <span class="text-surface-900 dark:text-white">{{ $contract->created_at->format('M d, Y') }}</span>
                            </div>
                            @if($contract->deadline)
                                <div class="flex justify-between">
                                    <span class="text-surface-600 dark:text-surface-400">Deadline</span>
                                    <span class="text-surface-900 dark:text-white">{{ $contract->deadline->format('M d, Y') }}</span>
                                </div>
                            @endif
                            @if($contract->completed_at)
                                <div class="flex justify-between">
                                    <span class="text-surface-600 dark:text-surface-400">Completed</span>
                                    <span class="text-success-600 dark:text-success-400">{{ $contract->completed_at->format('M d, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    @if($contract->status === 'active')
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <a href="{{ route('support.create') }}?contract={{ $contract->id }}" class="block w-full px-4 py-2 text-center border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">Report Issue</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
