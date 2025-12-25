<x-layouts.app title="Join Affiliate Program - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-surface-900 dark:text-white">Join Our Affiliate Program</h1>
                <p class="mt-2 text-surface-600 dark:text-surface-400">Earn commissions by referring customers to our marketplace</p>
            </div>

            <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                <div class="p-6 border-b border-surface-200 dark:border-surface-700 bg-gradient-to-r from-primary-500 to-primary-600 text-white">
                    <div class="flex items-center justify-center gap-8">
                        <div class="text-center">
                            <p class="text-3xl font-bold">10%</p>
                            <p class="text-sm opacity-90">Commission Rate</p>
                        </div>
                        <div class="h-12 w-px bg-white/30"></div>
                        <div class="text-center">
                            <p class="text-3xl font-bold">30</p>
                            <p class="text-sm opacity-90">Day Cookie</p>
                        </div>
                        <div class="h-12 w-px bg-white/30"></div>
                        <div class="text-center">
                            <p class="text-3xl font-bold">$50</p>
                            <p class="text-sm opacity-90">Min Payout</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <div>
                        <h3 class="font-semibold text-surface-900 dark:text-white mb-3">How it works</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-sm font-medium">1</span>
                                <span class="text-surface-600 dark:text-surface-400">Sign up for our affiliate program</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-sm font-medium">2</span>
                                <span class="text-surface-600 dark:text-surface-400">Share your unique referral link</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-sm font-medium">3</span>
                                <span class="text-surface-600 dark:text-surface-400">Earn 10% commission on every sale</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-sm font-medium">4</span>
                                <span class="text-surface-600 dark:text-surface-400">Get paid monthly via PayPal</span>
                            </li>
                        </ul>
                    </div>

                    <form action="{{ route('affiliate.apply.store') }}" method="POST" class="space-y-6 pt-6 border-t border-surface-200 dark:border-surface-700">
                        @csrf

                        <div>
                            <label for="paypal_email" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">PayPal Email</label>
                            <input type="email" name="paypal_email" id="paypal_email" value="{{ old('paypal_email') }}" required class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500" placeholder="your@email.com">
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">We'll use this to send your commission payments</p>
                            @error('paypal_email')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-start gap-3">
                            <input type="checkbox" name="terms" id="terms" required class="mt-1 rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500">
                            <label for="terms" class="text-sm text-surface-600 dark:text-surface-400">
                                I agree to the <a href="#" class="text-primary-600 hover:underline">affiliate terms and conditions</a>
                            </label>
                        </div>

                        <button type="submit" class="w-full rounded-lg bg-primary-600 px-6 py-3 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                            Apply Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
