<x-layouts.app :title="$seller->store_name . ' - Codexse'">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm mb-6">
                <a href="{{ route('home') }}" class="text-surface-500 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400">Home</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('sellers.index') }}" class="text-surface-500 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400">Sellers</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-surface-900 dark:text-white font-medium">{{ $seller->store_name }}</span>
            </nav>

            <!-- Seller Header Card -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-8 mb-8">
                <div class="flex flex-col items-center text-center">
                    <!-- Avatar -->
                    <div class="relative mb-4">
                        <div class="h-24 w-24 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white shadow-lg">
                            @if($seller->logo)
                                <img src="{{ $seller->logo_url }}" alt="{{ $seller->store_name }}" class="h-24 w-24 rounded-full object-cover">
                            @else
                                <span class="text-3xl font-bold">{{ substr($seller->store_name, 0, 1) }}</span>
                            @endif
                        </div>
                        @if($seller->is_verified)
                            <div class="absolute bottom-0 right-0 h-7 w-7 rounded-full bg-primary-500 border-2 border-white dark:border-surface-800 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Name -->
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $seller->store_name }}</h1>

                    <!-- Badges -->
                    @if($seller->is_featured || $seller->is_verified)
                        <div class="flex justify-center gap-2 mt-2">
                            @if($seller->is_featured)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-xs font-medium">Featured</span>
                            @endif
                            @if($seller->is_verified)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-xs font-medium">Verified</span>
                            @endif
                        </div>
                    @endif

                    <!-- Description -->
                    <p class="text-surface-500 dark:text-surface-400 mt-3 max-w-lg">{{ $seller->description ?? 'Digital products creator' }}</p>

                    <!-- Stats -->
                    <div class="flex justify-center gap-8 mt-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-surface-900 dark:text-white">{{ $products->total() }}</div>
                            <div class="text-sm text-surface-500 dark:text-surface-400">Products</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-surface-900 dark:text-white">${{ number_format($seller->total_sales, 0) }}</div>
                            <div class="text-sm text-surface-500 dark:text-surface-400">Sales</div>
                        </div>
                    </div>

                    <!-- Website Link -->
                    @if($seller->website)
                        <a href="{{ $seller->website }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-600 transition-colors text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Visit Website
                        </a>
                    @endif
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
                    <p class="text-surface-500 dark:text-surface-400 mb-4">This seller hasn't added any products yet</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                        Browse All Products
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
