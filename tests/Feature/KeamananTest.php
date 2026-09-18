<?php

use App\Models\User;
use App\Modules\Servis\Models\Servis;

test('peran tidak bisa dinaikkan lewat isian request saat mendaftar', function () {
    /*
     * Regresi untuk celah lama: kolom "role" ikut di dalam $fillable milik
     * model User, sehingga satu field tambahan pada body request bisa
     * menentukan peran akun yang baru dibuat.
     */
    $this->post('/register', [
        'name'                  => 'Penyusup',
        'email'                 => 'penyusup@contoh.test',
        'password'              => passwordValid(),
        'password_confirmation' => passwordValid(),
        'role'                  => 'owner',
    ]);

    $user = User::where('email', 'penyusup@contoh.test')->firstOrFail();

    expect($user->role)->toBe(config('auth.registration_role'));
});

test('peran tidak bisa diubah lewat pembaruan profil', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->patch('/profile', [
        'name'  => 'Nama Baru',
        'email' => $user->email,
        'role'  => 'owner',
    ]);

    expect($user->refresh()->role)->toBe(User::ROLE_ADMIN);
});

test('header keamanan terpasang pada respons halaman', function () {
    $respons = $this->get('/');

    $respons->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

    expect($respons->headers->get('Content-Security-Policy'))
        ->toContain("default-src 'self'")
        ->toContain("frame-ancestors 'self'");
});

test('halaman di balik login diminta untuk tidak diindeks mesin pencari', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/dashboard')
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('percobaan masuk dibatasi lajunya', function () {
    $user = User::factory()->create();

    foreach (range(1, 6) as $percobaan) {
        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'salah-sekali',
        ]);
    }

    $this->post('/login', [
        'email'    => $user->email,
        'password' => 'salah-sekali',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('middleware peran tidak membuat galat server ketika sesi habis', function () {
    /*
     * Versi lama membaca auth()->user()->role tanpa memeriksa apakah
     * pengguna masih ada. Ketika sesi kedaluwarsa, baris itu memicu galat
     * 500 yang halaman errornya justru membocorkan jejak stack.
     */
    $this->get('/setting')->assertRedirect('/login');
});

test('halaman galat tidak membocorkan detail teknis', function () {
    $this->actingAs(User::factory()->owner()->create())
        ->get('/setting')
        ->assertForbidden()
        ->assertDontSee('vendor\\laravel')
        ->assertDontSee('Stack trace');
});

test('data servis milik orang lain tidak bisa ditebak lewat id berurutan tanpa login', function () {
    $servis = Servis::factory()->create();

    $this->get("/servis/{$servis->id}")->assertRedirect('/login');
    $this->get("/servis/{$servis->id}/invoice")->assertRedirect('/login');
});
