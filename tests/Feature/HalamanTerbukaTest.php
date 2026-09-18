<?php

use App\Models\User;
use App\Modules\Servis\Models\Servis;

/*
|--------------------------------------------------------------------------
| Smoke test seluruh halaman
|--------------------------------------------------------------------------
|
| Memastikan setiap halaman benar-benar bisa dirender setelah pemindahan ke
| struktur HMVC — tidak ada view yang salah namespace, tidak ada variabel
| yang lupa dikirim controller, dan tidak ada berkas yang tertinggal.
|
*/

beforeEach(function () {
    $this->admin  = User::factory()->admin()->create();
    $this->owner  = User::factory()->owner()->create();
    $this->servis = Servis::factory()->denganPin()->selesai()->create();
});

test('halaman admin dapat dirender', function (string $alamat) {
    $this->actingAs($this->admin)->get($alamat)->assertOk();
})->with(function () {
    return [
        '/dashboard',
        '/servis',
        '/servis/create',
        '/setting',
    ];
});

test('halaman detail dan formulir ubah servis dapat dirender', function () {
    $this->actingAs($this->admin)->get("/servis/{$this->servis->id}")->assertOk();
    $this->actingAs($this->admin)->get("/servis/{$this->servis->id}/edit")->assertOk();
});

test('halaman invoice dapat dirender', function () {
    $this->actingAs($this->admin)->get("/servis/{$this->servis->id}/invoice")->assertOk();
    $this->actingAs($this->admin)->get("/servis/{$this->servis->id}/invoice/cetak")->assertOk();
});

test('invoice pdf dapat diunduh', function () {
    $respons = $this->actingAs($this->admin)
        ->get("/servis/{$this->servis->id}/invoice/pdf")
        ->assertOk();

    expect($respons->headers->get('content-type'))->toContain('application/pdf');
});

test('halaman owner dapat dirender', function (string $alamat) {
    $this->actingAs($this->owner)->get($alamat)->assertOk();
})->with(function () {
    return [
        '/dashboard',
        '/servis',
        '/laporan',
        '/laporan/cetak',
        '/statistik',
    ];
});

test('laporan pdf dapat diunduh', function () {
    $respons = $this->actingAs($this->owner)
        ->get('/laporan/pdf')
        ->assertOk();

    expect($respons->headers->get('content-type'))->toContain('application/pdf');
});

test('halaman publik dapat dirender', function (string $alamat) {
    $this->get($alamat)->assertOk();
})->with(function () {
    return [
        '/',
        '/cek-servis',
        '/kebijakan-privasi',
        '/syarat-ketentuan',
        '/sitemap.xml',
        '/login',
        '/forgot-password',
    ];
});

test('pengaturan nota dapat disimpan', function () {
    $this->actingAs($this->admin)
        ->put('/setting', [
            'footer_thanks'     => 'Terima kasih sudah mempercayakan perangkat Anda.',
            'garansi_servis'    => 'Garansi 14 hari.',
            'batas_pengambilan' => 'Diambil maksimal 2 bulan.',
        ])
        ->assertRedirect(route('setting.index'))
        ->assertSessionHasNoErrors();

    expect(App\Modules\Setting\Models\Setting::get('garansi_servis'))->toBe('Garansi 14 hari.');
});

test('markup pada pengaturan nota dibersihkan sebelum disimpan', function () {
    $this->actingAs($this->admin)->put('/setting', [
        'footer_thanks'     => '<script>alert(1)</script>Terima kasih',
        'garansi_servis'    => 'Garansi 7 hari.',
        'batas_pengambilan' => 'Maksimal 3 bulan.',
    ])->assertRedirect();

    expect(App\Modules\Setting\Models\Setting::get('footer_thanks'))
        ->toBe('alert(1)Terima kasih')
        ->not->toContain('<script>');
});
