<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Bootstrapper arsitektur HMVC.
 *
 * Setiap sub-folder di dalam app/Modules dianggap sebagai satu modul mandiri
 * yang membawa Controller, Model, Request, Policy, Service, dan View-nya sendiri.
 * Provider ini mendaftarkan dua hal untuk tiap modul secara otomatis:
 *
 *   1. routes.php        -> dimuat ke dalam middleware group "web"
 *   2. Views/            -> didaftarkan sebagai view namespace, contoh: servis::index
 *
 * Dengan begitu penambahan modul baru cukup membuat folder — tidak perlu
 * menyentuh file konfigurasi mana pun.
 */
class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Lokasi root seluruh modul.
     */
    public static function modulesPath(): string
    {
        return app_path('Modules');
    }

    /**
     * Daftar nama modul yang terdeteksi, terurut agar hasilnya deterministik.
     *
     * @return list<string>
     */
    public static function modules(): array
    {
        $path = static::modulesPath();

        if (! is_dir($path)) {
            return [];
        }

        $modules = array_map('basename', glob($path.'/*', GLOB_ONLYDIR) ?: []);
        sort($modules);

        return $modules;
    }

    public function register(): void
    {
        foreach (static::modules() as $module) {
            $config = static::modulesPath()."/{$module}/config.php";

            if (is_file($config)) {
                $this->mergeConfigFrom($config, strtolower($module));
            }
        }
    }

    public function boot(): void
    {
        foreach (static::modules() as $module) {
            $this->registerViews($module);
            $this->registerRoutes($module);
        }
    }

    /**
     * View modul dipanggil dengan namespace huruf kecil: view('laporan::pdf').
     */
    private function registerViews(string $module): void
    {
        $views = static::modulesPath()."/{$module}/Views";

        if (is_dir($views)) {
            $this->loadViewsFrom($views, strtolower($module));
        }
    }

    /**
     * Route modul ikut middleware group "web" sehingga session, CSRF, dan
     * cookie encryption tetap aktif persis seperti routes/web.php.
     */
    private function registerRoutes(string $module): void
    {
        $routes = static::modulesPath()."/{$module}/routes.php";

        if (is_file($routes)) {
            Route::middleware('web')->group($routes);
        }
    }
}
