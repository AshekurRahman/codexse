<x-layouts.app title="Activity & Sessions - Codexse">
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-2xl font-bold text-surface-900 dark:text-white mb-2">Activity & Sessions</h1>
            <p class="text-surface-600 dark:text-surface-400 mb-8">Monitor your account activity and manage active sessions</p>

            {{-- Security Alerts --}}
            @if(count($alerts) > 0)
                <div class="mb-8 space-y-4">
                    @foreach($alerts as $alert)
                        <div class="rounded-lg p-4 {{ $alert['type'] === 'danger' ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' : ($alert['type'] === 'warning' ? 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800' : 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800') }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 {{ $alert['type'] === 'danger' ? 'text-red-600 dark:text-red-400' : ($alert['type'] === 'warning' ? 'text-yellow-600 dark:text-yellow-400' : 'text-blue-600 dark:text-blue-400') }} mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span class="{{ $alert['type'] === 'danger' ? 'text-red-800 dark:text-red-200' : ($alert['type'] === 'warning' ? 'text-yellow-800 dark:text-yellow-200' : 'text-blue-800 dark:text-blue-200') }}">
                                    {{ $alert['message'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Activity Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                    <div class="text-sm font-medium text-surface-500 dark:text-surface-400 mb-1">Total Activities</div>
                    <div class="text-2xl font-bold text-surface-900 dark:text-white">{{ $summary['total_activities'] }}</div>
                    <div class="text-xs text-surface-500 dark:text-surface-400 mt-1">Last 30 days</div>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                    <div class="text-sm font-medium text-surface-500 dark:text-surface-400 mb-1">Login Count</div>
                    <div class="text-2xl font-bold text-surface-900 dark:text-white">{{ $summary['login_count'] }}</div>
                    <div class="text-xs text-surface-500 dark:text-surface-400 mt-1">Last 30 days</div>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                    <div class="text-sm font-medium text-surface-500 dark:text-surface-400 mb-1">Suspicious Activity</div>
                    <div class="text-2xl font-bold {{ $summary['suspicious_count'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                        {{ $summary['suspicious_count'] }}
                    </div>
                    <div class="text-xs text-surface-500 dark:text-surface-400 mt-1">Last 30 days</div>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                    <div class="text-sm font-medium text-surface-500 dark:text-surface-400 mb-1">Last Login</div>
                    <div class="text-lg font-bold text-surface-900 dark:text-white">
                        {{ $summary['last_login'] ? $summary['last_login']->diffForHumans() : 'N/A' }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Active Sessions --}}
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700">
                        <div class="p-6 border-b border-surface-200 dark:border-surface-700">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Active Sessions</h2>
                                @if($sessions->count() > 1)
                                    <form action="{{ route('activity.revoke-all') }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                                onclick="return confirm('Are you sure you want to revoke all other sessions?')">
                                            Revoke All Others
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="divide-y divide-surface-200 dark:divide-surface-700">
                            @forelse($sessions as $session)
                                <div class="p-4 {{ $session->session_id === session()->getId() ? 'bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                @if($session->device_type === 'mobile')
                                                    <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                @elseif($session->device_type === 'tablet')
                                                    <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-8 h-8 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-surface-900 dark:text-white">
                                                    {{ $session->browser ?? 'Unknown' }} on {{ $session->platform ?? 'Unknown' }}
                                                </div>
                                                <div class="text-xs text-surface-500 dark:text-surface-400">
                                                    {{ $session->ip_address }}
                                                </div>
                                                <div class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                                                    Last active: {{ $session->last_active_at?->diffForHumans() ?? 'Unknown' }}
                                                </div>
                                                @if($session->session_id === session()->getId())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 mt-1">
                                                        Current Session
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($session->session_id !== session()->getId())
                                            <form action="{{ route('activity.revoke', $session) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                                        onclick="return confirm('Are you sure you want to revoke this session?')">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-surface-500 dark:text-surface-400">
                                    No active sessions found.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Activity Log --}}
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700">
                        <div class="p-6 border-b border-surface-200 dark:border-surface-700">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Recent Activity</h2>
                        </div>
                        <div class="divide-y divide-surface-200 dark:divide-surface-700 max-h-[600px] overflow-y-auto">
                            @forelse($activities as $activity)
                                <div class="p-4 hover:bg-surface-50 dark:hover:bg-surface-750 {{ $activity->is_suspicious ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                                {{ $activity->action_color === 'success' ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                                {{ $activity->action_color === 'danger' ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                                {{ $activity->action_color === 'warning' ? 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                                {{ $activity->action_color === 'info' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                                {{ $activity->action_color === 'primary' ? 'bg-primary-100 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400' : '' }}
                                                {{ $activity->action_color === 'gray' ? 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400' : '' }}
                                            ">
                                                @switch($activity->action)
                                                    @case('login')
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                                        </svg>
                                                        @break
                                                    @case('logout')
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                        </svg>
                                                        @break
                                                    @case('login_failed')
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        @break
                                                    @case('password_changed')
                                                    @case('password_reset')
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                                        </svg>
                                                        @break
                                                    @case('order_placed')
                                                    @case('order_completed')
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                        </svg>
                                                        @break
                                                    @case('2fa_enabled')
                                                    @case('2fa_disabled')
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                        </svg>
                                                        @break
                                                    @default
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                @endswitch
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-surface-900 dark:text-white">
                                                    {{ $activity->action_name }}
                                                    @if($activity->is_suspicious)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 ml-2">
                                                            Suspicious
                                                        </span>
                                                    @endif
                                                </p>
                                                <span class="text-xs text-surface-500 dark:text-surface-400">
                                                    {{ $activity->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-surface-600 dark:text-surface-400 mt-1">
                                                {{ $activity->description }}
                                            </p>
                                            <div class="flex items-center space-x-4 mt-2 text-xs text-surface-500 dark:text-surface-400">
                                                <span>{{ $activity->ip_address }}</span>
                                                <span>{{ $activity->device_info }}</span>
                                                @if($activity->location)
                                                    <span>{{ $activity->location }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="mt-4 text-sm text-surface-500 dark:text-surface-400">No activity recorded yet.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
