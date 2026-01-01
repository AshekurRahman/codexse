<x-layouts.app>
    <x-slot name="title">Subscription Details</x-slot>

    <div class="min-h-screen bg-surface-50 dark:bg-surface-900 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <a href="{{ route('subscriptions.manage') }}" class="inline-flex items-center gap-2 text-surface-600 dark:text-surface-400 hover:text-primary-600 mb-8">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Subscriptions
            </a>

            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-surface-200 dark:border-surface-700">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4">
                            @if($subscription->plan->product && $subscription->plan->product->thumbnail)
                                <img src="{{ asset('storage/' . $subscription->plan->product->thumbnail) }}" alt="{{ $subscription->plan->product->name }}" class="w-20 h-20 rounded-lg object-cover">
                            @elseif($subscription->plan->service && $subscription->plan->service->image)
                                <img src="{{ asset('storage/' . $subscription->plan->service->image) }}" alt="{{ $subscription->plan->service->title }}" class="w-20 h-20 rounded-lg object-cover">
                            @else
                                <div class="w-20 h-20 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $subscription->plan->name }}</h1>
                                @if($subscription->plan->product)
                                    <p class="text-surface-600 dark:text-surface-400">{{ $subscription->plan->product->name }}</p>
                                @elseif($subscription->plan->service)
                                    <p class="text-surface-600 dark:text-surface-400">{{ $subscription->plan->service->title }}</p>
                                @endif
                                <div class="flex items-center gap-3 mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($subscription->status === 'active') bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400
                                        @elseif($subscription->status === 'trialing') bg-info-100 text-info-800 dark:bg-info-900/30 dark:text-info-400
                                        @elseif($subscription->status === 'past_due') bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-400
                                        @else bg-danger-100 text-danger-800 dark:bg-danger-900/30 dark:text-danger-400
                                        @endif
                                    ">
                                        {{ $subscription->status_label }}
                                    </span>
                                    @if($subscription->cancel_at_period_end)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-danger-100 text-danger-800 dark:bg-danger-900/30 dark:text-danger-400">
                                            Canceling at period end
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-3xl font-bold text-surface-900 dark:text-white">
                                {{ $subscription->plan->formatted_price }}
                            </span>
                            <p class="text-surface-500 dark:text-surface-400">per {{ strtolower($subscription->plan->billing_period_label) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Subscription Info -->
                <div class="p-6 space-y-6">
                    <!-- Billing Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-3">Billing Information</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-surface-600 dark:text-surface-400">Started</dt>
                                    <dd class="font-medium text-surface-900 dark:text-white">{{ $subscription->created_at->format('M j, Y') }}</dd>
                                </div>
                                @if($subscription->isTrialing() && $subscription->trial_ends_at)
                                    <div class="flex justify-between">
                                        <dt class="text-surface-600 dark:text-surface-400">Trial Ends</dt>
                                        <dd class="font-medium text-info-600 dark:text-info-400">{{ $subscription->trial_ends_at->format('M j, Y') }}</dd>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <dt class="text-surface-600 dark:text-surface-400">Current Period</dt>
                                    <dd class="font-medium text-surface-900 dark:text-white">
                                        {{ $subscription->current_period_start?->format('M j') }} - {{ $subscription->current_period_end?->format('M j, Y') }}
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-surface-600 dark:text-surface-400">
                                        @if($subscription->cancel_at_period_end)
                                            Access Ends
                                        @else
                                            Next Billing
                                        @endif
                                    </dt>
                                    <dd class="font-medium {{ $subscription->cancel_at_period_end ? 'text-danger-600 dark:text-danger-400' : 'text-surface-900 dark:text-white' }}">
                                        {{ $subscription->current_period_end?->format('M j, Y') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-3">Plan Features</h3>
                            @if($subscription->plan->features && count($subscription->plan->features) > 0)
                                <ul class="space-y-2">
                                    @foreach($subscription->plan->features as $feature)
                                        <li class="flex items-center gap-2 text-surface-600 dark:text-surface-400">
                                            <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-surface-500 dark:text-surface-400">Standard access included</p>
                            @endif
                        </div>
                    </div>

                    <!-- Usage Section -->
                    @if($subscription->plan->max_downloads || $subscription->plan->max_requests)
                        <div class="pt-6 border-t border-surface-200 dark:border-surface-700">
                            <h3 class="text-sm font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-4">Usage This Period</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if($subscription->plan->max_downloads)
                                    <div class="bg-surface-50 dark:bg-surface-700/50 rounded-xl p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-medium text-surface-900 dark:text-white">Downloads</span>
                                            <span class="text-sm text-surface-600 dark:text-surface-400">
                                                {{ $subscription->downloads_remaining ?? 0 }} remaining
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="flex-1 h-3 bg-surface-200 dark:bg-surface-600 rounded-full overflow-hidden">
                                                @php
                                                    $downloadPercent = ($subscription->downloads_used / $subscription->plan->max_downloads) * 100;
                                                @endphp
                                                <div class="h-full {{ $downloadPercent > 90 ? 'bg-danger-500' : ($downloadPercent > 70 ? 'bg-warning-500' : 'bg-primary-500') }} rounded-full transition-all" style="width: {{ min($downloadPercent, 100) }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-surface-900 dark:text-white">
                                                {{ $subscription->downloads_used }}/{{ $subscription->plan->max_downloads }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                @if($subscription->plan->max_requests)
                                    <div class="bg-surface-50 dark:bg-surface-700/50 rounded-xl p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-medium text-surface-900 dark:text-white">Requests</span>
                                            <span class="text-sm text-surface-600 dark:text-surface-400">
                                                {{ $subscription->requests_remaining ?? 0 }} remaining
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="flex-1 h-3 bg-surface-200 dark:bg-surface-600 rounded-full overflow-hidden">
                                                @php
                                                    $requestPercent = ($subscription->requests_used / $subscription->plan->max_requests) * 100;
                                                @endphp
                                                <div class="h-full {{ $requestPercent > 90 ? 'bg-danger-500' : ($requestPercent > 70 ? 'bg-warning-500' : 'bg-primary-500') }} rounded-full transition-all" style="width: {{ min($requestPercent, 100) }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-surface-900 dark:text-white">
                                                {{ $subscription->requests_used }}/{{ $subscription->plan->max_requests }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Recent Invoices -->
                    @if($subscription->invoices->isNotEmpty())
                        <div class="pt-6 border-t border-surface-200 dark:border-surface-700">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Recent Invoices</h3>
                                <a href="{{ route('subscriptions.invoices', $subscription) }}" class="text-sm text-primary-600 hover:text-primary-700">View All</a>
                            </div>
                            <div class="space-y-2">
                                @foreach($subscription->invoices->take(3) as $invoice)
                                    <div class="flex items-center justify-between py-2">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                @if($invoice->status === 'paid') bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400
                                                @elseif($invoice->status === 'open') bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-400
                                                @else bg-surface-100 text-surface-800 dark:bg-surface-700 dark:text-surface-400
                                                @endif
                                            ">
                                                {{ $invoice->status_label }}
                                            </span>
                                            <span class="text-surface-600 dark:text-surface-400">{{ $invoice->created_at->format('M j, Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <span class="font-medium text-surface-900 dark:text-white">{{ $invoice->formatted_total }}</span>
                                            @if($invoice->pdf_url)
                                                <a href="{{ $invoice->pdf_url }}" target="_blank" class="text-primary-600 hover:text-primary-700">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="pt-6 border-t border-surface-200 dark:border-surface-700 flex flex-wrap gap-3" x-data="{ showCancelModal: false }">
                        @if(auth()->user()->stripe_id)
                            <a href="{{ route('subscriptions.billing-portal') }}" class="px-4 py-2 bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600 text-surface-700 dark:text-surface-300 font-medium rounded-lg transition-colors">
                                Update Payment Method
                            </a>
                        @endif

                        @if($subscription->cancel_at_period_end)
                            <form action="{{ route('subscriptions.resume', $subscription) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-success-600 hover:bg-success-700 text-white font-medium rounded-lg transition-colors">
                                    Resume Subscription
                                </button>
                            </form>
                        @elseif($subscription->isActive())
                            <button @click="showCancelModal = true" class="px-4 py-2 bg-danger-100 dark:bg-danger-900/20 hover:bg-danger-200 dark:hover:bg-danger-900/40 text-danger-700 dark:text-danger-400 font-medium rounded-lg transition-colors">
                                Cancel Subscription
                            </button>
                        @endif

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
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
