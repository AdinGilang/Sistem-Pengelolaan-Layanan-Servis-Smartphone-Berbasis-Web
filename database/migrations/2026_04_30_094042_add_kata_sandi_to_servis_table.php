<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servis', function (Blueprint $table) {
            $table->string('kata_sandi')->nullable()->after('pola_kunci');
        });
    }

    public function down(): void
    {
        Schema::table('servis', function (Blueprint $table) {
            $table->dropColumn('kata_sandi');
        });
    }
};