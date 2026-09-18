<?php

namespace App\Modules\Servis\Policies;

use App\Models\User;
use App\Modules\Servis\Models\Servis;

/**
 * Sumber kebenaran tunggal untuk hak akses data servis.
 *
 * Aturan bisnis: Admin mengelola data, Owner hanya memantau.
 * Sebelumnya pembatasan ini hanya disembunyikan di tampilan, sehingga akun
 * Owner masih bisa menembak POST/PUT/DELETE secara langsung. Policy ini
 * menutup celah tersebut di sisi server.
 */
class ServisPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_OWNER]);
    }

    public function view(User $user, Servis $servis): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Servis $servis): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Servis $servis): bool
    {
        return $user->isAdmin();
    }

    /**
     * Pola kunci dan PIN perangkat hanya relevan bagi teknisi yang
     * mengerjakan unit. Owner yang sifatnya memantau tidak diberi akses.
     */
    public function viewCredentials(User $user, Servis $servis): bool
    {
        return $user->isAdmin();
    }
}
