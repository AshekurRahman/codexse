<x-layouts.app title="About Us - Codexse">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-500 to-accent-500 py-20">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z\" fill=\"rgba(255,255,255,0.07)\"%3E%3C/path%3E%3C/svg%3E')] opacity-50"></div>
        <div class="relative mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-white sm:text-5xl">About Codexse</h1>
            <p class="mt-6 text-xl text-primary-100">The premier marketplace for premium digital assets, connecting talented creators with millions of customers worldwide.</p>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-20 bg-white dark:bg-surface-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-2 lg:gap-16 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-surface-900 dark:text-white mb-6">Our Mission</h2>
                    <p class="text-lg text-surface-600 dark:text-surface-400 mb-4">
                        At Codexse, we believe that great design should be accessible to everyone. Our mission is to empower creators to share their work with the world while helping businesses and individuals find the perfect digital assets for their projects.
                    </p>
                    <p class="text-lg text-surface-600 dark:text-surface-400">
                        We've built a platform that makes it easy for designers, developers, and digital artists to monetize their creativity, and for customers to discover high-quality resources that accelerate their work.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    @foreach([
                        ['value' => '10K+', 'label' => 'Digital Products'],
                        ['value' => '50K+', 'label' => 'Happy Customers'],
                        ['value' => '2.5K+', 'label' => 'Active Sellers'],
                        ['value' => '$5M+', 'label' => 'Paid to Creators'],
                    ] as $stat)
                        <div class="bg-surface-50 dark:bg-surface-800 rounded-2xl p-6 text-center">
                            <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ $stat['value'] }}</div>
                            <div class="mt-1 text-surface-600 dark:text-surface-400">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-20 bg-surface-50 dark:bg-surface-800/50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-surface-900 dark:text-white">Our Values</h2>
                <p class="mt-4 text-lg text-surface-600 dark:text-surface-400">The principles that guide everything we do</p>
            </div>

            <div class="grid gap-8 md:grid-cols-3">
                @foreach([
                    [
                        'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                        'title' => 'Quality First',
                        'description' => 'We curate our marketplace to ensure only the highest quality products are available. Every item is reviewed for design excellence and technical quality.',
                    ],
                    [
                        'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                        'title' => 'Creator Focused',
                        'description' => 'We believe creators deserve fair compensation for their work. That is why we offer industry-leading revenue shares and tools to help sellers succeed.',
                    ],
                    [
                        'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                        'title' => 'Customer Love',
                        'description' => 'Our customers are at the heart of everything we do. We are committed to providing exceptional support and a seamless purchasing experience.',
                    ],
                ] as $value)
                    <div class="bg-white dark:bg-surface-800 rounded-2xl p-8 text-center">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <!-- Story Section -->
    <section class="py-20 bg-white dark:bg-surface-900">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-surface-900 dark:text-white mb-8 text-center">Our Story</h2>
            <div class="prose prose-lg dark:prose-invert max-w-none">
                <p class="text-surface-600 dark:text-surface-400">
                    Codexse was founded in 2023 with a simple idea: make it easier for talented creators to share their digital products with the world, and help businesses find the resources they need to bring their visions to life.
                </p>
                <p class="text-surface-600 dark:text-surface-400">
                    What started as a small collection of UI kits has grown into a thriving marketplace with thousands of products across multiple categories including templates, icons, illustrations, themes, and code snippets.
                </p>
                <p class="text-surface-600 dark:text-surface-400">
                    Today, Codexse serves customers in over 150 countries and has paid out millions of dollars to our community of talented creators. We are proud to be a platform where creativity meets opportunity.
                </p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-surface-50 dark:bg-surface-800/50">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-surface-900 dark:text-white mb-4">Join Our Community</h2>
            <p class="text-lg text-surface-600 dark:text-surface-400 mb-8">Whether you are a creator looking to sell your work or a customer searching for the perfect asset, we would love to have you.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-6 py-3 text-base font-semibold text-white hover:bg-primary-700 transition-colors">
                    Explore Products
                </a>
                <a href="{{ route('become-seller') }}" class="inline-flex items-center justify-center rounded-xl border-2 border-surface-300 dark:border-surface-600 px-6 py-3 text-base font-semibold text-surface-700 dark:text-surface-200 hover:bg-surface-100 dark:hover:bg-surface-700 transition-colors">
                    Become a Seller
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
