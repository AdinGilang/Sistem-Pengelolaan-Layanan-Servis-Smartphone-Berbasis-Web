<?php

use App\Models\User;
use App\Modules\Servis\Models\Servis;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('pin-verify');
    $this->admin  = User::factory()->admin()->create();
    $this->owner  = User::factory()->owner()->create();
    $this->servis = Servis::factory()->denganPin('1234')->create();
});

test('PIN yang benar diterima', function () {
    $this->actingAs($this->admin)
        ->postJson("/servis/{$this->servis->id}/pin-verify", ['pin' => '1234'])
        ->assertOk()
        ->assertJson(['success' => true]);
});

test('PIN yang salah ditolak', function () {
    $this->actingAs($this->admin)
        ->postJson("/servis/{$this->servis->id}/pin-verify", ['pin' => '9999'])
        ->assertStatus(422)
        ->assertJson(['success' => false]);
});

test('owner tidak boleh memverifikasi kredensial perangkat', function () {
    /*
     * Pola kunci dan PIN hanya relevan bagi teknisi yang mengerjakan unit.
     * Akun pemantau tidak punya keperluan membukanya.
     */
    $this->actingAs($this->owner)
        ->postJson("/servis/{$this->servis->id}/pin-verify", ['pin' => '1234'])
        ->assertForbidden();
});

test('percobaan PIN dibatasi setelah lima kali salah', function () {
    /*
     * Regresi untuk celah lama: endpoint ini tidak punya pembatasan sama
     * sekali. PIN 4 digit hanya punya 10.000 kemungkinan, jadi seluruhnya
     * bisa dicoba dalam hitungan menit.
     */
    foreach (range(1, 5) as $percobaan) {
        $this->actingAs($this->admin)
            ->postJson("/servis/{$this->servis->id}/pin-verify", ['pin' => '0000'])
            ->assertStatus(422);
    }

    $this->actingAs($this->admin)
        ->postJson("/servis/{$this->servis->id}/pin-verify", ['pin' => '1234'])
        ->assertStatus(429);
});

test('PIN wajib berupa empat sampai enam digit angka', function () {
    $this->actingAs($this->admin)
        ->postJson("/servis/{$this->servis->id}/pin-verify", ['pin' => 'bukan-angka'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('pin');
});

test('keterangan PIN tidak bisa dibuka sebelum diverifikasi', function () {
    $this->actingAs($this->admin)
        ->getJson("/servis/{$this->servis->id}/pin-show")
        ->assertForbidden();
});

test('nilai asli PIN tidak pernah dikirim balik ke browser', function () {
    $this->actingAs($this->admin)
        ->postJson("/servis/{$this->servis->id}/pin-verify", ['pin' => '1234'])
        ->assertOk();

    $this->actingAs($this->admin)
        ->getJson("/servis/{$this->servis->id}/pin-show")
        ->assertOk()
        ->assertJson(['verified' => true])
        ->assertDontSee('1234');
});
