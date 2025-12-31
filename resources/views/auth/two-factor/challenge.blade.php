<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-100 dark:bg-primary-900/30 rounded-full mb-4">
            <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Two-Factor Authentication</h1>
        <p class="mt-2 text-surface-600 dark:text-surface-400">Enter the code from your authenticator app</p>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ session('warning') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.verify') }}" x-data="{ useRecovery: false }">
        @csrf

        <div x-show="!useRecovery">
            <div class="mb-4">
                <label for="code" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
                    Authentication Code
                </label>
                <input
                    type="text"
                    id="code"
                    name="code"
                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 font-mono text-xl tracking-widest text-center py-4"
                    placeholder="000000"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    autofocus
                >
                <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">
                    Enter the 6-digit code from your authenticator app
                </p>
            </div>
        </div>

        <div x-show="useRecovery" style="display: none;">
            <div class="mb-4">
                <label for="recovery_code" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
                    Recovery Code
                </label>
                <input
                    type="text"
                    id="recovery_code"
                    name="code"
                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 font-mono text-lg tracking-widest text-center py-4"
                    placeholder="XXXXXXXXXX"
                    maxlength="10"
                >
                <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">
                    Enter one of your recovery codes
                </p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" class="w-full px-6 py-3 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors">
                Verify
            </button>
        </div>

        <!-- Toggle Recovery Code -->
        <div class="mt-4 text-center">
            <button
                type="button"
                @click="useRecovery = !useRecovery"
                class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300"
            >
                <span x-show="!useRecovery">Use a recovery code</span>
                <span x-show="useRecovery" style="display: none;">Use authenticator app</span>
            </button>
        </div>

        <!-- Back to Login -->
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white">
                Back to login
            </a>
        </div>
    </form>
</x-guest-layout>
