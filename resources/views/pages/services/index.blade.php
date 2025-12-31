<x-layouts.app title="Professional Services | Find Expert Freelancers">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <!-- Hero Section -->
        <div class="relative bg-gradient-to-br from-primary-50 via-cyan-50 to-teal-100 overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0">
                <div class="absolute top-0 left-1/4 w-96 h-96 bg-gradient-to-br from-primary-200/40 to-cyan-200/30 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-gradient-to-br from-teal-200/40 to-emerald-200/30 rounded-full blur-3xl"></div>
                <div class="absolute top-1/3 right-1/3 w-64 h-64 bg-gradient-to-br from-cyan-200/30 to-primary-200/30 rounded-full blur-3xl"></div>
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"50\" height=\"43\" viewBox=\"0 0 50 43\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpolygon points=\"25,0 50,14.4 50,43 25,28.6 0,43 0,14.4\" fill=\"none\" stroke=\"%2306b6d4\" stroke-opacity=\"0.05\" stroke-width=\"0.5\"/%3E%3C/svg%3E')]"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
                <div class="text-center max-w-3xl mx-auto">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 backdrop-blur-sm border border-primary-200 shadow-sm mb-6">
                        <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm font-medium text-surface-700">{{ number_format($services->total()) }}+ Professional Services</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl font-bold text-surface-900 mb-4 tracking-tight">
                        Find the Perfect <span class="bg-gradient-to-r from-primary-600 to-cyan-600 bg-clip-text text-transparent">Service</span> for Your Needs
                    </h1>
                    <p class="text-lg text-surface-600 mb-8 max-w-2xl mx-auto">
                        Connect with skilled professionals ready to bring your projects to life. Quality work, secure payments, and satisfaction guaranteed.
                    </p>

                    <!-- Search Box -->
                    <form action="{{ route('services.index') }}" method="GET" class="max-w-2xl mx-auto mb-6">
                        <div class="relative">
                            <div class="flex flex-col sm:flex-row gap-3 bg-white/90 backdrop-blur-sm border border-surface-200 shadow-xl shadow-primary-200/30 rounded-2xl p-2">
                                <div class="relative flex-1">
                                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Try 'logo design', 'web development', 'content writing'..."
                                        class="w-full rounded-xl border-0 bg-surface-50 pl-12 pr-4 py-3.5 text-surface-900 placeholder-surface-500 focus:ring-2 focus:ring-primary-500 focus:bg-white transition-colors">
                                </div>
                                <button type="submit" class="rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-8 py-3.5 font-semibold text-white shadow-lg shadow-primary-500/30 hover:shadow-xl transition-all hover:scale-105">
                                    Search
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Popular Searches -->
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <span class="text-sm text-surface-500">Popular:</span>
                        @foreach(['Web Design', 'Logo Design', 'SEO', 'Video Editing', 'WordPress'] as $term)
                            <a href="{{ route('services.index', ['search' => $term]) }}"
                                class="px-3 py-1.5 rounded-full bg-white hover:bg-primary-50 border border-primary-200 text-sm text-primary-600 font-medium transition-all shadow-sm hover:shadow-md">
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
                <div class="px-6 py-5 text-center">
                    <div class="text-2xl font-bold text-surface-900 dark:text-white">{{ number_format($services->total()) }}+</div>
                    <div class="text-sm text-surface-500 dark:text-surface-400">Services</div>
                </div>
                <div class="px-6 py-5 text-center">
                    <div class="text-2xl font-bold text-surface-900 dark:text-white">{{ $categories->count() }}</div>
                    <div class="text-sm text-surface-500 dark:text-surface-400">Categories</div>
                </div>
                <div class="px-6 py-5 text-center">
                    <div class="flex items-center justify-center gap-1 text-2xl font-bold text-yellow-500">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        4.9
                    </div>
                    <div class="text-sm text-surface-500 dark:text-surface-400">Avg. Rating</div>
                </div>
                <div class="px-6 py-5 text-center">
                    <div class="flex items-center justify-center gap-1 text-2xl font-bold text-green-600 dark:text-green-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        100%
                    </div>
                    <div class="text-sm text-surface-500 dark:text-surface-400">Secure Payment</div>
                </div>
            </div>
        </div>

        <!-- Category Pills -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-hide">
                <a href="{{ route('services.index', request()->except('category')) }}"
                    class="shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all {{ !request('category') ? 'bg-primary-600 text-white shadow-lg' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:border-primary-500 hover:text-primary-600' }}">
                    All Services
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('services.index', array_merge(request()->except('category'), ['category' => $category->id])) }}"
                        class="shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all {{ request('category') == $category->id ? 'bg-primary-600 text-white shadow-lg' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:border-primary-500 hover:text-primary-600' }}">
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
                            {{ $categories->firstWhere('id', request('category'))?->name ?? 'Services' }}
                        @else
                            Browse All Services
                        @endif
                    </h2>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">{{ number_format($services->total()) }} services available</p>
                </div>

                <div class="flex items-center gap-3">
                    <select onchange="window.location.href = this.value"
                        class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm text-surface-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters -->
                <aside class="w-full lg:w-72 shrink-0">
                    <div class="sticky top-24 space-y-6">
                        <!-- Budget -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="flex items-center gap-2 font-semibold text-surface-900 dark:text-white mb-4">
                                <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Budget
                            </h3>
                            <div class="space-y-1">
                                @php
                                    $budgetRanges = [
                                        ['label' => 'Under $50', 'max' => 50],
                                        ['label' => '$50 - $100', 'min' => 50, 'max' => 100],
                                        ['label' => '$100 - $250', 'min' => 100, 'max' => 250],
                                        ['label' => '$250 - $500', 'min' => 250, 'max' => 500],
                                        ['label' => '$500+', 'min' => 500],
                                    ];
                                @endphp
                                <a href="{{ route('services.index', request()->except(['min_price', 'max_price'])) }}"
                                    class="flex items-center px-3 py-2.5 rounded-xl text-sm transition-colors {{ !request('min_price') && !request('max_price') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                    Any Budget
                                </a>
                                @foreach($budgetRanges as $range)
                                    @php
                                        $isActive = (request('min_price') == ($range['min'] ?? null) && request('max_price') == ($range['max'] ?? null));
                                        $params = request()->except(['min_price', 'max_price']);
                                        if (isset($range['min'])) $params['min_price'] = $range['min'];
                                        if (isset($range['max'])) $params['max_price'] = $range['max'];
                                    @endphp
                                    <a href="{{ route('services.index', $params) }}"
                                        class="flex items-center px-3 py-2.5 rounded-xl text-sm transition-colors {{ $isActive ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                        {{ $range['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Delivery Time -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="flex items-center gap-2 font-semibold text-surface-900 dark:text-white mb-4">
                                <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Delivery Time
                            </h3>
                            <div class="space-y-1">
                                <a href="{{ route('services.index', request()->except('delivery_time')) }}"
                                    class="flex items-center px-3 py-2.5 rounded-xl text-sm transition-colors {{ !request('delivery_time') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                    Any Time
                                </a>
                                @foreach([1 => 'Express 24h', 3 => 'Up to 3 days', 7 => 'Up to 1 week', 14 => 'Up to 2 weeks'] as $days => $label)
                                    <a href="{{ route('services.index', array_merge(request()->all(), ['delivery_time' => $days])) }}"
                                        class="flex items-center px-3 py-2.5 rounded-xl text-sm transition-colors {{ request('delivery_time') == $days ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Seller Level -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-5">
                            <h3 class="flex items-center gap-2 font-semibold text-surface-900 dark:text-white mb-4">
                                <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                                Seller Level
                            </h3>
                            <div class="space-y-1">
                                <a href="{{ route('services.index', request()->except('seller_level')) }}"
                                    class="flex items-center px-3 py-2.5 rounded-xl text-sm transition-colors {{ !request('seller_level') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                    Any Level
                                </a>
                                @foreach(['top_rated' => 'Top Rated', 'level_2' => 'Level 2', 'level_1' => 'Level 1', 'new' => 'New Sellers'] as $level => $label)
                                    <a href="{{ route('services.index', array_merge(request()->all(), ['seller_level' => $level])) }}"
                                        class="flex items-center px-3 py-2.5 rounded-xl text-sm transition-colors {{ request('seller_level') == $level ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-medium' : 'text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700' }}">
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Clear Filters -->
                        @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'delivery_time', 'seller_level']))
                            <a href="{{ route('services.index') }}" class="flex items-center justify-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-3 text-sm font-medium text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear All Filters
                            </a>
                        @endif

                        <!-- How It Works -->
                        <div class="rounded-2xl bg-gradient-to-br from-primary-50 to-cyan-50 dark:from-primary-900/20 dark:to-cyan-900/20 border border-primary-100 dark:border-primary-800/50 p-5">
                            <h3 class="flex items-center gap-2 font-semibold text-surface-900 dark:text-white mb-4">
                                <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                How It Works
                            </h3>
                            <ul class="space-y-3 text-sm">
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-primary-200 dark:bg-primary-800 flex items-center justify-center text-xs font-bold text-primary-700 dark:text-primary-300 shrink-0">1</span>
                                    <span class="text-surface-600 dark:text-surface-400">Find the service you need</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-primary-200 dark:bg-primary-800 flex items-center justify-center text-xs font-bold text-primary-700 dark:text-primary-300 shrink-0">2</span>
                                    <span class="text-surface-600 dark:text-surface-400">Choose a package & order</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-primary-200 dark:bg-primary-800 flex items-center justify-center text-xs font-bold text-primary-700 dark:text-primary-300 shrink-0">3</span>
                                    <span class="text-surface-600 dark:text-surface-400">Payment held in escrow</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-primary-200 dark:bg-primary-800 flex items-center justify-center text-xs font-bold text-primary-700 dark:text-primary-300 shrink-0">4</span>
                                    <span class="text-surface-600 dark:text-surface-400">Approve & release payment</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Buyer Protection -->
                        <div class="rounded-2xl border border-green-200 dark:border-green-800/50 bg-green-50 dark:bg-green-900/20 p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-800/50 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-green-900 dark:text-green-100">Buyer Protection</h4>
                                    <p class="text-xs text-green-700 dark:text-green-300">100% satisfaction guarantee</p>
                                </div>
                            </div>
                            <ul class="space-y-2 text-sm text-green-700 dark:text-green-300">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Secure escrow payments
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Money-back guarantee
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    24/7 customer support
                                </li>
                            </ul>
                        </div>
                    </div>
                </aside>

                <!-- Services Grid -->
                <div class="flex-1">
                    @if($services->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($services as $service)
                                <x-service-card :service="$service" />
                            @endforeach
                        </div>

                        @if($services->hasPages())
                            <div class="mt-10">
                                {{ $services->withQueryString()->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-16 bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700">
                            <div class="w-20 h-20 rounded-2xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-2">No Services Found</h3>
                            <p class="text-surface-500 dark:text-surface-400 mb-6 max-w-md mx-auto">We couldn't find any services matching your criteria. Try adjusting your filters or search for something else.</p>
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                                <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white hover:bg-primary-700 transition-colors shadow-lg shadow-primary-600/25">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    View All Services
                                </a>
                                <a href="{{ route('jobs.create') }}" class="inline-flex items-center gap-2 rounded-xl border border-surface-300 dark:border-surface-600 px-6 py-3 text-sm font-semibold text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                    Post a Job Request
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
                <div class="relative bg-gradient-to-br from-primary-50 via-cyan-50 to-teal-50 dark:from-primary-900/20 dark:via-cyan-900/20 dark:to-teal-900/20 rounded-3xl overflow-hidden border border-primary-100 dark:border-primary-800">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M15 0v30M0 15h30\" stroke=\"%2306b6d4\" stroke-opacity=\"0.05\" stroke-width=\"0.5\"/%3E%3C/svg%3E')]"></div>
                    <div class="absolute top-0 right-0 w-64 h-64 bg-primary-200/40 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-cyan-200/40 rounded-full blur-3xl"></div>

                    <div class="relative px-8 py-12 sm:px-12 lg:px-16 lg:py-16 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
                        <div class="max-w-xl">
                            <h2 class="text-2xl sm:text-3xl font-bold text-surface-900 dark:text-white mb-4">Ready to Offer Your Services?</h2>
                            <p class="text-surface-600 dark:text-surface-400 text-lg">Join our community of freelancers and start earning today. Create your first gig and reach thousands of potential clients.</p>
                            <div class="mt-6 flex flex-wrap gap-6">
                                <div class="flex items-center gap-2 text-surface-700 dark:text-surface-300">
                                    <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    Set your rates
                                </div>
                                <div class="flex items-center gap-2 text-surface-700 dark:text-surface-300">
                                    <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    Global clients
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
                                    <a href="{{ route('seller.services.create') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/25">
                                        Create a Service
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                    </a>
                                @else
                                    <a href="{{ route('seller.apply') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/25">
                                        Become a Seller
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/25">
                                    Get Started
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </a>
                            @endauth
                            <a href="{{ route('jobs.index') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-white border border-surface-200 text-surface-700 font-semibold hover:bg-surface-50 hover:border-surface-300 transition-colors shadow-sm">
                                Browse Jobs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
