<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servis', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('kode_unik');
            $table->string('merk_hp')->nullable()->after('no_wa');
            $table->string('tipe_hp')->nullable()->after('merk_hp');
            $table->json('kelengkapan')->nullable()->after('tipe_hp');
            $table->string('teknisi')->nullable()->after('kelengkapan');
            $table->string('pola_kunci')->nullable()->after('teknisi');
        });
    }

    public function down(): void
    {
        Schema::table('servis', function (Blueprint $table) {
            $table->dropColumn(['tanggal','merk_hp','tipe_hp','kelengkapan','teknisi','pola_kunci']);
        });
    }
};