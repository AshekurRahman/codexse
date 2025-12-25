<x-layouts.app title="Unsubscribed - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen flex items-center justify-center">
        <div class="mx-auto max-w-md px-4 py-12 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-success-100 dark:bg-success-900/30 mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-success-600 dark:text-success-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-white mb-4">You've been unsubscribed</h1>
            <p class="text-surface-600 dark:text-surface-400 mb-8">
                You will no longer receive our newsletter emails. If this was a mistake, you can subscribe again anytime.
            </p>
            <a href="{{ route('home') }}" class="inline-flex items-center rounded-lg bg-primary-600 px-6 py-3 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                Back to Home
            </a>
        </div>
    </div>
</x-layouts.app>
