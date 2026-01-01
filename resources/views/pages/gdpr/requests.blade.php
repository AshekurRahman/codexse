<x-layouts.app title="My GDPR Requests">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <a href="{{ route('gdpr.index') }}" class="inline-flex items-center gap-2 text-surface-600 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Privacy Center
            </a>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white">My GDPR Requests</h1>
                <p class="mt-2 text-surface-600 dark:text-surface-400">View and manage your data requests</p>
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

            @if($requests->count() > 0)
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="divide-y divide-surface-200 dark:divide-surface-700">
                    @foreach($requests as $request)
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    @if($request->type === 'export')
                                        <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </div>
                                    @elseif($request->type === 'deletion')
                                        <div class="w-12 h-12 bg-danger-100 dark:bg-danger-900/30 rounded-lg flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-danger-600 dark:text-danger-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-surface-100 dark:bg-surface-700 rounded-lg flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-surface-600 dark:text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-semibold text-surface-900 dark:text-white">{{ $request->type_name }}</h3>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">{{ $request->request_number }}</p>
                                    <div class="flex items-center gap-4 mt-2 text-sm text-surface-500 dark:text-surface-400">
                                        <span>Submitted: {{ $request->created_at->format('M d, Y') }}</span>
                                        @if($request->completed_at)
                                            <span>Completed: {{ $request->completed_at->format('M d, Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    @if($request->status === 'completed') bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-300
                                    @elseif($request->status === 'pending') bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-300
                                    @elseif($request->status === 'processing') bg-info-100 text-info-800 dark:bg-info-900/30 dark:text-info-300
                                    @elseif($request->status === 'rejected') bg-danger-100 text-danger-800 dark:bg-danger-900/30 dark:text-danger-300
                                    @else bg-surface-100 text-surface-800 dark:bg-surface-700 dark:text-surface-300
                                    @endif">
                                    {{ $request->status_name }}
                                </span>
                            </div>
                        </div>

                        @if($request->is_export_available)
                        <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-surface-600 dark:text-surface-400">
                                    Download available until {{ $request->export_expires_at->format('M d, Y \a\t g:i A') }}
                                </p>
                                <a href="{{ route('gdpr.download', $request) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Download
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($request->canBeCancelled())
                        <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                            <form action="{{ route('gdpr.request.cancel', $request) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-danger-600 dark:text-danger-400 hover:underline">
                                    Cancel this request
                                </button>
                            </form>
                        </div>
                        @endif

                        @if($request->admin_notes)
                        <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                            <p class="text-sm font-medium text-surface-700 dark:text-surface-300">Admin Notes:</p>
                            <p class="text-sm text-surface-600 dark:text-surface-400 mt-1">{{ $request->admin_notes }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6">
                {{ $requests->links() }}
            </div>
            @else
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-12 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-surface-100 dark:bg-surface-700 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-2">No requests yet</h3>
                <p class="text-surface-600 dark:text-surface-400 mb-6">You haven't submitted any GDPR requests.</p>
                <div class="flex justify-center gap-4">
                    <a href="{{ route('gdpr.export') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors">
                        Export My Data
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-layouts.app>
