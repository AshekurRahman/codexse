<x-layouts.app title="Order #{{ $serviceOrder->order_number }}">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('service-orders.index') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to My Orders
                </a>
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Order #{{ $serviceOrder->order_number }}</h1>
                            <x-status-badge :status="$serviceOrder->status" />
                        </div>
                        <p class="text-surface-600 dark:text-surface-400 mt-1">Placed on {{ $serviceOrder->created_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($serviceOrder->conversation)
                            <a href="{{ route('conversations.show', $serviceOrder->conversation) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Message Seller
                            </a>
                        @endif
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Service Info -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Service Details</h2>
                        <div class="flex items-start gap-4">
                            @if($serviceOrder->service?->thumbnail)
                                <img src="{{ Storage::url($serviceOrder->service->thumbnail) }}" alt="" class="w-20 h-16 object-cover rounded-lg">
                            @endif
                            <div class="flex-1">
                                <h3 class="font-medium text-surface-900 dark:text-white">{{ $serviceOrder->service->name ?? 'Deleted Service' }}</h3>
                                <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Package: {{ $serviceOrder->package->name ?? 'N/A' }}</p>
                                <div class="flex items-center gap-4 mt-2 text-sm text-surface-600 dark:text-surface-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $serviceOrder->delivery_days ?? 0 }} days delivery
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        {{ $serviceOrder->revisions_allowed ?? 0 }} revisions ({{ $serviceOrder->revisions_used ?? 0 }} used)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Requirements (if pending) -->
                    @if($serviceOrder->status === 'pending_requirements')
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Submit Requirements</h2>
                            <p class="text-surface-600 dark:text-surface-400 mb-4">Please provide the following information so the seller can start working on your order.</p>
                            <form action="{{ route('service-orders.submit-requirements', $serviceOrder) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="space-y-4">
                                    @foreach($serviceOrder->service->requirements as $requirement)
                                        <div>
                                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                                {{ $requirement->question }}
                                                @if($requirement->is_required)
                                                    <span class="text-danger-500">*</span>
                                                @endif
                                            </label>
                                            @if($requirement->type === 'textarea')
                                                <textarea name="requirements[{{ $requirement->id }}]" rows="4" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" {{ $requirement->is_required ? 'required' : '' }}>{{ old("requirements.{$requirement->id}") }}</textarea>
                                            @elseif($requirement->type === 'file')
                                                <input type="file" name="requirements[{{ $requirement->id }}]" class="w-full text-surface-700 dark:text-surface-300" {{ $requirement->is_required ? 'required' : '' }}>
                                            @else
                                                <input type="text" name="requirements[{{ $requirement->id }}]" value="{{ old("requirements.{$requirement->id}") }}" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" {{ $requirement->is_required ? 'required' : '' }}>
                                            @endif
                                            @if($requirement->description)
                                                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">{{ $requirement->description }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-6">
                                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                                        Submit Requirements
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Your Requirements -->
                    @if($serviceOrder->requirements_data && count($serviceOrder->requirements_data) > 0 && $serviceOrder->status !== 'pending_requirements')
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Your Requirements</h2>
                            <div class="space-y-4">
                                @foreach($serviceOrder->requirements_data as $requirement)
                                    <div class="p-4 bg-surface-50 dark:bg-surface-900/50 rounded-lg">
                                        <p class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">{{ $requirement['question'] ?? 'Question' }}</p>
                                        <p class="text-surface-900 dark:text-white">{{ $requirement['answer'] ?? 'No answer provided' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Deliveries -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Deliveries</h2>
                        @if($serviceOrder->deliveries && $serviceOrder->deliveries->count() > 0)
                            <div class="space-y-4">
                                @foreach($serviceOrder->deliveries as $delivery)
                                    <div class="p-4 border border-surface-200 dark:border-surface-700 rounded-lg">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-sm text-surface-500 dark:text-surface-400">{{ $delivery->created_at->format('M d, Y \a\t g:i A') }}</span>
                                            <x-status-badge :status="$delivery->status" size="sm" />
                                        </div>
                                        <p class="text-surface-900 dark:text-white">{{ $delivery->notes }}</p>
                                        @if($delivery->files && count($delivery->files) > 0)
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @foreach($delivery->files as $file)
                                                    <a href="{{ Storage::url($file['path']) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 rounded-lg text-sm hover:bg-surface-200 dark:hover:bg-surface-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                        </svg>
                                                        {{ $file['name'] ?? 'Download File' }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Action Buttons for Latest Delivery -->
                                        @if($loop->first && $delivery->status === 'pending' && $serviceOrder->status === 'delivered')
                                            <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700 flex flex-col sm:flex-row gap-3">
                                                <form action="{{ route('service-orders.approve', $serviceOrder) }}" method="POST" class="flex-1">
                                                    @csrf
                                                    <button type="submit" class="w-full bg-success-600 hover:bg-success-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                                        Accept Delivery
                                                    </button>
                                                </form>
                                                @if($serviceOrder->revisions_used < $serviceOrder->revisions_allowed)
                                                    <button type="button" onclick="document.getElementById('revision-modal').classList.remove('hidden')" class="flex-1 bg-warning-600 hover:bg-warning-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                                        Request Revision
                                                    </button>
                                                @endif
                                            </div>
                                        @endif

                                        @if($delivery->status === 'revision_requested' && $delivery->revision_notes)
                                            <div class="mt-3 p-3 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-lg">
                                                <p class="text-sm font-medium text-warning-700 dark:text-warning-300">Your Revision Request:</p>
                                                <p class="text-sm text-warning-600 dark:text-warning-400 mt-1">{{ $delivery->revision_notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-surface-400 dark:text-surface-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-surface-500 dark:text-surface-400">No deliveries yet. The seller is working on your order.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Activity Timeline -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Activity Timeline</h2>
                        <div class="space-y-4">
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-3 h-3 rounded-full bg-success-500"></div>
                                    <div class="flex-1 w-0.5 bg-surface-200 dark:bg-surface-700"></div>
                                </div>
                                <div class="pb-4">
                                    <p class="font-medium text-surface-900 dark:text-white">Order Placed</p>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">{{ $serviceOrder->created_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                            @if($serviceOrder->started_at)
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-3 h-3 rounded-full bg-primary-500"></div>
                                        <div class="flex-1 w-0.5 bg-surface-200 dark:bg-surface-700"></div>
                                    </div>
                                    <div class="pb-4">
                                        <p class="font-medium text-surface-900 dark:text-white">Work Started</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ $serviceOrder->started_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($serviceOrder->delivered_at)
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-3 h-3 rounded-full bg-info-500"></div>
                                        <div class="flex-1 w-0.5 bg-surface-200 dark:bg-surface-700"></div>
                                    </div>
                                    <div class="pb-4">
                                        <p class="font-medium text-surface-900 dark:text-white">Work Delivered</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ $serviceOrder->delivered_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($serviceOrder->completed_at)
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-3 h-3 rounded-full bg-success-500"></div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-surface-900 dark:text-white">Order Completed</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ $serviceOrder->completed_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($serviceOrder->cancelled_at)
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-3 h-3 rounded-full bg-danger-500"></div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-surface-900 dark:text-white">Order Cancelled</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ $serviceOrder->cancelled_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Order Summary -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Order Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Package Price</span>
                                <span class="text-surface-900 dark:text-white">${{ number_format($serviceOrder->price, 2) }}</span>
                            </div>
                            <div class="pt-3 border-t border-surface-200 dark:border-surface-700 flex justify-between">
                                <span class="font-semibold text-surface-900 dark:text-white">Total Paid</span>
                                <span class="font-semibold text-primary-600 dark:text-primary-400">${{ number_format($serviceOrder->price, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Seller Info -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Seller</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center overflow-hidden">
                                @if($serviceOrder->seller?->logo)
                                    <img src="{{ Storage::url($serviceOrder->seller->logo) }}" alt="" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <span class="text-lg font-medium text-primary-700 dark:text-primary-300">{{ strtoupper(substr($serviceOrder->seller->store_name ?? 'S', 0, 1)) }}</span>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('sellers.show', $serviceOrder->seller) }}" class="font-medium text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                    {{ $serviceOrder->seller->store_name ?? 'Unknown Seller' }}
                                </a>
                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                    Level {{ $serviceOrder->seller->level ?? 1 }} Seller
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Due Date -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Delivery Deadline</h3>
                        @if($serviceOrder->due_at)
                            @php
                                $isOverdue = $serviceOrder->due_at->isPast() && !in_array($serviceOrder->status, ['completed', 'cancelled']);
                                $daysLeft = now()->diffInDays($serviceOrder->due_at, false);
                            @endphp
                            <div class="text-center">
                                <p class="text-3xl font-bold {{ $isOverdue ? 'text-danger-600 dark:text-danger-400' : 'text-surface-900 dark:text-white' }}">
                                    {{ $serviceOrder->due_at->format('M d') }}
                                </p>
                                <p class="text-surface-500 dark:text-surface-400">{{ $serviceOrder->due_at->format('Y') }}</p>
                                @if(!in_array($serviceOrder->status, ['completed', 'cancelled']))
                                    <p class="mt-2 text-sm {{ $isOverdue ? 'text-danger-600 dark:text-danger-400' : ($daysLeft <= 1 ? 'text-warning-600 dark:text-warning-400' : 'text-surface-600 dark:text-surface-400') }}">
                                        @if($isOverdue)
                                            {{ abs($daysLeft) }} day{{ abs($daysLeft) != 1 ? 's' : '' }} overdue
                                        @else
                                            {{ $daysLeft }} day{{ $daysLeft != 1 ? 's' : '' }} remaining
                                        @endif
                                    </p>
                                @endif
                            </div>
                        @else
                            <p class="text-surface-500 dark:text-surface-400 text-center">No deadline set</p>
                        @endif
                    </div>

                    <!-- Escrow Status -->
                    @if($serviceOrder->escrowTransaction)
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Payment Status</h3>
                            <div class="flex items-center gap-3 p-3 bg-success-50 dark:bg-success-900/20 rounded-lg">
                                <svg class="w-8 h-8 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <div>
                                    <p class="font-medium text-success-700 dark:text-success-300">Funds Protected</p>
                                    <p class="text-sm text-success-600 dark:text-success-400">${{ number_format($serviceOrder->escrowTransaction->amount, 2) }} in escrow</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    @if(!in_array($serviceOrder->status, ['completed', 'cancelled']))
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                @if(in_array($serviceOrder->status, ['pending_payment', 'pending_requirements', 'ordered']))
                                    <button type="button" onclick="document.getElementById('cancel-modal').classList.remove('hidden')" class="w-full px-4 py-2 border border-danger-300 dark:border-danger-700 text-danger-600 dark:text-danger-400 rounded-lg hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors">
                                        Cancel Order
                                    </button>
                                @endif
                                <a href="{{ route('support.create') }}?order={{ $serviceOrder->id }}" class="block w-full px-4 py-2 text-center border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                    Report Issue
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Revision Request Modal -->
    <div id="revision-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-surface-800 rounded-xl max-w-lg w-full mx-4 p-6">
            <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Request Revision</h3>
            <form action="{{ route('service-orders.revision', $serviceOrder) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">What needs to be changed?</label>
                    <textarea name="revision_notes" rows="4" required class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" placeholder="Please describe what needs to be revised..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('revision-modal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 bg-warning-600 hover:bg-warning-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div id="cancel-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-surface-800 rounded-xl max-w-lg w-full mx-4 p-6">
            <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Cancel Order</h3>
            <p class="text-surface-600 dark:text-surface-400 mb-4">Are you sure you want to cancel this order? If payment was made, you'll receive a refund.</p>
            <form action="{{ route('service-orders.cancel', $serviceOrder) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Reason for cancellation</label>
                    <textarea name="cancellation_reason" rows="3" required class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" placeholder="Please explain why you're cancelling..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('cancel-modal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                        Keep Order
                    </button>
                    <button type="submit" class="flex-1 bg-danger-600 hover:bg-danger-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Cancel Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
