<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('servis', function (Blueprint $table) {
        $table->integer('estimasi_jam')->nullable()->after('estimasi_hari');
    });
}

public function down(): void
{
    Schema::table('servis', function (Blueprint $table) {
        $table->dropColumn('estimasi_jam');
    });
}
};
