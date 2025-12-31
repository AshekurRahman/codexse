<x-layouts.app :title="'Order ' . $service->name . ' - Codexse'">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('services.show', $service) }}" class="inline-flex items-center gap-1 text-sm text-surface-500 dark:text-surface-400 hover:text-primary-600 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to service details
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Complete Your Service Order</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Provide project details so the freelancer can deliver exactly what you need. Your payment will be held securely in escrow until you approve the work.</p>
            </div>

            <form action="{{ route('services.process-order', [$service, $package]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Requirements Form -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Service Info -->
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                            <div class="flex items-start gap-4">
                                @if($service->thumbnail)
                                    <img src="{{ asset('storage/' . $service->thumbnail) }}" alt="{{ $service->name }}" class="w-24 h-16 rounded-lg object-cover">
                                @endif
                                <div class="flex-1">
                                    <h2 class="font-semibold text-surface-900 dark:text-white">{{ $service->name }}</h2>
                                    <p class="text-sm text-surface-600 dark:text-surface-400">{{ $package->name }} Package</p>
                                    <div class="mt-2 flex items-center gap-4 text-sm text-surface-500 dark:text-surface-400">
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
                                </div>
                            </div>
                        </div>

                        <!-- Requirements -->
                        @if($service->requirements->count() > 0)
                            <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                                <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-2">Project Requirements</h2>
                                <p class="text-sm text-surface-500 dark:text-surface-400 mb-6">Answer these questions to help the freelancer understand your project and deliver quality work on time.</p>
                                <div class="space-y-6">
                                    @foreach($service->requirements as $requirement)
                                        <div>
                                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                                {{ $requirement->question }}
                                                @if($requirement->is_required)
                                                    <span class="text-red-500">*</span>
                                                @endif
                                            </label>

                                            @if($requirement->type === 'text')
                                                <input type="text" name="requirements[{{ $requirement->id }}]"
                                                    class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 px-4 py-3 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                                                    {{ $requirement->is_required ? 'required' : '' }}>

                                            @elseif($requirement->type === 'textarea')
                                                <textarea name="requirements[{{ $requirement->id }}]" rows="4"
                                                    class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 px-4 py-3 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                                                    {{ $requirement->is_required ? 'required' : '' }}></textarea>

                                            @elseif($requirement->type === 'select' && $requirement->options)
                                                <select name="requirements[{{ $requirement->id }}]"
                                                    class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 px-4 py-3 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                                                    {{ $requirement->is_required ? 'required' : '' }}>
                                                    <option value="">Select an option</option>
                                                    @foreach($requirement->options as $option)
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                </select>

                                            @elseif($requirement->type === 'multiple_select' && $requirement->options)
                                                <div class="space-y-2">
                                                    @foreach($requirement->options as $option)
                                                        <label class="flex items-center gap-2">
                                                            <input type="checkbox" name="requirements[{{ $requirement->id }}][]" value="{{ $option }}"
                                                                class="rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500">
                                                            <span class="text-surface-700 dark:text-surface-300">{{ $option }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>

                                            @elseif($requirement->type === 'file')
                                                <input type="file" name="requirements[{{ $requirement->id }}]"
                                                    class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 px-4 py-3 text-surface-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 dark:file:bg-primary-900/30 dark:file:text-primary-400 hover:file:bg-primary-100"
                                                    {{ $requirement->is_required ? 'required' : '' }}>
                                                <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">Max file size: 10MB</p>
                                            @endif

                                            @error("requirements.{$requirement->id}")
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6">
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-1">Ready to Order</h3>
                                    <p class="text-surface-600 dark:text-surface-400">No additional information required. You can proceed to payment.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-6 sticky top-24">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-2">Order Summary</h2>
                            <p class="text-sm text-surface-500 dark:text-surface-400 mb-6">Review your order details before proceeding to secure payment.</p>

                            <div class="space-y-4 mb-6">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-surface-600 dark:text-surface-400">{{ $package->name }} Package</span>
                                    <span class="font-medium text-surface-900 dark:text-white">${{ number_format($package->price, 2) }}</span>
                                </div>
                                <hr class="border-surface-200 dark:border-surface-700">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-surface-900 dark:text-white">Total</span>
                                    <span class="text-2xl font-bold text-surface-900 dark:text-white">${{ number_format($package->price, 2) }}</span>
                                </div>
                            </div>

                            <button type="submit" class="w-full inline-flex items-center justify-center rounded-xl bg-primary-600 px-6 py-4 text-base font-semibold text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 hover:shadow-xl transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Continue to Secure Payment
                            </button>

                            <!-- Escrow Protection Info -->
                            <div class="mt-6 p-4 rounded-xl bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-success-600 dark:text-success-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    <div>
                                        <h4 class="font-medium text-success-800 dark:text-success-300 text-sm">Escrow Payment Protection</h4>
                                        <p class="text-xs text-success-700 dark:text-success-400 mt-1">Your payment is held securely until you review and approve the delivered work. Get a full refund if the freelancer fails to deliver.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Trust Badges -->
                            <div class="mt-4 grid grid-cols-2 gap-2 text-xs text-surface-500 dark:text-surface-400">
                                <div class="flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <span>Secure Checkout</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    <span>SSL Encrypted</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span>Money-Back Guarantee</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <span>24/7 Support</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
