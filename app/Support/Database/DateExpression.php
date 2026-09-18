<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

/**
 * Ekspresi tanggal yang sadar driver database.
 *
 * Versi sebelumnya menulis MONTH(created_at) dan YEAR(created_at) langsung
 * di dalam selectRaw. Fungsi itu hanya ada di MySQL, sehingga laporan dan
 * statistik langsung gagal ketika dijalankan di atas SQLite — termasuk saat
 * test suite berjalan, karena phpunit.xml memang memakai SQLite in-memory.
 */
final class DateExpression
{
    private function __construct()
    {
    }

    /** Ekspresi SQL yang menghasilkan nomor bulan (1-12) dari sebuah kolom. */
    public static function month(string $column = 'created_at'): string
    {
        return match (self::driver()) {
            'sqlite' => "CAST(strftime('%m', {$column}) AS INTEGER)",
            'pgsql'  => "EXTRACT(MONTH FROM {$column})",
            default  => "MONTH({$column})",
        };
    }

    /** Ekspresi SQL yang menghasilkan tahun dari sebuah kolom. */
    public static function year(string $column = 'created_at'): string
    {
        return match (self::driver()) {
            'sqlite' => "CAST(strftime('%Y', {$column}) AS INTEGER)",
            'pgsql'  => "EXTRACT(YEAR FROM {$column})",
            default  => "YEAR({$column})",
        };
    }

    private static function driver(): string
    {
        return DB::connection()->getDriverName();
    }
}
