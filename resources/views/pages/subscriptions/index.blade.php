<x-layouts.app>
    <x-slot name="title">Subscription Plans</x-slot>

    <div class="min-h-screen bg-surface-50 dark:bg-surface-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-surface-900 dark:text-white mb-4">
                    Choose Your Plan
                </h1>
                <p class="text-lg text-surface-600 dark:text-surface-400 max-w-2xl mx-auto">
                    Get access to premium features and content with our flexible subscription plans
                </p>
            </div>

            <!-- Plans Grid -->
            @if($plans->isEmpty())
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-surface-100 dark:bg-surface-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-surface-900 dark:text-white mb-2">No Plans Available</h3>
                    <p class="text-surface-600 dark:text-surface-400">Check back later for subscription plans.</p>
                </div>
            @else
                @foreach($groupedPlans as $key => $group)
                    @php
                        $firstPlan = $group->first();
                        $item = $firstPlan->item;
                    @endphp

                    @if($item)
                        <div class="mb-12">
                            <div class="flex items-center gap-4 mb-6">
                                @if($firstPlan->product_id && $item->thumbnail)
                                    <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="{{ $item->name }}" class="w-16 h-16 rounded-lg object-cover">
                                @endif
                                <div>
                                    <h2 class="text-2xl font-bold text-surface-900 dark:text-white">
                                        {{ $item->name ?? $item->title }}
                                    </h2>
                                    <p class="text-surface-600 dark:text-surface-400">
                                        {{ $firstPlan->type === 'product' ? 'Product Subscription' : 'Service Subscription' }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($group as $plan)
                                    @include('pages.subscriptions._plan-card', ['plan' => $plan])
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- Standalone Plans -->
                @if(isset($groupedPlans['standalone']) && $groupedPlans['standalone']->isNotEmpty())
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-surface-900 dark:text-white mb-6">
                            Standalone Plans
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($groupedPlans['standalone'] as $plan)
                                @include('pages.subscriptions._plan-card', ['plan' => $plan])
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <!-- Features Section -->
            <div class="mt-16 bg-white dark:bg-surface-800 rounded-2xl p-8 shadow-sm">
                <h2 class="text-2xl font-bold text-surface-900 dark:text-white text-center mb-8">
                    All Plans Include
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-surface-900 dark:text-white">Instant Downloads</h3>
                        <p class="text-sm text-surface-600 dark:text-surface-400 mt-1">Access your files immediately</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-success-100 dark:bg-success-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-surface-900 dark:text-white">Free Updates</h3>
                        <p class="text-sm text-surface-600 dark:text-surface-400 mt-1">Get updates during subscription</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-accent-100 dark:bg-accent-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-surface-900 dark:text-white">Priority Support</h3>
                        <p class="text-sm text-surface-600 dark:text-surface-400 mt-1">Get help when you need it</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-warning-100 dark:bg-warning-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-surface-900 dark:text-white">Cancel Anytime</h3>
                        <p class="text-sm text-surface-600 dark:text-surface-400 mt-1">No long-term commitment</p>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-surface-900 dark:text-white text-center mb-8">
                    Frequently Asked Questions
                </h2>
                <div class="max-w-3xl mx-auto space-y-4" x-data="{ open: null }">
                    <div class="bg-white dark:bg-surface-800 rounded-lg shadow-sm">
                        <button @click="open = open === 1 ? null : 1" class="w-full px-6 py-4 text-left flex items-center justify-between">
                            <span class="font-medium text-surface-900 dark:text-white">How do billing cycles work?</span>
                            <svg class="w-5 h-5 text-surface-500 transition-transform" :class="{ 'rotate-180': open === 1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open === 1" x-collapse class="px-6 pb-4">
                            <p class="text-surface-600 dark:text-surface-400">Your subscription will automatically renew at the end of each billing period. You'll be charged the same amount unless you cancel before the renewal date.</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-surface-800 rounded-lg shadow-sm">
                        <button @click="open = open === 2 ? null : 2" class="w-full px-6 py-4 text-left flex items-center justify-between">
                            <span class="font-medium text-surface-900 dark:text-white">Can I cancel my subscription?</span>
                            <svg class="w-5 h-5 text-surface-500 transition-transform" :class="{ 'rotate-180': open === 2 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open === 2" x-collapse class="px-6 pb-4">
                            <p class="text-surface-600 dark:text-surface-400">Yes, you can cancel anytime from your subscriptions dashboard. You'll continue to have access until the end of your current billing period.</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-surface-800 rounded-lg shadow-sm">
                        <button @click="open = open === 3 ? null : 3" class="w-full px-6 py-4 text-left flex items-center justify-between">
                            <span class="font-medium text-surface-900 dark:text-white">What happens to my downloads if I cancel?</span>
                            <svg class="w-5 h-5 text-surface-500 transition-transform" :class="{ 'rotate-180': open === 3 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open === 3" x-collapse class="px-6 pb-4">
                            <p class="text-surface-600 dark:text-surface-400">Files you've already downloaded are yours to keep. However, you won't be able to download new files or updates after your subscription ends.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
