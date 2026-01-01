<x-layouts.app>
    <x-slot name="title">Subscription Invoices</x-slot>

    <div class="min-h-screen bg-surface-50 dark:bg-surface-900 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <a href="{{ route('subscriptions.subscription', $subscription) }}" class="inline-flex items-center gap-2 text-surface-600 dark:text-surface-400 hover:text-primary-600 mb-8">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Subscription
            </a>

            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-surface-200 dark:border-surface-700">
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Invoices</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">
                        {{ $subscription->plan->name }} subscription invoices
                    </p>
                </div>

                <!-- Invoices List -->
                @if($invoices->isEmpty())
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 bg-surface-100 dark:bg-surface-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No Invoices Yet</h3>
                        <p class="text-surface-600 dark:text-surface-400">Invoices will appear here after your first billing cycle.</p>
                    </div>
                @else
                    <div class="divide-y divide-surface-200 dark:divide-surface-700">
                        @foreach($invoices as $invoice)
                            <div class="p-4 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-lg {{ $invoice->isPaid() ? 'bg-success-100 dark:bg-success-900/30' : 'bg-surface-100 dark:bg-surface-700' }} flex items-center justify-center">
                                            @if($invoice->isPaid())
                                                <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-surface-900 dark:text-white">
                                                {{ $invoice->invoice_number }}
                                            </p>
                                            <p class="text-sm text-surface-500 dark:text-surface-400">
                                                {{ $invoice->created_at->format('F j, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-6">
                                        <div class="text-right">
                                            <p class="font-semibold text-surface-900 dark:text-white">{{ $invoice->formatted_total }}</p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                @if($invoice->status === 'paid') bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400
                                                @elseif($invoice->status === 'open') bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-400
                                                @elseif($invoice->status === 'void') bg-surface-100 text-surface-800 dark:bg-surface-700 dark:text-surface-400
                                                @else bg-danger-100 text-danger-800 dark:bg-danger-900/30 dark:text-danger-400
                                                @endif
                                            ">
                                                {{ $invoice->status_label }}
                                            </span>
                                        </div>
                                        @if($invoice->pdf_url)
                                            <a href="{{ $invoice->pdf_url }}" target="_blank" class="p-2 text-surface-400 hover:text-primary-600 transition-colors" title="Download PDF">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <!-- Invoice Details -->
                                @if($invoice->line_items)
                                    <div class="mt-4 ml-14 p-3 bg-surface-50 dark:bg-surface-700/30 rounded-lg">
                                        <div class="space-y-1">
                                            @foreach($invoice->line_items as $item)
                                                <div class="flex items-center justify-between text-sm">
                                                    <span class="text-surface-600 dark:text-surface-400">{{ $item['description'] ?? 'Subscription' }}</span>
                                                    <span class="text-surface-900 dark:text-white">{{ format_price(($item['amount'] ?? 0) / 100) }}</span>
                                                </div>
                                            @endforeach
                                            @if($invoice->tax > 0)
                                                <div class="flex items-center justify-between text-sm pt-1 border-t border-surface-200 dark:border-surface-600">
                                                    <span class="text-surface-600 dark:text-surface-400">Tax</span>
                                                    <span class="text-surface-900 dark:text-white">{{ format_price($invoice->tax) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($invoices->hasPages())
                        <div class="p-4 border-t border-surface-200 dark:border-surface-700">
                            {{ $invoices->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
