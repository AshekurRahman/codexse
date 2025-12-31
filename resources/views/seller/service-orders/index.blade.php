<x-layouts.app title="Service Orders">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Service Orders</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">Manage orders for your services</p>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-success-50 dark:bg-success-900/30 border border-success-200 dark:border-success-800 rounded-lg text-success-700 dark:text-success-300">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Total Orders</p>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white mt-1">{{ $orders->total() }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">Pending</p>
                    <p class="text-2xl font-bold text-warning-600 dark:text-warning-400 mt-1">{{ $pendingCount ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-sm text-surface-500 dark:text-surface-400">In Progress</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400 mt-1">{{ $inProgressCount ?? 0 }}</p>
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
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4 mb-6">
                <form action="{{ route('seller.service-orders.index') }}" method="GET" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order # or buyer..." class="w-full px-4 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 text-surface-900 dark:text-white placeholder-surface-400">
                    </div>
                    <select name="status" class="px-4 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 text-surface-900 dark:text-white">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <select name="service" class="px-4 py-2 bg-white dark:bg-surface-900 border border-surface-300 dark:border-surface-600 rounded-lg focus:ring-2 focus:ring-primary-500 text-surface-900 dark:text-white">
                        <option value="">All Services</option>
                        @foreach($services ?? [] as $service)
                            <option value="{{ $service->id }}" {{ request('service') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">Filter</button>
                    @if(request()->hasAny(['search', 'status', 'service']))
                        <a href="{{ route('seller.service-orders.index') }}" class="px-4 py-2 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">Clear</a>
                    @endif
                </form>
            </div>

            <!-- Orders Table -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                @if($orders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-surface-50 dark:bg-surface-900/50">
                                <tr>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Order</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Buyer</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Service</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Price</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Due Date</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Status</th>
                                    <th class="text-right text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-200 dark:divide-surface-700">
                                @foreach($orders as $order)
                                    <tr class="hover:bg-surface-50 dark:hover:bg-surface-700/50">
                                        <td class="px-6 py-4">
                                            <a href="{{ route('seller.service-orders.show', $order) }}" class="font-medium text-primary-600 dark:text-primary-400 hover:underline">#{{ $order->order_number }}</a>
                                            <p class="text-sm text-surface-500 dark:text-surface-400">{{ $order->created_at->format('M d, Y') }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-primary-700 dark:text-primary-300">{{ strtoupper(substr($order->buyer->name ?? 'U', 0, 1)) }}</span>
                                                </div>
                                                <span class="text-surface-900 dark:text-white">{{ $order->buyer->name ?? 'Unknown' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-surface-900 dark:text-white truncate max-w-[150px]">{{ $order->service->name ?? 'Deleted Service' }}</p>
                                            <p class="text-sm text-surface-500 dark:text-surface-400">{{ $order->package->name ?? '' }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-semibold text-surface-900 dark:text-white">${{ number_format($order->price, 2) }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($order->due_at)
                                                @php
                                                    $isOverdue = $order->due_at->isPast() && !in_array($order->status, ['completed', 'cancelled']);
                                                @endphp
                                                <span class="{{ $isOverdue ? 'text-danger-600 dark:text-danger-400' : 'text-surface-600 dark:text-surface-400' }}">
                                                    {{ $order->due_at->format('M d, Y') }}
                                                    @if($isOverdue)
                                                        <span class="text-xs">(Overdue)</span>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-surface-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <x-status-badge :status="$order->status" />
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('seller.service-orders.show', $order) }}" class="p-2 text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors" title="View">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                                @if($order->status === 'pending')
                                                    <form action="{{ route('seller.service-orders.accept', $order) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="p-2 text-success-500 hover:text-success-600 dark:text-success-400 dark:hover:text-success-300 transition-colors" title="Accept">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($order->status === 'in_progress')
                                                    <a href="{{ route('seller.service-orders.deliver', $order) }}" class="p-2 text-primary-500 hover:text-primary-600 dark:text-primary-400 dark:hover:text-primary-300 transition-colors" title="Deliver">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($orders->hasPages())
                        <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700">
                            {{ $orders->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No orders yet</h3>
                        <p class="text-surface-600 dark:text-surface-400">Orders will appear here when buyers purchase your services.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
