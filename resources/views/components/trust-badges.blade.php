@props(['variant' => 'default'])

<section class="py-12 bg-white dark:bg-surface-900 border-y border-surface-100 dark:border-surface-800">
    <div class="mx-auto max-w-7xl container-padding">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8 items-center">
            <!-- Secure Payments -->
            <div class="trust-badge group" x-scroll-animate>
                <div class="trust-badge-icon w-16 h-16 rounded-2xl bg-success-100 dark:bg-success-900/30 flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-success-600 dark:text-success-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-surface-900 dark:text-white">Secure Payments</span>
                <span class="text-xs text-surface-500 dark:text-surface-400 mt-1">256-bit SSL</span>
            </div>

            <!-- Money Back Guarantee -->
            <div class="trust-badge group" x-scroll-animate>
                <div class="trust-badge-icon w-16 h-16 rounded-2xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-surface-900 dark:text-white">Money Back</span>
                <span class="text-xs text-surface-500 dark:text-surface-400 mt-1">30-day guarantee</span>
            </div>

            <!-- 24/7 Support -->
            <div class="trust-badge group" x-scroll-animate>
                <div class="trust-badge-icon w-16 h-16 rounded-2xl bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-accent-600 dark:text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-surface-900 dark:text-white">24/7 Support</span>
                <span class="text-xs text-surface-500 dark:text-surface-400 mt-1">Always here to help</span>
            </div>

            <!-- Verified Sellers -->
            <div class="trust-badge group" x-scroll-animate>
                <div class="trust-badge-icon w-16 h-16 rounded-2xl bg-info-100 dark:bg-info-900/30 flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-info-600 dark:text-info-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-surface-900 dark:text-white">Verified Sellers</span>
                <span class="text-xs text-surface-500 dark:text-surface-400 mt-1">Quality assured</span>
            </div>

            <!-- Instant Download -->
            <div class="trust-badge group" x-scroll-animate>
                <div class="trust-badge-icon w-16 h-16 rounded-2xl bg-warning-100 dark:bg-warning-900/30 flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-warning-600 dark:text-warning-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-surface-900 dark:text-white">Instant Download</span>
                <span class="text-xs text-surface-500 dark:text-surface-400 mt-1">Get it now</span>
            </div>

            <!-- Lifetime Updates -->
            <div class="trust-badge group" x-scroll-animate>
                <div class="trust-badge-icon w-16 h-16 rounded-2xl bg-danger-100 dark:bg-danger-900/30 flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-danger-600 dark:text-danger-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-surface-900 dark:text-white">Lifetime Updates</span>
                <span class="text-xs text-surface-500 dark:text-surface-400 mt-1">Always current</span>
            </div>
        </div>
    </div>
</section>
