<x-layouts.app :title="$category->name . ' - Codexse'">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm mb-6">
                <a href="{{ route('home') }}" class="text-surface-500 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400">Home</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('categories.index') }}" class="text-surface-500 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400">Categories</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-surface-900 dark:text-white font-medium">{{ $category->name }}</span>
            </nav>

            <!-- Category Header Card -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden mb-8">
                <!-- Gradient Header -->
                <div class="h-24 bg-gradient-to-br from-primary-500 to-accent-500 relative">
                    @if($category->image)
                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                    @endif
                </div>

                <!-- Content -->
                <div class="px-8 pb-8 -mt-10 relative">
                    <!-- Icon -->
                    <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white shadow-lg border-4 border-white dark:border-surface-800 mb-4">
                        <x-category-icon :icon="$category->icon" class="h-10 w-10" />
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $category->name }}</h1>
                            <p class="text-surface-500 dark:text-surface-400 mt-1 max-w-lg">{{ $category->description ?? 'Browse our collection of premium ' . strtolower($category->name) . ' designed for modern projects.' }}</p>
                        </div>
                        <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-surface-100 dark:bg-surface-700">
                            <span class="text-2xl font-bold text-surface-900 dark:text-white">{{ $products->total() }}</span>
                            <span class="text-sm text-surface-500 dark:text-surface-400">products</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-surface-900 dark:text-white">Products</h2>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-surface-500 dark:text-surface-400">Sort:</span>
                    <select onchange="window.location.href = this.value" class="rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-1.5 text-sm text-surface-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popular</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
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
                    <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-1">No products yet</h3>
                    <p class="text-surface-500 dark:text-surface-400 mb-4">This category doesn't have any products yet</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                        Browse All Products
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
