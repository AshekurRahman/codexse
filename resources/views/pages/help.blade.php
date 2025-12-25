<x-layouts.app title="Help Center - Codexse">
    <!-- Header -->
    <section class="bg-gradient-to-br from-surface-50 to-white dark:from-surface-950 dark:to-surface-900 py-16">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-surface-900 dark:text-white">Help Center</h1>
            <p class="mt-4 text-lg text-surface-600 dark:text-surface-400">Find answers to common questions and get the support you need.</p>

            <!-- Search -->
            <div class="mt-8 max-w-xl mx-auto">
                <div class="relative">
                    <input type="text" placeholder="Search for help..." class="w-full rounded-xl border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 pl-12 pr-4 py-4 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Links -->
    <section class="py-12 bg-white dark:bg-surface-900 border-b border-surface-200 dark:border-surface-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'title' => 'Buying', 'desc' => 'Purchases, downloads, refunds', 'route' => 'refund'],
                    ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Selling', 'desc' => 'Payouts, products, analytics', 'route' => 'become-seller'],
                    ['icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'title' => 'Account', 'desc' => 'Profile, settings, security', 'route' => 'contact'],
                    ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'Licensing', 'desc' => 'Terms, usage, restrictions', 'route' => 'license'],
                ] as $link)
                    <a href="{{ route($link['route']) }}" class="flex items-center gap-4 p-4 rounded-xl bg-surface-50 dark:bg-surface-800 border border-surface-200 dark:border-surface-700 hover:border-primary-500 transition-colors">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-surface-900 dark:text-white">{{ $link['title'] }}</h3>
                            <p class="text-sm text-surface-500 dark:text-surface-400">{{ $link['desc'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FAQs -->
    <section class="py-16 bg-white dark:bg-surface-900">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-surface-900 dark:text-white mb-8 text-center">Frequently Asked Questions</h2>

            <div class="space-y-4" x-data="{ open: 0 }">
                @foreach([
                    ['q' => 'How do I download my purchased products?', 'a' => 'After completing your purchase, you can download your products from the "My Purchases" section in your dashboard. Each product has a download button that will provide you with the files. Downloads are available for 1 year from the date of purchase.'],
                    ['q' => 'What payment methods do you accept?', 'a' => 'We accept all major credit cards (Visa, MasterCard, American Express), PayPal, and Apple Pay. All payments are processed securely through Stripe.'],
                    ['q' => 'Can I get a refund?', 'a' => 'We offer refunds within 14 days of purchase if the product is significantly different from its description, the files are corrupted, or there are technical issues preventing download. Please review our full refund policy for details.'],
                    ['q' => 'What is included in the license?', 'a' => 'Our standard license allows you to use the product in personal and commercial projects. You can modify the files and use them in unlimited projects. However, you cannot resell or redistribute the original files.'],
                    ['q' => 'How do I contact a seller?', 'a' => 'You can contact a seller by visiting their store page and clicking the "Contact" button. You can also leave comments on product pages. Sellers typically respond within 24-48 hours.'],
                    ['q' => 'How do I become a seller?', 'a' => 'To become a seller, visit our "Become a Seller" page and submit an application. We will review your portfolio and get back to you within 24-48 hours. Once approved, you can start uploading and selling your products.'],
                    ['q' => 'How long do I have access to purchased products?', 'a' => 'You have lifetime access to all purchased products. However, download links are available for 1 year from purchase. We recommend downloading your products promptly and keeping backups.'],
                    ['q' => 'Can I use products in client projects?', 'a' => 'Yes, our standard license allows you to use purchased products in client projects. However, you cannot transfer the license to your client - the end product must be a finished work, not a template for resale.'],
                ] as $index => $faq)
                    <div class="bg-surface-50 dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                        <button
                            @click="open = open === {{ $index }} ? null : {{ $index }}"
                            class="flex items-center justify-between w-full px-6 py-4 text-left"
                        >
                            <span class="font-medium text-surface-900 dark:text-white">{{ $faq['q'] }}</span>
                            <svg class="w-5 h-5 text-surface-500 transition-transform" :class="open === {{ $index }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open === {{ $index }}" x-collapse x-cloak>
                            <div class="px-6 pb-4 text-surface-600 dark:text-surface-400">
                                {{ $faq['a'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="py-16 bg-surface-50 dark:bg-surface-800/50">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-bold text-surface-900 dark:text-white mb-4">Still Need Help?</h2>
            <p class="text-surface-600 dark:text-surface-400 mb-8">Our support team is here to help you with any questions.</p>
            <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-6 py-3 text-base font-semibold text-white hover:bg-primary-700 transition-colors">
                Contact Support
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </section>
</x-layouts.app>
