<x-layouts.app title="Digital Products - Codexse">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <!-- Hero Section -->
        <div class="relative bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-100 overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0">
                <div class="absolute top-0 left-1/4 w-96 h-96 bg-gradient-to-br from-indigo-200/40 to-blue-200/30 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-gradient-to-br from-purple-200/40 to-pink-200/30 rounded-full blur-3xl"></div>
                <div class="absolute top-1/3 right-1/3 w-64 h-64 bg-gradient-to-br from-blue-200/30 to-indigo-200/30 rounded-full blur-3xl"></div>
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%234f46e5\" fill-opacity=\"0.03\"%3E%3Ccircle cx=\"20\" cy=\"20\" r=\"2\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
                <div class="text-center max-w-3xl mx-auto">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 backdrop-blur-sm border border-indigo-200 shadow-sm mb-6">
                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="text-sm font-medium text-surface-700">Premium Digital Products</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl font-bold text-surface-900 mb-4 tracking-tight">
                        Discover <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Digital Products</span>
                    </h1>
                    <p class="text-lg text-surface-600 mb-8 max-w-2xl mx-auto">
                        Explore thousands of premium themes, plugins, scripts, and digital assets. Instant download with lifetime updates and dedicated support.
                    </p>

                    <!-- Search Box -->
                    <form action="{{ route('products.index') }}" method="GET" class="max-w-2xl mx-auto mb-6">
                        <div class="relative">
                            <div class="flex flex-col sm:flex-row gap-3 bg-white/90 backdrop-blur-sm border border-surface-200 shadow-xl shadow-indigo-200/30 rounded-2xl p-2">
                                <div class="relative flex-1">
                                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Search themes, plugins, scripts..."
                                        class="w-full rounded-xl border-0 bg-surface-50 pl-12 pr-4 py-3.5 text-surface-900 placeholder-surface-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-colors">
                                </div>
                                <button type="submit" class="rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 px-8 py-3.5 font-semibold text-white shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all hover:scale-105">
                                    Search
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Popular Searches -->
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <span class="text-sm text-surface-500">Popular:</span>
                        @foreach(['WordPress Theme', 'Laravel', 'React', 'Admin Dashboard', 'E-commerce'] as $term)
                            <a href="{{ route('products.index', ['search' => $term]) }}"
                                class="px-3 py-1.5 rounded-full bg-white hover:bg-indigo-50 border border-indigo-200 text-sm text-indigo-600 font-medium transition-all shadow-sm hover:shadow-md">
                                {{ $term }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="bg-white dark:bg-surface-800 border-b border-surface-200 dark:border-surface-700 shadow-sm -mt-4 relative z-10 mx-4 sm:mx-6 lg:mx-auto max-w-5xl rounded-2xl">
            <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-surface-200 dark:divide-surface-700">
                <div class="px-3 sm:px-6 py-4 sm:py-5 text-center">
                    <div class="text-xl sm:text-2xl font-bold text-surface-900 dark:text-white">{{ number_format($products->total()) }}+</div>
                    <div class="text-xs sm:text-sm text-surface-500 dark:text-surface-400">Products</div>
                </div>
                <div class="px-3 sm:px-6 py-4 sm:py-5 text-center">
                    <div class="text-xl sm:text-2xl font-bold text-surface-900 dark:text-white">{{ $categories->count() }}</div>
                    <div class="text-xs sm:text-sm text-surface-500 dark:text-surface-400">Categories</div>
                </div>
                <div class="px-3 sm:px-6 py-4 sm:py-5 text-center">
                    <div class="flex items-center justify-center gap-1 text-xl sm:text-2xl font-bold text-yellow-500">
                        <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        4.8
                    </div>
                    <div class="text-xs sm:text-sm text-surface-500 dark:text-surface-400">Avg. Rating</div>
                </div>
                <div class="px-3 sm:px-6 py-4 sm:py-5 text-center">
                    <div class="flex items-center justify-center gap-1 text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400">
                        <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span class="hidden sm:inline">Instant</span>
                        <span class="sm:hidden">Fast</span>
                    </div>
                    <div class="text-xs sm:text-sm text-surface-500 dark:text-surface-400">Download</div>
                </div>
            </div>
        </div>

        <!-- Category Pills -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-hide">
                <a href="{{ route('products.index', request()->except('category')) }}"
                    class="shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all {{ !request('category') ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:border-indigo-500 hover:text-indigo-600' }}">
                    All Products
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('products.index', array_merge(request()->except('category'), ['category' => $category->slug])) }}"
                        class="shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all {{ request('category') == $category->slug ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:border-indigo-500 hover:text-indigo-600' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-16">
            <!-- Results Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-surface-900 dark:text-white">
                        @if(request('search'))
                            Results for "{{ request('search') }}"
                        @elseif(request('category'))
                            {{ $categories->firstWhere('slug', request('category'))?->name ?? 'Products' }}
                        @else
                            Browse All Products
                        @endif
                    </h2>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">{{ number_format($products->total()) }} products available</p>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Mobile Filter Toggle -->
                    <button
                        type="button"
                        onclick="document.getElementById('mobile-filters').classList.toggle('hidden')"
                        class="lg:hidden inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 text-surface-700 dark:text-surface-300 font-medium"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filters
                        @if($activeFiltersCount > 0)
                            <span class="px-2 py-0.5 bg-indigo-600 text-white text-xs font-bold rounded-full">{{ $activeFiltersCount }}</span>
                        @endif
                    </button>

                    <select onchange="window.location.href = this.value" class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm text-surface-900 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'bestselling']) }}" {{ request('sort') == 'bestselling' ? 'selected' : '' }}>Best Selling</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'trending']) }}" {{ request('sort') == 'trending' ? 'selected' : '' }}>Trending</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}" {{ request('sort') == 'rating' ? 'selected' : '' }}>Top Rated</option>
                    </select>
                </div>
            </div>

            <!-- Mobile Filter Drawer -->
            <div id="mobile-filters" class="hidden lg:hidden mb-6">
                <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-surface-900 dark:text-white">Filters</h3>
                        <button onclick="document.getElementById('mobile-filters').classList.add('hidden')" class="text-surface-400 hover:text-surface-600">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Quick Filters -->
                        <div class="col-span-2 flex flex-wrap gap-2">
                            <a href="{{ route('products.index', array_merge(request()->except('on_sale'), request()->boolean('on_sale') ? [] : ['on_sale' => 1])) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->boolean('on_sale') ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 'bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-400' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                On Sale
                            </a>
                            <a href="{{ route('products.index', array_merge(request()->except('featured'), request()->boolean('featured') ? [] : ['featured' => 1])) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->boolean('featured') ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' : 'bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-400' }}">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                Featured
                            </a>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-xs font-medium text-surface-500 dark:text-surface-400 mb-2">Price Range</label>
                            <select onchange="window.location.href = this.value" class="w-full text-sm rounded-lg border-surface-200 dark:border-surface-600 bg-surface-50 dark:bg-surface-700">
                                <option value="{{ route('products.index', request()->except(['min_price', 'max_price'])) }}" {{ !request('min_price') && !request('max_price') ? 'selected' : '' }}>Any Price</option>
                                <option value="{{ route('products.index', array_merge(request()->except(['min_price', 'max_price']), ['max_price' => 25])) }}" {{ request('max_price') == 25 && !request('min_price') ? 'selected' : '' }}>Under $25</option>
                                <option value="{{ route('products.index', array_merge(request()->except(['min_price', 'max_price']), ['min_price' => 25, 'max_price' => 50])) }}" {{ request('min_price') == 25 && request('max_price') == 50 ? 'selected' : '' }}>$25 - $50</option>
                                <option value="{{ route('products.index', array_merge(request()->except(['min_price', 'max_price']), ['min_price' => 50, 'max_price' => 100])) }}" {{ request('min_price') == 50 && request('max_price') == 100 ? 'selected' : '' }}>$50 - $100</option>
                                <option value="{{ route('products.index', array_merge(request()->except(['min_price', 'max_price']), ['min_price' => 100])) }}" {{ request('min_price') == 100 && !request('max_price') ? 'selected' : '' }}>$100+</option>
                            </select>
                        </div>

                        <!-- Rating -->
                        <div>
                            <label class="block text-xs font-medium text-surface-500 dark:text-surface-400 mb-2">Rating</label>
                            <select onchange="window.location.href = this.value" class="w-full text-sm rounded-lg border-surface-200 dark:border-surface-600 bg-surface-50 dark:bg-surface-700">
                                <option value="{{ route('products.index', request()->except('min_rating')) }}" {{ !request('min_rating') ? 'selected' : '' }}>Any Rating</option>
                                <option value="{{ route('products.index', array_merge(request()->except('min_rating'), ['min_rating' => 4])) }}" {{ request('min_rating') == 4 ? 'selected' : '' }}>4+ Stars</option>
                                <option value="{{ route('products.index', array_merge(request()->except('min_rating'), ['min_rating' => 3])) }}" {{ request('min_rating') == 3 ? 'selected' : '' }}>3+ Stars</option>
                                <option value="{{ route('products.index', array_merge(request()->except('min_rating'), ['min_rating' => 2])) }}" {{ request('min_rating') == 2 ? 'selected' : '' }}>2+ Stars</option>
                            </select>
                        </div>

                        <!-- Date Added -->
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-surface-500 dark:text-surface-400 mb-2">Date Added</label>
                            <select onchange="window.location.href = this.value" class="w-full text-sm rounded-lg border-surface-200 dark:border-surface-600 bg-surface-50 dark:bg-surface-700">
                                <option value="{{ route('products.index', request()->except('date_range')) }}" {{ !request('date_range') ? 'selected' : '' }}>Any Time</option>
                                <option value="{{ route('products.index', array_merge(request()->except('date_range'), ['date_range' => 'week'])) }}" {{ request('date_range') == 'week' ? 'selected' : '' }}>Last Week</option>
                                <option value="{{ route('products.index', array_merge(request()->except('date_range'), ['date_range' => 'month'])) }}" {{ request('date_range') == 'month' ? 'selected' : '' }}>Last Month</option>
                                <option value="{{ route('products.index', array_merge(request()->except('date_range'), ['date_range' => '3months'])) }}" {{ request('date_range') == '3months' ? 'selected' : '' }}>Last 3 Months</option>
                                <option value="{{ route('products.index', array_merge(request()->except('date_range'), ['date_range' => 'year'])) }}" {{ request('date_range') == 'year' ? 'selected' : '' }}>Last Year</option>
                            </select>
                        </div>

                        <!-- Clear Filters -->
                        @if($activeFiltersCount > 0)
                        <div class="col-span-2">
                            <a href="{{ route('products.index', request()->only('search')) }}" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Clear All Filters ({{ $activeFiltersCount }})
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters -->
                <aside class="w-full lg:w-72 shrink-0 hidden lg:block" id="desktop-filters">
                    <div class="lg:sticky lg:top-24 space-y-6">
                        <!-- Quick Filters -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="flex items-center gap-2 font-semibold text-surface-900 dark:text-white mb-4">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Quick Filters
                            </h3>
                            <div class="space-y-2">
                                <a href="{{ route('products.index', array_merge(request()->except('on_sale'), request()->boolean('on_sale') ? [] : ['on_sale' => 1])) }}"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-colors {{ request()->boolean('on_sale') ? 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        On Sale
                                    </span>
                                    @if(request()->boolean('on_sale'))
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    @endif
                                </a>
                                <a href="{{ route('products.index', array_merge(request()->except('featured'), request()->boolean('featured') ? [] : ['featured' => 1])) }}"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-colors {{ request()->boolean('featured') ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        Featured
                                    </span>
                                    @if(request()->boolean('featured'))
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    @endif
                                </a>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="flex items-center gap-2 font-semibold text-surface-900 dark:text-white mb-4">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Price Range
                            </h3>
                            <!-- Custom Price Input -->
                            <form action="{{ route('products.index') }}" method="GET" class="mb-4">
                                @foreach(request()->except(['min_price', 'max_price']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <div class="flex items-center gap-2">
                                    <div class="relative flex-1">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 text-sm">$</span>
                                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" min="0" class="w-full pl-7 pr-2 py-2 text-sm border border-surface-200 dark:border-surface-600 rounded-lg bg-surface-50 dark:bg-surface-700 text-surface-900 dark:text-white">
                                    </div>
                                    <span class="text-surface-400">-</span>
                                    <div class="relative flex-1">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 text-sm">$</span>
                                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" min="0" class="w-full pl-7 pr-2 py-2 text-sm border border-surface-200 dark:border-surface-600 rounded-lg bg-surface-50 dark:bg-surface-700 text-surface-900 dark:text-white">
                                    </div>
                                    <button type="submit" class="p-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>
                            </form>
                            <div class="space-y-1">
                                @php
                                    $priceRanges = [
                                        ['label' => 'Under $25', 'max' => 25],
                                        ['label' => '$25 - $50', 'min' => 25, 'max' => 50],
                                        ['label' => '$50 - $100', 'min' => 50, 'max' => 100],
                                        ['label' => '$100 - $200', 'min' => 100, 'max' => 200],
                                        ['label' => '$200+', 'min' => 200],
                                    ];
                                @endphp
                                <a href="{{ route('products.index', request()->except(['min_price', 'max_price'])) }}"
                                    class="flex items-center px-3 py-2.5 rounded-xl text-sm transition-colors {{ !request('min_price') && !request('max_price') ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                    Any Price
                                </a>
                                @foreach($priceRanges as $range)
                                    @php
                                        $isActive = (request('min_price') == ($range['min'] ?? null) && request('max_price') == ($range['max'] ?? null));
                                        $params = request()->except(['min_price', 'max_price']);
                                        if (isset($range['min'])) $params['min_price'] = $range['min'];
                                        if (isset($range['max'])) $params['max_price'] = $range['max'];
                                    @endphp
                                    <a href="{{ route('products.index', $params) }}"
                                        class="flex items-center px-3 py-2.5 rounded-xl text-sm transition-colors {{ $isActive ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                        {{ $range['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="flex items-center gap-2 font-semibold text-surface-900 dark:text-white mb-4">
                                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                Rating
                            </h3>
                            <div class="space-y-1">
                                <a href="{{ route('products.index', request()->except('min_rating')) }}"
                                    class="flex items-center px-3 py-2.5 rounded-xl text-sm transition-colors {{ !request('min_rating') ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                    Any Rating
                                </a>
                                @foreach([4, 3, 2, 1] as $rating)
                                    <a href="{{ route('products.index', array_merge(request()->except('min_rating'), ['min_rating' => $rating])) }}"
                                        class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm transition-colors {{ request('min_rating') == $rating ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $rating ? 'text-yellow-400' : 'text-surface-300 dark:text-surface-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span>& Up</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Date Added -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="flex items-center gap-2 font-semibold text-surface-900 dark:text-white mb-4">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Date Added
                            </h3>
                            <div class="space-y-1">
                                @php
                                    $dateRanges = [
                                        ['label' => 'Any Time', 'value' => null],
                                        ['label' => 'Last Week', 'value' => 'week'],
                                        ['label' => 'Last Month', 'value' => 'month'],
                                        ['label' => 'Last 3 Months', 'value' => '3months'],
                                        ['label' => 'Last Year', 'value' => 'year'],
                                    ];
                                @endphp
                                @foreach($dateRanges as $range)
                                    @php
                                        $isActive = request('date_range') == $range['value'];
                                        $params = $range['value'] ? array_merge(request()->except('date_range'), ['date_range' => $range['value']]) : request()->except('date_range');
                                    @endphp
                                    <a href="{{ route('products.index', $params) }}"
                                        class="flex items-center px-3 py-2.5 rounded-xl text-sm transition-colors {{ $isActive ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                        {{ $range['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Clear Filters -->
                        @if($activeFiltersCount > 0)
                            <a href="{{ route('products.index', request()->only('search')) }}" class="flex items-center justify-center gap-2 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear All Filters ({{ $activeFiltersCount }})
                            </a>
                        @endif

                        <!-- What's Included -->
                        <div class="rounded-2xl bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 border border-indigo-100 dark:border-indigo-800/50 p-5">
                            <h3 class="flex items-center gap-2 font-semibold text-surface-900 dark:text-white mb-4">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Every Purchase Includes
                            </h3>
                            <ul class="space-y-3 text-sm">
                                <li class="flex items-center gap-3 text-surface-600 dark:text-surface-400">
                                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Lifetime updates
                                </li>
                                <li class="flex items-center gap-3 text-surface-600 dark:text-surface-400">
                                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    6 months support
                                </li>
                                <li class="flex items-center gap-3 text-surface-600 dark:text-surface-400">
                                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Documentation included
                                </li>
                                <li class="flex items-center gap-3 text-surface-600 dark:text-surface-400">
                                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Instant download
                                </li>
                            </ul>
                        </div>

                        <!-- Money Back Guarantee -->
                        <div class="rounded-2xl border border-green-200 dark:border-green-800/50 bg-green-50 dark:bg-green-900/20 p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-800/50 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-green-900 dark:text-green-100">Money Back Guarantee</h4>
                                    <p class="text-xs text-green-700 dark:text-green-300">30-day refund policy</p>
                                </div>
                            </div>
                            <p class="text-sm text-green-700 dark:text-green-300">Not satisfied? Get a full refund within 30 days of purchase. No questions asked.</p>
                        </div>
                    </div>
                </aside>

                <!-- Products Grid -->
                <div class="flex-1">
                    @if($products->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>

                        @if($products->hasPages())
                            <div class="mt-8">
                                {{ $products->withQueryString()->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-16 bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700">
                            <div class="w-20 h-20 rounded-2xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-2">No Products Found</h3>
                            <p class="text-surface-500 dark:text-surface-400 mb-6 max-w-md mx-auto">We couldn't find any products matching your criteria. Try adjusting your filters or search terms.</p>
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-600/25">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    View All Products
                                </a>
                                <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-surface-300 dark:border-surface-600 px-6 py-3 text-sm font-semibold text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                    Browse Services
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="border-t border-surface-200 dark:border-surface-700">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
                <div class="relative bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-indigo-900/20 dark:via-purple-900/20 dark:to-pink-900/20 rounded-3xl overflow-hidden border border-indigo-100 dark:border-indigo-800">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M15 0v30M0 15h30\" stroke=\"%236366f1\" stroke-opacity=\"0.05\" stroke-width=\"0.5\"/%3E%3C/svg%3E')]"></div>
                    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-200/40 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-200/40 rounded-full blur-3xl"></div>

                    <div class="relative px-8 py-12 sm:px-12 lg:px-16 lg:py-16 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
                        <div class="max-w-xl">
                            <h2 class="text-2xl sm:text-3xl font-bold text-surface-900 dark:text-white mb-4">Ready to Sell Your Digital Products?</h2>
                            <p class="text-surface-600 dark:text-surface-400 text-lg">Join thousands of creators earning money from their digital assets. Upload once, sell forever with instant payouts.</p>
                            <div class="mt-6 flex flex-wrap gap-6">
                                <div class="flex items-center gap-2 text-surface-700 dark:text-surface-300">
                                    <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    Free to list
                                </div>
                                <div class="flex items-center gap-2 text-surface-700 dark:text-surface-300">
                                    <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    Instant delivery
                                </div>
                                <div class="flex items-center gap-2 text-surface-700 dark:text-surface-300">
                                    <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    Fast payouts
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4">
                            @auth
                                @if(auth()->user()->seller)
                                    <a href="{{ route('seller.products.create') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/25">
                                        Upload Product
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                    </a>
                                @else
                                    <a href="{{ route('seller.apply') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/25">
                                        Start Selling
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/25">
                                    Get Started
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </a>
                            @endauth
                            <a href="{{ route('services.index') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-white border border-surface-200 text-surface-700 font-semibold hover:bg-surface-50 hover:border-surface-300 transition-colors shadow-sm">
                                Browse Services
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
