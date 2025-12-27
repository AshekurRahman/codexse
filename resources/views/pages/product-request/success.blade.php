<x-layouts.app title="Request Submitted - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-12">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-8 text-center">
                <!-- Success Icon -->
                <div class="mx-auto w-16 h-16 bg-success-100 dark:bg-success-900/30 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-surface-900 dark:text-white mb-2">Request Submitted!</h1>
                <p class="text-surface-600 dark:text-surface-400 mb-6">
                    Thank you for your product request. Our team will review it and get back to you within 1-2 business days.
                </p>

                <!-- What Happens Next -->
                <div class="bg-surface-50 dark:bg-surface-700/50 rounded-xl p-6 text-left mb-6">
                    <h2 class="font-semibold text-surface-900 dark:text-white mb-4">What happens next?</h2>
                    <ol class="space-y-3">
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-full flex items-center justify-center text-sm font-medium">1</span>
                            <span class="text-surface-600 dark:text-surface-400">Our team reviews your request to understand your needs</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-full flex items-center justify-center text-sm font-medium">2</span>
                            <span class="text-surface-600 dark:text-surface-400">We search our marketplace for matching products</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-full flex items-center justify-center text-sm font-medium">3</span>
                            <span class="text-surface-600 dark:text-surface-400">You'll receive an email with recommendations or next steps</span>
                        </li>
                    </ol>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
                        Browse Products
                    </a>
                    <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 font-semibold rounded-lg hover:bg-surface-100 dark:hover:bg-surface-700 transition-colors">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
