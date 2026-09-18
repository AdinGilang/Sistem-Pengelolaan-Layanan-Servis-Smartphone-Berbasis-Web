<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Route fitur dimuat per modul oleh App\Providers\ModuleServiceProvider.
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Header keamanan dipasang untuk seluruh request web.
        $middleware->web(append: [
            SecurityHeaders::class,
        ]);

        // Proxy bawaan platform hosting (Render, Cloudflare, dsb.) dipercaya
        // agar deteksi HTTPS dan alamat IP asli untuk rate limiter tetap benar.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /*
         * Jangan pernah menampilkan detail teknis ke pengguna di production.
         *
         * Saat APP_DEBUG mati, exception apa pun yang bukan HttpException
         * dirender sebagai halaman 500 yang netral. Pesan aslinya tetap
         * tercatat lengkap di log untuk keperluan penelusuran.
         */
        $exceptions->render(function (Throwable $e, Request $request) {
            if (config('app.debug') || $e instanceof HttpExceptionInterface) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan pada server. Silakan coba beberapa saat lagi.',
                ], 500);
            }

            return response()->view('errors.500', [], 500);
        });

        /*
         * Data yang tidak boleh ikut tertulis ke berkas log bila sebuah
         * request gagal divalidasi atau melempar exception.
         */
        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
            'kata_sandi',
            'pola_kunci',
            'pin',
        ]);
    })->create();
