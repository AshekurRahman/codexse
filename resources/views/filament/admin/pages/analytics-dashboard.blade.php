<x-filament-panels::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}
    </form>

    @php
        $revenueStats = $this->getRevenueStats();
        $customerStats = $this->getCustomerStats();
        $refundStats = $this->getRefundStats();
        $topProducts = $this->getTopProducts();
        $topSellers = $this->getTopSellers();
        $paymentMethods = $this->getPaymentMethodStats();
        $revenueByDay = $this->getRevenueByDay();
    @endphp

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($revenueStats['revenue'], 2) }}</p>
                </div>
                <div class="text-sm {{ $revenueStats['revenue_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $revenueStats['revenue_change'] >= 0 ? '+' : '' }}{{ $revenueStats['revenue_change'] }}%
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($revenueStats['orders']) }}</p>
                </div>
                <div class="text-sm {{ $revenueStats['orders_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $revenueStats['orders_change'] >= 0 ? '+' : '' }}{{ $revenueStats['orders_change'] }}%
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Average Order Value</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($revenueStats['avg_order_value'], 2) }}</p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">New Customers</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($customerStats['new_customers']) }}</p>
            </div>
        </x-filament::section>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Revenue Chart -->
        <x-filament::section>
            <x-slot name="heading">Revenue Over Time</x-slot>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </x-filament::section>

        <!-- Payment Methods -->
        <x-filament::section>
            <x-slot name="heading">Payment Methods</x-slot>
            <div class="space-y-4">
                @foreach($paymentMethods as $method)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 rounded-full bg-primary-500"></div>
                            <span class="text-sm font-medium">{{ $method['method'] }}</span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold">${{ number_format($method['total'], 2) }}</p>
                            <p class="text-xs text-gray-500">{{ $method['count'] }} orders</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>

    <!-- Top Products and Sellers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Top Products -->
        <x-filament::section>
            <x-slot name="heading">Top Products</x-slot>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400 border-b">
                            <th class="pb-2">Product</th>
                            <th class="pb-2 text-right">Sales</th>
                            <th class="pb-2 text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $product)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2">{{ Str::limit($product->name, 30) }}</td>
                                <td class="py-2 text-right">{{ $product->sales }}</td>
                                <td class="py-2 text-right font-semibold">${{ number_format($product->revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-gray-500">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <!-- Top Sellers -->
        <x-filament::section>
            <x-slot name="heading">Top Sellers</x-slot>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400 border-b">
                            <th class="pb-2">Seller</th>
                            <th class="pb-2 text-right">Sales</th>
                            <th class="pb-2 text-right">Earnings</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topSellers as $seller)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2">{{ Str::limit($seller->business_name, 30) }}</td>
                                <td class="py-2 text-right">{{ $seller->sales }}</td>
                                <td class="py-2 text-right font-semibold">${{ number_format($seller->earnings, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-gray-500">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <x-filament::section>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Repeat Customers</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($customerStats['repeat_customers']) }}</p>
                <p class="text-xs text-gray-500">Customers with 2+ orders</p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Refunds</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($refundStats['total_refunds']) }}</p>
                <p class="text-xs text-gray-500">${{ number_format($refundStats['refund_amount'], 2) }} refunded</p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pending Refunds</p>
                <p class="text-2xl font-bold text-{{ $refundStats['pending_refunds'] > 0 ? 'warning' : 'success' }}-600">{{ number_format($refundStats['pending_refunds']) }}</p>
                <p class="text-xs text-gray-500">Awaiting processing</p>
            </div>
        </x-filament::section>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const revenueData = @json($revenueByDay);

            const ctx = document.getElementById('revenueChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: revenueData.map(d => d.date),
                        datasets: [{
                            label: 'Revenue',
                            data: revenueData.map(d => d.revenue),
                            borderColor: 'rgb(124, 58, 237)',
                            backgroundColor: 'rgba(124, 58, 237, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-filament-panels::page>
