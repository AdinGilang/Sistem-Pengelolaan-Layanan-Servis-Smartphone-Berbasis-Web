<?php

namespace App\Support\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * Cast enkripsi yang toleran terhadap data lama.
 *
 * Nilai baru selalu ditulis dalam keadaan terenkripsi. Saat membaca, bila
 * payload ternyata bukan ciphertext yang valid (baris lama yang tersimpan
 * sebelum enkripsi diterapkan) nilainya dikembalikan apa adanya, bukan
 * melempar exception. Ini membuat kolom sensitif bisa dienkripsi tanpa
 * memaksa migrasi data berjalan lebih dulu.
 *
 * @implements CastsAttributes<string|null, string|null>
 */
class EncryptedOrPlain implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return $value;
        }
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Crypt::encryptString((string) $value);
    }
}
