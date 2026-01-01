<x-layouts.app title="Analytics Dashboard">
    <div class="min-h-screen bg-surface-50 dark:bg-surface-950">
        <!-- Header -->
        <div class="bg-white dark:bg-surface-900 border-b border-surface-200 dark:border-surface-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Analytics Dashboard</h1>
                        <p class="text-surface-600 dark:text-surface-400 mt-1">Track your sales performance and growth</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Period Selector -->
                        <select
                            id="period-selector"
                            onchange="updatePeriod(this.value)"
                            class="px-4 py-2 border border-surface-300 dark:border-surface-600 rounded-lg bg-white dark:bg-surface-800 text-surface-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500"
                        >
                            <option value="7" {{ $period == 7 ? 'selected' : '' }}>Last 7 days</option>
                            <option value="30" {{ $period == 30 ? 'selected' : '' }}>Last 30 days</option>
                            <option value="90" {{ $period == 90 ? 'selected' : '' }}>Last 90 days</option>
                            <option value="365" {{ $period == 365 ? 'selected' : '' }}>Last year</option>
                        </select>
                        <a href="{{ route('seller.analytics.export', ['period' => $period]) }}" class="btn-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Revenue Card -->
                <div class="bg-white dark:bg-surface-800 rounded-2xl p-6 shadow-sm border border-surface-200 dark:border-surface-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="flex items-center text-sm font-medium {{ $stats['revenue_change'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                            @if($stats['revenue_change'] >= 0)
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            @else
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                </svg>
                            @endif
                            {{ abs($stats['revenue_change']) }}%
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold text-surface-900 dark:text-white">{{ format_price($stats['revenue']) }}</h3>
                    <p class="text-surface-600 dark:text-surface-400 text-sm mt-1">Revenue ({{ $period }} days)</p>
                </div>

                <!-- Orders Card -->
                <div class="bg-white dark:bg-surface-800 rounded-2xl p-6 shadow-sm border border-surface-200 dark:border-surface-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-success-100 dark:bg-success-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <span class="flex items-center text-sm font-medium {{ $stats['orders_change'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                            @if($stats['orders_change'] >= 0)
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            @else
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                </svg>
                            @endif
                            {{ abs($stats['orders_change']) }}%
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold text-surface-900 dark:text-white">{{ number_format($stats['orders']) }}</h3>
                    <p class="text-surface-600 dark:text-surface-400 text-sm mt-1">Total Orders</p>
                </div>

                <!-- Conversion Rate Card -->
                <div class="bg-white dark:bg-surface-800 rounded-2xl p-6 shadow-sm border border-surface-200 dark:border-surface-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-surface-900 dark:text-white">{{ $stats['conversion_rate'] }}%</h3>
                    <p class="text-surface-600 dark:text-surface-400 text-sm mt-1">Conversion Rate</p>
                </div>

                <!-- Average Order Value Card -->
                <div class="bg-white dark:bg-surface-800 rounded-2xl p-6 shadow-sm border border-surface-200 dark:border-surface-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-warning-100 dark:bg-warning-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-surface-900 dark:text-white">{{ format_price($stats['avg_order_value']) }}</h3>
                    <p class="text-surface-600 dark:text-surface-400 text-sm mt-1">Avg Order Value</p>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Sales Trend Chart -->
                <div class="lg:col-span-2 bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
                    <div class="p-6 border-b border-surface-200 dark:border-surface-700">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Sales Trend</h2>
                        <p class="text-surface-600 dark:text-surface-400 text-sm">Revenue and orders over time</p>
                    </div>
                    <div class="p-6">
                        <canvas id="salesTrendChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Revenue by License Type -->
                <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
                    <div class="p-6 border-b border-surface-200 dark:border-surface-700">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Revenue by License</h2>
                        <p class="text-surface-600 dark:text-surface-400 text-sm">Distribution by license type</p>
                    </div>
                    <div class="p-6">
                        <canvas id="licenseChart" height="250"></canvas>
                    </div>
                    <div class="px-6 pb-6">
                        <div class="space-y-3">
                            @forelse($revenueByLicense as $license)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $loop->index == 0 ? '#6366f1' : ($loop->index == 1 ? '#06b6d4' : ($loop->index == 2 ? '#10b981' : '#f59e0b')) }}"></span>
                                        <span class="text-sm text-surface-600 dark:text-surface-400">{{ $license['type'] }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-surface-900 dark:text-white">{{ format_price($license['revenue']) }}</span>
                                </div>
                            @empty
                                <p class="text-center text-surface-500 dark:text-surface-400 text-sm py-4">No data available</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products & Traffic Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Top Products -->
                <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
                    <div class="p-6 border-b border-surface-200 dark:border-surface-700">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Top Products</h2>
                        <p class="text-surface-600 dark:text-surface-400 text-sm">Best performing products by sales</p>
                    </div>
                    <div class="divide-y divide-surface-200 dark:divide-surface-700">
                        @forelse($topProducts as $index => $product)
                            <div class="p-4 flex items-center gap-4">
                                <span class="w-8 h-8 rounded-full bg-surface-100 dark:bg-surface-700 flex items-center justify-center text-sm font-semibold text-surface-600 dark:text-surface-300">
                                    {{ $index + 1 }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-surface-900 dark:text-white truncate">{{ $product['name'] }}</h4>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">{{ $product['sales'] }} sales &bull; {{ number_format($product['views']) }} views</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-surface-900 dark:text-white">{{ format_price($product['revenue']) }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <svg class="w-12 h-12 mx-auto text-surface-300 dark:text-surface-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <p class="text-surface-500 dark:text-surface-400">No sales data yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Traffic Sources -->
                <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
                    <div class="p-6 border-b border-surface-200 dark:border-surface-700">
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Traffic by Product</h2>
                        <p class="text-surface-600 dark:text-surface-400 text-sm">Total views: {{ number_format($trafficData['total_views']) }}</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($trafficData['products'] as $product)
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-surface-700 dark:text-surface-300 truncate max-w-[60%]">{{ $product['name'] }}</span>
                                        <span class="text-sm text-surface-500 dark:text-surface-400">{{ number_format($product['views']) }} views</span>
                                    </div>
                                    <div class="w-full bg-surface-200 dark:bg-surface-700 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-primary-500 to-accent-500 h-2 rounded-full transition-all duration-500" style="width: {{ $product['percentage'] }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-surface-500 dark:text-surface-400 py-4">No traffic data yet</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
                <div class="p-6 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-surface-900 dark:text-white">Recent Transactions</h2>
                        <p class="text-surface-600 dark:text-surface-400 text-sm">Latest sales activity</p>
                    </div>
                    <a href="{{ route('seller.orders.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View all</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-surface-50 dark:bg-surface-700/50">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 dark:text-surface-400 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 dark:text-surface-400 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 dark:text-surface-400 uppercase tracking-wider">License</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 dark:text-surface-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-surface-600 dark:text-surface-400 uppercase tracking-wider">Earnings</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-200 dark:divide-surface-700">
                            @forelse($recentTransactions as $transaction)
                                <tr class="hover:bg-surface-50 dark:hover:bg-surface-700/30">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-surface-900 dark:text-white truncate max-w-xs">{{ $transaction->product_name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-surface-600 dark:text-surface-400">{{ $transaction->order->user->name ?? 'N/A' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="badge-primary">{{ ucfirst($transaction->license_type ?? 'Standard') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-surface-600 dark:text-surface-400">{{ $transaction->created_at->format('M d, Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="font-semibold text-success-600">{{ format_price($transaction->seller_amount) }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <svg class="w-12 h-12 mx-auto text-surface-300 dark:text-surface-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="text-surface-500 dark:text-surface-400">No transactions yet</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Period change handler
        function updatePeriod(period) {
            window.location.href = '{{ route('seller.analytics.index') }}?period=' + period;
        }

        // Dark mode detection
        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? '#94a3b8' : '#64748b';
        const gridColor = isDarkMode ? '#334155' : '#e2e8f0';

        // Sales Trend Chart
        const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
        const salesData = @json($salesTrend);

        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: salesData.labels,
                datasets: [
                    {
                        label: 'Revenue ($)',
                        data: salesData.revenue,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Orders',
                        data: salesData.orders,
                        borderColor: '#06b6d4',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.4,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { color: textColor }
                    },
                    tooltip: {
                        backgroundColor: isDarkMode ? '#1e293b' : '#fff',
                        titleColor: isDarkMode ? '#fff' : '#0f172a',
                        bodyColor: isDarkMode ? '#94a3b8' : '#64748b',
                        borderColor: isDarkMode ? '#334155' : '#e2e8f0',
                        borderWidth: 1,
                    }
                },
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: { color: textColor }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { color: gridColor },
                        ticks: {
                            color: textColor,
                            callback: (value) => '$' + value
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { color: textColor }
                    }
                }
            }
        });

        // License Chart
        const licenseCtx = document.getElementById('licenseChart').getContext('2d');
        const licenseData = @json($revenueByLicense);

        new Chart(licenseCtx, {
            type: 'doughnut',
            data: {
                labels: licenseData.map(l => l.type),
                datasets: [{
                    data: licenseData.map(l => l.revenue),
                    backgroundColor: ['#6366f1', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</x-layouts.app>
