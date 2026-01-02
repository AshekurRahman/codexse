<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class DashboardSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Dashboard Widgets';
    protected static ?string $title = 'Dashboard Widget Settings';
    protected static ?int $navigationSort = 112;

    protected static string $view = 'filament.admin.pages.dashboard-settings';

    public ?array $data = [];

    public static array $availableWidgets = [
        'stats_overview' => [
            'name' => 'Stats Overview',
            'description' => 'Shows total revenue, orders, users, and products',
            'icon' => 'heroicon-o-chart-bar',
            'default' => true,
        ],
        'revenue_chart' => [
            'name' => 'Revenue Chart',
            'description' => 'Monthly revenue comparison chart',
            'icon' => 'heroicon-o-currency-dollar',
            'default' => true,
        ],
        'orders_chart' => [
            'name' => 'Orders Chart',
            'description' => 'Order statistics over time',
            'icon' => 'heroicon-o-shopping-cart',
            'default' => true,
        ],
        'top_products' => [
            'name' => 'Top Products',
            'description' => 'Best selling products chart',
            'icon' => 'heroicon-o-star',
            'default' => true,
        ],
        'latest_orders' => [
            'name' => 'Latest Orders',
            'description' => 'Recent orders table',
            'icon' => 'heroicon-o-clipboard-document-list',
            'default' => true,
        ],
        'sellers_chart' => [
            'name' => 'Sellers Chart',
            'description' => 'Seller registration statistics',
            'icon' => 'heroicon-o-user-group',
            'default' => true,
        ],
        'low_stock_alert' => [
            'name' => 'Low Stock Alert',
            'description' => 'Products running low on stock',
            'icon' => 'heroicon-o-exclamation-triangle',
            'default' => false,
        ],
        'pending_reviews' => [
            'name' => 'Pending Reviews',
            'description' => 'Products awaiting review',
            'icon' => 'heroicon-o-clock',
            'default' => false,
        ],
        'recent_users' => [
            'name' => 'Recent Users',
            'description' => 'Newly registered users',
            'icon' => 'heroicon-o-users',
            'default' => false,
        ],
        'support_tickets' => [
            'name' => 'Support Tickets',
            'description' => 'Open support requests',
            'icon' => 'heroicon-o-ticket',
            'default' => false,
        ],
        'system_health' => [
            'name' => 'System Health',
            'description' => 'Server and application status',
            'icon' => 'heroicon-o-cpu-chip',
            'default' => false,
        ],
        'activity_log' => [
            'name' => 'Activity Log',
            'description' => 'Recent admin activities',
            'icon' => 'heroicon-o-document-text',
            'default' => false,
        ],
        'sales_funnel' => [
            'name' => 'Sales Funnel',
            'description' => 'Conversion funnel from visitors to completed orders',
            'icon' => 'heroicon-o-funnel',
            'default' => true,
        ],
        'geographic_sales' => [
            'name' => 'Geographic Sales',
            'description' => 'Sales distribution by region and country',
            'icon' => 'heroicon-o-globe-americas',
            'default' => true,
        ],
        'customer_insights' => [
            'name' => 'Customer Insights',
            'description' => 'New vs returning customers and segmentation',
            'icon' => 'heroicon-o-users',
            'default' => true,
        ],
        'revenue_forecast' => [
            'name' => 'Revenue Forecast',
            'description' => 'Revenue prediction with 7-day forecast',
            'icon' => 'heroicon-o-chart-bar-square',
            'default' => false,
        ],
        'conversion_tracker' => [
            'name' => 'Conversion Tracker',
            'description' => 'Daily order completion rate tracking',
            'icon' => 'heroicon-o-arrow-trending-up',
            'default' => false,
        ],
    ];

    public function mount(): void
    {
        $enabledWidgets = Setting::get('dashboard_widgets', $this->getDefaultWidgets());

        // Ensure it's an array
        if (is_string($enabledWidgets)) {
            $enabledWidgets = json_decode($enabledWidgets, true) ?? $this->getDefaultWidgets();
        }

        $this->form->fill([
            'enabled_widgets' => $enabledWidgets,
            'dashboard_refresh_interval' => Setting::get('dashboard_refresh_interval', 0),
            'dashboard_columns' => Setting::get('dashboard_columns', 2),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dashboard Layout')
                    ->description('Configure how the dashboard looks')
                    ->schema([
                        Forms\Components\Select::make('dashboard_columns')
                            ->label('Widget Columns')
                            ->options([
                                1 => '1 Column (Full Width)',
                                2 => '2 Columns',
                                3 => '3 Columns',
                            ])
                            ->default(2)
                            ->helperText('Number of columns for widget layout'),

                        Forms\Components\Select::make('dashboard_refresh_interval')
                            ->label('Auto Refresh')
                            ->options([
                                0 => 'Disabled',
                                30 => 'Every 30 seconds',
                                60 => 'Every minute',
                                300 => 'Every 5 minutes',
                                600 => 'Every 10 minutes',
                            ])
                            ->default(0)
                            ->helperText('Automatically refresh dashboard data'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Active Widgets')
                    ->description('Select which widgets to display on the dashboard')
                    ->schema([
                        Forms\Components\CheckboxList::make('enabled_widgets')
                            ->label('')
                            ->options(collect(static::$availableWidgets)->mapWithKeys(fn ($widget, $key) => [
                                $key => $widget['name'],
                            ])->toArray())
                            ->descriptions(collect(static::$availableWidgets)->mapWithKeys(fn ($widget, $key) => [
                                $key => $widget['description'],
                            ])->toArray())
                            ->columns(2)
                            ->gridDirection('row'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set(
            'dashboard_widgets',
            json_encode($data['enabled_widgets'] ?? $this->getDefaultWidgets()),
            'dashboard',
            'json',
            false
        );
        Setting::set('dashboard_refresh_interval', $data['dashboard_refresh_interval'] ?? 0, 'dashboard', 'integer', false);
        Setting::set('dashboard_columns', $data['dashboard_columns'] ?? 2, 'dashboard', 'integer', false);

        Notification::make()
            ->title('Dashboard settings saved')
            ->body('Refresh the dashboard to see your changes.')
            ->success()
            ->send();
    }

    public function resetToDefaults(): void
    {
        Setting::set('dashboard_widgets', json_encode($this->getDefaultWidgets()), 'dashboard', 'json', false);
        Setting::set('dashboard_refresh_interval', 0, 'dashboard', 'integer', false);
        Setting::set('dashboard_columns', 2, 'dashboard', 'integer', false);

        $this->form->fill([
            'enabled_widgets' => $this->getDefaultWidgets(),
            'dashboard_refresh_interval' => 0,
            'dashboard_columns' => 2,
        ]);

        Notification::make()
            ->title('Settings reset to defaults')
            ->success()
            ->send();
    }

    protected function getDefaultWidgets(): array
    {
        return collect(static::$availableWidgets)
            ->filter(fn ($widget) => $widget['default'])
            ->keys()
            ->toArray();
    }

    /**
     * Get enabled widgets for the dashboard
     */
    public static function getEnabledWidgets(): array
    {
        $enabledWidgets = Setting::get('dashboard_widgets', null);

        if ($enabledWidgets === null) {
            return collect(static::$availableWidgets)
                ->filter(fn ($widget) => $widget['default'])
                ->keys()
                ->toArray();
        }

        if (is_string($enabledWidgets)) {
            return json_decode($enabledWidgets, true) ?? [];
        }

        return $enabledWidgets;
    }

    /**
     * Check if a specific widget is enabled
     */
    public static function isWidgetEnabled(string $widgetKey): bool
    {
        return in_array($widgetKey, static::getEnabledWidgets());
    }

    /**
     * Get dashboard configuration
     */
    public static function getDashboardConfig(): array
    {
        return [
            'widgets' => static::getEnabledWidgets(),
            'refresh_interval' => Setting::get('dashboard_refresh_interval', 0),
            'columns' => Setting::get('dashboard_columns', 2),
        ];
    }
}
