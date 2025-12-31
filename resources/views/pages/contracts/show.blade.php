<x-layouts.app title="Contract Details - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('contracts.index') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Contracts
                </a>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $contract->title ?? $contract->jobPosting->title ?? 'Contract' }}</h1>
                            <x-status-badge :status="$contract->status" />
                        </div>
                        <p class="mt-1 text-surface-600 dark:text-surface-400">Contract #{{ $contract->contract_number }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('conversations.show', $contract->conversation ?? '') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Message
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Contract Details -->
                    <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                            <h2 class="font-semibold text-surface-900 dark:text-white">Project Scope & Requirements</h2>
                            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">The agreed-upon work and deliverables for this contract.</p>
                        </div>
                        <div class="p-6">
                            <div class="prose prose-surface dark:prose-invert max-w-none">
                                {!! $contract->jobPosting->description ?? '<p class="text-surface-500 dark:text-surface-400">No description available.</p>' !!}
                            </div>
                        </div>
                    </div>

                    <!-- Milestones -->
                    <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="font-semibold text-surface-900 dark:text-white">Project Milestones</h2>
                                    <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Track progress through milestone-based payments. Funds are released when work is approved.</p>
                                </div>
                                @if($contract->milestones && $contract->milestones->count())
                                    <span class="text-sm font-medium px-2 py-1 rounded-lg bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300">
                                        {{ $contract->milestones->where('status', 'completed')->count() }}/{{ $contract->milestones->count() }} completed
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="divide-y divide-surface-200 dark:divide-surface-700">
                            @forelse($contract->milestones ?? [] as $milestone)
                                <div class="p-6">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h3 class="font-medium text-surface-900 dark:text-white">{{ $milestone->title }}</h3>
                                                <x-status-badge :status="$milestone->status" size="sm" />
                                            </div>
                                            @if($milestone->description)
                                                <p class="text-sm text-surface-600 dark:text-surface-400 mb-3">{{ $milestone->description }}</p>
                                            @endif
                                            <div class="flex items-center gap-4 text-sm text-surface-500 dark:text-surface-400">
                                                <span>${{ number_format($milestone->amount, 2) }}</span>
                                                @if($milestone->due_date)
                                                    <span>Due: {{ $milestone->due_date->format('M d, Y') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if($milestone->status === 'pending' && $contract->seller_id === auth()->user()->seller?->id)
                                                <form action="{{ route('seller.milestones.submit', $milestone) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                        Submit Work
                                                    </button>
                                                </form>
                                            @elseif($milestone->status === 'submitted' && $contract->client_id === auth()->id())
                                                <div class="flex items-center gap-2">
                                                    <form action="{{ route('contracts.milestones.approve', $milestone) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-success-600 hover:bg-success-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('contracts.milestones.revision', $milestone) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-warning-600 hover:bg-warning-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                            Request Revision
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center">
                                    <p class="text-surface-500 dark:text-surface-400">No milestones defined for this contract.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Activity Timeline -->
                    <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                            <h2 class="font-semibold text-surface-900 dark:text-white">Activity Timeline</h2>
                        </div>
                        <div class="p-6">
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    <li>
                                        <div class="relative pb-8">
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-surface-200 dark:bg-surface-700" aria-hidden="true"></span>
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-success-100 dark:bg-success-900/30 flex items-center justify-center ring-8 ring-white dark:ring-surface-800">
                                                        <svg class="h-4 w-4 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-surface-600 dark:text-surface-400">Contract created</p>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-sm text-surface-500 dark:text-surface-400">
                                                        {{ $contract->created_at->format('M d, Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @if($contract->status === 'active' || $contract->status === 'in_progress')
                                        <li>
                                            <div class="relative pb-8">
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full bg-info-100 dark:bg-info-900/30 flex items-center justify-center ring-8 ring-white dark:ring-surface-800">
                                                            <svg class="h-4 w-4 text-info-600 dark:text-info-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                        <div>
                                                            <p class="text-sm text-surface-600 dark:text-surface-400">Work in progress</p>
                                                        </div>
                                                        <div class="whitespace-nowrap text-right text-sm text-surface-500 dark:text-surface-400">
                                                            Ongoing
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Contract Summary -->
                    <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                            <h3 class="font-semibold text-surface-900 dark:text-white">Contract Summary</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Total Value</span>
                                <span class="font-semibold text-surface-900 dark:text-white">${{ number_format($contract->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Payment Type</span>
                                <span class="font-medium text-surface-900 dark:text-white">{{ ucfirst($contract->payment_type ?? 'Fixed') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Start Date</span>
                                <span class="font-medium text-surface-900 dark:text-white">{{ $contract->created_at->format('M d, Y') }}</span>
                            </div>
                            @if($contract->completed_at)
                                <div class="flex justify-between">
                                    <span class="text-surface-600 dark:text-surface-400">Completed</span>
                                    <span class="font-medium text-surface-900 dark:text-white">{{ $contract->completed_at->format('M d, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Parties -->
                    <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                            <h3 class="font-semibold text-surface-900 dark:text-white">Parties</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- Client -->
                            <div>
                                <p class="text-xs font-medium text-surface-400 uppercase tracking-wider mb-2">Client</p>
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($contract->client->name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-surface-900 dark:text-white">{{ $contract->client->name ?? 'N/A' }}</p>
                                        @if($contract->client_id === auth()->id())
                                            <p class="text-xs text-primary-600 dark:text-primary-400">You</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Freelancer -->
                            <div>
                                <p class="text-xs font-medium text-surface-400 uppercase tracking-wider mb-2">Freelancer</p>
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-accent-500 to-primary-500 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($contract->seller->user->name ?? 'F', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-surface-900 dark:text-white">{{ $contract->seller->user->name ?? 'N/A' }}</p>
                                        @if($contract->seller_id === auth()->user()->seller?->id)
                                            <p class="text-xs text-primary-600 dark:text-primary-400">You</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Escrow Status -->
                    <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                        <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                            <h3 class="font-semibold text-surface-900 dark:text-white">Payment Protection</h3>
                            <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">Funds secured in escrow until work is approved</p>
                        </div>
                        <div class="p-6">
                            @if($contract->escrowTransactions && $contract->escrowTransactions->count())
                                @foreach($contract->escrowTransactions as $escrow)
                                    <div class="flex items-center justify-between {{ !$loop->last ? 'mb-3 pb-3 border-b border-surface-200 dark:border-surface-700' : '' }}">
                                        <div>
                                            <p class="text-sm font-medium text-surface-900 dark:text-white">${{ number_format($escrow->amount, 2) }}</p>
                                            <p class="text-xs text-surface-500 dark:text-surface-400">{{ ucfirst($escrow->status) }}</p>
                                        </div>
                                        @if($escrow->status === 'held')
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400">
                                                Secured in Escrow
                                            </span>
                                        @elseif($escrow->status === 'released')
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400">
                                                Payment Released
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <svg class="w-8 h-8 text-surface-300 dark:text-surface-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">No escrow transactions yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    @if($contract->status === 'active' || $contract->status === 'in_progress')
                        <div class="rounded-xl border border-danger-200 dark:border-danger-800 bg-danger-50 dark:bg-danger-900/20 p-4">
                            <h4 class="font-medium text-danger-900 dark:text-danger-100 mb-2">Need Help?</h4>
                            <p class="text-sm text-danger-700 dark:text-danger-300 mb-3">If there's an issue with this contract, you can open a dispute.</p>
                            <a href="{{ route('disputes.create', ['contract' => $contract->id]) }}" class="inline-flex items-center text-sm font-medium text-danger-600 dark:text-danger-400 hover:text-danger-700">
                                Open a Dispute
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
