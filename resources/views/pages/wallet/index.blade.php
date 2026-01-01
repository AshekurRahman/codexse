<x-layouts.app title="My Wallet - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-surface-900 dark:text-white">My Wallet</h1>
                <p class="mt-2 text-surface-600 dark:text-surface-400">Manage your wallet balance and view transaction history</p>
            </div>

            @if(session('success'))
                <div class="mb-6 rounded-lg bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-success-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm text-success-700 dark:text-success-300">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 rounded-lg bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-danger-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm text-danger-700 dark:text-danger-300">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Balance Card -->
                    <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-primary-200 text-sm font-medium">Available Balance</p>
                                <p class="text-4xl font-bold mt-1">{{ $wallet->formatted_balance }}</p>
                                @if($wallet->pending_balance > 0)
                                    <p class="text-primary-200 text-sm mt-2">
                                        + {{ $wallet->formatted_pending_balance }} pending
                                    </p>
                                @endif
                            </div>
                            <div class="bg-white/20 rounded-full p-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('wallet.deposit') }}" class="flex-1 bg-white text-primary-600 font-semibold py-3 px-4 rounded-xl text-center hover:bg-primary-50 transition">
                                Add Funds
                            </a>
                            <a href="{{ route('wallet.transactions') }}" class="flex-1 bg-white/20 text-white font-semibold py-3 px-4 rounded-xl text-center hover:bg-white/30 transition">
                                View History
                            </a>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="bg-white dark:bg-surface-800 rounded-xl p-4 border border-surface-200 dark:border-surface-700">
                            <p class="text-xs text-surface-500 dark:text-surface-400 uppercase tracking-wide">Total Deposits</p>
                            <p class="text-xl font-bold text-surface-900 dark:text-white mt-1">{{ format_price($stats['total_deposits']) }}</p>
                        </div>
                        <div class="bg-white dark:bg-surface-800 rounded-xl p-4 border border-surface-200 dark:border-surface-700">
                            <p class="text-xs text-surface-500 dark:text-surface-400 uppercase tracking-wide">Total Purchases</p>
                            <p class="text-xl font-bold text-surface-900 dark:text-white mt-1">{{ format_price($stats['total_purchases']) }}</p>
                        </div>
                        <div class="bg-white dark:bg-surface-800 rounded-xl p-4 border border-surface-200 dark:border-surface-700">
                            <p class="text-xs text-surface-500 dark:text-surface-400 uppercase tracking-wide">Withdrawals</p>
                            <p class="text-xl font-bold text-surface-900 dark:text-white mt-1">{{ format_price($stats['total_withdrawals']) }}</p>
                        </div>
                        <div class="bg-white dark:bg-surface-800 rounded-xl p-4 border border-surface-200 dark:border-surface-700">
                            <p class="text-xs text-surface-500 dark:text-surface-400 uppercase tracking-wide">This Month</p>
                            <p class="text-xl font-bold text-success-600 dark:text-success-400 mt-1">+{{ format_price($stats['this_month_deposits']) }}</p>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Recent Transactions</h2>
                            <a href="{{ route('wallet.transactions') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium">
                                View All
                            </a>
                        </div>
                        <div class="divide-y divide-surface-100 dark:divide-surface-700">
                            @forelse($transactions as $transaction)
                                <div class="px-6 py-4 flex items-center gap-4 hover:bg-surface-50 dark:hover:bg-surface-700/50">
                                    <div class="flex-shrink-0">
                                        @if($transaction->is_positive)
                                            <div class="w-10 h-10 rounded-full bg-success-100 dark:bg-success-900/30 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-danger-100 dark:bg-danger-900/30 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-danger-600 dark:text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-surface-900 dark:text-white">{{ $transaction->type_label }}</p>
                                        <p class="text-xs text-surface-500 dark:text-surface-400 truncate">{{ $transaction->description ?? $transaction->reference }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold {{ $transaction->is_positive ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                            {{ $transaction->formatted_amount }}
                                        </p>
                                        <p class="text-xs text-surface-500 dark:text-surface-400">{{ $transaction->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-surface-300 dark:text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="mt-4 text-surface-500 dark:text-surface-400">No transactions yet</p>
                                    <a href="{{ route('wallet.deposit') }}" class="mt-2 inline-block text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium text-sm">
                                        Make your first deposit
                                    </a>
                                </div>
                            @endforelse
                        </div>
                        @if($transactions->hasPages())
                            <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('wallet.deposit') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition group">
                                <div class="w-10 h-10 rounded-lg bg-success-100 dark:bg-success-900/30 flex items-center justify-center group-hover:bg-success-200 dark:group-hover:bg-success-900/50">
                                    <svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-surface-900 dark:text-white">Add Funds</p>
                                    <p class="text-xs text-surface-500 dark:text-surface-400">Deposit money to wallet</p>
                                </div>
                            </a>
                            <a href="{{ route('products.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition group">
                                <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center group-hover:bg-primary-200 dark:group-hover:bg-primary-900/50">
                                    <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-surface-900 dark:text-white">Browse Products</p>
                                    <p class="text-xs text-surface-500 dark:text-surface-400">Use balance to purchase</p>
                                </div>
                            </a>
                            <a href="{{ route('wallet.transactions') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition group">
                                <div class="w-10 h-10 rounded-lg bg-info-100 dark:bg-info-900/30 flex items-center justify-center group-hover:bg-info-200 dark:group-hover:bg-info-900/50">
                                    <svg class="w-5 h-5 text-info-600 dark:text-info-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-surface-900 dark:text-white">Transaction History</p>
                                    <p class="text-xs text-surface-500 dark:text-surface-400">View all transactions</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Wallet Info -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Wallet Info</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-surface-500 dark:text-surface-400">Status</span>
                                @if($wallet->is_active && !$wallet->is_frozen)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span>
                                        Active
                                    </span>
                                @elseif($wallet->is_frozen)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-info-100 dark:bg-info-900/30 text-info-700 dark:text-info-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-info-500"></span>
                                        Frozen
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-surface-500"></span>
                                        Inactive
                                    </span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-surface-500 dark:text-surface-400">Currency</span>
                                <span class="text-sm font-medium text-surface-900 dark:text-white">{{ $wallet->currency }}</span>
                            </div>
                            @if($wallet->last_transaction_at)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-surface-500 dark:text-surface-400">Last Activity</span>
                                    <span class="text-sm font-medium text-surface-900 dark:text-white">{{ $wallet->last_transaction_at->diffForHumans() }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-surface-500 dark:text-surface-400">Created</span>
                                <span class="text-sm font-medium text-surface-900 dark:text-white">{{ $wallet->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-900/10 rounded-xl border border-primary-100 dark:border-primary-800 p-6">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-surface-900 dark:text-white">Need Help?</h4>
                                <p class="mt-1 text-xs text-surface-600 dark:text-surface-400">
                                    Use your wallet balance to make purchases instantly. Funds are secure and transactions are tracked.
                                </p>
                                <a href="{{ route('support.index') }}" class="mt-2 inline-block text-xs text-primary-600 dark:text-primary-400 hover:text-primary-700 font-medium">
                                    Contact Support
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
