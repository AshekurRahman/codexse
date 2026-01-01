<x-layouts.app title="Export My Data - GDPR">
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
                    <div class="flex items-center justify-center w-16 h-16 bg-primary-100 dark:bg-primary-900/30 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Export My Data</h1>
                        <p class="text-surface-600 dark:text-surface-400">Request a copy of all your personal data</p>
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
            @if($pendingExport)
            <div class="bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-800 rounded-xl p-6 mb-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-info-600 dark:text-info-400 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-info-900 dark:text-info-100">Export Request In Progress</h3>
                        <p class="text-sm text-info-700 dark:text-info-300 mt-1">
                            Your data export request ({{ $pendingExport->request_number }}) is currently being processed.
                            You'll receive an email notification when it's ready.
                        </p>
                        <p class="text-sm text-info-600 dark:text-info-400 mt-2">
                            Submitted: {{ $pendingExport->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Available Download -->
            @if($completedExport && $completedExport->is_export_available)
            <div class="bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-xl p-6 mb-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success-600 dark:text-success-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-success-900 dark:text-success-100">Your Data Export is Ready</h3>
                        <p class="text-sm text-success-700 dark:text-success-300 mt-1">
                            Download expires: {{ $completedExport->export_expires_at->format('M d, Y \a\t g:i A') }}
                        </p>
                        <a href="{{ route('gdpr.download', $completedExport) }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-success-600 text-white rounded-lg text-sm font-medium hover:bg-success-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Your Data
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Request Form -->
            @if(!$pendingExport)
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                    <h2 class="font-semibold text-surface-900 dark:text-white">Request Data Export</h2>
                </div>
                <form action="{{ route('gdpr.export.submit') }}" method="POST" class="p-6">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-3">
                            Select data categories to include:
                        </label>
                        <div class="space-y-3">
                            @foreach(\App\Models\GdprDataRequest::DATA_CATEGORIES as $key => $label)
                            <label class="flex items-center gap-3 p-3 rounded-lg border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700/50 cursor-pointer transition-colors">
                                <input type="checkbox" name="categories[]" value="{{ $key }}" checked
                                    class="w-4 h-4 text-primary-600 border-surface-300 rounded focus:ring-primary-500">
                                <span class="text-surface-700 dark:text-surface-300">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-surface-50 dark:bg-surface-700/50 rounded-lg p-4 mb-6">
                        <div class="flex gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-info-600 dark:text-info-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-sm text-surface-600 dark:text-surface-400">
                                <p>Your data will be compiled into a downloadable ZIP file containing:</p>
                                <ul class="list-disc list-inside mt-2 space-y-1">
                                    <li>JSON file with all your data</li>
                                    <li>Human-readable HTML summary</li>
                                    <li>README with information about your rights</li>
                                </ul>
                                <p class="mt-2">The download link will be available for 7 days.</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Submit Export Request
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</x-layouts.app>
