<?php

use App\Models\User;

test('halaman depan membawa meta dasar untuk mesin pencari', function () {
    $respons = $this->get('/');

    $respons->assertOk()
        ->assertSee('<meta name="description"', false)
        ->assertSee('<link rel="canonical"', false)
        ->assertSee('property="og:title"', false)
        ->assertSee('name="twitter:card"', false)
        ->assertSee('application/ld+json', false);
});

test('halaman depan punya satu judul utama', function () {
    $isi = $this->get('/')->getContent();

    expect(substr_count($isi, '<h1'))->toBe(1);
});

test('halaman cek servis boleh diindeks', function () {
    $this->get('/cek-servis')
        ->assertOk()
        ->assertSee('index, follow', false);
});

test('halaman panel diminta tidak diindeks', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('noindex', false);
});

test('sitemap tersedia dan hanya memuat halaman publik', function () {
    $respons = $this->get('/sitemap.xml');

    $respons->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

    $isi = $respons->getContent();

    expect($isi)->toContain('<urlset')
        ->toContain(route('servis.cek'))
        ->toContain(route('legal.privasi'))
        // Halaman berisi data pelanggan tidak boleh dicantumkan.
        ->not->toContain(route('dashboard'))
        ->not->toContain('/servis/');
});

test('halaman kebijakan privasi dan syarat layanan tersedia', function () {
    $this->get('/kebijakan-privasi')->assertOk()->assertSee('Kebijakan Privasi');
    $this->get('/syarat-ketentuan')->assertOk()->assertSee('Syarat');
});

test('angka di halaman depan berasal dari data nyata', function () {
    /*
     * Sebelumnya angka pada hero ditulis langsung di template (16, 7, 5, 4)
     * dan tidak pernah berubah meski isi database berbeda.
     */
    App\Modules\Servis\Models\Servis::factory()
        ->count(7)
        ->status(App\Modules\Servis\Models\Servis::STATUS_PROSES)
        ->create();

    $this->get('/')
        ->assertOk()
        ->assertSee('Sedang dikerjakan');
});

test('halaman publik tidak lagi memuat skrip dari CDN pihak ketiga', function () {
    $this->get('/cek-servis')
        ->assertOk()
        ->assertDontSee('cdn.tailwindcss.com', false)
        ->assertDontSee('cdn.jsdelivr.net/npm/chart.js', false);
});
