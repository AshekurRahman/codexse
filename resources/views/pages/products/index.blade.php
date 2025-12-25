<x-layouts.app title="Products - Codexse">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">
                        @if(request('search'))
                            Search results for "{{ request('search') }}"
                        @elseif(request('category'))
                            {{ $categories->firstWhere('id', request('category'))?->name ?? 'Products' }}
                        @else
                            All Products
                        @endif
                    </h1>
                    <p class="mt-1 text-surface-600 dark:text-surface-400">{{ $products->total() }} products found</p>
                </div>

                <!-- Sort Dropdown -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-surface-500 dark:text-surface-400">Sort:</span>
                    <select onchange="window.location.href = this.value" class="rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-1.5 text-sm text-surface-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popular</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters -->
                <aside class="w-full lg:w-64 shrink-0">
                    <div class="sticky top-24 space-y-6">
                        <!-- Search -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-3">Search</h3>
                            <form action="{{ route('products.index') }}" method="GET">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                                    class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 px-4 py-2.5 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                            </form>
                        </div>

                        <!-- Categories -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-3">Categories</h3>
                            <div class="space-y-1">
                                <a href="{{ route('products.index', request()->except('category')) }}"
                                    class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors {{ !request('category') ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                    <span>All Categories</span>
                                </a>
                                @foreach($categories as $category)
                                    <a href="{{ route('products.index', array_merge(request()->except('category'), ['category' => $category->id])) }}"
                                        class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors {{ request('category') == $category->id ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                        <span>{{ $category->name }}</span>
                                        <span class="text-xs text-surface-400">{{ $category->products_count }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-3">Price Range</h3>
                            <form action="{{ route('products.index') }}" method="GET" class="space-y-3">
                                @foreach(request()->except(['min_price', 'max_price']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <div class="flex items-center gap-2">
                                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min"
                                        class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 px-3 py-2 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                    <span class="text-surface-400">-</span>
                                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max"
                                        class="w-full rounded-lg border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 px-3 py-2 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                </div>
                                <button type="submit" class="w-full rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                                    Apply Filter
                                </button>
                            </form>
                        </div>

                        <!-- Clear Filters -->
                        @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
                            <a href="{{ route('products.index') }}" class="flex items-center justify-center gap-2 rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm font-medium text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear All Filters
                            </a>
                        @endif
                    </div>
                </aside>

                <!-- Products Grid -->
                <div class="flex-1">
                    @if($products->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($products->hasPages())
                            <div class="mt-8">
                                {{ $products->withQueryString()->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-16 bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700">
                            <div class="w-16 h-16 rounded-full bg-surface-100 dark:bg-surface-700 flex items-center justify-center mx-auto mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-1">No products found</h3>
                            <p class="text-surface-500 dark:text-surface-400 mb-4">Try adjusting your search or filter criteria</p>
                            <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                                View All Products
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
