<?php

namespace App\Filament\Admin\Pages;

use App\Models\Order;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Seller;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\DB;

class AnalyticsDashboard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.admin.pages.analytics-dashboard';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 0;

    protected static ?string $title = 'Analytics Dashboard';

    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public ?string $period = '30';

    public function mount(): void
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Date Range')
                    ->schema([
                        Select::make('period')
                            ->label('Quick Select')
                            ->options([
                                '7' => 'Last 7 Days',
                                '30' => 'Last 30 Days',
                                '90' => 'Last 90 Days',
                                '365' => 'Last Year',
                                'custom' => 'Custom Range',
                            ])
                            ->default('30')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state !== 'custom') {
                                    $set('dateFrom', now()->subDays((int) $state)->format('Y-m-d'));
                                    $set('dateTo', now()->format('Y-m-d'));
                                }
                            }),

                        DatePicker::make('dateFrom')
                            ->label('From')
                            ->visible(fn ($get) => $get('period') === 'custom'),

                        DatePicker::make('dateTo')
                            ->label('To')
                            ->visible(fn ($get) => $get('period') === 'custom'),
                    ])
                    ->columns(3),
            ]);
    }

    public function getRevenueStats(): array
    {
        $query = Order::where('status', 'completed')
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59']);

        $previousPeriodDays = now()->parse($this->dateFrom)->diffInDays(now()->parse($this->dateTo));
        $previousQuery = Order::where('status', 'completed')
            ->whereBetween('created_at', [
                now()->parse($this->dateFrom)->subDays($previousPeriodDays)->format('Y-m-d'),
                now()->parse($this->dateFrom)->subDay()->format('Y-m-d') . ' 23:59:59'
            ]);

        $currentRevenue = $query->sum('total');
        $previousRevenue = $previousQuery->sum('total');
        $revenueChange = $previousRevenue > 0 ? round((($currentRevenue - $previousRevenue) / $previousRevenue) * 100, 1) : 0;

        $currentOrders = $query->count();
        $previousOrders = $previousQuery->count();
        $ordersChange = $previousOrders > 0 ? round((($currentOrders - $previousOrders) / $previousOrders) * 100, 1) : 0;

        $avgOrderValue = $currentOrders > 0 ? $currentRevenue / $currentOrders : 0;

        return [
            'revenue' => $currentRevenue,
            'revenue_change' => $revenueChange,
            'orders' => $currentOrders,
            'orders_change' => $ordersChange,
            'avg_order_value' => $avgOrderValue,
        ];
    }

    public function getRevenueByDay(): array
    {
        return Order::where('status', 'completed')
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($item) => [
                'date' => $item->date,
                'revenue' => (float) $item->revenue,
                'orders' => $item->orders,
            ])
            ->toArray();
    }

    public function getTopProducts(): array
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->select('products.id', 'products.name', DB::raw('COUNT(*) as sales'), DB::raw('SUM(order_items.price) as revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function getTopSellers(): array
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('sellers', 'order_items.seller_id', '=', 'sellers.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->select('sellers.id', 'sellers.business_name', DB::raw('COUNT(*) as sales'), DB::raw('SUM(order_items.seller_amount) as earnings'))
            ->groupBy('sellers.id', 'sellers.business_name')
            ->orderByDesc('earnings')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function getPaymentMethodStats(): array
    {
        return Order::where('status', 'completed')
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get()
            ->map(fn ($item) => [
                'method' => ucfirst($item->payment_method),
                'count' => $item->count,
                'total' => (float) $item->total,
            ])
            ->toArray();
    }

    public function getCustomerStats(): array
    {
        $newCustomers = User::whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])->count();

        $repeatCustomers = DB::table('orders')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        $totalCustomers = User::count();

        return [
            'new_customers' => $newCustomers,
            'repeat_customers' => $repeatCustomers,
            'total_customers' => $totalCustomers,
        ];
    }

    public function getRefundStats(): array
    {
        $refunds = Refund::whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59']);

        return [
            'total_refunds' => $refunds->count(),
            'refund_amount' => $refunds->where('status', 'completed')->sum('amount'),
            'pending_refunds' => Refund::where('status', 'pending')->count(),
        ];
    }
}
