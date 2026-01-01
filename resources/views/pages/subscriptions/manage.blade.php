<x-layouts.app>
    <x-slot name="title">Manage Subscriptions</x-slot>

    <div class="min-h-screen bg-surface-50 dark:bg-surface-900 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-surface-900 dark:text-white">My Subscriptions</h1>
                    <p class="text-surface-600 dark:text-surface-400 mt-1">Manage your active subscriptions and billing</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('subscriptions.index') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        Browse Plans
                    </a>
                    @if(auth()->user()->stripe_id)
                        <a href="{{ route('subscriptions.billing-portal') }}" class="px-4 py-2 bg-surface-200 dark:bg-surface-700 hover:bg-surface-300 dark:hover:bg-surface-600 text-surface-700 dark:text-surface-300 font-medium rounded-lg transition-colors">
                            Billing Portal
                        </a>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-success-50 dark:bg-success-900/20 text-success-700 dark:text-success-400 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-danger-50 dark:bg-danger-900/20 text-danger-700 dark:text-danger-400 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Active Subscriptions -->
            <div class="mb-12">
                <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-4">Active Subscriptions</h2>

                @if($activeSubscriptions->isEmpty())
                    <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm p-8 text-center">
                        <div class="w-16 h-16 bg-surface-100 dark:bg-surface-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">No Active Subscriptions</h3>
                        <p class="text-surface-600 dark:text-surface-400 mb-4">You don't have any active subscriptions yet.</p>
                        <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                            Browse Plans
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($activeSubscriptions as $subscription)
                            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm overflow-hidden" x-data="{ showCancelModal: false }">
                                <div class="p-6">
                                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                        <div class="flex items-start gap-4">
                                            @if($subscription->plan->product && $subscription->plan->product->thumbnail)
                                                <img src="{{ asset('storage/' . $subscription->plan->product->thumbnail) }}" alt="{{ $subscription->plan->product->name }}" class="w-16 h-16 rounded-lg object-cover">
                                            @elseif($subscription->plan->service && $subscription->plan->service->image)
                                                <img src="{{ asset('storage/' . $subscription->plan->service->image) }}" alt="{{ $subscription->plan->service->title }}" class="w-16 h-16 rounded-lg object-cover">
                                            @else
                                                <div class="w-16 h-16 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <h3 class="text-lg font-bold text-surface-900 dark:text-white">{{ $subscription->plan->name }}</h3>
                                                @if($subscription->plan->product)
                                                    <p class="text-surface-600 dark:text-surface-400">{{ $subscription->plan->product->name }}</p>
                                                @elseif($subscription->plan->service)
                                                    <p class="text-surface-600 dark:text-surface-400">{{ $subscription->plan->service->title }}</p>
                                                @endif
                                                <div class="flex items-center gap-3 mt-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        @if($subscription->status === 'active') bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400
                                                        @elseif($subscription->status === 'trialing') bg-info-100 text-info-800 dark:bg-info-900/30 dark:text-info-400
                                                        @elseif($subscription->status === 'past_due') bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-400
                                                        @elseif($subscription->status === 'paused') bg-surface-100 text-surface-800 dark:bg-surface-700 dark:text-surface-400
                                                        @else bg-danger-100 text-danger-800 dark:bg-danger-900/30 dark:text-danger-400
                                                        @endif
                                                    ">
                                                        {{ $subscription->status_label }}
                                                    </span>
                                                    @if($subscription->cancel_at_period_end)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-100 text-danger-800 dark:bg-danger-900/30 dark:text-danger-400">
                                                            Canceling
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-col items-end gap-2">
                                            <span class="text-2xl font-bold text-surface-900 dark:text-white">
                                                {{ $subscription->plan->formatted_price }}
                                                <span class="text-sm font-normal text-surface-500">/ {{ strtolower($subscription->plan->billing_period_label) }}</span>
                                            </span>
                                            @if($subscription->isTrialing() && $subscription->trial_ends_at)
                                                <span class="text-sm text-info-600 dark:text-info-400">
                                                    Trial ends {{ $subscription->trial_ends_at->format('M j, Y') }}
                                                </span>
                                            @elseif($subscription->current_period_end)
                                                <span class="text-sm text-surface-500 dark:text-surface-400">
                                                    @if($subscription->cancel_at_period_end)
                                                        Ends {{ $subscription->current_period_end->format('M j, Y') }}
                                                    @else
                                                        Renews {{ $subscription->current_period_end->format('M j, Y') }}
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Usage Stats -->
                                    @if($subscription->plan->max_downloads || $subscription->plan->max_requests)
                                        <div class="mt-6 pt-6 border-t border-surface-200 dark:border-surface-700">
                                            <h4 class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-3">Usage This Period</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                @if($subscription->plan->max_downloads)
                                                    <div>
                                                        <div class="flex items-center justify-between text-sm mb-1">
                                                            <span class="text-surface-600 dark:text-surface-400">Downloads</span>
                                                            <span class="font-medium text-surface-900 dark:text-white">
                                                                {{ $subscription->downloads_used }} / {{ $subscription->plan->max_downloads }}
                                                            </span>
                                                        </div>
                                                        <div class="w-full h-2 bg-surface-200 dark:bg-surface-700 rounded-full overflow-hidden">
                                                            @php
                                                                $downloadPercent = ($subscription->downloads_used / $subscription->plan->max_downloads) * 100;
                                                            @endphp
                                                            <div class="h-full {{ $downloadPercent > 90 ? 'bg-danger-500' : ($downloadPercent > 70 ? 'bg-warning-500' : 'bg-success-500') }} rounded-full transition-all" style="width: {{ min($downloadPercent, 100) }}%"></div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($subscription->plan->max_requests)
                                                    <div>
                                                        <div class="flex items-center justify-between text-sm mb-1">
                                                            <span class="text-surface-600 dark:text-surface-400">Requests</span>
                                                            <span class="font-medium text-surface-900 dark:text-white">
                                                                {{ $subscription->requests_used }} / {{ $subscription->plan->max_requests }}
                                                            </span>
                                                        </div>
                                                        <div class="w-full h-2 bg-surface-200 dark:bg-surface-700 rounded-full overflow-hidden">
                                                            @php
                                                                $requestPercent = ($subscription->requests_used / $subscription->plan->max_requests) * 100;
                                                            @endphp
                                                            <div class="h-full {{ $requestPercent > 90 ? 'bg-danger-500' : ($requestPercent > 70 ? 'bg-warning-500' : 'bg-success-500') }} rounded-full transition-all" style="width: {{ min($requestPercent, 100) }}%"></div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="mt-6 pt-6 border-t border-surface-200 dark:border-surface-700 flex flex-wrap gap-3">
                                        <a href="{{ route('subscriptions.subscription', $subscription) }}" class="px-4 py-2 bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600 text-surface-700 dark:text-surface-300 font-medium rounded-lg transition-colors">
                                            View Details
                                        </a>
                                        <a href="{{ route('subscriptions.invoices', $subscription) }}" class="px-4 py-2 bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600 text-surface-700 dark:text-surface-300 font-medium rounded-lg transition-colors">
                                            Invoices
                                        </a>
                                        @if($subscription->cancel_at_period_end)
                                            <form action="{{ route('subscriptions.resume', $subscription) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-success-600 hover:bg-success-700 text-white font-medium rounded-lg transition-colors">
                                                    Resume Subscription
                                                </button>
                                            </form>
                                        @else
                                            <button @click="showCancelModal = true" class="px-4 py-2 bg-danger-100 dark:bg-danger-900/20 hover:bg-danger-200 dark:hover:bg-danger-900/40 text-danger-700 dark:text-danger-400 font-medium rounded-lg transition-colors">
                                                Cancel Subscription
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Cancel Modal -->
                                <div x-show="showCancelModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                    <div class="fixed inset-0 bg-black/50" @click="showCancelModal = false"></div>
                                    <div class="relative bg-white dark:bg-surface-800 rounded-2xl shadow-xl max-w-md w-full p-6">
                                        <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-2">Cancel Subscription</h3>
                                        <p class="text-surface-600 dark:text-surface-400 mb-6">
                                            Are you sure you want to cancel? You'll continue to have access until {{ $subscription->current_period_end?->format('M j, Y') }}.
                                        </p>
                                        <div class="flex gap-3">
                                            <button @click="showCancelModal = false" class="flex-1 px-4 py-2 bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600 text-surface-700 dark:text-surface-300 font-medium rounded-lg transition-colors">
                                                Keep Subscription
                                            </button>
                                            <form action="{{ route('subscriptions.cancel', $subscription) }}" method="POST" class="flex-1">
                                                @csrf
                                                <button type="submit" class="w-full px-4 py-2 bg-danger-600 hover:bg-danger-700 text-white font-medium rounded-lg transition-colors">
                                                    Yes, Cancel
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Past Subscriptions -->
            @if($pastSubscriptions->isNotEmpty())
                <div>
                    <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-4">Past Subscriptions</h2>
                    <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm overflow-hidden">
                        <div class="divide-y divide-surface-200 dark:divide-surface-700">
                            @foreach($pastSubscriptions as $subscription)
                                <div class="p-4 flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-lg bg-surface-100 dark:bg-surface-700 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-surface-900 dark:text-white">{{ $subscription->plan->name }}</p>
                                            <p class="text-sm text-surface-500 dark:text-surface-400">
                                                Ended {{ $subscription->ended_at?->format('M j, Y') ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-100 text-surface-800 dark:bg-surface-700 dark:text-surface-400">
                                        {{ $subscription->status_label }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
