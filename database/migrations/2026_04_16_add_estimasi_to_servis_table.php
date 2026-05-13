<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servis', function (Blueprint $table) {
            $table->date('estimasi_selesai')->nullable()->after('tanggal');
            $table->integer('estimasi_hari')->nullable()->after('estimasi_selesai');
            $table->integer('estimasi_jam')->nullable()->after('estimasi_hari');
        });
    }

    public function down(): void
    {
        Schema::table('servis', function (Blueprint $table) {
            $table->dropColumn(['estimasi_selesai', 'estimasi_hari', 'estimasi_jam']);
        });
    }
};