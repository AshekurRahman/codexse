<x-layouts.app title="Consent Preferences - GDPR">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <a href="{{ route('gdpr.index') }}" class="inline-flex items-center gap-2 text-surface-600 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Privacy Center
            </a>

            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex items-center justify-center w-16 h-16 bg-success-100 dark:bg-success-900/30 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-success-600 dark:text-success-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Consent Preferences</h1>
                        <p class="text-surface-600 dark:text-surface-400">Control how we use your data</p>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-xl text-success-700 dark:text-success-300">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('gdpr.consent.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden mb-6">
                    <!-- Essential Cookies -->
                    <div class="p-6 border-b border-surface-200 dark:border-surface-700">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 pr-4">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-surface-900 dark:text-white">Essential Cookies</h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400">
                                        Required
                                    </span>
                                </div>
                                <p class="text-sm text-surface-600 dark:text-surface-400 mt-1">
                                    These cookies are necessary for the website to function and cannot be switched off. They are usually set in response to actions made by you such as logging in or filling in forms.
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="relative inline-flex items-center">
                                    <input type="checkbox" checked disabled class="sr-only peer">
                                    <div class="w-11 h-6 bg-success-500 rounded-full"></div>
                                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full translate-x-5"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Marketing -->
                    <div class="p-6 border-b border-surface-200 dark:border-surface-700">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 pr-4">
                                <h3 class="font-semibold text-surface-900 dark:text-white">Marketing Communications</h3>
                                <p class="text-sm text-surface-600 dark:text-surface-400 mt-1">
                                    Receive promotional emails, newsletters, and special offers about our products and services. You can unsubscribe at any time.
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="marketing_consent" value="0">
                                    <input type="checkbox" name="marketing_consent" value="1" class="sr-only peer" {{ $user->marketing_consent ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-surface-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-surface-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-surface-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-surface-600 peer-checked:bg-primary-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Analytics -->
                    <div class="p-6 border-b border-surface-200 dark:border-surface-700">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 pr-4">
                                <h3 class="font-semibold text-surface-900 dark:text-white">Analytics & Performance</h3>
                                <p class="text-sm text-surface-600 dark:text-surface-400 mt-1">
                                    Help us improve our website by allowing anonymous usage statistics collection. This data is used to understand how visitors interact with the website.
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="analytics_consent" value="0">
                                    <input type="checkbox" name="analytics_consent" value="1" class="sr-only peer" {{ $user->analytics_consent ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-surface-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-surface-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-surface-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-surface-600 peer-checked:bg-primary-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Third Party -->
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 pr-4">
                                <h3 class="font-semibold text-surface-900 dark:text-white">Third Party Services</h3>
                                <p class="text-sm text-surface-600 dark:text-surface-400 mt-1">
                                    Allow integration with third-party services such as social media platforms, payment providers, and analytics tools. Some features may not work if disabled.
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="third_party_consent" value="0">
                                    <input type="checkbox" name="third_party_consent" value="1" class="sr-only peer" {{ $user->third_party_consent ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-surface-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-surface-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-surface-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-surface-600 peer-checked:bg-primary-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-800 rounded-xl p-4 mb-6">
                    <div class="flex gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-info-600 dark:text-info-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-info-700 dark:text-info-300">
                            <p>Your consent choices are logged and stored securely. You can change your preferences at any time. Changes take effect immediately.</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Preferences
                </button>
            </form>

            <!-- Privacy Policy Link -->
            <div class="mt-8 text-center">
                <a href="{{ route('pages.show', 'privacy-policy') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                    Read our full Privacy Policy
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
