<x-layouts.app title="Secure Payment - Codexse">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Secure Escrow Payment</h1>
                <p class="mt-2 text-surface-600 dark:text-surface-400">Your payment is protected until the work is completed</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Order Details -->
                <div class="space-y-6">
                    <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">
                            @if($type === 'service_order')
                                Service Order Details
                            @else
                                Milestone Details
                            @endif
                        </h2>

                        @if($type === 'service_order')
                            <div class="space-y-4">
                                <div class="flex items-start gap-4">
                                    @if($escrowable->service->thumbnail)
                                        <img src="{{ asset('storage/' . $escrowable->service->thumbnail) }}" alt="{{ $escrowable->service->name }}" class="w-20 h-20 rounded-xl object-cover">
                                    @else
                                        <div class="w-20 h-20 rounded-xl bg-surface-100 dark:bg-surface-700 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-surface-900 dark:text-white">{{ $escrowable->title }}</h3>
                                        <p class="text-sm text-surface-600 dark:text-surface-400">{{ $escrowable->package->name ?? 'Custom' }} Package</p>
                                        <div class="mt-2 flex items-center gap-4 text-sm text-surface-500 dark:text-surface-400">
                                            <span class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $escrowable->delivery_days }} days delivery
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                {{ $escrowable->revisions_allowed ?: 'Unlimited' }} revisions
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-4 border-t border-surface-200 dark:border-surface-700">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $escrowable->seller->logo_url }}" alt="{{ $escrowable->seller->store_name }}" class="w-10 h-10 rounded-full">
                                        <div>
                                            <p class="text-sm font-medium text-surface-900 dark:text-white">{{ $escrowable->seller->store_name }}</p>
                                            <p class="text-xs text-surface-500 dark:text-surface-400">Seller</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="space-y-4">
                                <div>
                                    <h3 class="font-semibold text-surface-900 dark:text-white">{{ $escrowable->title }}</h3>
                                    @if($escrowable->description)
                                        <p class="mt-1 text-sm text-surface-600 dark:text-surface-400">{{ $escrowable->description }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4 text-sm text-surface-500 dark:text-surface-400">
                                    <span>Contract: {{ $contract->contract_number }}</span>
                                    @if($escrowable->due_date)
                                        <span>Due: {{ $escrowable->due_date->format('M d, Y') }}</span>
                                    @endif
                                </div>
                                <div class="pt-4 border-t border-surface-200 dark:border-surface-700">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $contract->seller->logo_url }}" alt="{{ $contract->seller->store_name }}" class="w-10 h-10 rounded-full">
                                        <div>
                                            <p class="text-sm font-medium text-surface-900 dark:text-white">{{ $contract->seller->store_name }}</p>
                                            <p class="text-xs text-surface-500 dark:text-surface-400">Freelancer</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- How Escrow Works -->
                    <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">How Escrow Works</h2>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                                    <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">1</span>
                                </div>
                                <div>
                                    <p class="font-medium text-surface-900 dark:text-white">You pay securely</p>
                                    <p class="text-sm text-surface-600 dark:text-surface-400">Your payment is held safely in escrow</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                                    <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">2</span>
                                </div>
                                <div>
                                    <p class="font-medium text-surface-900 dark:text-white">Seller delivers the work</p>
                                    <p class="text-sm text-surface-600 dark:text-surface-400">The seller completes and delivers your order</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                                    <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">3</span>
                                </div>
                                <div>
                                    <p class="font-medium text-surface-900 dark:text-white">You approve & release</p>
                                    <p class="text-sm text-surface-600 dark:text-surface-400">Payment is released to the seller after your approval</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div>
                    <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6 sticky top-24">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-6">Payment Summary</h2>

                        <div class="space-y-4 mb-6">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-surface-600 dark:text-surface-400">Amount</span>
                                <span class="font-medium text-surface-900 dark:text-white">${{ number_format($fees['amount'], 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-surface-600 dark:text-surface-400">Processing Fee</span>
                                <span class="font-medium text-surface-900 dark:text-white">${{ number_format($fees['platform_fee'], 2) }}</span>
                            </div>
                            <hr class="border-surface-200 dark:border-surface-700">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-surface-900 dark:text-white">Total</span>
                                <span class="text-2xl font-bold text-surface-900 dark:text-white">${{ number_format($fees['amount'], 2) }}</span>
                            </div>
                        </div>

                        <!-- Stripe Payment Form -->
                        <div id="payment-form">
                            <div id="payment-element" class="mb-6"></div>
                            <div id="payment-message" class="hidden text-sm text-red-600 dark:text-red-400 mb-4"></div>

                            <button id="submit-button" type="button" class="w-full inline-flex items-center justify-center rounded-xl bg-primary-600 px-6 py-4 text-base font-semibold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg id="spinner" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg id="lock-icon" xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span id="button-text">Pay ${{ number_format($fees['amount'], 2) }}</span>
                            </button>
                        </div>

                        <div class="mt-6 flex items-center justify-center gap-4 text-xs text-surface-500 dark:text-surface-400">
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>SSL Secured</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span>Buyer Protection</span>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-center text-surface-500 dark:text-surface-400">
                            By completing this payment, you agree to our
                            <a href="{{ route('terms') }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">Terms of Service</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ $stripePublicKey }}');
        let elements;
        let paymentElement;
        let transactionId;

        document.addEventListener('DOMContentLoaded', async function() {
            // Create PaymentIntent
            const response = await fetch('{{ route('escrow.create-payment-intent') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    type: '{{ $type }}',
                    id: {{ $escrowable->id }}
                })
            });

            const data = await response.json();

            if (data.error) {
                showMessage(data.error);
                return;
            }

            transactionId = data.transactionId;

            elements = stripe.elements({
                clientSecret: data.clientSecret,
                appearance: {
                    theme: document.documentElement.classList.contains('dark') ? 'night' : 'stripe',
                    variables: {
                        colorPrimary: '#7c3aed',
                        borderRadius: '12px'
                    }
                }
            });

            paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');
        });

        document.getElementById('submit-button').addEventListener('click', async function() {
            setLoading(true);

            const { error } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: '{{ route('escrow.confirm') }}?transaction_id=' + transactionId,
                }
            });

            if (error) {
                showMessage(error.message);
                setLoading(false);
            }
        });

        function showMessage(messageText) {
            const messageContainer = document.getElementById('payment-message');
            messageContainer.classList.remove('hidden');
            messageContainer.textContent = messageText;
        }

        function setLoading(isLoading) {
            const submitButton = document.getElementById('submit-button');
            const spinner = document.getElementById('spinner');
            const lockIcon = document.getElementById('lock-icon');
            const buttonText = document.getElementById('button-text');

            if (isLoading) {
                submitButton.disabled = true;
                spinner.classList.remove('hidden');
                lockIcon.classList.add('hidden');
                buttonText.textContent = 'Processing...';
            } else {
                submitButton.disabled = false;
                spinner.classList.add('hidden');
                lockIcon.classList.remove('hidden');
                buttonText.textContent = 'Pay ${{ number_format($fees['amount'], 2) }}';
            }
        }
    </script>
    @endpush
</x-layouts.app>
