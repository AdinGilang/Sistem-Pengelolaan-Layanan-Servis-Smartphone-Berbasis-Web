<?php

use App\Models\User;
use App\Modules\Servis\Models\Servis;

/*
|--------------------------------------------------------------------------
| Halaman pelacakan publik
|--------------------------------------------------------------------------
|
| Halaman ini terbuka tanpa login, jadi isinya harus dibatasi pada hal yang
| memang perlu diketahui pemilik perangkat.
|
*/

test('halaman cek servis dapat dibuka tanpa login', function () {
    $this->get('/cek-servis')->assertOk();
});

test('kode yang benar menampilkan status perbaikan', function () {
    $servis = Servis::factory()->create([
        'pelanggan' => 'Budi Santoso',
        'kerusakan' => 'LCD pecah',
        'status'    => Servis::STATUS_PROSES,
    ]);

    $this->get('/cek-servis?kode=' . $servis->kode_unik)
        ->assertOk()
        ->assertSee($servis->kode_unik)
        ->assertSee('Budi Santoso')
        ->assertSee('LCD pecah');
});

test('data pribadi tidak ikut tampil di halaman publik', function () {
    $servis = Servis::factory()->denganPin('1234')->create([
        'alamat' => 'Jalan Rahasia Nomor 7',
        'no_wa'  => '081298765432',
    ]);

    $this->get('/cek-servis?kode=' . $servis->kode_unik)
        ->assertOk()
        ->assertDontSee('Jalan Rahasia Nomor 7')
        ->assertDontSee('081298765432')
        ->assertDontSee('1-2-3-6-9');
});

test('kode yang tidak dikenal menampilkan pesan tidak ditemukan', function () {
    Servis::factory()->create();

    $this->get('/cek-servis?kode=SRV-2026-TIDAKADA')
        ->assertOk()
        ->assertSee('tidak ditemukan', false);
});

test('halaman publik tidak dibatasi peran pengguna', function () {
    $servis = Servis::factory()->create();

    $this->actingAs(User::factory()->owner()->create())
        ->get('/cek-servis?kode=' . $servis->kode_unik)
        ->assertOk();
});
