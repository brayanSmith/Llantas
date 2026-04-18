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
use Illuminate\Support\Facades\Schema;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Navigation\NavigationGroup;
use App\Models\Empresa;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName($this->getBrandName())
            ->brandLogo(fn() => $this->getBrandLogo())
            ->brandLogoHeight('2.5rem')
            ->login()
            ->registration()
            ->profile()
            ->passwordReset()
            ->globalSearch(true)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
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
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),

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

            ->navigationGroups([
                NavigationGroup::make('Sistema'),
                NavigationGroup::make('Pedidos'),
                NavigationGroup::make('Compras'),
                NavigationGroup::make('Productos'),
                NavigationGroup::make('Stock'),
                NavigationGroup::make('Users'),
                NavigationGroup::make('Filament Shield'),

            ])

            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Get brand name safely, avoiding database queries during migrations
     */
    private function getBrandName(): string
    {
        try {
            // Verificar si la tabla empresas existe antes de hacer la consulta
            if (\Schema::hasTable('empresas')) {
                return Empresa::first()?->nombre_empresa ?? 'Mi Ferretería';
            }
        } catch (\Exception $e) {
            // Si hay cualquier error, usar el nombre por defecto
        }

        return 'Mi Ferretería';
    }

    /**
     * Get brand logo safely, avoiding database queries during migrations
     */
    private function getBrandLogo(): ?string
    {
        try {
            // Verificar si la tabla empresas existe antes de hacer la consulta
            if (\Schema::hasTable('empresas')) {
                $empresa = Empresa::first();
                return $empresa?->logo_empresa ? asset('storage/' . $empresa->logo_empresa) : null;
            }
        } catch (\Exception $e) {
            // Si hay cualquier error, no mostrar logo
        }

        return null;
    }
}
