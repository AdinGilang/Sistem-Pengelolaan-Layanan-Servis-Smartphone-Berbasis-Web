<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Membuat satu akun Admin dan satu akun Owner.
     *
     * Kata sandi tidak ditulis keras di dalam berkas ini. Isi dulu
     * SEED_ADMIN_PASSWORD dan SEED_OWNER_PASSWORD pada .env; kalau kosong,
     * seeder membangkitkan kata sandi acak lalu menampilkannya sekali di
     * terminal. Dengan begitu tidak ada kredensial bawaan yang sama di
     * setiap pemasangan.
     */
    public function run(): void
    {
        $this->buatAkun(
            nama: 'Administrator',
            email: env('SEED_ADMIN_EMAIL', 'admin@phonerepair.test'),
            role: User::ROLE_ADMIN,
            password: env('SEED_ADMIN_PASSWORD'),
        );

        $this->buatAkun(
            nama: 'Pemilik',
            email: env('SEED_OWNER_EMAIL', 'owner@phonerepair.test'),
            role: User::ROLE_OWNER,
            password: env('SEED_OWNER_PASSWORD'),
        );
    }

    private function buatAkun(string $nama, string $email, string $role, ?string $password): void
    {
        if (User::query()->where('email', $email)->exists()) {
            $this->command?->warn("Akun {$email} sudah ada, dilewati.");

            return;
        }

        $dibangkitkan = blank($password);
        $password ??= Str::password(16);

        $user = new User();
        $user->name              = $nama;
        $user->email             = $email;
        $user->password          = $password;
        $user->role              = $role;
        $user->email_verified_at = now();
        $user->save();

        if ($dibangkitkan) {
            $this->command?->info("Akun {$role}: {$email} — kata sandi: {$password}");
            $this->command?->warn('Simpan kata sandi di atas sekarang; nilainya tidak ditampilkan lagi.');
        } else {
            $this->command?->info("Akun {$role}: {$email} dibuat dengan kata sandi dari .env.");
        }
    }
}
