<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Sitemap XML berisi halaman publik saja.
     *
     * Halaman di balik autentikasi sengaja tidak dicantumkan: isinya data
     * pelanggan dan tidak boleh sampai masuk indeks mesin pencari.
     */
    public function __invoke(): Response
    {
        $halaman = [
            ['route' => 'home', 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['route' => 'servis.cek', 'changefreq' => 'daily', 'priority' => '0.9'],
            ['route' => 'legal.privasi', 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['route' => 'legal.syarat', 'changefreq' => 'yearly', 'priority' => '0.3'],
        ];

        $tanggal = now()->toAtomString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($halaman as $item) {
            $xml .= '  <url>' . PHP_EOL
                . '    <loc>' . e(route($item['route'])) . '</loc>' . PHP_EOL
                . '    <lastmod>' . $tanggal . '</lastmod>' . PHP_EOL
                . '    <changefreq>' . $item['changefreq'] . '</changefreq>' . PHP_EOL
                . '    <priority>' . $item['priority'] . '</priority>' . PHP_EOL
                . '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type'  => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
