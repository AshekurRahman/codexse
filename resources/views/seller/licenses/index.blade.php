<x-layouts.app title="Licenses">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Licenses</h1>
                <p class="text-surface-600 dark:text-surface-400 mt-1">View all licenses issued for your products</p>
            </div>

            <!-- Filters -->
            <div class="mb-6 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                <form action="{{ route('seller.licenses.index') }}" method="GET" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by license key or customer..."
                            class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 text-sm">
                    </div>
                    <div class="w-40">
                        <select name="status" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 text-sm">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="revoked" {{ request('status') === 'revoked' ? 'selected' : '' }}>Revoked</option>
                        </select>
                    </div>
                    <div class="w-48">
                        <select name="product_id" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 text-sm">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'product_id']))
                        <a href="{{ route('seller.licenses.index') }}" class="px-4 py-2 bg-surface-200 dark:bg-surface-700 text-surface-700 dark:text-surface-300 rounded-lg hover:bg-surface-300 dark:hover:bg-surface-600 text-sm font-medium">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <!-- Licenses Table -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                @if($licenses->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-surface-50 dark:bg-surface-900/50">
                                <tr>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">License Key</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Product</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Customer</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Type</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Status</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Activations</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Issued</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-200 dark:divide-surface-700">
                                @foreach($licenses as $license)
                                    <tr class="hover:bg-surface-50 dark:hover:bg-surface-700/50">
                                        <td class="px-6 py-4">
                                            <code class="text-sm font-mono bg-surface-100 dark:bg-surface-700 px-2 py-1 rounded">{{ $license->license_key }}</code>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg bg-surface-100 dark:bg-surface-700 overflow-hidden shrink-0">
                                                    @if($license->product && $license->product->thumbnail)
                                                        <img src="{{ $license->product->thumbnail_url }}" alt="" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center">
                                                            <svg class="w-5 h-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <span class="text-sm text-surface-900 dark:text-white truncate max-w-[150px]">{{ $license->product->name ?? 'Unknown' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm">
                                                <p class="text-surface-900 dark:text-white">{{ $license->user->name ?? 'Unknown' }}</p>
                                                <p class="text-surface-500 dark:text-surface-400 text-xs">{{ $license->user->email ?? '' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($license->license_type === 'extended') bg-info-100 text-info-800 dark:bg-info-900/30 dark:text-info-400
                                                @elseif($license->license_type === 'unlimited') bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400
                                                @else bg-surface-100 text-surface-800 dark:bg-surface-700 dark:text-surface-300 @endif">
                                                {{ ucfirst($license->license_type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($license->status === 'active') bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400
                                                @elseif($license->status === 'suspended') bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-400
                                                @elseif($license->status === 'revoked') bg-danger-100 text-danger-800 dark:bg-danger-900/30 dark:text-danger-400
                                                @else bg-surface-100 text-surface-800 dark:bg-surface-700 dark:text-surface-300 @endif">
                                                {{ ucfirst($license->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-surface-600 dark:text-surface-400">
                                            @if($license->max_activations === 0)
                                                {{ $license->activations_count }} / ∞
                                            @else
                                                {{ $license->activations_count }} / {{ $license->max_activations }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-surface-600 dark:text-surface-400">
                                            {{ $license->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($licenses->hasPages())
                        <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700">
                            {{ $licenses->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No licenses found</h3>
                        <p class="text-surface-600 dark:text-surface-400">When customers purchase your products, licenses will appear here</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
