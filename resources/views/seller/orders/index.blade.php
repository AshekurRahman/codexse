<x-layouts.app title="Orders">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Orders</h1>
                <p class="text-surface-600 dark:text-surface-400 mt-1">View all orders for your products</p>
            </div>

            <!-- Orders Table -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                @if($orderItems->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-surface-50 dark:bg-surface-900/50">
                                <tr>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Order</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Product</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Customer</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">License</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Date</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Price</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Your Earnings</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-200 dark:divide-surface-700">
                                @foreach($orderItems as $orderItem)
                                    <tr class="hover:bg-surface-50 dark:hover:bg-surface-700/50">
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-medium text-surface-900 dark:text-white">#{{ $orderItem->order->order_number }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg bg-surface-100 dark:bg-surface-700 overflow-hidden shrink-0">
                                                    @if($orderItem->product && $orderItem->product->thumbnail)
                                                        <img src="{{ $orderItem->product->thumbnail_url }}" alt="" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center">
                                                            <svg class="w-5 h-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <span class="text-sm text-surface-900 dark:text-white truncate max-w-[200px]">{{ $orderItem->product_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-surface-600 dark:text-surface-400">
                                            {{ $orderItem->order->user->name ?? 'Guest' }}
                                        </td>
                                        <td class="px-6 py-4" x-data="{ copied: false }">
                                            @if($orderItem->license)
                                                <div class="space-y-1">
                                                    <div class="flex items-center gap-2">
                                                        <code class="text-xs font-mono bg-surface-100 dark:bg-surface-700 px-2 py-1 rounded text-surface-700 dark:text-surface-300">{{ $orderItem->license_key }}</code>
                                                        <button @click="navigator.clipboard.writeText('{{ $orderItem->license_key }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                            class="text-surface-400 hover:text-surface-600 dark:hover:text-surface-300 transition-colors">
                                                            <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                            </svg>
                                                            <svg x-show="copied" x-cloak class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                            @if($orderItem->license->status === 'active') bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400
                                                            @elseif($orderItem->license->status === 'suspended') bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400
                                                            @else bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 @endif">
                                                            {{ ucfirst($orderItem->license->status) }}
                                                        </span>
                                                        <span class="text-xs text-surface-500">
                                                            @if($orderItem->license->max_activations === 0)
                                                                {{ $orderItem->license->activations_count }} uses
                                                            @else
                                                                {{ $orderItem->license->activations_count }}/{{ $orderItem->license->max_activations }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            @elseif($orderItem->license_key)
                                                <code class="text-xs font-mono bg-surface-100 dark:bg-surface-700 px-2 py-1 rounded text-surface-700 dark:text-surface-300">{{ $orderItem->license_key }}</code>
                                            @else
                                                <span class="text-surface-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-surface-600 dark:text-surface-400">
                                            {{ $orderItem->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-surface-900 dark:text-white">
                                            ${{ number_format($orderItem->price, 2) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-semibold text-success-600 dark:text-success-400">${{ number_format($orderItem->seller_amount, 2) }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('seller.invoice.download', $orderItem->order) }}" class="inline-flex items-center gap-1 text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Statement
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($orderItems->hasPages())
                        <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700">
                            {{ $orderItems->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No orders yet</h3>
                        <p class="text-surface-600 dark:text-surface-400">When customers buy your products, orders will appear here</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
