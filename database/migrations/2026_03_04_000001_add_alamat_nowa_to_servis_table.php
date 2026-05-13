<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servis', function (Blueprint $table) {
            $table->string('alamat')->nullable()->after('pelanggan');
            $table->string('no_wa')->nullable()->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('servis', function (Blueprint $table) {
            $table->dropColumn(['alamat', 'no_wa']);
        });
    }
};