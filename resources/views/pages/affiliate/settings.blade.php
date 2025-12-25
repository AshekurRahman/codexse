<x-layouts.app title="Affiliate Settings - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <a href="{{ route('affiliate.dashboard') }}" class="inline-flex items-center text-sm text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Dashboard
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Affiliate Settings</h1>
                <p class="mt-1 text-surface-600 dark:text-surface-400">Manage your affiliate account settings</p>
            </div>

            <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                <form action="{{ route('affiliate.settings.update') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="paypal_email" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">PayPal Email</label>
                        <input type="email" name="paypal_email" id="paypal_email" value="{{ old('paypal_email', $affiliate->paypal_email) }}" required class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">We'll use this to send your commission payments</p>
                        @error('paypal_email')
                            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 border-t border-surface-200 dark:border-surface-700">
                        <h3 class="font-medium text-surface-900 dark:text-white mb-4">Account Information</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-surface-500 dark:text-surface-400">Affiliate Code</dt>
                                <dd class="font-mono text-surface-900 dark:text-white">{{ $affiliate->code }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-surface-500 dark:text-surface-400">Commission Rate</dt>
                                <dd class="text-surface-900 dark:text-white">{{ $affiliate->commission_rate }}%</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-surface-500 dark:text-surface-400">Status</dt>
                                <dd>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                        @if($affiliate->status === 'active') bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400
                                        @elseif($affiliate->status === 'pending') bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400
                                        @else bg-danger-100 dark:bg-danger-900/30 text-danger-700 dark:text-danger-400
                                        @endif">
                                        {{ ucfirst($affiliate->status) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-surface-500 dark:text-surface-400">Member Since</dt>
                                <dd class="text-surface-900 dark:text-white">{{ $affiliate->created_at->format('M d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="inline-flex items-center rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
