<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-globe-americas class="w-5 h-5 text-primary-500" />
                Geographic Sales Distribution
            </div>
        </x-slot>

        <x-slot name="headerEnd">
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="filter">
                    @foreach($this->getFilters() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </x-slot>

        @php
            $topRegions = $this->getTopRegions();
            $stats = $this->getTotalStats();
            $countries = $this->getCountryDistribution();
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Stats Summary -->
            <div class="lg:col-span-1 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_orders']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Orders</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                        <p class="text-2xl font-bold text-primary-600">${{ number_format($stats['total_revenue'], 2) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Revenue</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['unique_regions'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Regions</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['avg_per_region'], 2) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Avg/Region</p>
                    </div>
                </div>

                <!-- Country Distribution -->
                @if(count($countries) > 0)
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Top Countries</h4>
                        <div class="space-y-2">
                            @foreach($countries as $country)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $country['country'] }}</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($country['revenue'], 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Regional Breakdown Table -->
            <div class="lg:col-span-2">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Top Performing Regions</h4>
                    </div>

                    @if(count($topRegions) > 0)
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($topRegions as $index => $region)
                                <div class="px-4 py-3 flex items-center gap-4">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                        <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ $index + 1 }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $region['region'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ number_format($region['orders']) }} orders
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 text-right">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            ${{ number_format($region['revenue'], 2) }}
                                        </p>
                                        <div class="flex items-center gap-2 justify-end">
                                            <div class="w-20 h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                                <div class="h-full bg-primary-500 rounded-full" style="width: {{ $region['intensity'] }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $region['intensity'] }}%</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <x-heroicon-o-map class="w-12 h-12 mx-auto mb-3 opacity-50" />
                            <p>No regional data available for this period</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
