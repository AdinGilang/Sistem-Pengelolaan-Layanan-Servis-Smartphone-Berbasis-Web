<?php

use App\Models\User;
use App\Modules\Servis\Models\Servis;

beforeEach(function () {
    $this->owner = User::factory()->owner()->create();
});

test('laporan bulanan berjalan di atas SQLite maupun MySQL', function () {
    /*
     * Regresi untuk bug lama: laporan dan statistik memakai MONTH() dan
     * YEAR() lewat selectRaw. Kedua fungsi itu khas MySQL, sehingga seluruh
     * halaman ini langsung gagal di SQLite — termasuk di test suite, yang
     * menurut phpunit.xml memang berjalan di atas SQLite in-memory.
     * Sekarang ekspresinya disesuaikan dengan driver yang aktif.
     */
    Servis::factory()->count(3)->selesai()->create();

    $this->actingAs($this->owner)
        ->get('/laporan')
        ->assertOk();
});

test('statistik tahunan berjalan di atas SQLite maupun MySQL', function () {
    Servis::factory()->count(4)->create();

    $this->actingAs($this->owner)
        ->get('/statistik?tahun=' . now()->year)
        ->assertOk();
});

test('periode di luar rentang wajar ditolak', function () {
    /*
     * Bulan dan tahun ikut membentuk kunci cache. Tanpa pembatasan, ribuan
     * entri sampah bisa disuntikkan ke penyimpanan cache hanya dengan
     * mengubah query string.
     */
    $this->actingAs($this->owner)
        ->get('/laporan?bulan=99&tahun=9999')
        ->assertSessionHasErrors(['bulan', 'tahun']);
});

test('ekspor excel menghasilkan berkas', function () {
    Servis::factory()->count(2)->create();

    $this->actingAs($this->owner)
        ->get('/laporan/excel?bulan=' . now()->month . '&tahun=' . now()->year)
        ->assertOk();
});

test('nomor urut ekspor selalu dimulai dari satu', function () {
    /*
     * Regresi: penomoran memakai "static $no" di dalam method map(), yang
     * melekat pada method dan bukan pada instance. Bila export dipanggil
     * dua kali dalam satu proses, hitungan lanjut dari export sebelumnya.
     */
    Servis::factory()->count(2)->create();

    $laporan = app(App\Modules\Laporan\Services\LaporanService::class);
    $bulan   = (int) now()->month;
    $tahun   = (int) now()->year;

    $pertama = new App\Modules\Laporan\Exports\LaporanExport($laporan, $bulan, $tahun);
    $kedua   = new App\Modules\Laporan\Exports\LaporanExport($laporan, $bulan, $tahun);

    $barisPertama = $pertama->map(Servis::first());
    $barisKedua   = $kedua->map(Servis::first());

    expect($barisPertama[0])->toBe(1)
        ->and($barisKedua[0])->toBe(1);
});

test('cache laporan dibuang otomatis ketika data servis berubah', function () {
    $this->actingAs($this->owner)->get('/laporan')->assertOk();

    Servis::factory()->create(['pelanggan' => 'Pelanggan Baru']);

    $this->actingAs($this->owner)
        ->get('/laporan')
        ->assertOk()
        ->assertSee('Pelanggan Baru');
});
