<x-layouts.app :title="$service->name . ' - Services - Codexse'">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-surface-500 dark:text-surface-400 mb-6">
                <a href="{{ route('services.index') }}" class="hover:text-primary-600">Services</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                @if($service->category)
                    <a href="{{ route('services.index', ['category' => $service->category_id]) }}" class="hover:text-primary-600">{{ $service->category->name }}</a>
                @else
                    <span class="text-surface-500">Uncategorized</span>
                @endif
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-surface-900 dark:text-white truncate">{{ $service->name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Title & Seller -->
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-surface-900 dark:text-white mb-4">{{ $service->name }}</h1>
                        <div class="flex items-center gap-4">
                            <a href="{{ route('sellers.show', $service->seller) }}" class="flex items-center gap-3">
                                <img src="{{ $service->seller->logo_url }}" alt="{{ $service->seller->store_name }}" class="w-12 h-12 rounded-full object-cover">
                                <div>
                                    <p class="font-medium text-surface-900 dark:text-white">{{ $service->seller->store_name }}</p>
                                    @if($service->seller->level)
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ ucfirst($service->seller->level) }} Seller</p>
                                    @endif
                                </div>
                            </a>
                            @if($service->rating_count > 0)
                                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-100 dark:bg-surface-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="font-semibold text-surface-900 dark:text-white">{{ number_format($service->rating_average, 1) }}</span>
                                    <span class="text-surface-500 dark:text-surface-400">({{ $service->rating_count }} reviews)</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Gallery -->
                    <div class="rounded-2xl overflow-hidden border border-surface-200 dark:border-surface-700">
                        @if($service->thumbnail)
                            <img src="{{ asset('storage/' . $service->thumbnail) }}" alt="{{ $service->name }}" class="w-full aspect-video object-cover">
                        @else
                            <div class="w-full aspect-video bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif

                        @if($service->gallery_images && count($service->gallery_images) > 0)
                            <div class="grid grid-cols-4 gap-2 p-2 bg-surface-100 dark:bg-surface-700">
                                @foreach($service->gallery_images as $image)
                                    <img src="{{ asset('storage/' . $image) }}" alt="Gallery" class="rounded aspect-video object-cover cursor-pointer hover:opacity-80 transition-opacity">
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                        <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-4">Service Overview</h2>
                        <p class="text-sm text-surface-500 dark:text-surface-400 mb-4">Learn what this freelancer offers and how they can help you achieve your goals.</p>
                        <div class="prose prose-surface dark:prose-invert max-w-none">
                            {!! $service->description !!}
                        </div>
                    </div>

                    <!-- Why Choose This Service -->
                    <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                        <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-4">Why Choose This Service?</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-success-100 dark:bg-success-900/30 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-surface-900 dark:text-white">Secure Payment</h3>
                                    <p class="text-sm text-surface-600 dark:text-surface-400">Your funds are protected in escrow until you approve the work.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-surface-900 dark:text-white">On-Time Delivery</h3>
                                    <p class="text-sm text-surface-600 dark:text-surface-400">Track progress and get your work delivered on schedule.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-info-100 dark:bg-info-900/30 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-info-600 dark:text-info-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-surface-900 dark:text-white">Direct Communication</h3>
                                    <p class="text-sm text-surface-600 dark:text-surface-400">Message the freelancer directly to discuss your project.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-warning-100 dark:bg-warning-900/30 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-surface-900 dark:text-white">Revision Support</h3>
                                    <p class="text-sm text-surface-600 dark:text-surface-400">Request revisions to ensure you're satisfied with the result.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- What's Included (from packages) -->
                    @if($service->packages->count() > 0)
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-2">Compare Package Options</h2>
                            <p class="text-sm text-surface-500 dark:text-surface-400 mb-4">Choose the package that best fits your budget and project requirements. Each tier offers different features and delivery times.</p>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-surface-200 dark:border-surface-700">
                                            <th class="text-left py-3 px-2 text-surface-500 dark:text-surface-400 font-medium">Package</th>
                                            @foreach($service->packages as $package)
                                                <th class="text-center py-3 px-4 min-w-[180px]">
                                                    <span class="text-sm uppercase text-primary-600 dark:text-primary-400 font-semibold">{{ $package->tier }}</span>
                                                    <p class="text-lg font-bold text-surface-900 dark:text-white">{{ format_price($package->price) }}</p>
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-surface-200 dark:divide-surface-700">
                                        <tr>
                                            <td class="py-3 px-2 text-surface-600 dark:text-surface-400">Delivery Time</td>
                                            @foreach($service->packages as $package)
                                                <td class="py-3 px-4 text-center text-surface-900 dark:text-white">{{ $package->delivery_days }} days</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td class="py-3 px-2 text-surface-600 dark:text-surface-400">Revisions</td>
                                            @foreach($service->packages as $package)
                                                <td class="py-3 px-4 text-center text-surface-900 dark:text-white">{{ $package->revisions ?: 'Unlimited' }}</td>
                                            @endforeach
                                        </tr>
                                        @if($service->packages->first()->deliverables)
                                            @foreach($service->packages->first()->deliverables as $index => $deliverable)
                                                <tr>
                                                    <td class="py-3 px-2 text-surface-600 dark:text-surface-400">{{ $deliverable }}</td>
                                                    @foreach($service->packages as $package)
                                                        <td class="py-3 px-4 text-center">
                                                            @if(isset($package->deliverables[$index]))
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            @else
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-surface-300 dark:text-surface-600 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        @endif
                                        <tr>
                                            <td class="py-3 px-2"></td>
                                            @foreach($service->packages as $package)
                                                <td class="py-3 px-4 text-center">
                                                    <a href="{{ route('services.order', [$service, $package]) }}" class="inline-flex items-center justify-center w-full rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 transition-colors">
                                                        Select
                                                    </a>
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 space-y-6">
                        <!-- Package Selection Card -->
                        @if($service->packages->count() > 0)
                            <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                                <!-- Package Tabs -->
                                <div class="flex border-b border-surface-200 dark:border-surface-700" x-data="{ activeTab: 0 }">
                                    @foreach($service->packages as $index => $package)
                                        <button @click="activeTab = {{ $index }}"
                                            :class="activeTab === {{ $index }} ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-surface-500 hover:text-surface-700 dark:hover:text-surface-300'"
                                            class="flex-1 py-3 px-4 text-sm font-medium border-b-2 transition-colors">
                                            {{ ucfirst($package->tier) }}
                                        </button>
                                    @endforeach
                                </div>

                                @foreach($service->packages as $index => $package)
                                    <div x-show="activeTab === {{ $index }}" class="p-6" x-cloak>
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-bold text-surface-900 dark:text-white">{{ $package->name }}</h3>
                                            <span class="text-2xl font-bold text-surface-900 dark:text-white">{{ format_price($package->price) }}</span>
                                        </div>

                                        <p class="text-sm text-surface-600 dark:text-surface-400 mb-4">{{ $package->description }}</p>

                                        <div class="flex items-center gap-4 text-sm text-surface-500 dark:text-surface-400 mb-6">
                                            <span class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $package->delivery_days }} day{{ $package->delivery_days != 1 ? 's' : '' }} delivery
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                {{ $package->revisions ?: 'Unlimited' }} revisions
                                            </span>
                                        </div>

                                        @if($package->deliverables && count($package->deliverables) > 0)
                                            <ul class="space-y-2 mb-6">
                                                @foreach($package->deliverables as $deliverable)
                                                    <li class="flex items-start gap-2 text-sm text-surface-600 dark:text-surface-400">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        {{ $deliverable }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        <a href="{{ route('services.order', [$service, $package]) }}"
                                            class="w-full inline-flex items-center justify-center rounded-xl bg-primary-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition-all">
                                            Continue ({{ format_price($package->price) }})
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Request Custom Quote -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-lg bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-accent-600 dark:text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-surface-900 dark:text-white">Need Something Custom?</h3>
                            </div>
                            <p class="text-sm text-surface-600 dark:text-surface-400 mb-4">Have specific requirements that don't fit the standard packages? Request a personalized quote tailored to your project needs and budget.</p>
                            <a href="{{ route('quotes.create', $service) }}"
                                class="w-full inline-flex items-center justify-center rounded-xl border-2 border-primary-600 px-6 py-3 text-base font-semibold text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Request Custom Quote
                            </a>
                        </div>

                        <!-- Subscription Plans -->
                        @if($service->hasSubscriptionPlans())
                            <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-lg bg-warning-100 dark:bg-warning-900/30 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </div>
                                    <h3 class="font-semibold text-surface-900 dark:text-white">Subscription Plans</h3>
                                </div>
                                <p class="text-sm text-surface-600 dark:text-surface-400 mb-4">Get ongoing access with a subscription plan for recurring service benefits.</p>
                                <div class="space-y-3">
                                    @foreach($service->activeSubscriptionPlans->take(2) as $plan)
                                        <a href="{{ route('subscriptions.show', $plan) }}" class="block p-3 rounded-xl border border-surface-200 dark:border-surface-700 hover:border-primary-300 dark:hover:border-primary-700 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-all group">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-medium text-surface-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400">{{ $plan->name }}</span>
                                                <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">
                                                    {{ format_price($plan->price) }}/{{ $plan->billing_interval }}
                                                </span>
                                            </div>
                                            @if($plan->description)
                                                <p class="text-xs text-surface-500 dark:text-surface-400 line-clamp-2">{{ Str::limit($plan->description, 60) }}</p>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                                @if($service->activeSubscriptionPlans->count() > 2)
                                    <a href="{{ route('subscriptions.index', ['service' => $service->id]) }}" class="block mt-3 text-center text-sm text-primary-600 dark:text-primary-400 hover:underline">
                                        View all {{ $service->activeSubscriptionPlans->count() }} plans
                                    </a>
                                @endif
                            </div>
                        @endif

                        <!-- Seller Card -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <img src="{{ $service->seller->logo_url }}" alt="{{ $service->seller->store_name }}" class="w-16 h-16 rounded-full object-cover">
                                <div>
                                    <a href="{{ route('sellers.show', $service->seller) }}" class="font-semibold text-surface-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                        {{ $service->seller->store_name }}
                                    </a>
                                    @if($service->seller->level)
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ ucfirst($service->seller->level) }} Seller</p>
                                    @endif
                                    @if($service->seller->is_verified)
                                        <span class="inline-flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Verified
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('sellers.show', $service->seller) }}"
                                class="w-full inline-flex items-center justify-center rounded-lg border border-surface-200 dark:border-surface-700 px-4 py-2 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                View Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Services -->
            @if($relatedServices->count() > 0)
                <div class="mt-16">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-surface-900 dark:text-white">Explore Similar Services</h2>
                        <p class="text-surface-600 dark:text-surface-400 mt-1">Discover more services from verified freelancers in this category.</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($relatedServices as $relatedService)
                            <x-service-card :service="$relatedService" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
