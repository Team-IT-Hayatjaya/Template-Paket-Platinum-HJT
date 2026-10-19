<?php

namespace App\Providers\Filament;

use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
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
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => view('filament.components.platinum-theme'))
            ->login(\App\Filament\Auth\Login::class)
            ->profile()
            ->multiFactorAuthentication([
                AppAuthentication::make(),
            ], isRequired: false)
            ->brandName(function () {
                $pkg = strtoupper(config('packages.default', 'platinum'));
                $prefix = $pkg === 'GOLD' ? '⭐ ' : ($pkg === 'PLATINUM' ? '💎 ' : '');
                return "Panel Admin Instansi — {$prefix}Paket {$pkg}";
            })
            ->renderHook(PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, fn () => view('filament.components.login-header'))
            ->renderHook(PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, fn () => view('filament.components.login-footer'))
            ->renderHook(PanelsRenderHook::USER_MENU_BEFORE, fn () => view('filament.components.package-badge'))
            ->renderHook(PanelsRenderHook::FOOTER, fn () => view('filament.components.package-footer'))
            // Paket Gold: banner peringatan kuota pembaruan konten (render kosong pada Silver)
            ->renderHook(PanelsRenderHook::CONTENT_START, fn () => \App\Support\Package::has('content_quota_monitor')
                ? view('filament.components.content-quota-banner')
                : '')
            ->colors([
                'primary' => [
                    50 => '238, 244, 255',
                    100 => '219, 234, 254',
                    200 => '191, 219, 254',
                    300 => '147, 197, 253',
                    400 => '96, 165, 250',
                    500 => '30, 64, 175',
                    600 => '20, 48, 79', // #14304F
                    700 => '17, 38, 63',
                    800 => '13, 28, 48',
                    900 => '10, 20, 35',
                    950 => '5, 11, 20',
                ],
                'warning' => [
                    50 => '254, 252, 232',
                    100 => '254, 249, 195',
                    200 => '254, 240, 138',
                    300 => '253, 224, 71',
                    400 => '250, 204, 21',
                    500 => '201, 162, 39', // #C9A227 Gold
                    600 => '180, 140, 25',
                    700 => '145, 110, 20',
                    800 => '115, 85, 15',
                    900 => '90, 65, 10',
                    950 => '60, 40, 5',
                ],
                'success' => [
                    50 => '236, 253, 245',
                    100 => '209, 250, 229',
                    200 => '167, 243, 208',
                    300 => '110, 231, 183',
                    400 => '52, 211, 153',
                    500 => '16, 185, 129', // Emerald
                    600 => '5, 150, 105',
                    700 => '4, 120, 87',
                    800 => '6, 95, 70',
                    900 => '6, 78, 59',
                    950 => '2, 44, 34',
                ],
            ])
            ->navigationGroups([
                'Layanan PPID',
                'Pelayanan Warga',
                'Publikasi & Informasi',
                'Tampilan & Navigasi',
                'Keamanan & Pengguna',
                'Pengaturan Instansi',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                \App\Filament\Widgets\PpidOverviewWidget::class,
                \App\Filament\Widgets\ContentQuotaWidget::class,
                \App\Filament\Widgets\PpidChartWidget::class,
                \App\Filament\Widgets\LatestPermohonanWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
