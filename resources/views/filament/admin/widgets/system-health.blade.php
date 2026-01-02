<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">System Health</x-slot>

        <div class="space-y-4">
            {{-- Health Checks --}}
            <div class="space-y-2">
                @foreach($this->getHealthChecks() as $name => $check)
                    <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <div class="flex items-center gap-2">
                            @php
                                $statusColor = match($check['status']) {
                                    'healthy' => 'text-success-600',
                                    'warning' => 'text-warning-600',
                                    'unhealthy' => 'text-danger-600',
                                    default => 'text-gray-500',
                                };
                            @endphp
                            <x-dynamic-component :component="$check['icon']" class="w-5 h-5 {{ $statusColor }}" />
                            <span class="font-medium text-sm capitalize">{{ $name }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">{{ $check['message'] }}</span>
                            <div class="w-2 h-2 rounded-full {{ match($check['status']) {
                                'healthy' => 'bg-success-500',
                                'warning' => 'bg-warning-500',
                                'unhealthy' => 'bg-danger-500',
                                default => 'bg-gray-500',
                            } }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- PHP Info --}}
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">PHP Environment</h4>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    @foreach($this->getPhpInfo() as $key => $value)
                        <div class="flex justify-between">
                            <span class="text-gray-500 capitalize">{{ str_replace('_', ' ', $key) }}</span>
                            <span class="font-mono text-gray-900 dark:text-white">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
