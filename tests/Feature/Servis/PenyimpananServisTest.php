<?php

use App\Models\User;
use App\Modules\Servis\Models\Servis;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

test('kelengkapan tersimpan sebagai array, bukan JSON berlapis dua', function () {
    /*
     * Regresi untuk bug lama: controller memanggil json_encode() pada
     * $validated['kelengkapan'], lalu cast "array" di model meng-encode
     * nilai itu sekali lagi. Yang tersimpan menjadi string JSON di dalam
     * string JSON, sehingga saat dibaca kembali bukan array — dan daftar
     * kelengkapan di halaman detail selalu tampil kosong.
     */
    $this->actingAs($this->admin)->post('/servis', [
        'tanggal'     => now()->toDateString(),
        'pelanggan'   => 'Pelanggan Uji',
        'kerusakan'   => 'Baterai drop',
        'status'      => Servis::STATUS_MENUNGGU,
        'kelengkapan' => ['Baterai', 'Charger'],
    ])->assertRedirect();

    $servis = Servis::firstOrFail();

    expect($servis->kelengkapan)->toBeArray()
        ->and($servis->kelengkapan)->toBe(['Baterai', 'Charger']);
});

test('status di luar daftar resmi ditolak', function () {
    $this->actingAs($this->admin)->post('/servis', [
        'tanggal'   => now()->toDateString(),
        'pelanggan' => 'Pelanggan Uji',
        'kerusakan' => 'LCD pecah',
        'status'    => 'Dibatalkan Sepihak',
    ])->assertSessionHasErrors('status');

    expect(Servis::count())->toBe(0);
});

test('kode servis dibangkitkan server dan tidak bisa ditentukan dari request', function () {
    $this->actingAs($this->admin)->post('/servis', [
        'tanggal'   => now()->toDateString(),
        'pelanggan' => 'Pelanggan Uji',
        'kerusakan' => 'LCD pecah',
        'status'    => Servis::STATUS_MENUNGGU,
        'kode_unik' => 'SRV-PALSU-0001',
    ])->assertRedirect();

    expect(Servis::firstOrFail()->kode_unik)
        ->not->toBe('SRV-PALSU-0001')
        ->toStartWith('SRV-');
});

test('PIN disimpan sebagai hash, bukan teks biasa', function () {
    $this->actingAs($this->admin)->post('/servis', [
        'tanggal'    => now()->toDateString(),
        'pelanggan'  => 'Pelanggan Uji',
        'kerusakan'  => 'LCD pecah',
        'status'     => Servis::STATUS_MENUNGGU,
        'kata_sandi' => '1234',
    ])->assertRedirect();

    $tersimpan = Servis::firstOrFail()->getAttributes()['kata_sandi'];

    expect($tersimpan)->not->toBe('1234')
        ->and(Hash::check('1234', $tersimpan))->toBeTrue();
});

test('pola kunci disimpan terenkripsi namun tetap terbaca lewat model', function () {
    $this->actingAs($this->admin)->post('/servis', [
        'tanggal'    => now()->toDateString(),
        'pelanggan'  => 'Pelanggan Uji',
        'kerusakan'  => 'LCD pecah',
        'status'     => Servis::STATUS_MENUNGGU,
        'pola_kunci' => '1-2-3-6-9',
    ])->assertRedirect();

    $servis = Servis::firstOrFail();

    expect($servis->getAttributes()['pola_kunci'])->not->toBe('1-2-3-6-9')
        ->and($servis->pola_kunci)->toBe('1-2-3-6-9');
});

test('PIN lama dipertahankan bila kolomnya dikosongkan saat mengubah data', function () {
    $servis = Servis::factory()->denganPin('4321')->create();
    $hashLama = $servis->getAttributes()['kata_sandi'];

    $this->actingAs($this->admin)->put("/servis/{$servis->id}", [
        'tanggal'    => now()->toDateString(),
        'pelanggan'  => $servis->pelanggan,
        'kerusakan'  => $servis->kerusakan,
        'status'     => Servis::STATUS_PROSES,
        'kata_sandi' => '',
    ])->assertRedirect();

    expect($servis->refresh()->getAttributes()['kata_sandi'])->toBe($hashLama);
});

test('kredensial perangkat tidak ikut saat model diserialisasi', function () {
    $servis = Servis::factory()->denganPin()->create();

    expect($servis->toArray())
        ->not->toHaveKey('kata_sandi')
        ->not->toHaveKey('pola_kunci');
});

test('karakter wildcard pada pencarian tidak mengembalikan seluruh baris', function () {
    Servis::factory()->create(['pelanggan' => 'Budi Santoso']);
    Servis::factory()->create(['pelanggan' => 'Siti Aminah']);

    // Tanpa penetralan, "%" pada input LIKE berarti "cocokkan apa saja".
    expect(Servis::query()->search('%')->count())->toBe(0);
});
