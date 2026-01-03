<x-layouts.app title="About Us - Codexse">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-500 to-accent-500 py-20">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z\" fill=\"rgba(255,255,255,0.07)\"%3E%3C/path%3E%3C/svg%3E')] opacity-50"></div>
        <div class="relative mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-white sm:text-5xl">About Codexse</h1>
            <p class="mt-6 text-xl text-primary-100">The premier marketplace for premium digital assets. We connect talented creators with millions of customers worldwide, empowering businesses and individuals to build amazing projects.</p>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-20 bg-white dark:bg-surface-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-2 lg:gap-16 items-center">
                <div>
                    <span class="inline-block px-3 py-1 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs font-semibold uppercase tracking-wider mb-4">Our Mission</span>
                    <h2 class="text-3xl font-bold text-surface-900 dark:text-white mb-6">Empowering Creativity, Enabling Success</h2>
                    <p class="text-lg text-surface-600 dark:text-surface-400 mb-4">
                        At Codexse, we believe exceptional design and code should be accessible to everyone. Our mission is to build a thriving ecosystem where creators can share their best work with the world, and customers can find the perfect digital assets to bring their visions to life.
                    </p>
                    <p class="text-lg text-surface-600 dark:text-surface-400 mb-4">
                        We've created a platform that makes it simple for designers, developers, and digital artists to monetize their creativity while maintaining the highest quality standards. Every product on our marketplace is carefully reviewed to ensure it meets our strict criteria for design excellence and technical quality.
                    </p>
                    <p class="text-lg text-surface-600 dark:text-surface-400">
                        For our customers, we provide a curated selection of premium templates, UI kits, icons, illustrations, themes, and code that accelerate projects and elevate results.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-2xl p-6 text-center">
                        <div class="text-4xl font-bold text-primary-600 dark:text-primary-400">10K+</div>
                        <div class="mt-2 text-surface-600 dark:text-surface-400">Digital Products</div>
                    </div>
                    <div class="bg-gradient-to-br from-accent-50 to-accent-100 dark:from-accent-900/20 dark:to-accent-800/20 rounded-2xl p-6 text-center">
                        <div class="text-4xl font-bold text-accent-600 dark:text-accent-400">50K+</div>
                        <div class="mt-2 text-surface-600 dark:text-surface-400">Happy Customers</div>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 rounded-2xl p-6 text-center">
                        <div class="text-4xl font-bold text-emerald-600 dark:text-emerald-400">2.5K+</div>
                        <div class="mt-2 text-surface-600 dark:text-surface-400">Active Sellers</div>
                    </div>
                    <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 rounded-2xl p-6 text-center">
                        <div class="text-4xl font-bold text-amber-600 dark:text-amber-400">150+</div>
                        <div class="mt-2 text-surface-600 dark:text-surface-400">Countries Served</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- What We Offer -->
    <section class="py-20 bg-surface-50 dark:bg-surface-800/50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-3 py-1 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs font-semibold uppercase tracking-wider mb-4">What We Offer</span>
                <h2 class="text-3xl font-bold text-surface-900 dark:text-white">Premium Digital Assets for Every Project</h2>
                <p class="mt-4 text-lg text-surface-600 dark:text-surface-400 max-w-2xl mx-auto">From stunning UI kits to production-ready code, we offer everything you need to build exceptional digital experiences.</p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach([
                    [
                        'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z',
                        'title' => 'UI Kits & Templates',
                        'description' => 'Professional design systems, website templates, and UI components for web and mobile applications.',
                        'bgClass' => 'bg-primary-100 dark:bg-primary-900/30',
                        'textClass' => 'text-primary-600 dark:text-primary-400',
                    ],
                    [
                        'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
                        'title' => 'Code & Scripts',
                        'description' => 'Production-ready code snippets, plugins, and full applications in popular frameworks and languages.',
                        'bgClass' => 'bg-cyan-100 dark:bg-cyan-900/30',
                        'textClass' => 'text-cyan-600 dark:text-cyan-400',
                    ],
                    [
                        'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
                        'title' => 'Graphics & Illustrations',
                        'description' => 'Icons, illustrations, mockups, and graphic elements to enhance your visual designs.',
                        'bgClass' => 'bg-emerald-100 dark:bg-emerald-900/30',
                        'textClass' => 'text-emerald-600 dark:text-emerald-400',
                    ],
                    [
                        'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
                        'title' => 'Themes & CMS',
                        'description' => 'WordPress themes, Shopify templates, and CMS solutions for every industry and niche.',
                        'bgClass' => 'bg-amber-100 dark:bg-amber-900/30',
                        'textClass' => 'text-amber-600 dark:text-amber-400',
                    ],
                    [
                        'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                        'title' => 'Services & Freelance',
                        'description' => 'Connect with skilled professionals for custom design, development, and consulting services.',
                        'bgClass' => 'bg-rose-100 dark:bg-rose-900/30',
                        'textClass' => 'text-rose-600 dark:text-rose-400',
                    ],
                    [
                        'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                        'title' => 'Product Bundles',
                        'description' => 'Curated collections of related products at discounted prices for maximum value.',
                        'bgClass' => 'bg-indigo-100 dark:bg-indigo-900/30',
                        'textClass' => 'text-indigo-600 dark:text-indigo-400',
                    ],
                ] as $item)
                    <div class="bg-white dark:bg-surface-800 rounded-2xl p-8 shadow-sm hover:shadow-lg transition-shadow">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl {{ $item['bgClass'] }} {{ $item['textClass'] }} mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-3">{{ $item['title'] }}</h3>
                        <p class="text-surface-600 dark:text-surface-400">{{ $item['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-20 bg-white dark:bg-surface-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-3 py-1 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs font-semibold uppercase tracking-wider mb-4">Our Values</span>
                <h2 class="text-3xl font-bold text-surface-900 dark:text-white">The Principles That Guide Us</h2>
                <p class="mt-4 text-lg text-surface-600 dark:text-surface-400">These core values shape every decision we make and define who we are.</p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    [
                        'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                        'title' => 'Quality First',
                        'description' => 'Every product is reviewed for design excellence, code quality, and technical standards before listing.',
                    ],
                    [
                        'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                        'title' => 'Creator Success',
                        'description' => 'We offer competitive revenue shares and powerful tools to help our creators build sustainable businesses.',
                    ],
                    [
                        'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                        'title' => 'Customer Focus',
                        'description' => 'Your satisfaction is our priority. We provide exceptional support and a seamless buying experience.',
                    ],
                    [
                        'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
                        'title' => 'Trust & Security',
                        'description' => 'We protect your data with enterprise-grade security and ensure safe, secure transactions.',
                    ],
                ] as $value)
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 mb-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $value['icon'] }}" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-3">{{ $value['title'] }}</h3>
                        <p class="text-surface-600 dark:text-surface-400">{{ $value['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-20 bg-surface-50 dark:bg-surface-800/50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-2 lg:gap-16 items-center">
                <div>
                    <span class="inline-block px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-4">Why Codexse</span>
                    <h2 class="text-3xl font-bold text-surface-900 dark:text-white mb-6">Built for Creators and Customers Alike</h2>

                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-surface-900 dark:text-white">Curated Quality</h3>
                                <p class="text-surface-600 dark:text-surface-400">Every product undergoes rigorous review before listing. No low-quality items.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-surface-900 dark:text-white">Fair Pricing</h3>
                                <p class="text-surface-600 dark:text-surface-400">Competitive prices with no hidden fees. Sellers keep more of what they earn.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-surface-900 dark:text-white">Lifetime Access</h3>
                                <p class="text-surface-600 dark:text-surface-400">Purchase once, download forever. Includes all future updates from the creator.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-surface-900 dark:text-white">Dedicated Support</h3>
                                <p class="text-surface-600 dark:text-surface-400">Fast, helpful support from both sellers and our team. We're here to help.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-800 rounded-2xl p-8 shadow-xl">
                    <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-6">For Creators</h3>
                    <ul class="space-y-4 text-surface-600 dark:text-surface-400 mb-8">
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Up to 85% revenue share on sales
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Global audience and marketing exposure
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Powerful analytics and seller tools
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Fast, reliable payouts worldwide
                        </li>
                    </ul>
                    <a href="{{ route('become-seller') }}" class="block w-full text-center rounded-xl bg-primary-600 px-6 py-3 font-semibold text-white hover:bg-primary-700 transition-colors">
                        Become a Seller
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-br from-primary-600 via-primary-500 to-accent-500">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to Get Started?</h2>
            <p class="text-xl text-primary-100 mb-8">Join thousands of creators and customers who trust Codexse for premium digital assets.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-8 py-4 text-base font-semibold text-primary-600 hover:bg-primary-50 transition-colors shadow-lg">
                    Explore Products
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl border-2 border-white/30 px-8 py-4 text-base font-semibold text-white hover:bg-white/10 transition-colors">
                    Create Free Account
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
