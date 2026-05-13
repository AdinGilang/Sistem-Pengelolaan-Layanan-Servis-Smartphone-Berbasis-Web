<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Default values
        DB::table('settings')->insert([
            ['key' => 'garansi_servis',   'value' => 'Garansi servis 7 hari setelah pengambilan.', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'batas_pengambilan','value' => 'Batas Pengambilan Maksimal 3 Bulan!',        'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_thanks',    'value' => 'Terima kasih atas kepercayaan Anda.',        'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};