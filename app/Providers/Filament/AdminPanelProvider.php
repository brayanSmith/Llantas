<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use App\Models\Empresa;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName(Empresa::first()?->nombre_empresa ?? 'Mi Ferretería')
            ->brandLogo(fn() => Empresa::first()?->logo_empresa ? asset('storage/' . Empresa::first()->logo_empresa) : null)
            ->brandLogoHeight('2.5rem')
            ->login()
            ->registration()
            ->profile()
            ->passwordReset()
            ->colors([
                'primary' => Color::Amber,
            ])            
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                
            ])
            ->databaseNotifications()
            ->viteTheme('resources/css/filament/admin/theme.css')

            // 👇 AQUI registras tus CDN
            ->assets([
                Css::make('tomselect-css', 'https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css'),
                Js::make('tomselect-js',  'https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js'),
            ])

            // 👇 (Opcional) inyecta un blade al final del <body> para tu JS de inicialización
            //->renderHook('panels::body.end', fn() => view('filament.custom-scripts'))


            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
