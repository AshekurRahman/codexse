<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Security Stats --}}
        @php $stats = $this->getSecurityStats(); @endphp
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-danger-600">
                        {{ number_format($stats['blocked_ips']) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Blocked IPs</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-warning-600">
                        {{ number_format($stats['security_events_today']) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Events Today</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold {{ $stats['critical_alerts'] > 0 ? 'text-danger-600 animate-pulse' : 'text-success-600' }}">
                        {{ number_format($stats['critical_alerts']) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Critical Alerts</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-info-600">
                        {{ number_format($stats['unresolved_alerts']) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Open Alerts</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-success-600">
                        {{ number_format($stats['attacks_blocked_today']) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Attacks Blocked Today</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600">
                        {{ number_format($stats['total_attacks_blocked']) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Attacks Blocked</div>
                </div>
            </x-filament::section>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Quick Block IP --}}
            <x-filament::section>
                <x-slot name="heading">Block IP Address</x-slot>
                <x-slot name="description">Manually block an IP address</x-slot>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">IP Address</label>
                        <input type="text" wire:model="blockIpAddress" placeholder="192.168.1.1 or 192.168.1.0/24"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason</label>
                        <input type="text" wire:model="blockReason" placeholder="Reason for blocking"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duration (hours)</label>
                        <input type="number" wire:model="blockDuration" min="1" max="8760"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    </div>
                    <x-filament::button wire:click="blockIp" color="danger" icon="heroicon-o-no-symbol">
                        Block IP
                    </x-filament::button>
                </div>
            </x-filament::section>

            {{-- Security Alerts --}}
            <x-filament::section>
                <x-slot name="heading">Active Alerts</x-slot>
                <x-slot name="description">Unresolved security alerts</x-slot>

                @php $alerts = $this->getRecentAlerts(); @endphp
                @if($alerts->count() > 0)
                    <div class="space-y-3">
                        @foreach($alerts as $alert)
                            <div class="flex items-center justify-between p-3 rounded-lg {{ $alert->severity === 'critical' ? 'bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800' : 'bg-gray-50 dark:bg-gray-800' }}">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                                        {{ match($alert->severity) {
                                            'critical' => 'bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-200',
                                            'high' => 'bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-200',
                                            'medium' => 'bg-info-100 text-info-800 dark:bg-info-900 dark:text-info-200',
                                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                                        } }}">
                                        {{ ucfirst($alert->severity) }}
                                    </span>
                                    <div>
                                        <p class="font-medium text-sm text-gray-900 dark:text-white">{{ $alert->title }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $alert->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <x-filament::button wire:click="resolveAlert({{ $alert->id }})" size="sm" color="success">
                                    Resolve
                                </x-filament::button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                        <x-heroicon-o-check-circle class="w-12 h-12 mx-auto mb-2 text-success-500" />
                        <p>No active alerts</p>
                    </div>
                @endif
            </x-filament::section>
        </div>

        {{-- Blocked IPs List --}}
        <x-filament::section>
            <x-slot name="heading">Blocked IP Addresses</x-slot>
            <x-slot name="description">Currently blocked IP addresses and ranges</x-slot>

            @php $blockedIps = $this->getBlockedIps(); @endphp
            @if($blockedIps->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">IP Address</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Reason</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Blocked By</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Expires</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Blocked Requests</th>
                                <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-400">
                            @foreach($blockedIps as $blocked)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-4 py-3 font-mono text-gray-900 dark:text-white">
                                        {{ $blocked->ip_address }}
                                        @if($blocked->is_range)
                                            <span class="text-xs text-info-500">(range)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ Str::limit($blocked->reason, 40) }}</td>
                                    <td class="px-4 py-3">{{ $blocked->blocked_by }}</td>
                                    <td class="px-4 py-3">
                                        {{ $blocked->expires_at ? $blocked->expires_at->diffForHumans() : 'Never' }}
                                    </td>
                                    <td class="px-4 py-3">{{ number_format($blocked->blocked_requests_count) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <x-filament::button
                                            wire:click="unblockIp({{ $blocked->id }})"
                                            size="sm"
                                            color="warning"
                                        >
                                            Unblock
                                        </x-filament::button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                    <x-heroicon-o-shield-check class="w-12 h-12 mx-auto mb-2" />
                    <p>No blocked IP addresses</p>
                </div>
            @endif
        </x-filament::section>

        {{-- Security Settings --}}
        <x-filament::section>
            <x-slot name="heading">Security Configuration</x-slot>
            <x-slot name="description">Configure security features and policies</x-slot>

            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button type="submit">
                        Save Security Settings
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- Recent Security Events --}}
        <x-filament::section collapsed>
            <x-slot name="heading">Recent Security Events</x-slot>

            @php $logs = $this->getRecentSecurityLogs(); @endphp
            @if($logs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Time</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Event</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Severity</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">IP</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Description</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-400">
                            @foreach($logs as $log)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-4 py-3 text-xs">{{ $log->created_at->format('M j, H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700">
                                            {{ str_replace('_', ' ', ucfirst($log->event_type)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full
                                            {{ match($log->severity) {
                                                'critical' => 'bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-200',
                                                'high' => 'bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-200',
                                                'medium' => 'bg-info-100 text-info-800 dark:bg-info-900 dark:text-info-200',
                                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                                            } }}">
                                            {{ ucfirst($log->severity) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $log->ip_address }}</td>
                                    <td class="px-4 py-3 text-xs">{{ Str::limit($log->description, 60) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                    <p>No security events recorded</p>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
