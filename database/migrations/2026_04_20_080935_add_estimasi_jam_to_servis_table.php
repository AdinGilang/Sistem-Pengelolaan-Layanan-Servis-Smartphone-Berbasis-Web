<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom estimasi_jam sebetulnya sudah dibuat migration 2026_04_16.
 *
 * Migration ini menambahkannya sekali lagi, sehingga `php artisan migrate`
 * pada database kosong berhenti dengan galat "duplicate column". Berkasnya
 * tidak dihapus supaya riwayat migration pada database yang sudah berjalan
 * tetap utuh; isinya cukup dibuat idempoten.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('servis', 'estimasi_jam')) {
            return;
        }

        Schema::table('servis', function (Blueprint $table) {
            $table->integer('estimasi_jam')->nullable()->after('estimasi_hari');
        });
    }

    public function down(): void
    {
        // Kepemilikan kolom ini ada pada migration 2026_04_16, jadi
        // rollback-nya diserahkan ke sana agar tidak terhapus dua kali.
    }
};
