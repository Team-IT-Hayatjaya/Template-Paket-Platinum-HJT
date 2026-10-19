<?php

namespace App\Providers;

use App\Listeners\LogAuthenticationActivity;
use App\Models\SiteSetting;
use App\Support\Package;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! class_exists('Package')) {
            class_alias(Package::class, 'Package');
        }

        $this->app->singleton(\App\Services\UnitContextService::class, function () {
            return new \App\Services\UnitContextService();
        });

        if (! class_exists('UnitContext')) {
            class_alias(\App\Facades\UnitContext::class, 'UnitContext');
        }

        // WhatsApp Provider: WhatsAppCloudApiProvider (Produksi) / MockWhatsAppProvider (Testing / CI)
        $this->app->bind(\App\Contracts\WhatsAppProvider::class, function () {
            if (env('WHATSAPP_TEST_MODE', false) || app()->environment('testing')) {
                return new \App\Services\WhatsApp\MockWhatsAppProvider();
            }

            return new \App\Services\WhatsApp\WhatsAppCloudApiProvider();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->header('X-Forwarded-Proto') === 'https' || env('APP_ENV') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Event Subscriber for Activity Log on Authentication
        Event::subscribe(LogAuthenticationActivity::class);

        // Blade Directives for Package Tier Checks
        Blade::if('packageHas', function (string $feature) {
            return Package::has($feature);
        });

        // Global Rate Limiter for Login
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip() . '|' . $request->input('email'));
        });

        // View Composer for Site Settings on Public & Blade Views
        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('site_settings')) {
                    $setting = SiteSetting::current();
                } else {
                    $setting = new SiteSetting([
                        'institution_name' => 'Pemerintah Kecamatan Loa Kulu',
                        'institution_acronym' => 'Kec. Loa Kulu',
                        'primary_color' => '#14304F',
                        'secondary_color' => '#C9A227',
                    ]);
                }
            } catch (\Throwable $e) {
                $setting = new SiteSetting([
                    'institution_name' => 'Pemerintah Kecamatan Loa Kulu',
                    'institution_acronym' => 'Kec. Loa Kulu',
                    'primary_color' => '#14304F',
                    'secondary_color' => '#C9A227',
                ]);
            }

            $view->with('siteSetting', $setting);
            $view->with('pengaturan', (object) [
                'nama_instansi' => $setting->institution_name ?? 'Pemerintah Kecamatan Loa Kulu',
                'sub_nama_instansi' => $setting->institution_acronym ?? 'Kec. Loa Kulu',
                'nama_ppid' => 'PPID ' . ($setting->institution_acronym ?? 'Loa Kulu'),
                'warna_utama' => $setting->primary_color ?? '#14304F',
                'warna_sekunder' => $setting->secondary_color ?? '#C9A227',
                'alamat' => $setting->address,
                'telepon' => $setting->phone,
                'email' => $setting->email,
                'jam_layanan' => $setting->service_hours,
            ]);

            // Navigasi Menu Dinamis
            try {
                if (Schema::hasTable('navigation_menus')) {
                    $navMenus = \App\Models\NavigationMenu::with(['children' => function ($q) {
                            $q->where('is_active', true)->orderBy('urutan');
                        }])
                        ->whereNull('parent_id')
                        ->where('is_active', true)
                        ->orderBy('urutan')
                        ->get();
                } else {
                    $navMenus = collect();
                }
            } catch (\Throwable $e) {
                $navMenus = collect();
            }
            $view->with('navigationMenus', $navMenus);
        });
    }
}
