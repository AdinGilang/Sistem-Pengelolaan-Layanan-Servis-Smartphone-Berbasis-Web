<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Penguatan skema: penghapusan lunak, indeks pencarian, dan kolom yang
 * cukup panjang untuk menampung nilai terenkripsi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servis', function (Blueprint $table) {
            // Data servis adalah bukti transaksi. Penghapusan permanen lewat
            // satu klik terlalu berisiko, jadi dipindahkan ke soft delete.
            if (! Schema::hasColumn('servis', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // pola_kunci kini disimpan terenkripsi. Ciphertext Laravel jauh lebih
        // panjang daripada teks aslinya sehingga kolom string biasa bisa
        // kepenuhan dan nilainya terpotong — yang berarti data tidak lagi
        // bisa didekripsi.
        if (Schema::hasColumn('servis', 'pola_kunci')) {
            Schema::table('servis', function (Blueprint $table) {
                $table->text('pola_kunci')->nullable()->change();
            });
        }

        Schema::table('servis', function (Blueprint $table) {
            // Kolom yang dipakai untuk filter daftar, laporan, dan statistik.
            $table->index('status', 'servis_status_index');
            $table->index('created_at', 'servis_created_at_index');
            $table->index('pelanggan', 'servis_pelanggan_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'users_role_index');
        });

        // Rapikan nilai role yang terlanjur di luar daftar resmi.
        DB::table('users')
            ->whereNotIn('role', User::ROLES)
            ->update(['role' => User::ROLE_ADMIN]);
    }

    public function down(): void
    {
        Schema::table('servis', function (Blueprint $table) {
            $table->dropIndex('servis_status_index');
            $table->dropIndex('servis_created_at_index');
            $table->dropIndex('servis_pelanggan_index');

            if (Schema::hasColumn('servis', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
        });
    }
};
