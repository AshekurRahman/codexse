<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-funnel class="w-5 h-5 text-primary-500" />
                Sales Funnel
            </div>
        </x-slot>

        <x-slot name="headerEnd">
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">
                    Overall Conversion: <strong class="text-primary-600">{{ $this->getOverallConversion() }}%</strong>
                </span>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="filter">
                        @foreach($this->getFilters() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
        </x-slot>

        @php $funnelData = $this->getFunnelData(); @endphp

        <div class="space-y-4 py-2">
            @foreach($funnelData as $index => $stage)
                @php
                    $width = max(15, $stage['percent']);
                @endphp
                <div class="relative">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full {{ $stage['color'] }}"></span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $stage['stage'] }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="text-gray-500 dark:text-gray-400">{{ number_format($stage['count']) }}</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $stage['percent'] }}%</span>
                            @if($index > 0 && isset($stage['conversion']))
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $stage['conversion'] >= 50 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($stage['conversion'] >= 25 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400') }}">
                                    {{ $stage['conversion'] }}% conv.
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-8 overflow-hidden">
                        <div class="{{ $stage['color'] }} h-8 rounded-full transition-all duration-700 ease-out flex items-center justify-end pr-3"
                             style="width: {{ $width }}%">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Funnel Summary -->
        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-3 gap-4">
                @php
                    $first = $funnelData[0];
                    $middle = $funnelData[2] ?? $funnelData[1];
                    $last = end($funnelData);
                @endphp
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($first['count']) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Visitors</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($middle['count']) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Checkouts</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-primary-600">{{ number_format($last['count']) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Completed Orders</p>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
