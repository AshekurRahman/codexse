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
                    <span class="text-sm font-medium text-surface-700">10,000+ Digital Assets Available</span>
                </div>

                <h1 class="text-4xl font-extrabold tracking-tight text-surface-900 sm:text-5xl lg:text-6xl xl:text-7xl">
                    Your One-Stop
                    <span class="block bg-gradient-to-r from-primary-600 via-accent-500 to-cyan-500 bg-clip-text text-transparent">Digital Marketplace</span>
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-surface-600 sm:text-xl">
                    Buy premium products, hire expert freelancers, or find your next project. Everything you need to build, grow, and succeed.
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

    <!-- Trust Badges -->
    <section class="bg-white dark:bg-surface-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 py-8">
                <div class="flex items-center gap-4 p-4 rounded-xl bg-surface-50 dark:bg-surface-800">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-surface-900 dark:text-white">10,000+</div>
                        <div class="text-sm text-surface-500 dark:text-surface-400">Digital Products</div>
                    </div>
                </div>
                <div class="flex items-center gap-4 p-4 rounded-xl bg-surface-50 dark:bg-surface-800">
                    <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-surface-900 dark:text-white">5,000+</div>
                        <div class="text-sm text-surface-500 dark:text-surface-400">Services</div>
                    </div>
                </div>
                <div class="flex items-center gap-4 p-4 rounded-xl bg-surface-50 dark:bg-surface-800">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-surface-900 dark:text-white">2,500+</div>
                        <div class="text-sm text-surface-500 dark:text-surface-400">Verified Sellers</div>
                    </div>
                </div>
                <div class="flex items-center gap-4 p-4 rounded-xl bg-surface-50 dark:bg-surface-800">
                    <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-surface-900 dark:text-white">100%</div>
                        <div class="text-sm text-surface-500 dark:text-surface-400">Secure Payments</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="bg-white dark:bg-surface-900 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-surface-900 dark:text-white">Browse Categories</h2>
                <p class="mt-3 text-surface-600 dark:text-surface-400">Find the perfect assets for your project</p>
            </div>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                @forelse($categories as $category)
                    <a href="{{ route('categories.show', $category) }}" class="group relative overflow-hidden rounded-2xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800 p-6 transition-all hover:border-primary-500 hover:shadow-xl hover:-translate-y-1">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-accent-500 text-white shadow-lg shadow-primary-500/30 mb-4">
                            <x-category-icon :icon="$category->icon" class="h-7 w-7" />
                        </div>
                        <h3 class="font-semibold text-surface-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $category->name }}</h3>
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">{{ $category->products_count }} products</p>
                    </a>
                @empty
                    @foreach(['UI Kits', 'Templates', 'Icons', 'Illustrations', 'Themes', 'Code'] as $name)
                        <div class="group relative overflow-hidden rounded-2xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800 p-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-accent-500 text-white shadow-lg shadow-primary-500/30 mb-4">
                                <x-category-icon class="h-7 w-7" />
                            </div>
                            <h3 class="font-semibold text-surface-900 dark:text-white">{{ $name }}</h3>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">0 products</p>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="bg-surface-50 dark:bg-surface-800/50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-indigo-100 dark:bg-indigo-900/30 px-3 py-1 mb-3">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="text-xs font-semibold text-indigo-700 dark:text-indigo-300">Featured</span>
                    </div>
                    <h2 class="text-3xl font-bold text-surface-900 dark:text-white">Premium Products</h2>
                    <p class="mt-2 text-surface-600 dark:text-surface-400">Hand-picked digital assets for your next project</p>
                </div>
                <a href="{{ route('products.index') }}?featured=1" class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                    View all products
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @forelse($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    @for($i = 0; $i < 4; $i++)
                        <x-product-card />
                    @endfor
                @endforelse
            </div>
            <div class="mt-8 text-center sm:hidden">
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-600 dark:text-primary-400">
                    View all products
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Services Section -->
    <section class="bg-white dark:bg-surface-900 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-primary-100 dark:bg-primary-900/30 px-3 py-1 mb-3">
                        <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs font-semibold text-primary-700 dark:text-primary-300">Services</span>
                    </div>
                    <h2 class="text-3xl font-bold text-surface-900 dark:text-white">Hire Expert Freelancers</h2>
                    <p class="mt-2 text-surface-600 dark:text-surface-400">Get your projects done by skilled professionals</p>
                </div>
                <a href="{{ route('services.index') }}" class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                    Browse all services
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @forelse($featuredServices ?? [] as $service)
                    <x-service-card :service="$service" />
                @empty
                    @for($i = 0; $i < 4; $i++)
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                            <div class="aspect-video bg-surface-100 dark:bg-surface-700 animate-pulse"></div>
                            <div class="p-5">
                                <div class="h-4 bg-surface-100 dark:bg-surface-700 rounded animate-pulse mb-3"></div>
                                <div class="h-3 bg-surface-100 dark:bg-surface-700 rounded animate-pulse w-2/3"></div>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>
            <div class="mt-8 text-center sm:hidden">
                <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-600 dark:text-primary-400">
                    Browse all services
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Latest Jobs Section -->
    <section class="bg-surface-50 dark:bg-surface-800/50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-accent-100 dark:bg-accent-900/30 px-3 py-1 mb-3">
                        <svg class="w-4 h-4 text-accent-600 dark:text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs font-semibold text-accent-700 dark:text-accent-300">Find Work</span>
                    </div>
                    <h2 class="text-3xl font-bold text-surface-900 dark:text-white">Latest Job Opportunities</h2>
                    <p class="mt-2 text-surface-600 dark:text-surface-400">Find freelance projects that match your skills</p>
                </div>
                <a href="{{ route('jobs.index') }}" class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                    View all jobs
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                @forelse($recentJobs ?? [] as $job)
                    <x-job-card :job="$job" />
                @empty
                    @for($i = 0; $i < 4; $i++)
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <div class="flex gap-4">
                                <div class="h-12 w-12 rounded-xl bg-surface-100 dark:bg-surface-700 animate-pulse shrink-0"></div>
                                <div class="flex-1">
                                    <div class="h-5 bg-surface-100 dark:bg-surface-700 rounded animate-pulse mb-2 w-3/4"></div>
                                    <div class="h-4 bg-surface-100 dark:bg-surface-700 rounded animate-pulse w-1/2"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>
            <div class="mt-8 text-center">
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-accent-600 px-6 py-3 text-sm font-semibold text-white hover:bg-accent-700 transition-colors shadow-lg shadow-accent-600/25">
                        Browse All Jobs
                    </a>
                    <a href="{{ route('jobs.create') }}" class="inline-flex items-center gap-2 rounded-xl border-2 border-accent-500 px-6 py-3 text-sm font-semibold text-accent-600 dark:text-accent-400 hover:bg-accent-50 dark:hover:bg-accent-900/20 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Post a Job
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="bg-white dark:bg-surface-900 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-surface-900 dark:text-white">How It Works</h2>
                <p class="mt-3 text-surface-600 dark:text-surface-400">Get started in just a few simple steps</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-primary-500/30">
                        <span class="text-2xl font-bold text-white">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-3">Browse & Discover</h3>
                    <p class="text-surface-600 dark:text-surface-400">Explore our vast marketplace of digital products, services, and job opportunities.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-accent-500 to-accent-600 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-accent-500/30">
                        <span class="text-2xl font-bold text-white">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-3">Purchase Securely</h3>
                    <p class="text-surface-600 dark:text-surface-400">Pay with confidence using our secure escrow system. Your money is protected until you're satisfied.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-500/30">
                        <span class="text-2xl font-bold text-white">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-3">Download & Enjoy</h3>
                    <p class="text-surface-600 dark:text-surface-400">Get instant access to your purchases with lifetime updates and dedicated support.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Recently Viewed Products -->
    @if(isset($recentlyViewed) && $recentlyViewed->count() > 0)
        <x-recently-viewed :products="$recentlyViewed" title="Continue Browsing" />
    @endif

    <!-- CTA Section -->
    <section class="bg-white dark:bg-surface-900 pb-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-surface-900 to-surface-800 dark:from-surface-800 dark:to-surface-700">
                <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 to-accent-600/20"></div>
                <div class="absolute top-0 right-0 -mt-20 -mr-20 h-80 w-80 rounded-full bg-primary-500/30 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-20 -ml-20 h-80 w-80 rounded-full bg-accent-500/30 blur-3xl"></div>
                <div class="relative px-8 py-16 sm:px-16 lg:flex lg:items-center lg:justify-between lg:py-20">
                    <div>
                        <h2 class="text-3xl font-bold text-white sm:text-4xl">
                            Ready to start selling?
                        </h2>
                        <p class="mt-4 max-w-xl text-lg text-surface-300">
                            Join thousands of creators earning money from their digital products and services. Set up your store in minutes.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-4">
                            <div class="flex items-center gap-2 text-surface-300">
                                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Free to join
                            </div>
                            <div class="flex items-center gap-2 text-surface-300">
                                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Low commission
                            </div>
                            <div class="flex items-center gap-2 text-surface-300">
                                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Fast payouts
                            </div>
                        </div>
                    </div>
                    <div class="mt-10 lg:mt-0 lg:shrink-0">
                        <a href="{{ route('seller.apply') }}" class="group inline-flex items-center rounded-xl bg-white px-8 py-4 text-base font-semibold text-surface-900 shadow-lg transition-all hover:bg-primary-50 hover:-translate-y-0.5 hover:shadow-xl">
                            Start Selling Today
                            <svg class="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
