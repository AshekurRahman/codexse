<x-filament-panels::page>
    @php
        $systemInfo = $this->getSystemInfo();
        $phpConfig = $this->getPhpConfig();
        $databaseInfo = $this->getDatabaseInfo();
        $cacheInfo = $this->getCacheInfo();
        $queueInfo = $this->getQueueInfo();
        $storageInfo = $this->getStorageInfo();
        $sessionInfo = $this->getSessionInfo();
        $mailInfo = $this->getMailInfo();
        $recentLogs = $this->getRecentLogs();
    @endphp

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Database Status -->
        <x-filament::section>
            <div class="flex items-center space-x-3">
                <div class="p-2 rounded-full {{ ($databaseInfo['status'] ?? '') === 'Connected' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                    <x-heroicon-o-circle-stack class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm text-gray-500">Database</p>
                    <p class="font-semibold">{{ $databaseInfo['status'] ?? 'Unknown' }}</p>
                </div>
            </div>
        </x-filament::section>

        <!-- Cache Status -->
        <x-filament::section>
            <div class="flex items-center space-x-3">
                <div class="p-2 rounded-full {{ ($cacheInfo['status'] ?? '') === 'Working' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                    <x-heroicon-o-bolt class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm text-gray-500">Cache</p>
                    <p class="font-semibold">{{ $cacheInfo['driver'] }} - {{ $cacheInfo['status'] }}</p>
                </div>
            </div>
        </x-filament::section>

        <!-- Queue Status -->
        <x-filament::section>
            <div class="flex items-center space-x-3">
                <div class="p-2 rounded-full {{ ($queueInfo['failed_jobs'] ?? 0) == 0 ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                    <x-heroicon-o-queue-list class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm text-gray-500">Queue</p>
                    <p class="font-semibold">{{ $queueInfo['failed_jobs'] ?? 0 }} failed jobs</p>
                </div>
            </div>
        </x-filament::section>

        <!-- Storage Status -->
        <x-filament::section>
            <div class="flex items-center space-x-3">
                <div class="p-2 rounded-full {{ ($storageInfo['used_percent'] ?? 0) < 80 ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                    <x-heroicon-o-server class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm text-gray-500">Storage</p>
                    <p class="font-semibold">{{ $storageInfo['used_percent'] ?? 0 }}% used</p>
                </div>
            </div>
        </x-filament::section>
    </div>

    <!-- Detailed Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- System Information -->
        <x-filament::section>
            <x-slot name="heading">System Information</x-slot>
            <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($systemInfo as $key => $value)
                    <div class="py-2 flex justify-between">
                        <dt class="text-sm text-gray-500">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                        <dd class="text-sm font-medium">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-filament::section>

        <!-- PHP Configuration -->
        <x-filament::section>
            <x-slot name="heading">PHP Configuration</x-slot>
            <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($phpConfig as $key => $value)
                    <div class="py-2 flex justify-between">
                        <dt class="text-sm text-gray-500">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                        <dd class="text-sm font-medium">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-filament::section>

        <!-- Database Information -->
        <x-filament::section>
            <x-slot name="heading">Database Information</x-slot>
            <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($databaseInfo as $key => $value)
                    <div class="py-2 flex justify-between">
                        <dt class="text-sm text-gray-500">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                        <dd class="text-sm font-medium">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-filament::section>

        <!-- Storage Information -->
        <x-filament::section>
            <x-slot name="heading">Storage Information</x-slot>
            <div class="mb-4">
                <div class="flex justify-between mb-1">
                    <span class="text-sm text-gray-500">Disk Usage</span>
                    <span class="text-sm font-medium">{{ $storageInfo['used'] }} / {{ $storageInfo['total'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div class="h-2.5 rounded-full {{ ($storageInfo['used_percent'] ?? 0) < 70 ? 'bg-green-600' : (($storageInfo['used_percent'] ?? 0) < 90 ? 'bg-yellow-500' : 'bg-red-600') }}"
                         style="width: {{ $storageInfo['used_percent'] ?? 0 }}%"></div>
                </div>
            </div>
            <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                <div class="py-2 flex justify-between">
                    <dt class="text-sm text-gray-500">Free Space</dt>
                    <dd class="text-sm font-medium">{{ $storageInfo['free'] }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="text-sm text-gray-500">Writable</dt>
                    <dd class="text-sm font-medium">{{ $storageInfo['writable'] }}</dd>
                </div>
            </dl>
        </x-filament::section>

        <!-- Session Configuration -->
        <x-filament::section>
            <x-slot name="heading">Session Configuration</x-slot>
            <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($sessionInfo as $key => $value)
                    <div class="py-2 flex justify-between">
                        <dt class="text-sm text-gray-500">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                        <dd class="text-sm font-medium">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-filament::section>

        <!-- Mail Configuration -->
        <x-filament::section>
            <x-slot name="heading">Mail Configuration</x-slot>
            <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($mailInfo as $key => $value)
                    <div class="py-2 flex justify-between">
                        <dt class="text-sm text-gray-500">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                        <dd class="text-sm font-medium">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-filament::section>
    </div>

    <!-- Recent Logs -->
    <x-filament::section class="mt-6">
        <x-slot name="heading">Recent Logs</x-slot>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-2 w-40">Time</th>
                        <th class="pb-2 w-24">Level</th>
                        <th class="pb-2">Message</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLogs as $log)
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="py-2 text-xs text-gray-500">{{ $log['time'] }}</td>
                            <td class="py-2">
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $log['level'] === 'ERROR' ? 'bg-red-100 text-red-700' :
                                       ($log['level'] === 'WARNING' ? 'bg-yellow-100 text-yellow-700' :
                                       'bg-gray-100 text-gray-700') }}">
                                    {{ $log['level'] }}
                                </span>
                            </td>
                            <td class="py-2 text-xs font-mono truncate max-w-md">{{ $log['message'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">No recent logs</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
