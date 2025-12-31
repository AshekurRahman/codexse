<x-layouts.app title="My Contracts">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">My Contracts</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">Manage your active and past contracts</p>
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
                    <p class="text-sm text-surface-500 dark:text-surface-400">Total Contracts</p>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white mt-1">{{ $contracts->total() }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Active</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400 mt-1">{{ $activeCount ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Completed</p>
                    <p class="text-2xl font-bold text-success-600 dark:text-success-400 mt-1">{{ $completedCount ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Total Earnings</p>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white mt-1">${{ number_format($totalEarnings ?? 0, 2) }}</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-2 mb-6">
                <a href="{{ route('seller.contracts.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request('status') ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                    All
                </a>
                <a href="{{ route('seller.contracts.index', ['status' => 'active']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'active' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                    Active
                </a>
                <a href="{{ route('seller.contracts.index', ['status' => 'completed']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'completed' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                    Completed
                </a>
                <a href="{{ route('seller.contracts.index', ['status' => 'cancelled']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'cancelled' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700' }}">
                    Cancelled
                </a>
            </div>

            <!-- Contracts List -->
            <div class="space-y-4">
                @forelse($contracts as $contract)
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-surface-900 dark:text-white">
                                        <a href="{{ route('seller.contracts.show', $contract) }}" class="hover:text-primary-600 dark:hover:text-primary-400">
                                            {{ $contract->jobPosting->title ?? 'Contract #' . $contract->contract_number }}
                                        </a>
                                    </h3>
                                    <x-status-badge :status="$contract->status" size="sm" />
                                </div>
                                <p class="text-sm text-surface-500 dark:text-surface-400 mb-4">
                                    Contract #{{ $contract->contract_number }} &bull; Started {{ $contract->created_at->format('M d, Y') }} &bull; Client: {{ $contract->client->name ?? 'Unknown' }}
                                </p>

                                <!-- Milestone Progress -->
                                @if($contract->milestones && $contract->milestones->count() > 0)
                                    @php
                                        $totalMilestones = $contract->milestones->count();
                                        $completedMilestones = $contract->milestones->where('status', 'approved')->count();
                                        $progress = $totalMilestones > 0 ? ($completedMilestones / $totalMilestones) * 100 : 0;
                                    @endphp
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between text-sm mb-1">
                                            <span class="text-surface-600 dark:text-surface-400">Milestone Progress</span>
                                            <span class="text-surface-900 dark:text-white font-medium">{{ $completedMilestones }}/{{ $totalMilestones }}</span>
                                        </div>
                                        <div class="w-full h-2 bg-surface-200 dark:bg-surface-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-primary-600 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex flex-wrap items-center gap-4 text-sm">
                                    <span class="text-surface-600 dark:text-surface-400">
                                        Total Value: <span class="font-semibold text-surface-900 dark:text-white">${{ number_format($contract->total_amount, 2) }}</span>
                                    </span>
                                    <span class="text-surface-600 dark:text-surface-400">
                                        Type: <span class="font-semibold text-surface-900 dark:text-white">{{ ucfirst($contract->payment_type) }}</span>
                                    </span>
                                    @if($contract->milestones)
                                        <span class="text-surface-600 dark:text-surface-400">
                                            Milestones: <span class="font-semibold text-surface-900 dark:text-white">{{ $contract->milestones->count() }}</span>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex lg:flex-col items-center lg:items-end gap-3">
                                <a href="{{ route('seller.contracts.show', $contract) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                    View Details
                                </a>
                                @if($contract->conversation)
                                    <a href="{{ route('conversations.show', $contract->conversation) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        Message
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
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No contracts yet</h3>
                        <p class="text-surface-600 dark:text-surface-400 mb-6">When your proposals are accepted, contracts will appear here.</p>
                        <a href="{{ route('seller.jobs.available') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Find Jobs
                        </a>
                    </div>
                @endforelse
            </div>

            @if($contracts->hasPages())
                <div class="mt-6">
                    {{ $contracts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
