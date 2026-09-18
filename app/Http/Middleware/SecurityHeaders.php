<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Header keamanan untuk seluruh response HTML.
 *
 * Header ini tidak menggantikan validasi dan escaping di sisi aplikasi,
 * tetapi menjadi lapisan pertahanan kedua: membatasi dari mana skrip boleh
 * dimuat, melarang halaman ditanam di dalam iframe situs lain, dan mencegah
 * browser menebak-nebak tipe konten.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // PDF hasil DomPDF dan unduhan lain tidak perlu header dokumen HTML.
        if (! $this->isHtml($response)) {
            return $response;
        }

        $headers = [
            // Blokir MIME sniffing.
            'X-Content-Type-Options'  => 'nosniff',
            // Cegah clickjacking pada halaman admin.
            'X-Frame-Options'         => 'SAMEORIGIN',
            // Jangan bocorkan URL halaman internal ke situs tujuan.
            'Referrer-Policy'         => 'strict-origin-when-cross-origin',
            // Matikan API perangkat yang memang tidak dipakai aplikasi ini.
            'Permissions-Policy'      => 'camera=(), microphone=(), geolocation=(), payment=(), usb=()',
            'X-Permitted-Cross-Domain-Policies' => 'none',
            'Content-Security-Policy' => $this->contentSecurityPolicy(),
        ];

        if ($request->secure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        // Seluruh halaman di balik login berisi data pelanggan, jadi mesin
        // pencari diminta tegas untuk tidak mengindeksnya.
        if ($request->user()) {
            $headers['X-Robots-Tag'] = 'noindex, nofollow';
            $headers['Cache-Control'] = 'no-store, no-cache, must-revalidate, private';
        }

        foreach ($headers as $name => $value) {
            // Jangan timpa header yang sudah diatur di tempat lain.
            if (! $response->headers->has($name)) {
                $response->headers->set($name, $value);
            }
        }

        return $response;
    }

    /**
     * CSP dibuat sesuai kebutuhan nyata halaman.
     *
     * 'unsafe-inline' pada script dan style masih diperlukan karena banyak
     * template membawa <style> dan <script> di dalam berkas Blade. Sumber
     * eksternal dibatasi hanya ke CDN yang benar-benar dipakai.
     */
    private function contentSecurityPolicy(): string
    {
        return implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'self'",
            "form-action 'self'",
            "img-src 'self' data:",
            "font-src 'self' data: https://fonts.gstatic.com https://fonts.bunny.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net",
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net",
            "connect-src 'self'",
        ]);
    }

    private function isHtml(Response $response): bool
    {
        return str_contains((string) $response->headers->get('Content-Type'), 'text/html');
    }
}
