<x-layouts.app title="Verification Details">
    <div class="bg-surface-50 dark:bg-surface-900 min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('seller.verification.index') }}" class="inline-flex items-center gap-2 text-sm text-surface-600 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400 mb-4">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Verification Status
                </a>
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Verification Request</h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($verification->status === 'approved') bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400
                        @elseif($verification->status === 'rejected') bg-danger-100 text-danger-800 dark:bg-danger-900/30 dark:text-danger-400
                        @elseif($verification->status === 'under_review') bg-info-100 text-info-800 dark:bg-info-900/30 dark:text-info-400
                        @else bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-400
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $verification->status)) }}
                    </span>
                </div>
            </div>

            <!-- Rejection Notice -->
            @if($verification->status === 'rejected' && $verification->rejection_reason)
                <div class="bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-danger-600 dark:text-danger-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h3 class="font-medium text-danger-800 dark:text-danger-200">Verification Rejected</h3>
                            <p class="mt-1 text-sm text-danger-700 dark:text-danger-300">{{ $verification->rejection_reason }}</p>
                            @if($seller->canRequestVerification())
                                <a href="{{ route('seller.verification.create') }}" class="inline-flex items-center gap-1 mt-3 text-sm font-medium text-danger-700 dark:text-danger-300 hover:text-danger-800 dark:hover:text-danger-200">
                                    Submit a new request
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Details Card -->
            <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
                <!-- Verification Type -->
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                            @if($verification->verification_type === 'identity')
                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            @elseif($verification->verification_type === 'business')
                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h2 class="font-semibold text-surface-900 dark:text-white">{{ ucfirst($verification->verification_type) }} Verification</h2>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Submitted {{ $verification->created_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Document Info -->
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700">
                    <h3 class="text-sm font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-3">Document Information</h3>
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-surface-500 dark:text-surface-400">Document Type</dt>
                            <dd class="mt-1 text-surface-900 dark:text-white">{{ \App\Models\SellerVerification::getDocumentTypeOptions()[$verification->document_type] ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-surface-500 dark:text-surface-400">Document Number</dt>
                            <dd class="mt-1 text-surface-900 dark:text-white">{{ $verification->document_number ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Personal Info -->
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700">
                    <h3 class="text-sm font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-3">Personal Information</h3>
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-surface-500 dark:text-surface-400">Full Name</dt>
                            <dd class="mt-1 text-surface-900 dark:text-white">{{ $verification->full_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-surface-500 dark:text-surface-400">Date of Birth</dt>
                            <dd class="mt-1 text-surface-900 dark:text-white">{{ $verification->date_of_birth?->format('M d, Y') ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-surface-500 dark:text-surface-400">Country</dt>
                            <dd class="mt-1 text-surface-900 dark:text-white">{{ $verification->country }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-surface-500 dark:text-surface-400">Address</dt>
                            <dd class="mt-1 text-surface-900 dark:text-white">{{ $verification->address ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Review Info -->
                @if($verification->reviewed_at)
                    <div class="px-6 py-4">
                        <h3 class="text-sm font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-3">Review Information</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-surface-500 dark:text-surface-400">Reviewed At</dt>
                                <dd class="mt-1 text-surface-900 dark:text-white">{{ $verification->reviewed_at->format('M d, Y \a\t h:i A') }}</dd>
                            </div>
                            @if($verification->expires_at)
                                <div>
                                    <dt class="text-sm text-surface-500 dark:text-surface-400">Expires At</dt>
                                    <dd class="mt-1 text-surface-900 dark:text-white">{{ $verification->expires_at->format('M d, Y') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
