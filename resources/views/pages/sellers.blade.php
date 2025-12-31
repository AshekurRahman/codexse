<x-layouts.app title="Sellers - Codexse">
    <div class="bg-white dark:bg-surface-900 min-h-screen">
        <!-- Hero Section -->
        <div class="relative bg-gradient-to-b from-primary-50 via-white to-white dark:from-surface-800 dark:via-surface-900 dark:to-surface-900 overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0">
                <div class="absolute top-0 left-0 w-full h-full bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%236366f1\" fill-opacity=\"0.03\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"2\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-primary-200/30 to-indigo-200/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-purple-200/30 to-pink-200/20 rounded-full blur-3xl"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-12 pb-16">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm mb-8">
                    <a href="{{ route('home') }}" class="text-surface-500 hover:text-primary-600 transition-colors">Home</a>
                    <svg class="w-4 h-4 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-surface-900 dark:text-white font-medium">Sellers</span>
                </nav>

                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary-100 dark:bg-primary-900/30 mb-4">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-sm font-medium text-primary-700 dark:text-primary-300">{{ $sellers->total() }} Active Sellers</span>
                        </div>
                        <h1 class="text-4xl sm:text-5xl font-bold text-surface-900 dark:text-white mb-4">
                            Meet Our Creative
                            <span class="bg-gradient-to-r from-primary-600 to-indigo-600 bg-clip-text text-transparent">Community</span>
                        </h1>
                        <p class="text-lg text-surface-600 dark:text-surface-400">
                            Discover talented professionals offering premium digital products and expert services. Connect, collaborate, and create something amazing.
                        </p>
                    </div>

                    <!-- Search Box -->
                    <div class="w-full lg:w-auto">
                        <form action="{{ route('sellers.index') }}" method="GET" class="relative">
                            <div class="flex items-center bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 shadow-lg shadow-surface-200/50 dark:shadow-none p-1.5">
                                <div class="relative flex-1">
                                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Search sellers..."
                                        class="w-full lg:w-64 rounded-xl border-0 bg-transparent pl-12 pr-4 py-3 text-surface-900 dark:text-white placeholder-surface-500 focus:ring-0">
                                </div>
                                <button type="submit" class="rounded-xl bg-primary-600 hover:bg-primary-700 px-6 py-3 font-semibold text-white transition-colors">
                                    Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="mt-10 flex flex-wrap items-center gap-3">
                    <a href="{{ route('sellers.index') }}"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all {{ !request('filter') && !request('sort') ? 'bg-surface-900 dark:bg-white text-white dark:text-surface-900 shadow-lg' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:border-surface-300 hover:text-surface-900' }}">
                        All Sellers
                    </a>
                    <a href="{{ route('sellers.index', ['filter' => 'featured']) }}"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request('filter') == 'featured' ? 'bg-surface-900 dark:bg-white text-white dark:text-surface-900 shadow-lg' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:border-surface-300 hover:text-surface-900' }}">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            Featured
                        </span>
                    </a>
                    <a href="{{ route('sellers.index', ['filter' => 'verified']) }}"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request('filter') == 'verified' ? 'bg-surface-900 dark:bg-white text-white dark:text-surface-900 shadow-lg' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:border-surface-300 hover:text-surface-900' }}">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Verified
                        </span>
                    </a>
                    <a href="{{ route('sellers.index', ['filter' => 'top-rated']) }}"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request('filter') == 'top-rated' ? 'bg-surface-900 dark:bg-white text-white dark:text-surface-900 shadow-lg' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:border-surface-300 hover:text-surface-900' }}">
                        Top Rated
                    </a>
                    <a href="{{ route('sellers.index', ['sort' => 'newest']) }}"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request('sort') == 'newest' ? 'bg-surface-900 dark:bg-white text-white dark:text-surface-900 shadow-lg' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 border border-surface-200 dark:border-surface-700 hover:border-surface-300 hover:text-surface-900' }}">
                        New Sellers
                    </a>

                    <div class="ml-auto">
                        <select onchange="window.location.href = this.value" class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm text-surface-700 dark:text-surface-300 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                            <option value="{{ route('sellers.index', array_merge(request()->except('sort'), ['sort' => 'featured'])) }}" {{ request('sort', 'featured') == 'featured' ? 'selected' : '' }}>Sort: Featured</option>
                            <option value="{{ route('sellers.index', array_merge(request()->except('sort'), ['sort' => 'newest'])) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>Sort: Newest</option>
                            <option value="{{ route('sellers.index', array_merge(request()->except('sort'), ['sort' => 'sales'])) }}" {{ request('sort') == 'sales' ? 'selected' : '' }}>Sort: Most Sales</option>
                            <option value="{{ route('sellers.index', array_merge(request()->except('sort'), ['sort' => 'products'])) }}" {{ request('sort') == 'products' ? 'selected' : '' }}>Sort: Most Products</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <!-- Results Info -->
            <div class="flex items-center justify-between mb-8">
                <p class="text-surface-600 dark:text-surface-400">
                    Showing <span class="font-semibold text-surface-900 dark:text-white">{{ $sellers->firstItem() ?? 0 }}-{{ $sellers->lastItem() ?? 0 }}</span> of <span class="font-semibold text-surface-900 dark:text-white">{{ $sellers->total() }}</span> sellers
                </p>
            </div>

            <!-- Sellers Grid -->
            @if($sellers->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($sellers as $seller)
                        <x-seller-card :seller="$seller" />
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($sellers->hasPages())
                    <div class="mt-12">
                        {{ $sellers->withQueryString()->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-20">
                    <div class="w-24 h-24 rounded-2xl bg-surface-100 dark:bg-surface-800 flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-2">No sellers found</h3>
                    <p class="text-surface-500 dark:text-surface-400 mb-8 max-w-md mx-auto">We couldn't find any sellers matching your criteria. Try adjusting your filters.</p>
                    <a href="{{ route('sellers.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors">
                        View All Sellers
                    </a>
                </div>
            @endif
        </div>

        <!-- Become a Seller CTA -->
        <div class="border-t border-surface-200 dark:border-surface-700">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
                <div class="relative bg-gradient-to-br from-primary-50 via-indigo-50 to-purple-50 dark:from-primary-900/20 dark:via-indigo-900/20 dark:to-purple-900/20 rounded-3xl overflow-hidden border border-primary-100 dark:border-primary-800">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M15 0v30M0 15h30\" stroke=\"%236366f1\" stroke-opacity=\"0.05\" stroke-width=\"0.5\"/%3E%3C/svg%3E')]"></div>
                    <div class="absolute top-0 right-0 w-64 h-64 bg-primary-200/40 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-200/40 rounded-full blur-3xl"></div>

                    <div class="relative px-8 py-12 sm:px-12 lg:px-16 lg:py-16 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
                        <div class="max-w-xl">
                            <h2 class="text-2xl sm:text-3xl font-bold text-surface-900 dark:text-white mb-4">Start Selling Today</h2>
                            <p class="text-surface-600 dark:text-surface-400 text-lg">Join thousands of creators earning money from their digital products and services. Zero setup fees, powerful tools, and instant payouts.</p>
                            <div class="mt-6 flex flex-wrap gap-6">
                                <div class="flex items-center gap-2 text-surface-700 dark:text-surface-300">
                                    <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    Free to join
                                </div>
                                <div class="flex items-center gap-2 text-surface-700 dark:text-surface-300">
                                    <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    Low fees
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
                                    <a href="{{ route('seller.dashboard') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/25">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                                        </svg>
                                        Go to Dashboard
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
                            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-white border border-surface-200 text-surface-700 font-semibold hover:bg-surface-50 hover:border-surface-300 transition-colors shadow-sm">
                                Browse Products
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
