<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Widgets\ActivityLog;
use App\Filament\Admin\Widgets\ConversionRateTracker;
use App\Filament\Admin\Widgets\CustomerInsightsWidget;
use App\Filament\Admin\Widgets\GeographicSalesMap;
use App\Filament\Admin\Widgets\LatestOrders;
use App\Filament\Admin\Widgets\LowStockAlert;
use App\Filament\Admin\Widgets\OrdersChart;
use App\Filament\Admin\Widgets\PaymentAnalytics;
use App\Filament\Admin\Widgets\PendingReviews;
use App\Filament\Admin\Widgets\RecentUsers;
use App\Filament\Admin\Widgets\RevenueForecastWidget;
use App\Filament\Admin\Widgets\RevenueChart;
use App\Filament\Admin\Widgets\SalesFunnelWidget;
use App\Filament\Admin\Widgets\SellersChart;
use App\Filament\Admin\Widgets\StatsOverview;
use App\Filament\Admin\Widgets\SystemHealth;
use App\Filament\Admin\Widgets\TopProductsChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Codexse')
            ->brandLogo(asset('images/logo-dark.svg'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/logo-icon.svg'))
            ->darkMode(false)
            ->colors([
                'primary' => Color::Violet,
                'danger' => Color::Rose,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Marketplace'),
                NavigationGroup::make()
                    ->label('Sales'),
                NavigationGroup::make()
                    ->label('User Management'),
                NavigationGroup::make()
                    ->label('Security'),
                NavigationGroup::make()
                    ->label('Settings')
                    ->collapsed(),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => new HtmlString('
                    <style>
                        .fi-sidebar {
                            background-color: #ffffff !important;
                            border-right: 1px solid #e5e7eb;
                        }
                        .fi-sidebar-nav {
                            background-color: #ffffff !important;
                        }
                        .fi-sidebar-header {
                            background-color: #ffffff !important;
                            border-bottom: 1px solid #e5e7eb;
                        }
                        .fi-sidebar-item-button {
                            color: #374151 !important;
                        }
                        .fi-sidebar-item-button:hover {
                            background-color: #f3f4f6 !important;
                        }
                        .fi-sidebar-item-button[data-active] {
                            background-color: #ede9fe !important;
                            color: #7c3aed !important;
                        }
                        .fi-sidebar-group-label {
                            color: #6b7280 !important;
                        }
                        .fi-sidebar-footer {
                            background-color: #ffffff !important;
                            border-top: 1px solid #e5e7eb;
                        }
                    </style>
                ')
            )
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                StatsOverview::class,
                PaymentAnalytics::class,
                RevenueChart::class,
                OrdersChart::class,
                TopProductsChart::class,
                LatestOrders::class,
                SellersChart::class,
                SalesFunnelWidget::class,
                GeographicSalesMap::class,
                CustomerInsightsWidget::class,
                RevenueForecastWidget::class,
                ConversionRateTracker::class,
                LowStockAlert::class,
                PendingReviews::class,
                RecentUsers::class,
                SystemHealth::class,
                ActivityLog::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\ForceHttps::class,
                \App\Http\Middleware\SecurityHeaders::class,
                \App\Http\Middleware\BlockMaliciousIps::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
