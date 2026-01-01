<x-layouts.app title="My Disputes">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">My Disputes</h1>
                <p class="text-surface-600 dark:text-surface-400 mt-1">Track and manage your dispute resolutions</p>
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

            <!-- Disputes List -->
            <div class="space-y-4">
                @forelse($disputes as $dispute)
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-surface-900 dark:text-white">
                                        Dispute #{{ $dispute->id }}
                                    </h3>
                                    <x-status-badge :status="$dispute->status" size="sm" />
                                </div>

                                <p class="text-sm text-surface-500 dark:text-surface-400 mb-3">
                                    Opened {{ $dispute->created_at->diffForHumans() }}
                                    @if($dispute->disputable)
                                        &bull;
                                        @if($dispute->disputable_type === 'App\\Models\\JobContract')
                                            Contract: {{ $dispute->disputable->title ?? 'N/A' }}
                                        @elseif($dispute->disputable_type === 'App\\Models\\ServiceOrder')
                                            Service Order: #{{ $dispute->disputable->order_number ?? $dispute->disputable->id }}
                                        @endif
                                    @endif
                                </p>

                                <div class="flex items-center gap-4 mb-3">
                                    <div>
                                        <p class="text-xs text-surface-500 dark:text-surface-400">Reason</p>
                                        <p class="font-medium text-surface-900 dark:text-white">{{ $dispute->reason_label }}</p>
                                    </div>
                                    @if($dispute->escrowTransaction)
                                        <div>
                                            <p class="text-xs text-surface-500 dark:text-surface-400">Amount in Escrow</p>
                                            <p class="font-medium text-surface-900 dark:text-white">{{ format_price($dispute->escrowTransaction->amount) }}</p>
                                        </div>
                                    @endif
                                </div>

                                <p class="text-sm text-surface-600 dark:text-surface-400 line-clamp-2">
                                    {{ $dispute->description }}
                                </p>
                            </div>

                            <div class="flex lg:flex-col items-center lg:items-end gap-3">
                                <a href="{{ route('disputes.show', $dispute) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                    View Details
                                </a>
                                @if($dispute->status === 'open')
                                    <form action="{{ route('disputes.cancel', $dispute) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this dispute?')">
                                        @csrf
                                        <button type="submit" class="text-sm text-danger-600 hover:text-danger-700 dark:text-danger-400 dark:hover:text-danger-300">
                                            Cancel Dispute
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-12 text-center">
                        <svg class="w-16 h-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No disputes</h3>
                        <p class="text-surface-600 dark:text-surface-400">You haven't opened any disputes yet.</p>
                    </div>
                @endforelse
            </div>

            @if($disputes->hasPages())
                <div class="mt-6">
                    {{ $disputes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
