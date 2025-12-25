<x-layouts.app title="Payouts">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Payouts</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">Manage your earnings and payouts</p>
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

            <!-- Balance Card -->
            <div class="bg-gradient-to-br from-primary-600 to-accent-600 rounded-xl p-6 mb-8 text-white">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                    <div>
                        <p class="text-primary-100 text-sm font-medium mb-1">Available Balance</p>
                        <p class="text-4xl font-bold">${{ number_format($seller->available_balance, 2) }}</p>
                        <p class="text-primary-100 text-sm mt-2">Minimum payout: $50.00</p>
                    </div>
                    <form action="{{ route('seller.payouts.request') }}" method="POST">
                        @csrf
                        <button type="submit" {{ $seller->available_balance < 50 ? 'disabled' : '' }} class="inline-flex items-center gap-2 px-6 py-3 bg-white text-primary-600 font-semibold rounded-lg hover:bg-primary-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Request Payout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Payouts Table -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="p-6 border-b border-surface-200 dark:border-surface-700">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Payout History</h2>
                </div>

                @if($payouts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-surface-50 dark:bg-surface-900/50">
                                <tr>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Date</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Amount</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Status</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Processed</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-200 dark:divide-surface-700">
                                @foreach($payouts as $payout)
                                    <tr class="hover:bg-surface-50 dark:hover:bg-surface-700/50">
                                        <td class="px-6 py-4 text-sm text-surface-900 dark:text-white">
                                            {{ $payout->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-surface-900 dark:text-white">
                                            ${{ number_format($payout->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($payout->status === 'completed')
                                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400 rounded-md">
                                                    <span class="w-1.5 h-1.5 bg-success-500 rounded-full"></span>
                                                    Completed
                                                </span>
                                            @elseif($payout->status === 'pending')
                                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400 rounded-md">
                                                    <span class="w-1.5 h-1.5 bg-warning-500 rounded-full"></span>
                                                    Pending
                                                </span>
                                            @elseif($payout->status === 'processing')
                                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400 rounded-md">
                                                    <span class="w-1.5 h-1.5 bg-primary-500 rounded-full"></span>
                                                    Processing
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-400 rounded-md">
                                                    <span class="w-1.5 h-1.5 bg-danger-500 rounded-full"></span>
                                                    {{ ucfirst($payout->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-surface-600 dark:text-surface-400">
                                            {{ $payout->processed_at ? $payout->processed_at->format('M d, Y') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($payouts->hasPages())
                        <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700">
                            {{ $payouts->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No payouts yet</h3>
                        <p class="text-surface-600 dark:text-surface-400">When you request a payout, it will appear here</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
