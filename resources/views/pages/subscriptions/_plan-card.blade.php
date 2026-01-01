@php
    $isUserSubscribed = auth()->check() && auth()->user()
        ->subscriptions()
        ->where('subscription_plan_id', $plan->id)
        ->whereIn('status', ['active', 'trialing'])
        ->exists();
@endphp

<div class="relative bg-white dark:bg-surface-800 rounded-2xl shadow-sm overflow-hidden transition-all duration-300 hover:shadow-lg {{ $plan->is_featured ? 'ring-2 ring-primary-500' : '' }}">
    @if($plan->is_featured)
        <div class="absolute top-0 right-0">
            <div class="bg-primary-500 text-white text-xs font-bold px-4 py-1 rounded-bl-lg">
                POPULAR
            </div>
        </div>
    @endif

    <div class="p-6">
        <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-2">
            {{ $plan->name }}
        </h3>

        @if($plan->description)
            <p class="text-surface-600 dark:text-surface-400 text-sm mb-4">
                {{ $plan->description }}
            </p>
        @endif

        <div class="mb-6">
            <span class="text-4xl font-bold text-surface-900 dark:text-white">{{ $plan->formatted_price }}</span>
            <span class="text-surface-500 dark:text-surface-400">/ {{ strtolower($plan->billing_period_label) }}</span>
        </div>

        @if($plan->trial_days > 0)
            <div class="mb-4 px-3 py-2 bg-success-50 dark:bg-success-900/20 rounded-lg">
                <p class="text-sm text-success-700 dark:text-success-400 font-medium">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $plan->trial_days }}-day free trial
                </p>
            </div>
        @endif

        <!-- Features List -->
        @if($plan->features && count($plan->features) > 0)
            <ul class="space-y-3 mb-6">
                @foreach($plan->features as $feature)
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-success-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-surface-600 dark:text-surface-400">{{ $feature }}</span>
                    </li>
                @endforeach
            </ul>
        @endif

        <!-- Limits -->
        <div class="flex items-center gap-4 mb-6 text-sm text-surface-500 dark:text-surface-400">
            @if($plan->max_downloads)
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>{{ $plan->max_downloads }} downloads/mo</span>
                </div>
            @else
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Unlimited downloads</span>
                </div>
            @endif
        </div>

        <!-- CTA Button -->
        @if($isUserSubscribed)
            <a href="{{ route('subscriptions.manage') }}" class="block w-full text-center px-6 py-3 bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 font-semibold rounded-xl">
                Currently Subscribed
            </a>
        @elseif(auth()->check())
            <a href="{{ route('subscriptions.checkout', $plan) }}" class="block w-full text-center px-6 py-3 {{ $plan->is_featured ? 'bg-primary-600 hover:bg-primary-700' : 'bg-surface-900 dark:bg-white hover:bg-surface-800 dark:hover:bg-surface-100' }} text-white {{ $plan->is_featured ? '' : 'dark:text-surface-900' }} font-semibold rounded-xl transition-colors">
                @if($plan->trial_days > 0)
                    Start Free Trial
                @else
                    Subscribe Now
                @endif
            </a>
        @else
            <a href="{{ route('login', ['redirect' => route('subscriptions.checkout', $plan)]) }}" class="block w-full text-center px-6 py-3 {{ $plan->is_featured ? 'bg-primary-600 hover:bg-primary-700' : 'bg-surface-900 dark:bg-white hover:bg-surface-800 dark:hover:bg-surface-100' }} text-white {{ $plan->is_featured ? '' : 'dark:text-surface-900' }} font-semibold rounded-xl transition-colors">
                Login to Subscribe
            </a>
        @endif
    </div>

    <!-- Seller Info -->
    @if($plan->seller)
        <div class="px-6 py-3 bg-surface-50 dark:bg-surface-700/50 border-t border-surface-100 dark:border-surface-700">
            <div class="flex items-center gap-2">
                <img src="{{ $plan->seller->user->avatar_url }}" alt="{{ $plan->seller->store_name }}" class="w-6 h-6 rounded-full">
                <span class="text-sm text-surface-600 dark:text-surface-400">by {{ $plan->seller->store_name }}</span>
            </div>
        </div>
    @endif
</div>
