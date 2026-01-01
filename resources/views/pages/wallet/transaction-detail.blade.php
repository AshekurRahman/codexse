<x-layouts.app title="Transaction Details - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('wallet.transactions') }}" class="inline-flex items-center text-sm text-surface-500 dark:text-surface-400 hover:text-surface-700 dark:hover:text-surface-300 mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Transactions
                </a>
                <h1 class="text-3xl font-bold text-surface-900 dark:text-white">Transaction Details</h1>
            </div>

            <!-- Transaction Card -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                <!-- Header with status -->
                <div class="px-6 py-5 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        @if($transaction->is_positive)
                            <div class="w-12 h-12 rounded-full bg-success-100 dark:bg-success-900/30 flex items-center justify-center">
                                <svg class="w-6 h-6 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                        @else
                            <div class="w-12 h-12 rounded-full bg-danger-100 dark:bg-danger-900/30 flex items-center justify-center">
                                <svg class="w-6 h-6 text-danger-600 dark:text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white">{{ $transaction->type_label }}</h2>
                            <p class="text-sm text-surface-500 dark:text-surface-400">{{ $transaction->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <span class="inline-flex px-3 py-1.5 rounded-full text-sm font-medium
                        @if($transaction->status === 'completed') bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400
                        @elseif($transaction->status === 'pending') bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400
                        @elseif($transaction->status === 'failed') bg-danger-100 dark:bg-danger-900/30 text-danger-700 dark:text-danger-400
                        @else bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 @endif">
                        {{ $transaction->status_label }}
                    </span>
                </div>

                <!-- Amount -->
                <div class="px-6 py-8 text-center border-b border-surface-200 dark:border-surface-700">
                    <p class="text-sm text-surface-500 dark:text-surface-400 mb-1">Amount</p>
                    <p class="text-4xl font-bold {{ $transaction->is_positive ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                        {{ $transaction->formatted_amount }}
                    </p>
                </div>

                <!-- Details -->
                <div class="px-6 py-5 space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-surface-100 dark:border-surface-700">
                        <span class="text-sm text-surface-500 dark:text-surface-400">Reference</span>
                        <span class="text-sm font-mono font-medium text-surface-900 dark:text-white">{{ $transaction->reference }}</span>
                    </div>

                    <div class="flex justify-between items-center py-2 border-b border-surface-100 dark:border-surface-700">
                        <span class="text-sm text-surface-500 dark:text-surface-400">Type</span>
                        <span class="text-sm font-medium text-surface-900 dark:text-white">{{ $transaction->type_label }}</span>
                    </div>

                    <div class="flex justify-between items-center py-2 border-b border-surface-100 dark:border-surface-700">
                        <span class="text-sm text-surface-500 dark:text-surface-400">Balance Before</span>
                        <span class="text-sm font-medium text-surface-900 dark:text-white">{{ $transaction->formatted_balance_before }}</span>
                    </div>

                    <div class="flex justify-between items-center py-2 border-b border-surface-100 dark:border-surface-700">
                        <span class="text-sm text-surface-500 dark:text-surface-400">Balance After</span>
                        <span class="text-sm font-medium text-surface-900 dark:text-white">{{ $transaction->formatted_balance_after }}</span>
                    </div>

                    @if($transaction->payment_method)
                        <div class="flex justify-between items-center py-2 border-b border-surface-100 dark:border-surface-700">
                            <span class="text-sm text-surface-500 dark:text-surface-400">Payment Method</span>
                            <span class="text-sm font-medium text-surface-900 dark:text-white capitalize">{{ $transaction->payment_method }}</span>
                        </div>
                    @endif

                    @if($transaction->payment_id)
                        <div class="flex justify-between items-center py-2 border-b border-surface-100 dark:border-surface-700">
                            <span class="text-sm text-surface-500 dark:text-surface-400">Payment ID</span>
                            <span class="text-sm font-mono font-medium text-surface-900 dark:text-white truncate max-w-[200px]">{{ $transaction->payment_id }}</span>
                        </div>
                    @endif

                    @if($transaction->description)
                        <div class="py-2 border-b border-surface-100 dark:border-surface-700">
                            <span class="text-sm text-surface-500 dark:text-surface-400 block mb-1">Description</span>
                            <p class="text-sm text-surface-900 dark:text-white">{{ $transaction->description }}</p>
                        </div>
                    @endif

                    @if($transaction->completed_at)
                        <div class="flex justify-between items-center py-2 border-b border-surface-100 dark:border-surface-700">
                            <span class="text-sm text-surface-500 dark:text-surface-400">Completed At</span>
                            <span class="text-sm font-medium text-surface-900 dark:text-white">{{ $transaction->completed_at->format('F d, Y h:i A') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-surface-500 dark:text-surface-400">Created At</span>
                        <span class="text-sm font-medium text-surface-900 dark:text-white">{{ $transaction->created_at->format('F d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('wallet.transactions') }}" class="inline-flex items-center justify-center px-6 py-3 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 font-medium rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Transactions
                </a>
                <a href="{{ route('wallet.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Go to Wallet
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
