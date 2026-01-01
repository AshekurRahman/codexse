<x-layouts.app title="Add Funds - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('wallet.index') }}" class="inline-flex items-center text-sm text-surface-500 dark:text-surface-400 hover:text-surface-700 dark:hover:text-surface-300 mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Wallet
                </a>
                <h1 class="text-3xl font-bold text-surface-900 dark:text-white">Add Funds</h1>
                <p class="mt-2 text-surface-600 dark:text-surface-400">Deposit money to your wallet for faster checkouts</p>
            </div>

            @if(session('error'))
                <div class="mb-6 rounded-lg bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-danger-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm text-danger-700 dark:text-danger-300">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 rounded-lg bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-800 p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-info-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm text-info-700 dark:text-info-300">{{ session('info') }}</p>
                    </div>
                </div>
            @endif

            <!-- Current Balance -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-surface-500 dark:text-surface-400">Current Balance</p>
                        <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $wallet->formatted_balance }}</p>
                    </div>
                    <div class="bg-primary-100 dark:bg-primary-900/30 rounded-full p-3">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Deposit Form -->
            <form action="{{ route('wallet.deposit.process') }}" method="POST" class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                @csrf

                <h2 class="text-lg font-semibold text-surface-900 dark:text-white mb-6">Select Amount</h2>

                <!-- Preset Amounts -->
                <div class="grid grid-cols-3 gap-3 mb-6">
                    @foreach($presetAmounts as $preset)
                        <button type="button"
                                onclick="setAmount({{ $preset }})"
                                class="preset-btn py-3 px-4 rounded-xl border-2 border-surface-200 dark:border-surface-600 text-center font-semibold text-surface-700 dark:text-surface-300 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-surface-800">
                            ${{ $preset }}
                        </button>
                    @endforeach
                </div>

                <!-- Custom Amount -->
                <div class="mb-6">
                    <label for="amount" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Or enter custom amount</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-surface-500 dark:text-surface-400 text-lg font-medium">$</span>
                        <input type="number"
                               name="amount"
                               id="amount"
                               step="0.01"
                               min="5"
                               max="10000"
                               value="{{ old('amount', 50) }}"
                               class="w-full pl-8 pr-4 py-4 text-xl font-semibold border-2 border-surface-200 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-800 transition"
                               placeholder="0.00">
                    </div>
                    @error('amount')
                        <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-surface-500 dark:text-surface-400">Minimum: $5.00 | Maximum: $10,000.00</p>
                </div>

                <!-- Payment Info -->
                <div class="bg-surface-50 dark:bg-surface-700/50 rounded-xl p-4 mb-6">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="w-8 h-8" viewBox="0 0 32 32" fill="none">
                            <rect width="32" height="32" rx="6" fill="#635BFF"/>
                            <path d="M15.5 13.5C15.5 12.67 16.17 12 17 12H20.5C21.33 12 22 12.67 22 13.5V18.5C22 19.33 21.33 20 20.5 20H17C16.17 20 15.5 19.33 15.5 18.5V13.5Z" fill="white"/>
                            <path d="M10 13.5C10 12.67 10.67 12 11.5 12H15C15.83 12 16.5 12.67 16.5 13.5V18.5C16.5 19.33 15.83 20 15 20H11.5C10.67 20 10 19.33 10 18.5V13.5Z" fill="white" fill-opacity="0.6"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-surface-900 dark:text-white">Secure Payment via Stripe</p>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Your payment details are encrypted</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <img src="https://cdn-icons-png.flaticon.com/512/349/349221.png" alt="Visa" class="h-6">
                        <img src="https://cdn-icons-png.flaticon.com/512/349/349228.png" alt="Mastercard" class="h-6">
                        <img src="https://cdn-icons-png.flaticon.com/512/349/349230.png" alt="Amex" class="h-6">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-4 px-6 rounded-xl transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Continue to Payment
                </button>
            </form>

            <!-- Benefits -->
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="text-center p-4">
                    <div class="w-12 h-12 bg-success-100 dark:bg-success-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-surface-900 dark:text-white">Instant Purchases</p>
                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">No payment needed at checkout</p>
                </div>
                <div class="text-center p-4">
                    <div class="w-12 h-12 bg-info-100 dark:bg-info-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-info-600 dark:text-info-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-surface-900 dark:text-white">Secure & Protected</p>
                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">Your funds are always safe</p>
                </div>
                <div class="text-center p-4">
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-surface-900 dark:text-white">Track Everything</p>
                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">Complete transaction history</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setAmount(amount) {
            document.getElementById('amount').value = amount;

            // Update button styles
            document.querySelectorAll('.preset-btn').forEach(btn => {
                btn.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/30', 'text-primary-600', 'dark:text-primary-400');
                btn.classList.add('border-surface-200', 'dark:border-surface-600', 'text-surface-700', 'dark:text-surface-300');
            });

            event.target.classList.remove('border-surface-200', 'dark:border-surface-600', 'text-surface-700', 'dark:text-surface-300');
            event.target.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/30', 'text-primary-600', 'dark:text-primary-400');
        }
    </script>
</x-layouts.app>
