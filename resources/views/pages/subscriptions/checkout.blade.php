<x-layouts.app>
    <x-slot name="title">Subscribe to {{ $plan->name }}</x-slot>

    <div class="min-h-screen bg-surface-50 dark:bg-surface-900 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center gap-2 text-surface-600 dark:text-surface-400 hover:text-primary-600 mb-8">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Plans
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <!-- Order Summary -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm p-6 sticky top-8">
                        <h2 class="text-lg font-bold text-surface-900 dark:text-white mb-6">Order Summary</h2>

                        <!-- Plan Details -->
                        <div class="flex items-start gap-4 pb-6 border-b border-surface-200 dark:border-surface-700">
                            @if($plan->product && $plan->product->thumbnail)
                                <img src="{{ asset('storage/' . $plan->product->thumbnail) }}" alt="{{ $plan->product->name }}" class="w-16 h-16 rounded-lg object-cover">
                            @elseif($plan->service && $plan->service->image)
                                <img src="{{ asset('storage/' . $plan->service->image) }}" alt="{{ $plan->service->title }}" class="w-16 h-16 rounded-lg object-cover">
                            @else
                                <div class="w-16 h-16 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="font-semibold text-surface-900 dark:text-white">{{ $plan->name }}</h3>
                                @if($plan->product)
                                    <p class="text-sm text-surface-600 dark:text-surface-400">{{ $plan->product->name }}</p>
                                @elseif($plan->service)
                                    <p class="text-sm text-surface-600 dark:text-surface-400">{{ $plan->service->title }}</p>
                                @endif
                                <p class="text-sm text-primary-600 dark:text-primary-400 mt-1">
                                    Billed {{ strtolower($plan->billing_period_label) }}
                                </p>
                            </div>
                        </div>

                        <!-- Features -->
                        @if($plan->features && count($plan->features) > 0)
                            <div class="py-6 border-b border-surface-200 dark:border-surface-700">
                                <h4 class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-3">Includes:</h4>
                                <ul class="space-y-2">
                                    @foreach($plan->features as $feature)
                                        <li class="flex items-center gap-2 text-sm text-surface-600 dark:text-surface-400">
                                            <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Pricing -->
                        <div class="pt-6 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-surface-600 dark:text-surface-400">Subtotal</span>
                                <span class="text-surface-900 dark:text-white">{{ $plan->formatted_price }}</span>
                            </div>
                            @if($plan->trial_days > 0)
                                <div class="flex items-center justify-between text-success-600">
                                    <span>{{ $plan->trial_days }}-day free trial</span>
                                    <span>-{{ $plan->formatted_price }}</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between pt-3 border-t border-surface-200 dark:border-surface-700">
                                <span class="font-semibold text-surface-900 dark:text-white">Due Today</span>
                                <span class="text-xl font-bold text-surface-900 dark:text-white">
                                    @if($plan->trial_days > 0)
                                        $0.00
                                    @else
                                        {{ $plan->formatted_price }}
                                    @endif
                                </span>
                            </div>
                            @if($plan->trial_days > 0)
                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                    Then {{ $plan->formatted_price }}/{{ strtolower($plan->billing_period_label) }} after trial
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Checkout Form -->
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm p-6">
                        <h2 class="text-lg font-bold text-surface-900 dark:text-white mb-6">Complete Subscription</h2>

                        <!-- User Info -->
                        <div class="mb-6 p-4 bg-surface-50 dark:bg-surface-700/50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="font-medium text-surface-900 dark:text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-sm text-surface-600 dark:text-surface-400">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Stripe Checkout -->
                        <div id="checkout-container">
                            <button
                                id="checkout-button"
                                class="w-full px-6 py-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                @if($plan->trial_days > 0)
                                    Start {{ $plan->trial_days }}-Day Free Trial
                                @else
                                    Subscribe for {{ $plan->formatted_price }}/{{ strtolower($plan->billing_period_label) }}
                                @endif
                            </button>

                            <div id="checkout-loading" class="hidden">
                                <div class="flex items-center justify-center py-4">
                                    <svg class="animate-spin h-8 w-8 text-primary-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                <p class="text-center text-surface-600 dark:text-surface-400">Redirecting to secure checkout...</p>
                            </div>

                            <div id="checkout-error" class="hidden mt-4 p-4 bg-danger-50 dark:bg-danger-900/20 text-danger-700 dark:text-danger-400 rounded-lg">
                            </div>
                        </div>

                        <!-- Security Notice -->
                        <div class="mt-6 flex items-center gap-2 text-sm text-surface-500 dark:text-surface-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Secure checkout powered by Stripe. Your payment info is never stored on our servers.</span>
                        </div>

                        <!-- Terms -->
                        <p class="mt-4 text-xs text-surface-500 dark:text-surface-400">
                            By subscribing, you agree to our
                            <a href="{{ route('terms') }}" class="text-primary-600 hover:underline">Terms of Service</a>
                            and
                            <a href="{{ route('privacy') }}" class="text-primary-600 hover:underline">Privacy Policy</a>.
                            @if($plan->trial_days > 0)
                                Your free trial will begin immediately. You will be charged {{ $plan->formatted_price }} after your {{ $plan->trial_days }}-day trial ends unless you cancel.
                            @else
                                Your subscription will begin immediately upon payment.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ config('services.stripe.key') }}');
        const checkoutButton = document.getElementById('checkout-button');
        const checkoutLoading = document.getElementById('checkout-loading');
        const checkoutError = document.getElementById('checkout-error');

        checkoutButton.addEventListener('click', async () => {
            checkoutButton.classList.add('hidden');
            checkoutLoading.classList.remove('hidden');
            checkoutError.classList.add('hidden');

            try {
                const response = await fetch('{{ route('subscriptions.create-checkout-session', $plan) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.error) {
                    throw new Error(data.error);
                }

                const result = await stripe.redirectToCheckout({
                    sessionId: data.sessionId
                });

                if (result.error) {
                    throw new Error(result.error.message);
                }
            } catch (error) {
                checkoutButton.classList.remove('hidden');
                checkoutLoading.classList.add('hidden');
                checkoutError.classList.remove('hidden');
                checkoutError.textContent = error.message || 'An error occurred. Please try again.';
            }
        });
    </script>
    @endpush
</x-layouts.app>
