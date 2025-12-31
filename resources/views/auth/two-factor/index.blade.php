<x-layouts.app title="Two-Factor Authentication - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-surface-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="flex">
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <p class="ml-3 text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <p class="ml-3 text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ session('warning') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($enabled)
                        <!-- 2FA is enabled -->
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-surface-900 dark:text-white">Two-Factor Authentication is Enabled</h3>
                                <p class="text-sm text-surface-600 dark:text-surface-400">Your account is protected with an additional layer of security.</p>
                            </div>
                        </div>

                        <!-- Recovery Codes -->
                        <div class="mb-6 p-4 bg-surface-50 dark:bg-surface-700 rounded-lg">
                            <h4 class="font-medium text-surface-900 dark:text-white mb-2">Recovery Codes</h4>
                            <p class="text-sm text-surface-600 dark:text-surface-400 mb-4">
                                Store these recovery codes in a secure location. They can be used to access your account if you lose access to your authenticator app.
                            </p>
                            @if(session('recoveryCodes'))
                                <div class="grid grid-cols-2 gap-2 mb-4 p-4 bg-white dark:bg-surface-800 rounded border border-surface-200 dark:border-surface-600 font-mono text-sm">
                                    @foreach(session('recoveryCodes') as $code)
                                        <div class="text-surface-900 dark:text-white">{{ $code }}</div>
                                    @endforeach
                                </div>
                            @else
                                <div class="grid grid-cols-2 gap-2 mb-4 p-4 bg-white dark:bg-surface-800 rounded border border-surface-200 dark:border-surface-600 font-mono text-sm">
                                    @foreach($recoveryCodes as $code)
                                        <div class="text-surface-900 dark:text-white">{{ $code }}</div>
                                    @endforeach
                                </div>
                            @endif

                            <form method="POST" action="{{ route('two-factor.regenerate-codes') }}" class="inline">
                                @csrf
                                <div class="flex items-center gap-3">
                                    <input type="password" name="password" placeholder="Enter password to regenerate" class="flex-1 rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 text-sm" required>
                                    <button type="submit" class="px-4 py-2 bg-surface-200 dark:bg-surface-600 text-surface-700 dark:text-surface-300 rounded-lg text-sm font-medium hover:bg-surface-300 dark:hover:bg-surface-500 transition-colors">
                                        Regenerate Codes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Disable 2FA -->
                        <div class="border-t border-surface-200 dark:border-surface-700 pt-6">
                            <h4 class="font-medium text-surface-900 dark:text-white mb-2">Disable Two-Factor Authentication</h4>
                            <p class="text-sm text-surface-600 dark:text-surface-400 mb-4">
                                If you disable two-factor authentication, your account will be less secure.
                            </p>
                            <form method="POST" action="{{ route('two-factor.disable') }}">
                                @csrf
                                <div class="flex items-center gap-3">
                                    <input type="password" name="password" placeholder="Enter your password" class="flex-1 rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-700 text-sm" required>
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                                        Disable 2FA
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </form>
                        </div>
                    @else
                        <!-- 2FA is not enabled -->
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex-shrink-0 w-12 h-12 bg-surface-100 dark:bg-surface-700 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-surface-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-surface-900 dark:text-white">Two-Factor Authentication is Not Enabled</h3>
                                <p class="text-sm text-surface-600 dark:text-surface-400">Add an extra layer of security to your account.</p>
                            </div>
                        </div>

                        <div class="prose dark:prose-invert max-w-none mb-6">
                            <p class="text-surface-600 dark:text-surface-400">
                                Two-factor authentication adds an additional layer of security to your account by requiring more than just a password to sign in.
                            </p>
                            <p class="text-surface-600 dark:text-surface-400">
                                When two-factor authentication is enabled, you'll need to enter a code from your authenticator app after entering your password.
                            </p>
                        </div>

                        <a href="{{ route('two-factor.setup') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Enable Two-Factor Authentication
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
