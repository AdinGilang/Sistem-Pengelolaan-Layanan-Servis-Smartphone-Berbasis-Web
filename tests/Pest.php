<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Helper bersama
|--------------------------------------------------------------------------
*/

/**
 * Kata sandi contoh yang memenuhi kebijakan aplikasi: minimal 10 karakter,
 * mengandung huruf besar, huruf kecil, angka, dan simbol.
 *
 * Ditulis sekali di sini supaya ketika kebijakan berubah, hanya satu tempat
 * yang perlu disesuaikan.
 */
function passwordValid(): string
{
    return 'Servis#2026aman';
}
