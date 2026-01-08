@php
    use App\Models\HomepageSection;
    use App\Models\HomepageStat;
    use App\Models\HowItWorksStep;

    $heroSection = HomepageSection::getSection('hero');
    $ctaSection = HomepageSection::getSection('cta_seller');
    $homepageStats = HomepageStat::getForHomepage();
    $howItWorksSteps = HowItWorksStep::getForHomepage();
@endphp

<x-layouts.app title="Codexse - Premium Digital Marketplace">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
        <!-- Decorative Background Elements -->
        <div class="absolute inset-0">
            <!-- Soft gradient orbs -->
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-gradient-to-br from-primary-200/40 to-primary-300/30 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-gradient-to-br from-accent-200/40 to-pink-200/30 rounded-full blur-3xl"></div>
            <div class="absolute top-1/3 right-1/3 w-64 h-64 bg-gradient-to-br from-cyan-200/30 to-blue-200/30 rounded-full blur-3xl"></div>
            <!-- Subtle grid pattern -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%236366f1\" fill-opacity=\"0.03\"%3E%3Cpath d=\"M0 0h40v40H0z\"/%3E%3C/g%3E%3Cpath stroke=\"%236366f1\" stroke-opacity=\"0.04\" d=\"M0 0v40M40 0v40M0 0h40M0 40h40\"/%3E%3C/g%3E%3C/svg%3E')]"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
            <div class="text-center">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 rounded-full bg-white/80 backdrop-blur-sm border border-primary-200 shadow-sm px-4 py-2 mb-8">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    <span class="text-sm font-medium text-surface-700">{{ $heroSection?->badge_text ?? '10,000+ Digital Assets Available' }}</span>
                </div>

                <h1 class="text-4xl font-extrabold tracking-tight text-surface-900 sm:text-5xl lg:text-6xl xl:text-7xl">
                    {{ $heroSection?->title ?? 'Your One-Stop' }}
                    <span class="block bg-gradient-to-r from-primary-600 via-accent-500 to-cyan-500 bg-clip-text text-transparent">{{ $heroSection?->subtitle ?? 'Digital Marketplace' }}</span>
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-surface-600 sm:text-xl">
                    {{ $heroSection?->description ?? 'Buy premium products, hire expert freelancers, or find your next project. Everything you need to build, grow, and succeed.' }}
                </p>

                <!-- Search Box -->
                <div class="mt-10 mx-auto max-w-3xl">
                    <form action="{{ route('products.index') }}" method="GET" class="relative">
                        <div class="flex flex-col sm:flex-row gap-3 bg-white/90 backdrop-blur-sm border border-surface-200 shadow-xl shadow-surface-200/50 rounded-2xl p-2">
                            <div class="relative flex-1">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" name="search" placeholder="Search products, services, or jobs..."
                                    class="w-full rounded-xl border-0 bg-surface-50 pl-12 pr-4 py-4 text-surface-900 placeholder-surface-500 focus:ring-2 focus:ring-primary-500 focus:bg-white transition-colors">
                            </div>
                            <button type="submit" class="rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-8 py-4 font-semibold text-white shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 transition-all hover:scale-105">
                                Search
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Quick Links -->
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white hover:bg-indigo-50 border border-indigo-200 text-indigo-600 text-sm font-medium transition-all shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Products
                    </a>
                    <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white hover:bg-primary-50 border border-primary-200 text-primary-600 text-sm font-medium transition-all shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Services
                    </a>
                    <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white hover:bg-accent-50 border border-accent-200 text-accent-600 text-sm font-medium transition-all shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Jobs
                    </a>
                    <a href="{{ route('sellers.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white hover:bg-emerald-50 border border-emerald-200 text-emerald-600 text-sm font-medium transition-all shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Sellers
                    </a>
                </div>
            </div>
        </div>

    </section>

    <!-- Stats Marquee -->
    @if($homepageStats->count() > 0)
    <section class="relative bg-surface-900 dark:bg-surface-950 py-4 overflow-hidden">
        <div class="flex animate-marquee whitespace-nowrap">
            @for($i = 0; $i < 2; $i++)
            <div class="flex items-center gap-12 mx-6">
                @foreach($homepageStats as $stat)
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-{{ $stat->color }}-500"></div>
                    <span class="text-white font-semibold">{{ $stat->display_value }} {{ $stat->label }}</span>
                </div>
                @endforeach
            </div>
            @endfor
        </div>
    </section>
    @endif

    <!-- Trust Badges -->
    <x-trust-badges />

    <!-- Categories Section - Redesigned -->
    @if($categories->count() > 0)
    <section class="bg-white dark:bg-surface-900 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-12" x-scroll-animate>
                <div>
                    <span class="inline-block px-3 py-1 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs font-semibold uppercase tracking-wider mb-3">Explore</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-surface-900 dark:text-white">Browse by Category</h2>
                    <p class="mt-2 text-surface-600 dark:text-surface-400 max-w-xl">Discover thousands of digital assets organized by category</p>
                </div>
                <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-2 text-primary-600 dark:text-primary-400 font-semibold hover:gap-3 transition-all">
                    All Categories
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($categories as $index => $category)
                    <a href="{{ route('categories.show', $category) }}" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-surface-50 to-surface-100 dark:from-surface-800 dark:to-surface-700 p-6 transition-all duration-300 hover:shadow-2xl hover:shadow-primary-500/10 hover:-translate-y-2 border border-surface-200 dark:border-surface-700 hover:border-primary-300 dark:hover:border-primary-700" x-scroll-animate.delay="{{ $index * 50 }}">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-primary-500/10 to-accent-500/10 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center mb-4 shadow-lg shadow-primary-500/20 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                <x-category-icon :icon="$category->icon ?? 'default'" class="w-7 h-7 text-white" />
                            </div>
                            <h3 class="font-bold text-surface-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $category->name }}</h3>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">{{ $category->products_count ?? 0 }} items</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Featured Products - Redesigned -->
    <section class="relative bg-gradient-to-b from-surface-50 to-white dark:from-surface-800/50 dark:to-surface-900 py-20 overflow-hidden">
        <div class="absolute top-0 left-0 w-72 h-72 bg-primary-500/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-accent-500/5 rounded-full blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-12" x-scroll-animate>
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gradient-to-r from-amber-100 to-yellow-100 dark:from-amber-900/30 dark:to-yellow-900/30 mb-3">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="text-xs font-bold text-amber-700 dark:text-amber-300 uppercase tracking-wider">Featured</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-surface-900 dark:text-white">Premium Products</h2>
                    <p class="mt-2 text-surface-600 dark:text-surface-400">Hand-picked digital assets loved by creators</p>
                </div>
                <a href="{{ route('products.index') }}?featured=1" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-surface-900 dark:bg-white text-white dark:text-surface-900 font-semibold hover:bg-surface-800 dark:hover:bg-surface-100 transition-all shadow-lg hover:shadow-xl">
                    View All
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    @for($i = 0; $i < 4; $i++)
                        <x-product-card />
                    @endfor
                @endforelse
            </div>
        </div>
    </section>

    <!-- Featured Sellers Section -->
    <section class="bg-white dark:bg-surface-900 py-20 border-t border-surface-100 dark:border-surface-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-12" x-scroll-animate>
                <div>
                    <span class="inline-block px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-3">Top Creators</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-surface-900 dark:text-white">Featured Sellers</h2>
                    <p class="mt-2 text-surface-600 dark:text-surface-400 max-w-xl">Meet our top-rated sellers creating amazing digital products</p>
                </div>
                <a href="{{ route('sellers.index') }}" class="inline-flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-semibold hover:gap-3 transition-all">
                    View All Sellers
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @forelse($featuredSellers ?? [] as $index => $seller)
                    <div x-scroll-animate.delay="{{ $index * 100 }}">
                        <x-seller-card-compact :seller="$seller" />
                    </div>
                @empty
                    @for($i = 0; $i < 6; $i++)
                        <div class="card p-4 text-center" x-scroll-animate.delay="{{ $i * 100 }}">
                            <div class="w-16 h-16 rounded-full skeleton-shimmer mx-auto mb-3"></div>
                            <div class="h-4 skeleton-shimmer w-24 mx-auto rounded mb-2"></div>
                            <div class="h-3 skeleton-shimmer w-16 mx-auto rounded mb-3"></div>
                            <div class="flex justify-center gap-4">
                                <div class="h-3 skeleton-shimmer w-12 rounded"></div>
                                <div class="h-3 skeleton-shimmer w-12 rounded"></div>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>
        </div>
    </section>

    <!-- Services Section - Redesigned with Bento Grid -->
    <section class="bg-surface-50 dark:bg-surface-800 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Left Column - Header -->
                <div class="lg:row-span-2">
                    <div class="sticky top-24">
                        <span class="inline-block px-3 py-1 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs font-semibold uppercase tracking-wider mb-4">Freelance Services</span>
                        <h2 class="text-3xl md:text-4xl font-bold text-surface-900 dark:text-white mb-4">Hire Expert Freelancers</h2>
                        <p class="text-surface-600 dark:text-surface-400 mb-8">Get your projects done by skilled professionals. From design to development, find the perfect freelancer for your needs.</p>

                        <div class="space-y-4 mb-8">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-success-100 dark:bg-success-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-surface-700 dark:text-surface-300 font-medium">Vetted Professionals</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-success-100 dark:bg-success-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-surface-700 dark:text-surface-300 font-medium">Secure Escrow Payments</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-success-100 dark:bg-success-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-surface-700 dark:text-surface-300 font-medium">Money-Back Guarantee</span>
                            </div>
                        </div>

                        <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/25 hover:shadow-xl hover:shadow-primary-500/30">
                            Browse Services
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Right Column - Service Cards -->
                <div class="lg:col-span-2 grid sm:grid-cols-2 gap-4">
                    @forelse($featuredServices ?? [] as $service)
                        <x-service-card :service="$service" />
                    @empty
                        @for($i = 0; $i < 4; $i++)
                            <div class="group rounded-2xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800 overflow-hidden hover:shadow-xl hover:border-primary-300 dark:hover:border-primary-700 transition-all duration-300">
                                <div class="aspect-[16/10] bg-gradient-to-br from-surface-100 to-surface-200 dark:from-surface-700 dark:to-surface-600"></div>
                                <div class="p-5">
                                    <div class="h-5 bg-surface-200 dark:bg-surface-700 rounded-lg w-3/4 mb-3"></div>
                                    <div class="h-4 bg-surface-200 dark:bg-surface-700 rounded w-1/2"></div>
                                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                                        <div class="h-4 bg-surface-200 dark:bg-surface-700 rounded w-20"></div>
                                        <div class="h-6 bg-surface-200 dark:bg-surface-700 rounded w-16"></div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- Jobs Section - Redesigned -->
    <section class="relative bg-gradient-to-br from-accent-600 via-primary-600 to-primary-700 py-20 overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-block px-4 py-1.5 rounded-full bg-white/20 text-white text-xs font-bold uppercase tracking-wider mb-4">Find Work</span>
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Latest Job Opportunities</h2>
                <p class="text-white/80 max-w-2xl mx-auto">Find freelance projects that match your skills and start earning today</p>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-10">
                @forelse($recentJobs ?? [] as $job)
                    <div class="group bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20 p-6 hover:bg-white/20 transition-all duration-300">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-white text-lg mb-1 truncate group-hover:text-accent-200 transition-colors">{{ $job->title }}</h3>
                                <p class="text-white/70 text-sm mb-3 line-clamp-2">{{ Str::limit(strip_tags($job->description), 100) }}</p>
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="inline-flex items-center gap-1 text-sm text-white/80">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $job->budget_range ?? '$500-1000' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-sm text-white/80">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $job->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('jobs.show', $job) }}" class="shrink-0 w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center hover:bg-white/30 transition-colors">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    @for($i = 0; $i < 4; $i++)
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20 p-6">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-white/20 shrink-0"></div>
                                <div class="flex-1">
                                    <div class="h-5 bg-white/20 rounded w-3/4 mb-3"></div>
                                    <div class="h-4 bg-white/20 rounded w-full mb-2"></div>
                                    <div class="h-4 bg-white/20 rounded w-1/2"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-white text-primary-600 font-bold hover:bg-surface-100 transition-all shadow-xl hover:shadow-2xl">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Browse All Jobs
                </a>
                <a href="{{ route('jobs.create') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-white/20 text-white font-bold border-2 border-white/30 hover:bg-white/30 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Post a Job
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works - Redesigned -->
    @if($howItWorksSteps->count() > 0)
    @php
        $stepIconSvgs = [
            'magnifying-glass' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
            'shopping-cart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>',
            'shield-check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
            'credit-card' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
            'arrow-down-tray' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>',
            'check-circle' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            'rocket-launch' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>',
            'cursor-arrow-rays' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>',
            'document-text' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
            'user-plus' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>',
            'chat-bubble-left-right' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12V9a2 2 0 00-2-2H6a2 2 0 00-2 2v12l4-4h10a2 2 0 002-2v-3z"/>',
            'cog-6-tooth' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
            'sparkles' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>',
            'hand-thumb-up' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>',
            'gift' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>',
            'truck' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>',
            'star' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
            'bolt' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
        ];
        $colorOrder = ['primary', 'accent', 'success', 'warning', 'info', 'danger'];
    @endphp
    <section class="bg-white dark:bg-surface-900 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-3 py-1 rounded-full bg-surface-100 dark:bg-surface-800 text-surface-600 dark:text-surface-400 text-xs font-semibold uppercase tracking-wider mb-4">Simple Process</span>
                <h2 class="text-3xl md:text-4xl font-bold text-surface-900 dark:text-white">How It Works</h2>
                <p class="mt-3 text-surface-600 dark:text-surface-400 max-w-2xl mx-auto">Get started in just a few simple steps</p>
            </div>

            <div class="grid md:grid-cols-{{ $howItWorksSteps->count() }} gap-8 relative">
                @if($howItWorksSteps->count() > 1)
                <!-- Connection Line -->
                <div class="hidden md:block absolute top-16 left-1/4 right-1/4 h-0.5 bg-gradient-to-r from-{{ $howItWorksSteps->first()->icon_color }}-500 via-{{ $howItWorksSteps->skip(1)->first()->icon_color ?? 'accent' }}-500 to-{{ $howItWorksSteps->last()->icon_color }}-500"></div>
                @endif

                @foreach($howItWorksSteps as $step)
                <div class="relative text-center group">
                    <div class="relative inline-flex">
                        <div class="w-32 h-32 rounded-3xl bg-gradient-to-br from-{{ $step->icon_color }}-500 to-{{ $step->icon_color }}-600 flex items-center justify-center shadow-2xl shadow-{{ $step->icon_color }}-500/30 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                            <svg class="w-14 h-14 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                {!! $stepIconSvgs[$step->icon] ?? $stepIconSvgs['check-circle'] !!}
                            </svg>
                        </div>
                        <div class="absolute -top-2 -right-2 w-10 h-10 rounded-full bg-surface-900 dark:bg-white text-white dark:text-surface-900 flex items-center justify-center font-bold text-lg shadow-lg">{{ $step->step_number }}</div>
                    </div>
                    <h3 class="text-xl font-bold text-surface-900 dark:text-white mt-8 mb-3">{{ $step->title }}</h3>
                    <p class="text-surface-600 dark:text-surface-400">{{ $step->description }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Recently Viewed -->
    @if(isset($recentlyViewed) && $recentlyViewed->count() > 0)
        <x-recently-viewed :products="$recentlyViewed" title="Continue Browsing" />
    @endif

    <!-- Testimonials Section -->
    <x-testimonials-carousel />

    <!-- CTA Section - Redesigned -->
    @php
        $ctaBenefits = $ctaSection?->metadata['benefits'] ?? ['Free to join', 'Low fees', 'Fast payouts'];
        $ctaStats = $ctaSection?->metadata['stats'] ?? [
            ['value' => '$2M+', 'label' => 'Paid to creators'],
            ['value' => '2,500+', 'label' => 'Active sellers'],
            ['value' => '50K+', 'label' => 'Happy customers'],
            ['value' => '4.9', 'label' => 'Average rating'],
        ];
    @endphp
    <section class="bg-surface-50 dark:bg-surface-800 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-[2.5rem] bg-surface-900 dark:bg-surface-950">
                <!-- Background Elements -->
                <div class="absolute inset-0">
                    <div class="absolute top-0 left-0 w-96 h-96 bg-primary-500/30 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute bottom-0 right-0 w-96 h-96 bg-accent-500/30 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
                    <div class="absolute inset-0 bg-gradient-to-br from-primary-600/10 via-transparent to-accent-600/10"></div>
                </div>

                <div class="relative px-8 py-16 sm:px-16 lg:py-24">
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <span class="inline-block px-4 py-1.5 rounded-full bg-white/10 text-white/80 text-xs font-bold uppercase tracking-wider mb-6">{{ $ctaSection?->badge_text ?? 'Start Earning' }}</span>
                            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight">
                                {{ $ctaSection?->title ?? 'Ready to turn your skills into' }}
                                <span class="bg-gradient-to-r from-primary-400 to-accent-400 bg-clip-text text-transparent">income?</span>
                            </h2>
                            <p class="text-lg text-surface-300 mb-8">
                                {{ $ctaSection?->description ?? 'Join thousands of creators earning money from their digital products and services. Set up your store in minutes and start selling today.' }}
                            </p>

                            <div class="grid sm:grid-cols-{{ count($ctaBenefits) }} gap-4 mb-10">
                                @foreach($ctaBenefits as $benefit)
                                <div class="flex items-center gap-3 text-surface-300">
                                    <div class="w-10 h-10 rounded-xl bg-success-500/20 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-success-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium">{{ $benefit }}</span>
                                </div>
                                @endforeach
                            </div>

                            <a href="{{ $ctaSection?->button_url ?? route('seller.apply') }}" class="group inline-flex items-center gap-3 px-8 py-4 rounded-2xl bg-white text-surface-900 font-bold text-lg shadow-2xl hover:shadow-white/20 transition-all hover:-translate-y-1">
                                {{ $ctaSection?->button_text ?? 'Start Selling Today' }}
                                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($ctaStats as $stat)
                            <div class="p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                                <div class="text-4xl font-bold text-white mb-2">{{ $stat['value'] }}</div>
                                <div class="text-surface-400">{{ $stat['label'] }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('styles')
    <style>
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            animation: marquee 30s linear infinite;
        }
    </style>
    @endpush
</x-layouts.app>
