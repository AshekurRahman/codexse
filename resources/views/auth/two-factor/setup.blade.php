<x-layouts.app title="Set Up Two-Factor Authentication - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-surface-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-2">Step 1: Scan QR Code</h3>
                        <p class="text-sm text-surface-600 dark:text-surface-400 mb-4">
                            Scan this QR code with your authenticator app (Google Authenticator, Authy, 1Password, etc.)
                        </p>

                        <div class="flex justify-center p-6 bg-white rounded-lg border border-surface-200 dark:border-surface-600 mb-4">
                            {!! $qrCodeSvg !!}
                        </div>

                        <div class="p-4 bg-surface-50 dark:bg-surface-700 rounded-lg">
                            <p class="text-sm text-surface-600 dark:text-surface-400 mb-2">
                                Can't scan the QR code? Enter this code manually:
                            </p>
                            <code class="block p-3 bg-white dark:bg-surface-800 rounded border border-surface-200 dark:border-surface-600 font-mono text-sm text-surface-900 dark:text-white break-all select-all">
                                {{ $secret }}
                            </code>
                        </div>
                    </div>

                    <div class="border-t border-surface-200 dark:border-surface-700 pt-6">
                        <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-2">Step 2: Verify Code</h3>
                        <p class="text-sm text-surface-600 dark:text-surface-400 mb-4">
                            Enter the 6-digit code from your authenticator app to complete the setup.
                        </p>

                        <form method="POST" action="{{ route('two-factor.enable') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="code" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">
                                    Verification Code
                                </label>
                                <input
                                    type="text"
                                    id="code"
                                    name="code"
                                    class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 font-mono text-lg tracking-widest text-center"
                                    placeholder="000000"
                                    maxlength="6"
                                    pattern="[0-9]{6}"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                    required
                                    autofocus
                                >
                                @error('code')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors">
                                    Enable Two-Factor Authentication
                                </button>
                                <a href="{{ route('two-factor.index') }}" class="px-6 py-3 text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white transition-colors">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
