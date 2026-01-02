<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-users class="w-5 h-5 text-primary-500" />
                Customer Insights
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
            $metrics = $this->getCustomerMetrics();
            $topCustomers = $this->getTopCustomers();
            $segments = $this->getCustomerSegments();
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Customer Distribution -->
            <div class="space-y-4">
                <!-- New vs Returning Chart -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">New vs Returning</h4>

                    <!-- Donut Chart Representation -->
                    <div class="relative w-32 h-32 mx-auto mb-4">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                            @php
                                $newDash = $metrics['new_percentage'] * 2.51327;
                                $returningDash = $metrics['returning_percentage'] * 2.51327;
                            @endphp
                            <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor"
                                    class="text-gray-200 dark:text-gray-700" stroke-width="12"/>
                            <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor"
                                    class="text-primary-500" stroke-width="12"
                                    stroke-dasharray="{{ $newDash }} 251.327"
                                    stroke-dashoffset="0"/>
                            <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor"
                                    class="text-green-500" stroke-width="12"
                                    stroke-dasharray="{{ $returningDash }} 251.327"
                                    stroke-dashoffset="-{{ $newDash }}"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xl font-bold text-gray-900 dark:text-white">{{ $metrics['total_customers'] }}</span>
                        </div>
                    </div>

                    <div class="flex justify-center gap-6 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-primary-500"></span>
                            <span class="text-gray-600 dark:text-gray-300">New {{ $metrics['new_percentage'] }}%</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-green-500"></span>
                            <span class="text-gray-600 dark:text-gray-300">Return {{ $metrics['returning_percentage'] }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-primary-600">${{ number_format($metrics['avg_ltv'], 0) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Avg LTV</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($metrics['avg_order_value'], 0) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Avg Order</p>
                    </div>
                </div>

                <!-- Repeat Rate -->
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold">{{ $metrics['repeat_purchase_rate'] }}%</p>
                            <p class="text-sm opacity-90">Repeat Purchase Rate</p>
                        </div>
                        <x-heroicon-o-arrow-path class="w-10 h-10 opacity-50" />
                    </div>
                </div>
            </div>

            <!-- Customer Segments -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Customer Segments</h4>

                <div class="space-y-4">
                    @foreach($segments as $segment)
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full {{ $segment['color'] }}"></span>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $segment['segment'] }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="text-gray-500">{{ number_format($segment['count']) }}</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $segment['percentage'] }}%</span>
                                </div>
                            </div>
                            <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="{{ $segment['color'] }} h-full rounded-full transition-all duration-500"
                                     style="width: {{ $segment['percentage'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $segments[2]['count'] + $segments[3]['count'] }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Loyal Customers</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-purple-600">
                                {{ $segments[3]['count'] }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">VIP Customers</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Customers -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Top Customers</h4>
                </div>

                @if(count($topCustomers) > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($topCustomers as $index => $customer)
                            <div class="px-4 py-3 flex items-center gap-3">
                                <div class="relative flex-shrink-0">
                                    @if($customer['avatar'])
                                        <img src="{{ $customer['avatar'] }}" alt="{{ $customer['name'] }}"
                                             class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                            <span class="text-sm font-bold text-primary-600 dark:text-primary-400">
                                                {{ strtoupper(substr($customer['name'], 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($index < 3)
                                        <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full text-xs font-bold flex items-center justify-center
                                            {{ $index === 0 ? 'bg-yellow-400 text-yellow-900' : ($index === 1 ? 'bg-gray-300 text-gray-700' : 'bg-orange-400 text-orange-900') }}">
                                            {{ $index + 1 }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $customer['name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $customer['orders_count'] }} orders</p>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <p class="text-sm font-semibold text-primary-600">${{ number_format($customer['total_spent'], 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        <x-heroicon-o-users class="w-12 h-12 mx-auto mb-3 opacity-50" />
                        <p>No customer data available</p>
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
