<x-layouts.app title="My Products">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">My Products</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">Manage your digital products</p>
                </div>
                <a href="{{ route('seller.products.create') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Product
                </a>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-success-50 dark:bg-success-900/30 border border-success-200 dark:border-success-800 rounded-lg text-success-700 dark:text-success-300">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Products Table -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                @if($products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-surface-50 dark:bg-surface-900/50">
                                <tr>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Product</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Category</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Price</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Status</th>
                                    <th class="text-left text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Sales</th>
                                    <th class="text-right text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-200 dark:divide-surface-700">
                                @foreach($products as $product)
                                    <tr class="hover:bg-surface-50 dark:hover:bg-surface-700/50">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-lg bg-surface-100 dark:bg-surface-700 overflow-hidden shrink-0">
                                                    @if($product->thumbnail)
                                                        <img src="{{ $product->thumbnail_url }}" alt="" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-medium text-surface-900 dark:text-white truncate max-w-[200px]">{{ $product->name }}</p>
                                                    <p class="text-sm text-surface-500 dark:text-surface-400">Added {{ $product->created_at->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-surface-600 dark:text-surface-400">
                                            {{ $product->category->name ?? 'Uncategorized' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($product->sale_price)
                                                <span class="font-semibold text-surface-900 dark:text-white">{{ format_price($product->sale_price) }}</span>
                                                <span class="text-sm text-surface-400 line-through ml-1">{{ format_price($product->price) }}</span>
                                            @else
                                                <span class="font-semibold text-surface-900 dark:text-white">{{ format_price($product->price) }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($product->status === 'published')
                                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400 rounded-md">
                                                    <span class="w-1.5 h-1.5 bg-success-500 rounded-full"></span>
                                                    Published
                                                </span>
                                            @elseif($product->status === 'pending')
                                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400 rounded-md">
                                                    <span class="w-1.5 h-1.5 bg-warning-500 rounded-full"></span>
                                                    Pending
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400 rounded-md">
                                                    <span class="w-1.5 h-1.5 bg-surface-400 rounded-full"></span>
                                                    {{ ucfirst($product->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-surface-600 dark:text-surface-400">
                                            {{ number_format($product->downloads_count ?? 0) }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('seller.products.edit', $product) }}" class="p-2 text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('seller.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-surface-500 hover:text-danger-600 dark:text-surface-400 dark:hover:text-danger-400 transition-colors" title="Delete">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($products->hasPages())
                        <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No products yet</h3>
                        <p class="text-surface-600 dark:text-surface-400 mb-6">Get started by adding your first product</p>
                        <a href="{{ route('seller.products.create') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Product
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
