<x-layouts.app title="Codexse - Premium Digital Marketplace">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-accent-50 dark:from-surface-950 dark:via-surface-900 dark:to-surface-900">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-primary-200/50 dark:bg-primary-900/20 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-accent-200/50 dark:bg-accent-900/20 blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-96 w-96 rounded-full bg-primary-100/30 dark:bg-primary-900/10 blur-3xl"></div>
        </div>
        <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
            <div class="text-center">
                <div class="inline-flex items-center rounded-full border border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-900/50 px-4 py-1.5 mb-6">
                    <span class="relative flex h-2 w-2 mr-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-accent-500"></span>
                    </span>
                    <span class="text-sm font-medium text-primary-700 dark:text-primary-300">New products added daily</span>
                </div>
                <h1 class="text-4xl font-extrabold tracking-tight text-surface-900 dark:text-white sm:text-5xl lg:text-6xl">
                    Discover Premium
                    <span class="bg-gradient-to-r from-primary-600 via-accent-500 to-primary-600 bg-clip-text text-transparent"> Digital Assets</span>
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-surface-600 dark:text-surface-400">
                    Thousands of high-quality UI kits, templates, icons, and design resources. Created by top designers for your next project.
                </p>
                <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <a href="{{ route('products.index') }}" class="group inline-flex items-center rounded-xl bg-gradient-to-r from-primary-600 to-primary-500 px-8 py-4 text-base font-semibold text-white shadow-lg shadow-primary-500/30 transition-all hover:shadow-xl hover:shadow-primary-500/40 hover:-translate-y-0.5">
                        Explore Products
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a href="{{ route('become-seller') }}" class="inline-flex items-center rounded-xl border-2 border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-8 py-4 text-base font-semibold text-surface-700 dark:text-surface-200 transition-all hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-lg hover:-translate-y-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Become a Seller
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="mt-12 mx-auto max-w-2xl">
                    <form action="{{ route('products.index') }}" method="GET" class="relative">
                        <input type="text" name="search" placeholder="Search for UI kits, templates, icons..."
                            class="w-full rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 pl-14 pr-6 py-4 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20 transition-all shadow-lg shadow-surface-200/50 dark:shadow-surface-900/50">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="bg-white dark:bg-surface-900 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-surface-900 dark:text-white">Browse Categories</h2>
                <p class="mt-3 text-surface-600 dark:text-surface-400">Find the perfect assets for your project</p>
            </div>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
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
    <section class="bg-surface-50 dark:bg-surface-800/50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-surface-900 dark:text-white">Featured Products</h2>
                    <p class="mt-3 text-surface-600 dark:text-surface-400">Hand-picked premium assets</p>
                </div>
                <a href="{{ route('products.index') }}?featured=1" class="hidden sm:inline-flex items-center text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                    View all featured
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
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

    <!-- Trending Products Section -->
    <section class="bg-white dark:bg-surface-900 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-12">
                <div>
                    <div class="inline-flex items-center rounded-full bg-warning-100 dark:bg-warning-900/30 px-3 py-1 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-warning-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                        </svg>
                        <span class="text-xs font-semibold text-warning-600 dark:text-warning-400">Trending</span>
                    </div>
                    <h2 class="text-3xl font-bold text-surface-900 dark:text-white">Popular Right Now</h2>
                    <p class="mt-3 text-surface-600 dark:text-surface-400">Most viewed products this week</p>
                </div>
                <a href="{{ route('products.index') }}?sort=popular" class="hidden sm:inline-flex items-center text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                    View all trending
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @forelse($trendingProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    @for($i = 0; $i < 4; $i++)
                        <x-product-card />
                    @endfor
                @endforelse
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="relative overflow-hidden bg-gradient-to-r from-primary-600 via-primary-500 to-accent-500 py-20">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z\" fill=\"rgba(255,255,255,0.07)\"%3E%3C/path%3E%3C/svg%3E')] opacity-50"></div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 lg:grid-cols-4">
                @foreach([
                    ['value' => '10,000+', 'label' => 'Digital Products', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    ['value' => '50,000+', 'label' => 'Happy Customers', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                    ['value' => '2,500+', 'label' => 'Verified Sellers', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                    ['value' => '$5M+', 'label' => 'Paid to Creators', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ] as $stat)
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-white/20 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}" />
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-white sm:text-4xl">{{ $stat['value'] }}</div>
                        <div class="mt-2 text-primary-100">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-white dark:bg-surface-900 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-3xl bg-surface-900 dark:bg-surface-800">
                <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 to-accent-600/20"></div>
                <div class="absolute top-0 right-0 -mt-20 -mr-20 h-80 w-80 rounded-full bg-primary-500/30 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-20 -ml-20 h-80 w-80 rounded-full bg-accent-500/30 blur-3xl"></div>
                <div class="relative px-8 py-16 sm:px-16 lg:flex lg:items-center lg:justify-between lg:py-20">
                    <div>
                        <h2 class="text-3xl font-bold text-white sm:text-4xl">
                            Ready to start selling?
                        </h2>
                        <p class="mt-4 max-w-xl text-lg text-surface-300">
                            Join thousands of creators earning money from their digital products. Set up your store in minutes.
                        </p>
                    </div>
                    <div class="mt-10 lg:mt-0 lg:shrink-0">
                        <a href="{{ route('become-seller') }}" class="group inline-flex items-center rounded-xl bg-white px-8 py-4 text-base font-semibold text-surface-900 shadow-lg transition-all hover:bg-primary-50 hover:-translate-y-0.5">
                            Start Selling Today
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
