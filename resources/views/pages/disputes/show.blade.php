<x-layouts.app title="Dispute #{{ $dispute->id }}">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('disputes.index') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Disputes
                </a>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Dispute #{{ $dispute->id }}</h1>
                        <p class="text-surface-600 dark:text-surface-400 mt-1">Opened {{ $dispute->created_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                    <x-status-badge :status="$dispute->status" size="lg" />
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
                    <!-- Dispute Details -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Dispute Details</h2>

                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Reason</p>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $dispute->reason_label }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-surface-500 dark:text-surface-400 mb-2">Description</p>
                                <div class="p-4 bg-surface-50 dark:bg-surface-900/50 rounded-lg">
                                    <p class="text-surface-700 dark:text-surface-300 whitespace-pre-wrap">{{ $dispute->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Evidence -->
                    @if($dispute->evidence && count($dispute->evidence) > 0)
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Evidence Submitted</h2>

                            <div class="space-y-3">
                                @foreach($dispute->evidence as $file)
                                    <div class="flex items-center justify-between p-3 bg-surface-50 dark:bg-surface-900/50 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-surface-200 dark:bg-surface-700 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-surface-900 dark:text-white">{{ $file['name'] ?? 'File' }}</p>
                                                <p class="text-xs text-surface-500 dark:text-surface-400">
                                                    {{ isset($file['size']) ? number_format($file['size'] / 1024 / 1024, 2) . ' MB' : '' }}
                                                    @if(isset($file['uploaded_at']))
                                                        &bull; {{ \Carbon\Carbon::parse($file['uploaded_at'])->diffForHumans() }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        @if(isset($file['path']))
                                            <a href="{{ Storage::url($file['path']) }}" target="_blank" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Add Evidence (if dispute is still open) -->
                    @if($dispute->canAddEvidence() && $dispute->initiated_by === auth()->id())
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Add More Evidence</h2>

                            <form action="{{ route('disputes.add-evidence', $dispute) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf

                                <div>
                                    <label for="note" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Note (Optional)</label>
                                    <input type="text" id="note" name="note" class="w-full px-4 py-2.5 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 text-surface-900 dark:text-white" placeholder="Add a note about this evidence...">
                                </div>

                                <div class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-lg p-4 text-center">
                                    <input type="file" name="evidence[]" multiple id="new-evidence" class="hidden" required>
                                    <label for="new-evidence" class="cursor-pointer">
                                        <svg class="w-8 h-8 text-surface-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <p class="text-sm text-surface-600 dark:text-surface-400">Click to upload files</p>
                                    </label>
                                </div>

                                <button type="submit" class="w-full px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                    Add Evidence
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Resolution (if resolved) -->
                    @if($dispute->isResolved())
                        <div class="bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-xl p-6">
                            <h2 class="text-lg font-semibold text-success-900 dark:text-success-100 mb-4">Resolution</h2>

                            <div class="space-y-4">
                                @if($dispute->resolution_type)
                                    <div>
                                        <p class="text-sm text-success-600 dark:text-success-400">Resolution Type</p>
                                        <p class="font-medium text-success-900 dark:text-success-100">
                                            {{ \App\Models\Dispute::RESOLUTION_TYPES[$dispute->resolution_type] ?? ucfirst(str_replace('_', ' ', $dispute->resolution_type)) }}
                                        </p>
                                    </div>
                                @endif

                                @if($dispute->resolution_notes)
                                    <div>
                                        <p class="text-sm text-success-600 dark:text-success-400 mb-2">Resolution Notes</p>
                                        <div class="p-4 bg-success-100 dark:bg-success-900/30 rounded-lg">
                                            <p class="text-success-800 dark:text-success-200 whitespace-pre-wrap">{{ $dispute->resolution_notes }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($dispute->resolver)
                                    <div>
                                        <p class="text-sm text-success-600 dark:text-success-400">Resolved by</p>
                                        <p class="font-medium text-success-900 dark:text-success-100">{{ $dispute->resolver->name }}</p>
                                        <p class="text-xs text-success-600 dark:text-success-400">{{ $dispute->resolved_at?->format('M d, Y \a\t h:i A') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Related Item -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-medium text-surface-900 dark:text-white mb-4">Related To</h3>

                        @if($dispute->disputable)
                            @if($dispute->disputable_type === 'App\\Models\\JobContract')
                                <div class="space-y-3">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Contract</p>
                                    <p class="font-medium text-surface-900 dark:text-white">{{ $dispute->disputable->title ?? 'Contract' }}</p>
                                    <a href="{{ route('contracts.show', $dispute->disputable) }}" class="inline-flex items-center gap-2 text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400">
                                        View Contract
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            @elseif($dispute->disputable_type === 'App\\Models\\ServiceOrder')
                                <div class="space-y-3">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Service Order</p>
                                    <p class="font-medium text-surface-900 dark:text-white">#{{ $dispute->disputable->order_number ?? $dispute->disputable->id }}</p>
                                    <a href="{{ route('service-orders.show', $dispute->disputable) }}" class="inline-flex items-center gap-2 text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400">
                                        View Order
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Escrow Info -->
                    @if($dispute->escrowTransaction)
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h3 class="font-medium text-surface-900 dark:text-white mb-4">Escrow Details</h3>

                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Amount</p>
                                    <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ format_price($dispute->escrowTransaction->amount) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">Status</p>
                                    <x-status-badge :status="$dispute->escrowTransaction->status" size="sm" />
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    @if($dispute->status === 'open' && $dispute->initiated_by === auth()->id())
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h3 class="font-medium text-surface-900 dark:text-white mb-4">Actions</h3>

                            <form action="{{ route('disputes.cancel', $dispute) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this dispute? This action cannot be undone.')">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 border border-danger-300 dark:border-danger-700 text-danger-600 dark:text-danger-400 rounded-lg hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors">
                                    Cancel Dispute
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Status Timeline -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-medium text-surface-900 dark:text-white mb-4">Timeline</h3>

                        <div class="space-y-4">
                            <div class="flex gap-3">
                                <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full bg-success-500"></div>
                                <div>
                                    <p class="text-sm font-medium text-surface-900 dark:text-white">Dispute Opened</p>
                                    <p class="text-xs text-surface-500 dark:text-surface-400">{{ $dispute->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>

                            @if($dispute->status === 'under_review')
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full bg-info-500"></div>
                                    <div>
                                        <p class="text-sm font-medium text-surface-900 dark:text-white">Under Review</p>
                                        <p class="text-xs text-surface-500 dark:text-surface-400">Our team is reviewing the case</p>
                                    </div>
                                </div>
                            @endif

                            @if($dispute->isResolved())
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full bg-success-500"></div>
                                    <div>
                                        <p class="text-sm font-medium text-surface-900 dark:text-white">Resolved</p>
                                        <p class="text-xs text-surface-500 dark:text-surface-400">{{ $dispute->resolved_at?->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($dispute->status === 'cancelled')
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full bg-surface-400"></div>
                                    <div>
                                        <p class="text-sm font-medium text-surface-900 dark:text-white">Cancelled</p>
                                        <p class="text-xs text-surface-500 dark:text-surface-400">Dispute was cancelled</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
