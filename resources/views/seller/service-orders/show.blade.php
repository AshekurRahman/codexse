<x-layouts.app title="Order #{{ $order->order_number }}">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('seller.service-orders.index') }}" class="inline-flex items-center gap-2 text-surface-600 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Orders
                </a>
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Order #{{ $order->order_number }}</h1>
                            <x-status-badge :status="$order->status" />
                        </div>
                        <p class="text-surface-600 dark:text-surface-400 mt-1">Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($order->conversation)
                            <a href="{{ route('conversations.show', $order->conversation) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Message Buyer
                            </a>
                        @endif
                        @if($order->status === 'pending')
                            <form action="{{ route('seller.service-orders.accept', $order) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 bg-success-600 hover:bg-success-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Accept Order
                                </button>
                            </form>
                        @elseif($order->status === 'in_progress')
                            <a href="{{ route('seller.service-orders.deliver', $order) }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Deliver Work
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Service Info -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Service Details</h2>
                        <div class="flex items-start gap-4">
                            @if($order->service?->thumbnail)
                                <img src="{{ Storage::url($order->service->thumbnail) }}" alt="" class="w-20 h-16 object-cover rounded-lg">
                            @endif
                            <div class="flex-1">
                                <h3 class="font-medium text-surface-900 dark:text-white">{{ $order->service->name ?? 'Deleted Service' }}</h3>
                                <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Package: {{ $order->package->name ?? 'N/A' }}</p>
                                <div class="flex items-center gap-4 mt-2 text-sm text-surface-600 dark:text-surface-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $order->package->delivery_days ?? 0 }} days delivery
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        {{ $order->package->revisions ?? 0 }} revisions
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Requirements -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Buyer Requirements</h2>
                        @if($order->requirements_data && count($order->requirements_data) > 0)
                            <div class="space-y-4">
                                @foreach($order->requirements_data as $requirement)
                                    <div class="p-4 bg-surface-50 dark:bg-surface-900/50 rounded-lg">
                                        <p class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">{{ $requirement['question'] ?? 'Question' }}</p>
                                        <p class="text-surface-900 dark:text-white">{{ $requirement['answer'] ?? 'No answer provided' }}</p>
                                        @if(isset($requirement['file']))
                                            <a href="{{ Storage::url($requirement['file']) }}" target="_blank" class="inline-flex items-center gap-2 mt-2 text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                </svg>
                                                View Attachment
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-surface-500 dark:text-surface-400">No requirements submitted.</p>
                        @endif
                    </div>

                    <!-- Deliveries -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Deliveries</h2>
                        @if($order->deliveries && $order->deliveries->count() > 0)
                            <div class="space-y-4">
                                @foreach($order->deliveries as $delivery)
                                    <div class="p-4 border border-surface-200 dark:border-surface-700 rounded-lg">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-sm text-surface-500 dark:text-surface-400">{{ $delivery->created_at->format('M d, Y \a\t g:i A') }}</span>
                                            <x-status-badge :status="$delivery->status" size="sm" />
                                        </div>
                                        <p class="text-surface-900 dark:text-white">{{ $delivery->notes }}</p>
                                        @if($delivery->message && $delivery->message->attachments)
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @foreach($delivery->message->attachments as $attachment)
                                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 rounded-lg text-sm hover:bg-surface-200 dark:hover:bg-surface-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                        </svg>
                                                        {{ $attachment->file_name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($delivery->status === 'revision_requested' && $delivery->revision_notes)
                                            <div class="mt-3 p-3 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-lg">
                                                <p class="text-sm font-medium text-warning-700 dark:text-warning-300">Revision Requested:</p>
                                                <p class="text-sm text-warning-600 dark:text-warning-400 mt-1">{{ $delivery->revision_notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-surface-500 dark:text-surface-400">No deliveries yet.</p>
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
                                    <p class="text-sm text-surface-500 dark:text-surface-400">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                            @if($order->accepted_at)
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-3 h-3 rounded-full bg-primary-500"></div>
                                        <div class="flex-1 w-0.5 bg-surface-200 dark:bg-surface-700"></div>
                                    </div>
                                    <div class="pb-4">
                                        <p class="font-medium text-surface-900 dark:text-white">Order Accepted</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ $order->accepted_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($order->delivered_at)
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-3 h-3 rounded-full bg-info-500"></div>
                                        <div class="flex-1 w-0.5 bg-surface-200 dark:bg-surface-700"></div>
                                    </div>
                                    <div class="pb-4">
                                        <p class="font-medium text-surface-900 dark:text-white">Work Delivered</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ $order->delivered_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($order->completed_at)
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-3 h-3 rounded-full bg-success-500"></div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-surface-900 dark:text-white">Order Completed</p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ $order->completed_at->format('M d, Y \a\t g:i A') }}</p>
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
                                <span class="text-surface-900 dark:text-white">${{ number_format($order->price, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Platform Fee</span>
                                <span class="text-surface-900 dark:text-white">-${{ number_format($order->platform_fee ?? $order->price * 0.1, 2) }}</span>
                            </div>
                            <div class="pt-3 border-t border-surface-200 dark:border-surface-700 flex justify-between">
                                <span class="font-semibold text-surface-900 dark:text-white">Your Earnings</span>
                                <span class="font-semibold text-success-600 dark:text-success-400">${{ number_format($order->price - ($order->platform_fee ?? $order->price * 0.1), 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Buyer Info -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Buyer</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                @if($order->buyer?->avatar)
                                    <img src="{{ Storage::url($order->buyer->avatar) }}" alt="" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <span class="text-lg font-medium text-primary-700 dark:text-primary-300">{{ strtoupper(substr($order->buyer->name ?? 'U', 0, 1)) }}</span>
                                @endif
                            </div>
                            <div>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $order->buyer->name ?? 'Unknown User' }}</p>
                                <p class="text-sm text-surface-500 dark:text-surface-400">Member since {{ $order->buyer?->created_at?->format('M Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Due Date -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Deadline</h3>
                        @if($order->due_at)
                            @php
                                $isOverdue = $order->due_at->isPast() && !in_array($order->status, ['completed', 'cancelled']);
                                $daysLeft = now()->diffInDays($order->due_at, false);
                            @endphp
                            <div class="text-center">
                                <p class="text-3xl font-bold {{ $isOverdue ? 'text-danger-600 dark:text-danger-400' : 'text-surface-900 dark:text-white' }}">
                                    {{ $order->due_at->format('M d') }}
                                </p>
                                <p class="text-surface-500 dark:text-surface-400">{{ $order->due_at->format('Y') }}</p>
                                @if(!in_array($order->status, ['completed', 'cancelled']))
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
                    @if($order->escrowTransaction)
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Payment Status</h3>
                            <div class="flex items-center gap-3 p-3 bg-success-50 dark:bg-success-900/20 rounded-lg">
                                <svg class="w-8 h-8 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <div>
                                    <p class="font-medium text-success-700 dark:text-success-300">Funds in Escrow</p>
                                    <p class="text-sm text-success-600 dark:text-success-400">${{ number_format($order->escrowTransaction->amount, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    @if(!in_array($order->status, ['completed', 'cancelled']))
                        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                @if($order->status === 'pending')
                                    <form action="{{ route('seller.service-orders.reject', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this order?')">
                                        @csrf
                                        <button type="submit" class="w-full px-4 py-2 border border-danger-300 dark:border-danger-700 text-danger-600 dark:text-danger-400 rounded-lg hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors">Reject Order</button>
                                    </form>
                                @endif
                                <a href="{{ route('support.create') }}?order={{ $order->id }}" class="block w-full px-4 py-2 text-center border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">Report Issue</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
