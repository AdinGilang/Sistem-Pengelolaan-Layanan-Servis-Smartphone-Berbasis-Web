<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password as tanyaKataSandi;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

/**
 * Jalur resmi pembuatan akun setelah pendaftaran mandiri ditutup.
 *
 *   php artisan user:create
 *   php artisan user:create --name="Budi" --email=budi@toko.test --role=admin
 *
 * Kata sandi tidak pernah diterima lewat opsi baris perintah, supaya tidak
 * ikut tersimpan di riwayat shell maupun terbaca dari daftar proses. Bila
 * perintah dijalankan tanpa terminal interaktif (skrip penyiapan server,
 * pipeline, kontainer), kata sandi acak dibangkitkan lalu ditampilkan sekali.
 */
class CreateUserCommand extends Command
{
    protected $signature = 'user:create
        {--name= : Nama lengkap pengguna}
        {--email= : Alamat surel untuk masuk}
        {--role= : Peran pengguna (admin atau owner)}';

    protected $description = 'Membuat akun pengguna baru (admin atau owner)';

    public function handle(): int
    {
        $interaktif = $this->input->isInteractive();

        $name  = $this->option('name');
        $email = $this->option('email');
        $role  = $this->option('role') ?: User::ROLE_ADMIN;

        if ($interaktif) {
            $name  = $name ?: text('Nama lengkap', required: true);
            $email = $email ?: text('Alamat surel', required: true);
            $role  = $this->option('role') ?: select('Peran', User::ROLES, default: User::ROLE_ADMIN);
        }

        if (blank($name) || blank($email)) {
            $this->components->error('Opsi --name dan --email wajib diisi saat perintah dijalankan tanpa terminal interaktif.');

            return self::FAILURE;
        }

        [$password, $dibangkitkan] = $this->kataSandi($interaktif);

        if ($password === null) {
            return self::FAILURE;
        }

        $validator = Validator::make([
            'name'     => $name,
            'email'    => $email,
            'role'     => $role,
            'password' => $password,
        ], [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', 'in:' . implode(',', User::ROLES)],
            'password' => ['required', Password::min(10)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $pesan) {
                $this->components->error($pesan);
            }

            return self::FAILURE;
        }

        $user = new User();
        $user->name              = $name;
        $user->email             = $email;
        $user->password          = $password;
        $user->role              = $role;
        $user->email_verified_at = now();
        $user->save();

        $this->components->info("Akun {$user->email} dibuat dengan peran {$user->role}.");

        if ($dibangkitkan) {
            $this->components->warn("Kata sandi: {$password}");
            $this->components->warn('Catat sekarang; nilainya tidak ditampilkan lagi.');
        }

        return self::SUCCESS;
    }

    /**
     * @return array{0: string|null, 1: bool}
     */
    private function kataSandi(bool $interaktif): array
    {
        if (! $interaktif) {
            return [Str::password(16), true];
        }

        $password   = tanyaKataSandi('Kata sandi', required: true);
        $konfirmasi = tanyaKataSandi('Ulangi kata sandi', required: true);

        if ($password !== $konfirmasi) {
            $this->components->error('Konfirmasi kata sandi tidak cocok.');

            return [null, false];
        }

        return [$password, false];
    }
}
