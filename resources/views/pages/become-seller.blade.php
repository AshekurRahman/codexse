<x-layouts.app title="Become a Seller - Codexse">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-500 to-accent-500 py-20 lg:py-28">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z\" fill=\"rgba(255,255,255,0.07)\"%3E%3C/path%3E%3C/svg%3E')] opacity-50"></div>
        <div class="absolute top-0 right-0 -mt-40 -mr-40 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-40 -ml-40 h-96 w-96 rounded-full bg-accent-400/20 blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="inline-flex items-center rounded-full bg-white/20 px-4 py-1.5 mb-6">
                    <svg class="w-4 h-4 text-white mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium text-white">Join 2,500+ successful creators</span>
                </div>
                <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Turn Your Creativity Into
                    <span class="block">Passive Income</span>
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-primary-100">
                    Sell your digital products to thousands of customers worldwide. UI kits, templates, icons, themes, code snippets, and more.
                </p>
                <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    @auth
                        @if(auth()->user()->seller)
                            @if(auth()->user()->seller->status === 'approved')
                                <a href="{{ route('seller.dashboard') }}" class="inline-flex items-center rounded-xl bg-white px-8 py-4 text-base font-semibold text-primary-600 shadow-lg transition-all hover:bg-primary-50 hover:-translate-y-0.5">
                                    Go to Seller Dashboard
                                    <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </a>
                            @elseif(auth()->user()->seller->status === 'pending')
                                <a href="{{ route('seller.pending') }}" class="inline-flex items-center rounded-xl bg-white/20 backdrop-blur px-8 py-4 text-base font-semibold text-white border border-white/30">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Application Under Review
                                </a>
                            @else
                                <a href="{{ route('seller.apply') }}" class="inline-flex items-center rounded-xl bg-white px-8 py-4 text-base font-semibold text-primary-600 shadow-lg transition-all hover:bg-primary-50 hover:-translate-y-0.5">
                                    Reapply Now
                                    <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </a>
                            @endif
                        @else
                            <a href="{{ route('seller.apply') }}" class="inline-flex items-center rounded-xl bg-white px-8 py-4 text-base font-semibold text-primary-600 shadow-lg transition-all hover:bg-primary-50 hover:-translate-y-0.5">
                                Start Selling Today
                                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}?seller=1" class="inline-flex items-center rounded-xl bg-white px-8 py-4 text-base font-semibold text-primary-600 shadow-lg transition-all hover:bg-primary-50 hover:-translate-y-0.5">
                            Start Selling Today
                            <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    @endauth
                    <a href="#how-it-works" class="inline-flex items-center rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 px-6 py-3 text-base font-semibold text-white hover:bg-white/20 transition-all">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        See How It Works
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-white dark:bg-surface-900 py-16 border-b border-surface-200 dark:border-surface-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
                <div class="text-center p-6 rounded-2xl bg-surface-50 dark:bg-surface-800">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-surface-900 dark:text-white sm:text-4xl">$5M+</div>
                    <div class="mt-2 text-sm font-medium text-surface-600 dark:text-surface-400">Paid to Creators</div>
                </div>
                <div class="text-center p-6 rounded-2xl bg-surface-50 dark:bg-surface-800">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-accent-100 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-surface-900 dark:text-white sm:text-4xl">50K+</div>
                    <div class="mt-2 text-sm font-medium text-surface-600 dark:text-surface-400">Active Customers</div>
                </div>
                <div class="text-center p-6 rounded-2xl bg-surface-50 dark:bg-surface-800">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-surface-900 dark:text-white sm:text-4xl">80%</div>
                    <div class="mt-2 text-sm font-medium text-surface-600 dark:text-surface-400">Revenue Share</div>
                </div>
                <div class="text-center p-6 rounded-2xl bg-surface-50 dark:bg-surface-800">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-surface-900 dark:text-white sm:text-4xl">24hr</div>
                    <div class="mt-2 text-sm font-medium text-surface-600 dark:text-surface-400">Support Response</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="bg-surface-50 dark:bg-surface-800/50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-3xl font-bold text-surface-900 dark:text-white sm:text-4xl">Why Sell on Codexse?</h2>
                <p class="mt-5 text-lg text-surface-600 dark:text-surface-400 max-w-2xl mx-auto">Everything you need to succeed as a digital creator</p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                <!-- Benefit 1 -->
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-primary-500 to-primary-600 rounded-2xl opacity-0 group-hover:opacity-10 blur transition-opacity"></div>
                    <div class="relative bg-white dark:bg-surface-800 rounded-2xl p-8 border border-surface-200 dark:border-surface-700 h-full">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 mb-6">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-3">Competitive Revenue Share</h3>
                        <p class="text-surface-600 dark:text-surface-400">Keep up to 80% of every sale. The more you sell, the higher your revenue share with our tiered commission structure.</p>
                    </div>
                </div>

                <!-- Benefit 2 -->
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-accent-500 to-accent-600 rounded-2xl opacity-0 group-hover:opacity-10 blur transition-opacity"></div>
                    <div class="relative bg-white dark:bg-surface-800 rounded-2xl p-8 border border-surface-200 dark:border-surface-700 h-full">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent-100 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400 mb-6">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-3">Instant Payouts</h3>
                        <p class="text-surface-600 dark:text-surface-400">Get paid quickly via Stripe. Request payouts anytime with low minimum thresholds and multiple currency support.</p>
                    </div>
                </div>

                <!-- Benefit 3 -->
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl opacity-0 group-hover:opacity-10 blur transition-opacity"></div>
                    <div class="relative bg-white dark:bg-surface-800 rounded-2xl p-8 border border-surface-200 dark:border-surface-700 h-full">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 mb-6">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-3">Powerful Analytics</h3>
                        <p class="text-surface-600 dark:text-surface-400">Track your sales, views, and customer behavior with detailed analytics. Make data-driven decisions to grow your store.</p>
                    </div>
                </div>

                <!-- Benefit 4 -->
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-amber-500 to-amber-600 rounded-2xl opacity-0 group-hover:opacity-10 blur transition-opacity"></div>
                    <div class="relative bg-white dark:bg-surface-800 rounded-2xl p-8 border border-surface-200 dark:border-surface-700 h-full">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 mb-6">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-3">Global Reach</h3>
                        <p class="text-surface-600 dark:text-surface-400">Access customers from 150+ countries. We handle international payments, taxes, and currency conversion.</p>
                    </div>
                </div>

                <!-- Benefit 5 -->
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-red-500 to-red-600 rounded-2xl opacity-0 group-hover:opacity-10 blur transition-opacity"></div>
                    <div class="relative bg-white dark:bg-surface-800 rounded-2xl p-8 border border-surface-200 dark:border-surface-700 h-full">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 mb-6">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-3">Secure & Protected</h3>
                        <p class="text-surface-600 dark:text-surface-400">Your products are protected with our secure download system. We handle licensing and prevent unauthorized distribution.</p>
                    </div>
                </div>

                <!-- Benefit 6 -->
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-primary-500 to-primary-600 rounded-2xl opacity-0 group-hover:opacity-10 blur transition-opacity"></div>
                    <div class="relative bg-white dark:bg-surface-800 rounded-2xl p-8 border border-surface-200 dark:border-surface-700 h-full">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 mb-6">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-3">Dedicated Support</h3>
                        <p class="text-surface-600 dark:text-surface-400">Get help when you need it with our dedicated seller support team. We are here to help you succeed.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="bg-white dark:bg-surface-900 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-3xl font-bold text-surface-900 dark:text-white sm:text-4xl">How It Works</h2>
                <p class="mt-5 text-lg text-surface-600 dark:text-surface-400 max-w-2xl mx-auto">Get started in just a few simple steps</p>
            </div>

            <div class="relative">
                <!-- Connector Line (Desktop) -->
                <div class="hidden lg:block absolute top-10 left-0 right-0 h-0.5 bg-gradient-to-r from-primary-200 via-accent-200 to-primary-200 dark:from-primary-800 dark:via-accent-800 dark:to-primary-800"></div>

                <div class="grid gap-8 lg:grid-cols-4">
                    <!-- Step 1 -->
                    <div class="relative text-center">
                        <div class="relative z-10 inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 text-white text-2xl font-bold mb-6 shadow-lg shadow-primary-500/30">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="bg-surface-50 dark:bg-surface-800 rounded-2xl p-6 border border-surface-200 dark:border-surface-700">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 mb-3">Step 1</span>
                            <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-2">Create Account</h3>
                            <p class="text-sm text-surface-600 dark:text-surface-400">Sign up for free and complete your seller application with your portfolio.</p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative text-center">
                        <div class="relative z-10 inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-accent-500 to-accent-600 text-white text-2xl font-bold mb-6 shadow-lg shadow-accent-500/30">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="bg-surface-50 dark:bg-surface-800 rounded-2xl p-6 border border-surface-200 dark:border-surface-700">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent-100 dark:bg-accent-900/30 text-accent-700 dark:text-accent-300 mb-3">Step 2</span>
                            <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-2">Get Approved</h3>
                            <p class="text-sm text-surface-600 dark:text-surface-400">Our team reviews your application within 24-48 hours.</p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative text-center">
                        <div class="relative z-10 inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 text-white text-2xl font-bold mb-6 shadow-lg shadow-amber-500/30">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </div>
                        <div class="bg-surface-50 dark:bg-surface-800 rounded-2xl p-6 border border-surface-200 dark:border-surface-700">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 mb-3">Step 3</span>
                            <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-2">Upload Products</h3>
                            <p class="text-sm text-surface-600 dark:text-surface-400">Add your digital products with descriptions, previews, and pricing.</p>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="relative text-center">
                        <div class="relative z-10 inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-green-500 to-green-600 text-white text-2xl font-bold mb-6 shadow-lg shadow-green-500/30">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="bg-surface-50 dark:bg-surface-800 rounded-2xl p-6 border border-surface-200 dark:border-surface-700">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 mb-3">Step 4</span>
                            <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-2">Start Earning</h3>
                            <p class="text-sm text-surface-600 dark:text-surface-400">Your products are live! Earn money from every sale.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Commission Tiers Section -->
    <section class="bg-surface-50 dark:bg-surface-800/50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-3xl font-bold text-surface-900 dark:text-white sm:text-4xl">Earn More as You Grow</h2>
                <p class="mt-5 text-lg text-surface-600 dark:text-surface-400 max-w-2xl mx-auto">Our tiered commission structure rewards successful sellers</p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <!-- Bronze Tier -->
                <div class="relative bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6 transition-all hover:shadow-lg hover:-translate-y-1">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/30 mb-4">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-surface-900 dark:text-white">Bronze</h3>
                        <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">$0 - $1,000</p>
                        <div class="mt-4">
                            <span class="text-4xl font-extrabold text-surface-900 dark:text-white">80%</span>
                            <span class="text-surface-500 dark:text-surface-400"> revenue</span>
                        </div>
                    </div>
                    <ul class="space-y-3">
                        <li class="flex items-center text-sm text-surface-600 dark:text-surface-400">
                            <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Basic analytics
                        </li>
                        <li class="flex items-center text-sm text-surface-600 dark:text-surface-400">
                            <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Email support
                        </li>
                        <li class="flex items-center text-sm text-surface-600 dark:text-surface-400">
                            <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Standard payouts
                        </li>
                    </ul>
                </div>

                <!-- Silver Tier -->
                <div class="relative bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6 transition-all hover:shadow-lg hover:-translate-y-1">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-700 mb-4">
                            <svg class="w-6 h-6 text-slate-500 dark:text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-surface-900 dark:text-white">Silver</h3>
                        <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">$1,000 - $10,000</p>
                        <div class="mt-4">
                            <span class="text-4xl font-extrabold text-surface-900 dark:text-white">82%</span>
                            <span class="text-surface-500 dark:text-surface-400"> revenue</span>
                        </div>
                    </div>
                    <ul class="space-y-3">
                        <li class="flex items-center text-sm text-surface-600 dark:text-surface-400">
                            <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Advanced analytics
                        </li>
                        <li class="flex items-center text-sm text-surface-600 dark:text-surface-400">
                            <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Priority support
                        </li>
                        <li class="flex items-center text-sm text-surface-600 dark:text-surface-400">
                            <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Weekly payouts
                        </li>
                    </ul>
                </div>

                <!-- Gold Tier (Most Popular) -->
                <div class="relative bg-white dark:bg-surface-800 rounded-2xl border-2 border-primary-500 p-6 shadow-xl shadow-primary-500/10 transition-all hover:shadow-2xl hover:-translate-y-1">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                        <span class="bg-gradient-to-r from-primary-500 to-accent-500 text-white text-xs font-semibold px-3 py-1 rounded-full">Most Popular</span>
                    </div>
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 mb-4">
                            <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-surface-900 dark:text-white">Gold</h3>
                        <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">$10,000 - $50,000</p>
                        <div class="mt-4">
                            <span class="text-4xl font-extrabold text-surface-900 dark:text-white">85%</span>
                            <span class="text-surface-500 dark:text-surface-400"> revenue</span>
                        </div>
                    </div>
                    <ul class="space-y-3">
                        <li class="flex items-center text-sm text-surface-600 dark:text-surface-400">
                            <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Pro analytics
                        </li>
                        <li class="flex items-center text-sm text-surface-600 dark:text-surface-400">
                            <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Dedicated support
                        </li>
                        <li class="flex items-center text-sm text-surface-600 dark:text-surface-400">
                            <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Daily payouts
                        </li>
                        <li class="flex items-center text-sm text-surface-600 dark:text-surface-400">
                            <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Featured placement
                        </li>
                    </ul>
                </div>

                <!-- Platinum Tier -->
                <div class="relative bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6 transition-all hover:shadow-lg hover:-translate-y-1">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-violet-100 dark:bg-violet-900/30 mb-4">
                            <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-surface-900 dark:text-white">Platinum</h3>
                        <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">$50,000+</p>
                        <div class="mt-4">
                            <span class="text-4xl font-extrabold text-surface-900 dark:text-white">88%</span>
                            <span class="text-surface-500 dark:text-surface-400"> revenue</span>
                        </div>
                    </div>
                    <ul class="space-y-3">
                        <li class="flex items-center text-sm text-surface-600 dark:text-surface-400">
                            <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Custom analytics
                        </li>
                        <li class="flex items-center text-sm text-surface-600 dark:text-surface-400">
                            <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Account manager
                        </li>
                        <li class="flex items-center text-sm text-surface-600 dark:text-surface-400">
                            <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Instant payouts
                        </li>
                        <li class="flex items-center text-sm text-surface-600 dark:text-surface-400">
                            <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Homepage feature
                        </li>
                        <li class="flex items-center text-sm text-surface-600 dark:text-surface-400">
                            <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Early access
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- What You Can Sell Section -->
    <section class="bg-white dark:bg-surface-900 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-surface-900 dark:text-white sm:text-4xl">What Can You Sell?</h2>
                <p class="mt-5 text-lg text-surface-600 dark:text-surface-400 max-w-2xl mx-auto">From design assets to code snippets, sell any digital product</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- UI Kits -->
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 p-8 transition-all hover:shadow-xl hover:-translate-y-1">
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm text-white mb-5">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">UI Kits</h3>
                        <p class="text-primary-100 text-sm">Complete design systems with components, patterns, and guidelines for modern applications.</p>
                        <div class="mt-4 flex items-center text-white/80 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            Top Seller
                        </div>
                    </div>
                </div>

                <!-- Templates -->
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-accent-500 to-accent-600 p-8 transition-all hover:shadow-xl hover:-translate-y-1">
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm text-white mb-5">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Templates</h3>
                        <p class="text-accent-100 text-sm">Website templates, landing pages, dashboards, and admin panels ready to customize.</p>
                        <div class="mt-4 flex items-center text-white/80 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
                            </svg>
                            Trending
                        </div>
                    </div>
                </div>

                <!-- Icons -->
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 p-8 transition-all hover:shadow-xl hover:-translate-y-1">
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm text-white mb-5">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Icons</h3>
                        <p class="text-amber-100 text-sm">Icon packs in multiple formats - SVG, PNG, Figma, and more for any project.</p>
                        <div class="mt-4 flex items-center text-white/80 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                            </svg>
                            Popular
                        </div>
                    </div>
                </div>

                <!-- Illustrations -->
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-pink-500 to-rose-500 p-8 transition-all hover:shadow-xl hover:-translate-y-1">
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm text-white mb-5">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Illustrations</h3>
                        <p class="text-pink-100 text-sm">Hand-crafted illustrations, character sets, and scene packs for websites and apps.</p>
                        <div class="mt-4 flex items-center text-white/80 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                            </svg>
                            Featured
                        </div>
                    </div>
                </div>

                <!-- Themes -->
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-500 to-purple-600 p-8 transition-all hover:shadow-xl hover:-translate-y-1">
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm text-white mb-5">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Themes</h3>
                        <p class="text-violet-100 text-sm">WordPress, Shopify, and other CMS themes with full customization options.</p>
                        <div class="mt-4 flex items-center text-white/80 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                            </svg>
                            High Demand
                        </div>
                    </div>
                </div>

                <!-- Code & Scripts -->
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-500 to-teal-500 p-8 transition-all hover:shadow-xl hover:-translate-y-1">
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm text-white mb-5">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Code & Scripts</h3>
                        <p class="text-cyan-100 text-sm">Plugins, scripts, components, and code snippets for developers.</p>
                        <div class="mt-4 flex items-center text-white/80 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            Developer Pick
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Categories -->
            <div class="mt-10 text-center">
                <p class="text-surface-600 dark:text-surface-400 mb-4">And many more categories including:</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-surface-100 dark:bg-surface-800 text-surface-700 dark:text-surface-300 text-sm font-medium">Fonts</span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-surface-100 dark:bg-surface-800 text-surface-700 dark:text-surface-300 text-sm font-medium">Mockups</span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-surface-100 dark:bg-surface-800 text-surface-700 dark:text-surface-300 text-sm font-medium">Presentations</span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-surface-100 dark:bg-surface-800 text-surface-700 dark:text-surface-300 text-sm font-medium">3D Assets</span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-surface-100 dark:bg-surface-800 text-surface-700 dark:text-surface-300 text-sm font-medium">Textures</span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-surface-100 dark:bg-surface-800 text-surface-700 dark:text-surface-300 text-sm font-medium">Animations</span>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="bg-surface-50 dark:bg-surface-800/50 py-20">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-surface-900 dark:text-white sm:text-4xl">Frequently Asked Questions</h2>
                <p class="mt-5 text-lg text-surface-600 dark:text-surface-400">Got questions? We have got answers</p>
            </div>

            <div class="space-y-4" x-data="{ open: null }">
                @foreach([
                    ['q' => 'How long does the approval process take?', 'a' => 'Most applications are reviewed within 24-48 hours. We review your portfolio, store details, and sample products to ensure quality. You will receive an email once your application is approved.'],
                    ['q' => 'What are the requirements to become a seller?', 'a' => 'You need to be at least 18 years old, have a portfolio or samples of your work, and provide accurate payment information. We look for quality and originality in the products you plan to sell.'],
                    ['q' => 'How and when do I get paid?', 'a' => 'Payments are processed via Stripe. Depending on your seller tier, you can request payouts weekly, daily, or instantly. The minimum payout threshold is $50 for new sellers.'],
                    ['q' => 'What percentage does Codexse take?', 'a' => 'New sellers start at 80% revenue share (we take 20%). As your sales grow, your revenue share increases up to 88% for Platinum sellers. This covers payment processing, hosting, and platform services.'],
                    ['q' => 'Can I sell products on other platforms too?', 'a' => 'Yes! We do not require exclusivity. You are free to sell your products on other marketplaces. However, exclusive products may receive better visibility and promotional opportunities.'],
                    ['q' => 'What file types can I upload?', 'a' => 'We support all common digital product formats including ZIP, PSD, AI, SKETCH, FIGMA, XD, PDF, and more. Each product can have multiple files and variations.'],
                ] as $index => $faq)
                    <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden transition-shadow hover:shadow-md">
                        <button
                            @click="open = open === {{ $index }} ? null : {{ $index }}"
                            class="flex items-center justify-between w-full px-6 py-5 text-left"
                        >
                            <span class="font-medium text-surface-900 dark:text-white pr-4">{{ $faq['q'] }}</span>
                            <div class="shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-surface-100 dark:bg-surface-700">
                                <svg class="w-4 h-4 text-surface-600 dark:text-surface-400 transition-transform duration-200" :class="open === {{ $index }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>
                        <div x-show="open === {{ $index }}" x-collapse x-cloak>
                            <div class="px-6 pb-5 text-surface-600 dark:text-surface-400 leading-relaxed">
                                {{ $faq['a'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-500 to-accent-500 py-24">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z\" fill=\"rgba(255,255,255,0.07)\"%3E%3C/path%3E%3C/svg%3E')] opacity-50"></div>
        <div class="absolute top-0 right-0 -mt-20 -mr-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 h-64 w-64 rounded-full bg-accent-400/20 blur-3xl"></div>

        <div class="relative mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white sm:text-4xl lg:text-5xl">Ready to Start Selling?</h2>
            <p class="mt-6 text-xl text-primary-100 max-w-2xl mx-auto">Join thousands of creators earning passive income on Codexse. Start your journey today.</p>
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                @auth
                    @if(!auth()->user()->seller)
                        <a href="{{ route('seller.apply') }}" class="inline-flex items-center rounded-xl bg-white px-8 py-4 text-base font-semibold text-primary-600 shadow-lg shadow-primary-900/20 transition-all hover:bg-primary-50 hover:-translate-y-0.5 hover:shadow-xl">
                            Apply Now - It's Free
                            <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}?seller=1" class="inline-flex items-center rounded-xl bg-white px-8 py-4 text-base font-semibold text-primary-600 shadow-lg shadow-primary-900/20 transition-all hover:bg-primary-50 hover:-translate-y-0.5 hover:shadow-xl">
                        Create Free Account
                        <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center text-white font-semibold hover:text-primary-100 transition-colors">
                        Already have an account? Sign in
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </section>
</x-layouts.app>
