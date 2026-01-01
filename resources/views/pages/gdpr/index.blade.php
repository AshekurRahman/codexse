<x-layouts.app title="Privacy Center - GDPR">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-surface-900 dark:text-white">Privacy Center</h1>
                <p class="mt-2 text-surface-600 dark:text-surface-400">
                    Manage your data privacy preferences and exercise your rights under GDPR
                </p>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <a href="{{ route('gdpr.export') }}" class="group p-6 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 hover:border-primary-500 dark:hover:border-primary-500 transition-colors">
                    <div class="flex items-center justify-center w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg mb-4 group-hover:bg-primary-200 dark:group-hover:bg-primary-900/50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-surface-900 dark:text-white">Export My Data</h3>
                    <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Download all your personal data</p>
                </a>

                <a href="{{ route('gdpr.delete') }}" class="group p-6 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 hover:border-danger-500 dark:hover:border-danger-500 transition-colors">
                    <div class="flex items-center justify-center w-12 h-12 bg-danger-100 dark:bg-danger-900/30 rounded-lg mb-4 group-hover:bg-danger-200 dark:group-hover:bg-danger-900/50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-danger-600 dark:text-danger-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-surface-900 dark:text-white">Delete My Data</h3>
                    <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Request account deletion</p>
                </a>

                <a href="{{ route('gdpr.consent') }}" class="group p-6 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 hover:border-success-500 dark:hover:border-success-500 transition-colors">
                    <div class="flex items-center justify-center w-12 h-12 bg-success-100 dark:bg-success-900/30 rounded-lg mb-4 group-hover:bg-success-200 dark:group-hover:bg-success-900/50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success-600 dark:text-success-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-surface-900 dark:text-white">Consent Settings</h3>
                    <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Manage your preferences</p>
                </a>

                <a href="{{ route('gdpr.requests') }}" class="group p-6 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 hover:border-info-500 dark:hover:border-info-500 transition-colors">
                    <div class="flex items-center justify-center w-12 h-12 bg-info-100 dark:bg-info-900/30 rounded-lg mb-4 group-hover:bg-info-200 dark:group-hover:bg-info-900/50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-info-600 dark:text-info-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-surface-900 dark:text-white">My Requests</h3>
                    <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">View request history</p>
                </a>
            </div>

            <!-- Your Rights -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6 mb-8">
                <h2 class="text-xl font-semibold text-surface-900 dark:text-white mb-4">Your Rights Under GDPR</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                            <span class="text-primary-600 dark:text-primary-400 font-semibold text-sm">15</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-surface-900 dark:text-white">Right of Access</h4>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Request a copy of your personal data</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                            <span class="text-primary-600 dark:text-primary-400 font-semibold text-sm">16</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-surface-900 dark:text-white">Right to Rectification</h4>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Correct inaccurate personal data</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                            <span class="text-primary-600 dark:text-primary-400 font-semibold text-sm">17</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-surface-900 dark:text-white">Right to Erasure</h4>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Request deletion of your data</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                            <span class="text-primary-600 dark:text-primary-400 font-semibold text-sm">20</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-surface-900 dark:text-white">Right to Portability</h4>
                            <p class="text-sm text-surface-500 dark:text-surface-400">Receive data in a portable format</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Requests -->
            @if($requests->count() > 0)
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Recent Requests</h2>
                </div>
                <div class="divide-y divide-surface-200 dark:divide-surface-700">
                    @foreach($requests->take(5) as $request)
                    <a href="{{ route('gdpr.request.show', $request) }}" class="flex items-center justify-between p-4 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                @if($request->type === 'export')
                                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-danger-100 dark:bg-danger-900/30 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-danger-600 dark:text-danger-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="font-medium text-surface-900 dark:text-white">{{ $request->type_name }}</p>
                                <p class="text-sm text-surface-500 dark:text-surface-400">{{ $request->request_number }} &bull; {{ $request->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($request->status === 'completed') bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-300
                            @elseif($request->status === 'pending') bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-300
                            @elseif($request->status === 'processing') bg-info-100 text-info-800 dark:bg-info-900/30 dark:text-info-300
                            @else bg-surface-100 text-surface-800 dark:bg-surface-700 dark:text-surface-300
                            @endif">
                            {{ $request->status_name }}
                        </span>
                    </a>
                    @endforeach
                </div>
                @if($requests->count() > 5)
                <div class="px-6 py-3 bg-surface-50 dark:bg-surface-700/50 border-t border-surface-200 dark:border-surface-700">
                    <a href="{{ route('gdpr.requests') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">View all requests</a>
                </div>
                @endif
            </div>
            @endif

            <!-- Current Consent Status -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-surface-900 dark:text-white">Current Consent Status</h2>
                    <a href="{{ route('gdpr.consent') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">Manage</a>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2">
                        <span class="text-surface-600 dark:text-surface-400">Marketing Communications</span>
                        @if($user->marketing_consent)
                            <span class="inline-flex items-center gap-1 text-success-600 dark:text-success-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Enabled
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-surface-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Disabled
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-surface-600 dark:text-surface-400">Analytics & Performance</span>
                        @if($user->analytics_consent)
                            <span class="inline-flex items-center gap-1 text-success-600 dark:text-success-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Enabled
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-surface-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Disabled
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-surface-600 dark:text-surface-400">Third Party Services</span>
                        @if($user->third_party_consent)
                            <span class="inline-flex items-center gap-1 text-success-600 dark:text-success-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Enabled
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-surface-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Disabled
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
