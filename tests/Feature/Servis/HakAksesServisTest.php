<?php

use App\Models\User;
use App\Modules\Servis\Models\Servis;

/*
|--------------------------------------------------------------------------
| Pemisahan hak akses Admin dan Owner
|--------------------------------------------------------------------------
|
| Aturan bisnisnya: Admin mengelola data, Owner hanya memantau.
|
| Sebelum perbaikan, seluruh route servis berada dalam satu grup
| "role:admin|owner" dan pembatasan Owner hanya berupa penyembunyian tombol
| di tampilan. Akun Owner masih bisa membuat, mengubah, dan menghapus data
| dengan mengirim request langsung. Berkas ini menjaga celah itu tetap
| tertutup.
|
*/

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->owner = User::factory()->owner()->create();
});

test('tamu diarahkan ke halaman masuk', function () {
    $this->get('/servis')->assertRedirect('/login');
    $this->post('/servis', [])->assertRedirect('/login');
});

test('owner boleh melihat daftar servis', function () {
    Servis::factory()->count(3)->create();

    $this->actingAs($this->owner)
        ->get('/servis')
        ->assertOk();
});

test('owner tidak boleh membuka formulir tambah servis', function () {
    $this->actingAs($this->owner)
        ->get('/servis/create')
        ->assertForbidden();
});

test('owner tidak boleh menyimpan data servis baru', function () {
    $this->actingAs($this->owner)
        ->post('/servis', dataServisValid())
        ->assertForbidden();

    expect(Servis::count())->toBe(0);
});

test('owner tidak boleh mengubah data servis', function () {
    $servis = Servis::factory()->create(['pelanggan' => 'Nama Asli']);

    $this->actingAs($this->owner)
        ->put("/servis/{$servis->id}", dataServisValid(['pelanggan' => 'Nama Diubah']))
        ->assertForbidden();

    expect($servis->refresh()->pelanggan)->toBe('Nama Asli');
});

test('owner tidak boleh menghapus data servis', function () {
    $servis = Servis::factory()->create();

    $this->actingAs($this->owner)
        ->delete("/servis/{$servis->id}")
        ->assertForbidden();

    expect(Servis::whereKey($servis->id)->exists())->toBeTrue();
});

test('admin boleh menyimpan data servis baru', function () {
    $this->actingAs($this->admin)
        ->post('/servis', dataServisValid())
        ->assertRedirect(route('servis.index'));

    expect(Servis::count())->toBe(1);
});

test('admin boleh menghapus data servis dan data masih dapat dipulihkan', function () {
    $servis = Servis::factory()->create();

    $this->actingAs($this->admin)
        ->delete("/servis/{$servis->id}")
        ->assertRedirect(route('servis.index'));

    // Soft delete: baris hilang dari daftar, tetapi arsipnya tetap ada.
    expect(Servis::whereKey($servis->id)->exists())->toBeFalse()
        ->and(Servis::withTrashed()->whereKey($servis->id)->exists())->toBeTrue();
});

test('laporan dan statistik tertutup bagi admin', function () {
    $this->actingAs($this->admin)->get('/laporan')->assertForbidden();
    $this->actingAs($this->admin)->get('/statistik')->assertForbidden();
});

test('pengaturan nota tertutup bagi owner', function () {
    $this->actingAs($this->owner)->get('/setting')->assertForbidden();
});

/**
 * @param  array<string, mixed>  $ganti
 * @return array<string, mixed>
 */
function dataServisValid(array $ganti = []): array
{
    return array_merge([
        'tanggal'   => now()->toDateString(),
        'pelanggan' => 'Pelanggan Uji',
        'kerusakan' => 'LCD pecah',
        'status'    => Servis::STATUS_MENUNGGU,
    ], $ganti);
}
