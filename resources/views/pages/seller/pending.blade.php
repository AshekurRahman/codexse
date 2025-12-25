<x-layouts.app title="Application Under Review - Codexse">
    <section class="bg-surface-50 dark:bg-surface-900 min-h-screen py-12">
        <div class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-8 text-center">
                <!-- Success Animation -->
                @if(session('success'))
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-success-100 dark:bg-success-900/30 mb-4">
                            <svg class="w-10 h-10 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Application Submitted!</h1>
                        <p class="mt-2 text-surface-600 dark:text-surface-400">Thank you for applying to become a seller on Codexse.</p>
                    </div>
                @else
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary-100 dark:bg-primary-900/30 mb-4">
                            <svg class="w-10 h-10 text-primary-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Application Under Review</h1>
                        <p class="mt-2 text-surface-600 dark:text-surface-400">Your seller application is being reviewed by our team.</p>
                    </div>
                @endif

                <!-- Status Card -->
                <div class="bg-surface-50 dark:bg-surface-700/50 rounded-xl p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-surface-600 dark:text-surface-400">Application Status</span>
                        @if($seller->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-100 dark:bg-warning-900/30 text-warning-800 dark:text-warning-300">
                                <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-warning-500 animate-pulse"></span>
                                Pending Review
                            </span>
                        @elseif($seller->status === 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-100 dark:bg-danger-900/30 text-danger-800 dark:text-danger-300">
                                Rejected
                            </span>
                        @endif
                    </div>

                    <div class="space-y-3 text-left">
                        <div class="flex justify-between">
                            <span class="text-sm text-surface-500 dark:text-surface-400">Store Name</span>
                            <span class="text-sm font-medium text-surface-900 dark:text-white">{{ $seller->store_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-surface-500 dark:text-surface-400">Submitted</span>
                            <span class="text-sm font-medium text-surface-900 dark:text-white">{{ $seller->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-surface-500 dark:text-surface-400">Expected Review</span>
                            <span class="text-sm font-medium text-surface-900 dark:text-white">24-48 hours</span>
                        </div>
                    </div>
                </div>

                @if($seller->status === 'pending')
                    <!-- What's Next -->
                    <div class="text-left mb-6">
                        <h3 class="font-medium text-surface-900 dark:text-white mb-3">What happens next?</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                    <span class="text-xs font-medium text-primary-600 dark:text-primary-400">1</span>
                                </div>
                                <p class="text-sm text-surface-600 dark:text-surface-400">Our team reviews your application and portfolio</p>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                    <span class="text-xs font-medium text-primary-600 dark:text-primary-400">2</span>
                                </div>
                                <p class="text-sm text-surface-600 dark:text-surface-400">You'll receive an email with the decision within 24-48 hours</p>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                    <span class="text-xs font-medium text-primary-600 dark:text-primary-400">3</span>
                                </div>
                                <p class="text-sm text-surface-600 dark:text-surface-400">Once approved, you can start uploading products immediately</p>
                            </li>
                        </ul>
                    </div>
                @elseif($seller->status === 'rejected')
                    <!-- Rejection Notice -->
                    <div class="bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-lg p-4 mb-6 text-left">
                        <h3 class="font-medium text-danger-800 dark:text-danger-200 mb-2">Application Not Approved</h3>
                        <p class="text-sm text-danger-700 dark:text-danger-300">
                            Unfortunately, your application was not approved at this time. This could be due to incomplete information or not meeting our quality guidelines. You can update your application and try again.
                        </p>
                    </div>

                    <a href="{{ route('seller.apply') }}" class="inline-flex items-center justify-center w-full rounded-xl bg-gradient-to-r from-primary-600 to-primary-500 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40 transition-all hover:-translate-y-0.5">
                        Update Application
                        <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                @endif

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <a href="{{ route('home') }}" class="flex-1 inline-flex items-center justify-center rounded-lg border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 px-4 py-2.5 text-sm font-medium text-surface-700 dark:text-surface-200 hover:bg-surface-50 dark:hover:bg-surface-600 transition-colors">
                        Back to Home
                    </a>
                    <a href="{{ route('products.index') }}" class="flex-1 inline-flex items-center justify-center rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                        Browse Products
                    </a>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="mt-6 text-center">
                <p class="text-sm text-surface-500 dark:text-surface-400">
                    Questions about your application?
                    <a href="mailto:sellers@codexse.com" class="text-primary-600 dark:text-primary-400 hover:underline">Contact seller support</a>
                </p>
            </div>
        </div>
    </section>
</x-layouts.app>
