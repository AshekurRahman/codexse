<x-layouts.app title="Escrow Transaction #{{ $transaction->id }}">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="javascript:history.back()" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Go Back
                </a>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Escrow Transaction</h1>
                        <p class="mt-1 text-surface-600 dark:text-surface-400">Transaction #{{ $transaction->id }}</p>
                    </div>
                    <div>
                        <span class="px-4 py-2 rounded-full text-sm font-medium
                            @if($transaction->status === 'held') bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-300
                            @elseif($transaction->status === 'released') bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-300
                            @elseif($transaction->status === 'refunded') bg-info-100 text-info-800 dark:bg-info-900/30 dark:text-info-300
                            @elseif($transaction->status === 'disputed') bg-danger-100 text-danger-800 dark:bg-danger-900/30 dark:text-danger-300
                            @else bg-surface-100 text-surface-800 dark:bg-surface-700 dark:text-surface-300
                            @endif">
                            {{ ucfirst($transaction->status) }}
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Transaction Details -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                            <h2 class="font-semibold text-surface-900 dark:text-white">Transaction Details</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Description</span>
                                <span class="text-surface-900 dark:text-white font-medium">{{ $transaction->description }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Amount</span>
                                <span class="text-surface-900 dark:text-white font-medium">{{ format_price($transaction->amount) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Platform Fee</span>
                                <span class="text-surface-900 dark:text-white font-medium">{{ format_price($transaction->platform_fee) }}</span>
                            </div>
                            <div class="flex justify-between pt-4 border-t border-surface-200 dark:border-surface-700">
                                <span class="text-surface-900 dark:text-white font-semibold">Seller Receives</span>
                                <span class="text-success-600 dark:text-success-400 font-bold">{{ format_price($transaction->seller_amount) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Related Order/Milestone -->
                    @if($transaction->escrowable)
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                            <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                                <h2 class="font-semibold text-surface-900 dark:text-white">Related {{ $transaction->escrowable_type === 'App\Models\ServiceOrder' ? 'Service Order' : 'Milestone' }}</h2>
                            </div>
                            <div class="p-6">
                                @if($transaction->escrowable_type === 'App\Models\ServiceOrder')
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-surface-900 dark:text-white">{{ $transaction->escrowable->title ?? 'Service Order' }}</p>
                                            <p class="text-sm text-surface-500 dark:text-surface-400">Order #{{ $transaction->escrowable->order_number ?? $transaction->escrowable->id }}</p>
                                        </div>
                                        <a href="{{ route('service-orders.show', $transaction->escrowable) }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium text-sm">
                                            View Order &rarr;
                                        </a>
                                    </div>
                                @elseif($transaction->escrowable_type === 'App\Models\JobMilestone')
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-surface-900 dark:text-white">{{ $transaction->escrowable->title ?? 'Milestone' }}</p>
                                            <p class="text-sm text-surface-500 dark:text-surface-400">Contract #{{ $transaction->escrowable->contract->contract_number ?? '' }}</p>
                                        </div>
                                        <a href="{{ route('contracts.show', $transaction->escrowable->contract ?? 0) }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium text-sm">
                                            View Contract &rarr;
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Timeline -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                            <h2 class="font-semibold text-surface-900 dark:text-white">Timeline</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-3 h-3 rounded-full bg-success-500"></div>
                                        <div class="flex-1 w-0.5 bg-surface-200 dark:bg-surface-700"></div>
                                    </div>
                                    <div class="pb-4">
                                        <p class="font-medium text-surface-900 dark:text-white">Transaction Created</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ $transaction->created_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                                @if($transaction->held_at)
                                    <div class="flex gap-4">
                                        <div class="flex flex-col items-center">
                                            <div class="w-3 h-3 rounded-full bg-warning-500"></div>
                                            <div class="flex-1 w-0.5 bg-surface-200 dark:bg-surface-700"></div>
                                        </div>
                                        <div class="pb-4">
                                            <p class="font-medium text-surface-900 dark:text-white">Funds Held in Escrow</p>
                                            <p class="text-sm text-surface-500 dark:text-surface-400">{{ $transaction->held_at->format('M d, Y \a\t g:i A') }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if($transaction->released_at)
                                    <div class="flex gap-4">
                                        <div class="flex flex-col items-center">
                                            <div class="w-3 h-3 rounded-full bg-success-500"></div>
                                        </div>
                                        <div>
                                            <p class="font-medium text-surface-900 dark:text-white">Funds Released to Seller</p>
                                            <p class="text-sm text-surface-500 dark:text-surface-400">{{ $transaction->released_at->format('M d, Y \a\t g:i A') }}</p>
                                            @if($transaction->release_reason)
                                                <p class="text-sm text-surface-600 dark:text-surface-400 mt-1">{{ $transaction->release_reason }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                @if($transaction->refunded_at)
                                    <div class="flex gap-4">
                                        <div class="flex flex-col items-center">
                                            <div class="w-3 h-3 rounded-full bg-info-500"></div>
                                        </div>
                                        <div>
                                            <p class="font-medium text-surface-900 dark:text-white">Funds Refunded to Buyer</p>
                                            <p class="text-sm text-surface-500 dark:text-surface-400">{{ $transaction->refunded_at->format('M d, Y \a\t g:i A') }}</p>
                                            @if($transaction->refund_reason)
                                                <p class="text-sm text-surface-600 dark:text-surface-400 mt-1">{{ $transaction->refund_reason }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Payer -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Buyer</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                <span class="text-lg font-medium text-primary-700 dark:text-primary-300">
                                    {{ strtoupper(substr($transaction->payer->name ?? 'U', 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $transaction->payer->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-surface-500 dark:text-surface-400">{{ $transaction->payer->email ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payee -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Seller</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-success-100 dark:bg-success-900/30 flex items-center justify-center">
                                <span class="text-lg font-medium text-success-700 dark:text-success-300">
                                    {{ strtoupper(substr($transaction->payee->store_name ?? 'S', 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $transaction->payee->store_name ?? 'Unknown' }}</p>
                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                    {{ $transaction->payee->user->email ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if($transaction->status === 'held' && $transaction->payer_id === auth()->id())
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Actions</h3>
                            <div class="space-y-3">
                                <form action="{{ route('escrow.release', $transaction) }}" method="POST" onsubmit="return confirm('Are you sure you want to release the funds to the seller?')">
                                    @csrf
                                    <button type="submit" class="w-full bg-success-600 hover:bg-success-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                                        Release Funds
                                    </button>
                                </form>
                                <a href="{{ route('escrow.refund', $transaction) }}" class="block w-full text-center border border-danger-300 dark:border-danger-700 text-danger-600 dark:text-danger-400 font-medium py-3 px-4 rounded-lg hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors">
                                    Request Refund
                                </a>
                            </div>
                            <p class="mt-4 text-xs text-surface-500 dark:text-surface-400 text-center">
                                Only release funds when you're satisfied with the work delivered.
                            </p>
                        </div>
                    @endif

                    <!-- Help -->
                    <div class="bg-surface-100 dark:bg-surface-800/50 rounded-xl p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-2">Need Help?</h3>
                        <p class="text-sm text-surface-600 dark:text-surface-400 mb-4">
                            If you have any questions about this transaction or need assistance, our support team is here to help.
                        </p>
                        <a href="{{ route('support.create') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium">
                            Contact Support &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
