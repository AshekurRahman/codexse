<x-layouts.app title="Delete My Data - GDPR">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <a href="{{ route('gdpr.index') }}" class="inline-flex items-center gap-2 text-surface-600 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Privacy Center
            </a>

            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex items-center justify-center w-16 h-16 bg-danger-100 dark:bg-danger-900/30 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-danger-600 dark:text-danger-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Delete My Account</h1>
                        <p class="text-surface-600 dark:text-surface-400">Request permanent deletion of your data</p>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-xl text-success-700 dark:text-success-300">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-xl text-danger-700 dark:text-danger-300">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Pending Request -->
            @if($pendingDeletion)
            <div class="bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-xl p-6 mb-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-warning-600 dark:text-warning-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-warning-900 dark:text-warning-100">Deletion Request Pending</h3>
                        <p class="text-sm text-warning-700 dark:text-warning-300 mt-1">
                            Your account deletion request ({{ $pendingDeletion->request_number }}) is being reviewed.
                            This process may take up to 30 days.
                        </p>
                        <p class="text-sm text-warning-600 dark:text-warning-400 mt-2">
                            Submitted: {{ $pendingDeletion->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                        @if($pendingDeletion->canBeCancelled())
                        <form action="{{ route('gdpr.request.cancel', $pendingDeletion) }}" method="POST" class="mt-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-warning-700 dark:text-warning-300 underline hover:no-underline">
                                Cancel this request
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @else

            <!-- Warning Card -->
            <div class="bg-danger-50 dark:bg-danger-900/20 border-2 border-danger-200 dark:border-danger-800 rounded-xl p-6 mb-6">
                <div class="flex gap-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-danger-600 dark:text-danger-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h3 class="font-semibold text-danger-900 dark:text-danger-100">Warning: This action cannot be undone</h3>
                        <p class="text-sm text-danger-700 dark:text-danger-300 mt-2">
                            Once your account is deleted:
                        </p>
                        <ul class="list-disc list-inside text-sm text-danger-700 dark:text-danger-300 mt-2 space-y-1">
                            <li>All your personal data will be permanently erased</li>
                            <li>You will lose access to purchased products and licenses</li>
                            <li>Your reviews and comments will be anonymized</li>
                            <li>Any pending orders or refunds will be processed first</li>
                            <li>Your seller account (if any) will be deactivated</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Before You Go -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 mb-6">
                <h3 class="font-semibold text-surface-900 dark:text-white mb-4">Before you go...</h3>
                <p class="text-surface-600 dark:text-surface-400 text-sm mb-4">
                    We'd hate to see you leave. Here are some alternatives:
                </p>
                <div class="space-y-3">
                    <a href="{{ route('gdpr.export') }}" class="flex items-center gap-3 p-3 rounded-lg border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                        <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-surface-900 dark:text-white">Export your data first</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Download a copy before deletion</p>
                        </div>
                    </a>
                    <a href="{{ route('gdpr.consent') }}" class="flex items-center gap-3 p-3 rounded-lg border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                        <div class="w-10 h-10 bg-success-100 dark:bg-success-900/30 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success-600 dark:text-success-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-surface-900 dark:text-white">Adjust privacy settings</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Control how we use your data</p>
                        </div>
                    </a>
                    <a href="{{ route('support.create') }}" class="flex items-center gap-3 p-3 rounded-lg border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                        <div class="w-10 h-10 bg-info-100 dark:bg-info-900/30 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-info-600 dark:text-info-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-surface-900 dark:text-white">Contact support</p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Tell us about any issues</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Deletion Form -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border-2 border-danger-200 dark:border-danger-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-danger-200 dark:border-danger-800 bg-danger-50 dark:bg-danger-900/20">
                    <h2 class="font-semibold text-danger-900 dark:text-danger-100">Request Account Deletion</h2>
                </div>
                <form action="{{ route('gdpr.delete.submit') }}" method="POST" class="p-6">
                    @csrf

                    <div class="mb-6">
                        <label for="reason" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            Why are you leaving? (optional)
                        </label>
                        <textarea id="reason" name="reason" rows="3"
                            class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Your feedback helps us improve..."></textarea>
                        @error('reason')
                            <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            Confirm your password
                        </label>
                        <input type="password" id="password" name="password" required
                            class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Enter your current password">
                        @error('password')
                            <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="flex items-start gap-3">
                            <input type="checkbox" name="confirm_deletion" value="1" required
                                class="w-4 h-4 mt-1 text-danger-600 border-surface-300 rounded focus:ring-danger-500">
                            <span class="text-sm text-surface-600 dark:text-surface-400">
                                I understand that this action is permanent and all my data will be deleted. I have exported any data I wish to keep.
                            </span>
                        </label>
                        @error('confirm_deletion')
                            <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-danger-600 text-white rounded-lg font-medium hover:bg-danger-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Request Account Deletion
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</x-layouts.app>
