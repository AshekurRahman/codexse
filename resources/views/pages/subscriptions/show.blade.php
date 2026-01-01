<x-layouts.app :title="$plan->name . ' - Subscription Plans - Codexse'">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-surface-500 dark:text-surface-400 mb-6">
                <a href="{{ route('subscriptions.index') }}" class="hover:text-primary-600">Subscriptions</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-surface-900 dark:text-white">{{ $plan->name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Plan Header -->
                    <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                @if($plan->is_featured)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400 mb-2">
                                        Popular Choice
                                    </span>
                                @endif
                                <h1 class="text-2xl sm:text-3xl font-bold text-surface-900 dark:text-white">{{ $plan->name }}</h1>
                            </div>
                            <div class="text-right">
                                <span class="text-3xl font-bold text-surface-900 dark:text-white">{{ $plan->formatted_price }}</span>
                                <span class="text-surface-500 dark:text-surface-400">/ {{ strtolower($plan->billing_period_label) }}</span>
                            </div>
                        </div>

                        @if($plan->description)
                            <p class="text-surface-600 dark:text-surface-400 mb-4">{{ $plan->description }}</p>
                        @endif

                        @if($plan->trial_days > 0)
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-success-50 dark:bg-success-900/20 rounded-lg">
                                <svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-success-700 dark:text-success-400 font-medium">{{ $plan->trial_days }}-day free trial included</span>
                            </div>
                        @endif
                    </div>

                    <!-- Associated Product/Service -->
                    @if($plan->product)
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Included Product</h2>
                            <a href="{{ route('products.show', $plan->product) }}" class="flex items-center gap-4 p-4 rounded-xl border border-surface-200 dark:border-surface-700 hover:border-primary-300 dark:hover:border-primary-700 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-all">
                                <img src="{{ $plan->product->thumbnail_url }}" alt="{{ $plan->product->name }}" class="w-20 h-20 rounded-lg object-cover">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-surface-900 dark:text-white">{{ $plan->product->name }}</h3>
                                    <p class="text-sm text-surface-500 dark:text-surface-400 line-clamp-2">{{ $plan->product->short_description }}</p>
                                    <p class="text-sm text-primary-600 dark:text-primary-400 mt-1">View Product</p>
                                </div>
                            </a>
                        </div>
                    @elseif($plan->service)
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Included Service</h2>
                            <a href="{{ route('services.show', $plan->service) }}" class="flex items-center gap-4 p-4 rounded-xl border border-surface-200 dark:border-surface-700 hover:border-primary-300 dark:hover:border-primary-700 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-all">
                                <img src="{{ $plan->service->thumbnail_url }}" alt="{{ $plan->service->name }}" class="w-20 h-20 rounded-lg object-cover">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-surface-900 dark:text-white">{{ $plan->service->name }}</h3>
                                    <p class="text-sm text-surface-500 dark:text-surface-400 line-clamp-2">{{ $plan->service->short_description }}</p>
                                    <p class="text-sm text-primary-600 dark:text-primary-400 mt-1">View Service</p>
                                </div>
                            </a>
                        </div>
                    @endif

                    <!-- Features -->
                    @if($plan->features && count($plan->features) > 0)
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">What's Included</h2>
                            <ul class="space-y-3">
                                @foreach($plan->features as $feature)
                                    <li class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-success-100 dark:bg-success-900/30 flex items-center justify-center mt-0.5">
                                            <svg class="w-4 h-4 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <span class="text-surface-700 dark:text-surface-300">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Usage Limits -->
                    <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Usage Limits</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-xl bg-surface-50 dark:bg-surface-700/50">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    <span class="text-sm font-medium text-surface-600 dark:text-surface-400">Downloads</span>
                                </div>
                                <p class="text-xl font-bold text-surface-900 dark:text-white">
                                    @if($plan->max_downloads)
                                        {{ $plan->max_downloads }}/month
                                    @else
                                        Unlimited
                                    @endif
                                </p>
                            </div>
                            <div class="p-4 rounded-xl bg-surface-50 dark:bg-surface-700/50">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span class="text-sm font-medium text-surface-600 dark:text-surface-400">API Requests</span>
                                </div>
                                <p class="text-xl font-bold text-surface-900 dark:text-white">
                                    @if($plan->max_requests)
                                        {{ number_format($plan->max_requests) }}/month
                                    @else
                                        Unlimited
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 space-y-6">
                        <!-- Subscribe Card -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <div class="text-center mb-6">
                                <span class="text-4xl font-bold text-surface-900 dark:text-white">{{ $plan->formatted_price }}</span>
                                <span class="text-surface-500 dark:text-surface-400">/ {{ strtolower($plan->billing_period_label) }}</span>
                            </div>

                            @if($userSubscription)
                                <div class="mb-4 p-4 rounded-xl bg-success-50 dark:bg-success-900/20 text-center">
                                    <svg class="w-8 h-8 text-success-600 dark:text-success-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-success-700 dark:text-success-400 font-semibold">You're Subscribed!</p>
                                    <p class="text-sm text-success-600 dark:text-success-500">Status: {{ ucfirst($userSubscription->status) }}</p>
                                </div>
                                <a href="{{ route('subscriptions.subscription', $userSubscription) }}" class="w-full inline-flex items-center justify-center rounded-xl border-2 border-primary-600 px-6 py-3 text-base font-semibold text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors">
                                    Manage Subscription
                                </a>
                            @elseif(auth()->check())
                                <a href="{{ route('subscriptions.checkout', $plan) }}" class="w-full inline-flex items-center justify-center rounded-xl bg-primary-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition-all">
                                    @if($plan->trial_days > 0)
                                        Start {{ $plan->trial_days }}-Day Free Trial
                                    @else
                                        Subscribe Now
                                    @endif
                                </a>
                            @else
                                <a href="{{ route('login', ['redirect' => route('subscriptions.checkout', $plan)]) }}" class="w-full inline-flex items-center justify-center rounded-xl bg-primary-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition-all">
                                    Login to Subscribe
                                </a>
                            @endif

                            @if($plan->trial_days > 0 && !$userSubscription)
                                <p class="text-center text-sm text-surface-500 dark:text-surface-400 mt-4">
                                    No payment required for trial
                                </p>
                            @endif
                        </div>

                        <!-- Seller Card -->
                        @if($plan->seller)
                            <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                                <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Offered by</h3>
                                <a href="{{ route('sellers.show', $plan->seller) }}" class="flex items-center gap-3">
                                    <img src="{{ $plan->seller->logo_url }}" alt="{{ $plan->seller->store_name }}" class="w-12 h-12 rounded-full object-cover">
                                    <div>
                                        <p class="font-medium text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                            {{ $plan->seller->store_name }}
                                        </p>
                                        @if($plan->seller->is_verified)
                                            <span class="inline-flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400">
                                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Verified Seller
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @endif

                        <!-- Billing Info -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Billing Details</h3>
                            <ul class="space-y-3 text-sm text-surface-600 dark:text-surface-400">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Billed {{ strtolower($plan->billing_period_label) }}
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Cancel anytime
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Secure payment via Stripe
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
