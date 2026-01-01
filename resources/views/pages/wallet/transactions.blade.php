<x-layouts.app title="Transaction History - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <a href="{{ route('wallet.index') }}" class="inline-flex items-center text-sm text-surface-500 dark:text-surface-400 hover:text-surface-700 dark:hover:text-surface-300 mb-2">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Wallet
                    </a>
                    <h1 class="text-3xl font-bold text-surface-900 dark:text-white">Transaction History</h1>
                    <p class="mt-1 text-surface-600 dark:text-surface-400">View and filter all your wallet transactions</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Current Balance</p>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $wallet->formatted_balance }}</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4 mb-6">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label for="type" class="block text-xs font-medium text-surface-500 dark:text-surface-400 mb-1">Type</label>
                        <select name="type" id="type" class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white text-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">All Types</option>
                            @foreach($typeOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-medium text-surface-500 dark:text-surface-400 mb-1">Status</label>
                        <select name="status" id="status" class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white text-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">All Statuses</option>
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="from" class="block text-xs font-medium text-surface-500 dark:text-surface-400 mb-1">From Date</label>
                        <input type="date" name="from" id="from" value="{{ request('from') }}" class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white text-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div>
                        <label for="to" class="block text-xs font-medium text-surface-500 dark:text-surface-400 mb-1">To Date</label>
                        <input type="date" name="to" id="to" value="{{ request('to') }}" class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white text-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium py-2.5 px-4 rounded-lg transition">
                            Filter
                        </button>
                        <a href="{{ route('wallet.transactions') }}" class="px-4 py-2.5 text-sm text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white border border-surface-300 dark:border-surface-600 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Transactions List -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                @if($transactions->count() > 0)
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-surface-50 dark:bg-surface-700/50 border-b border-surface-200 dark:border-surface-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">Transaction</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">Balance</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-100 dark:divide-surface-700">
                                @foreach($transactions as $transaction)
                                    <tr class="hover:bg-surface-50 dark:hover:bg-surface-700/50">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                @if($transaction->is_positive)
                                                    <div class="w-8 h-8 rounded-full bg-success-100 dark:bg-success-900/30 flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-4 h-4 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-danger-100 dark:bg-danger-900/30 flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-4 h-4 text-danger-600 dark:text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-surface-900 dark:text-white">{{ $transaction->type_label }}</p>
                                                    <p class="text-xs text-surface-500 dark:text-surface-400 truncate max-w-[200px]">{{ $transaction->description ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs font-mono text-surface-500 dark:text-surface-400">{{ $transaction->reference }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                                @if($transaction->status === 'completed') bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400
                                                @elseif($transaction->status === 'pending') bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400
                                                @elseif($transaction->status === 'failed') bg-danger-100 dark:bg-danger-900/30 text-danger-700 dark:text-danger-400
                                                @else bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 @endif">
                                                {{ $transaction->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-sm font-semibold {{ $transaction->is_positive ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                                {{ $transaction->formatted_amount }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-sm text-surface-600 dark:text-surface-400">{{ $transaction->formatted_balance_after }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <p class="text-sm text-surface-900 dark:text-white">{{ $transaction->created_at->format('M d, Y') }}</p>
                                            <p class="text-xs text-surface-500 dark:text-surface-400">{{ $transaction->created_at->format('h:i A') }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile List -->
                    <div class="md:hidden divide-y divide-surface-100 dark:divide-surface-700">
                        @foreach($transactions as $transaction)
                            <div class="p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3">
                                        @if($transaction->is_positive)
                                            <div class="w-10 h-10 rounded-full bg-success-100 dark:bg-success-900/30 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-danger-100 dark:bg-danger-900/30 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-danger-600 dark:text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium text-surface-900 dark:text-white">{{ $transaction->type_label }}</p>
                                            <p class="text-xs text-surface-500 dark:text-surface-400">{{ $transaction->created_at->format('M d, Y h:i A') }}</p>
                                            <span class="inline-flex mt-1 px-2 py-0.5 rounded-full text-xs font-medium
                                                @if($transaction->status === 'completed') bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400
                                                @elseif($transaction->status === 'pending') bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400
                                                @elseif($transaction->status === 'failed') bg-danger-100 dark:bg-danger-900/30 text-danger-700 dark:text-danger-400
                                                @else bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 @endif">
                                                {{ $transaction->status_label }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold {{ $transaction->is_positive ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                            {{ $transaction->formatted_amount }}
                                        </p>
                                        <p class="text-xs text-surface-500 dark:text-surface-400">Bal: {{ $transaction->formatted_balance_after }}</p>
                                    </div>
                                </div>
                                @if($transaction->description)
                                    <p class="mt-2 text-xs text-surface-500 dark:text-surface-400 ml-13">{{ $transaction->description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-6 py-16 text-center">
                        <svg class="mx-auto h-12 w-12 text-surface-300 dark:text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="mt-4 text-surface-500 dark:text-surface-400">No transactions found</p>
                        @if(request()->hasAny(['type', 'status', 'from', 'to']))
                            <a href="{{ route('wallet.transactions') }}" class="mt-2 inline-block text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium text-sm">
                                Clear filters
                            </a>
                        @else
                            <a href="{{ route('wallet.deposit') }}" class="mt-2 inline-block text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium text-sm">
                                Make your first deposit
                            </a>
                        @endif
                    </div>
                @endif

                @if($transactions->hasPages())
                    <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
