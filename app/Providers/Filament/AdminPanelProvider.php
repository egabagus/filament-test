<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Resources\DashboardResource\Widgets\AmountChartWidget;
use App\Filament\Resources\DashboardResource\Widgets\ItemSoldWidget;
use App\Filament\Resources\DashboardResource\Widgets\TransactionChart;
use App\Http\Middleware\UserMenuItemMiddleware;
use App\Models\Team;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Rupadana\ApiService\ApiServicePlugin;
use Stephenjude\FilamentDebugger\DebuggerPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->maxContentWidth(MaxWidth::Full)
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->colors([
                'primary' => Color::Teal,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
                TransactionChart::class,
                AmountChartWidget::class,
                ItemSoldWidget::class
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
                // UserMenuItemMiddleware::class
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->topNavigation()
            ->plugins([
                FilamentSpatieRolesPermissionsPlugin::make(),
                ApiServicePlugin::make(),
                // DebuggerPlugin::make(),
                FilamentShieldPlugin::make()
            ]);
        // ->tenant(Team::class);
    }
}
