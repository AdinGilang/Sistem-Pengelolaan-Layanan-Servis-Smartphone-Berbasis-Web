<?php

namespace App\Modules\Laporan\Services;

use App\Modules\Servis\Models\Servis;
use App\Support\Database\DateExpression;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Perhitungan laporan bulanan beserta cache-nya.
 */
class LaporanService
{
    /** Umur cache laporan dalam menit. */
    public const CACHE_TTL = 60;

    private const NAMA_BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function namaBulan(int $bulan): string
    {
        return self::NAMA_BULAN[$bulan] ?? 'Bulan';
    }

    /**
     * Pastikan bulan dan tahun berada pada rentang masuk akal.
     *
     * Nilai ini ikut membentuk cache key, jadi tanpa pembatasan seorang
     * penyerang bisa membanjiri cache store dengan ribuan entri sampah
     * hanya dengan mengubah query string.
     *
     * @return array{0:int, 1:int}
     */
    public function normalisasiPeriode(mixed $bulan, mixed $tahun): array
    {
        $bulan = (int) ($bulan ?: now()->month);
        $tahun = (int) ($tahun ?: now()->year);

        $bulan = max(1, min(12, $bulan));
        $tahun = max(2000, min((int) now()->year + 1, $tahun));

        return [$bulan, $tahun];
    }

    /**
     * @return array{
     *     data: Collection,
     *     totalServis: int,
     *     totalSelesai: int,
     *     totalProses: int,
     *     totalMenunggu: int,
     *     totalPendapatan: int
     * }
     */
    public function ringkasan(int $bulan, int $tahun): array
    {
        return Cache::remember(
            $this->cacheKey($bulan, $tahun),
            now()->addMinutes(self::CACHE_TTL),
            function () use ($bulan, $tahun): array {
                $data = $this->query($bulan, $tahun)->get();

                return [
                    'data'            => $data,
                    'totalServis'     => $data->count(),
                    'totalSelesai'    => $data->where('status', Servis::STATUS_SELESAI)->count(),
                    'totalProses'     => $data->where('status', Servis::STATUS_PROSES)->count(),
                    'totalMenunggu'   => $data->where('status', Servis::STATUS_MENUNGGU)->count(),
                    'totalPendapatan' => (int) $data->where('status', Servis::STATUS_SELESAI)->sum('biaya'),
                ];
            }
        );
    }

    public function query(int $bulan, int $tahun)
    {
        return Servis::query()
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->orderByDesc('created_at');
    }

    /**
     * Daftar tahun yang punya data, untuk mengisi dropdown filter.
     *
     * @return list<int>
     */
    public function daftarTahun(): array
    {
        $tahunAwal = Cache::remember('laporan.tahun_awal', now()->addMinutes(self::CACHE_TTL), function (): int {
            $nilai = DB::table('servis')
                ->whereNull('deleted_at')
                ->selectRaw(DateExpression::year('MIN(created_at)') . ' as tahun_awal')
                ->value('tahun_awal');

            return (int) ($nilai ?: now()->year);
        });

        $tahunAwal = max(2000, min($tahunAwal, (int) now()->year));

        return range($tahunAwal, (int) now()->year);
    }

    public function lupakanCache(int $bulan, int $tahun): void
    {
        Cache::forget($this->cacheKey($bulan, $tahun));
        Cache::forget('laporan.tahun_awal');
    }

    private function cacheKey(int $bulan, int $tahun): string
    {
        return "laporan.servis.{$tahun}.{$bulan}";
    }
}
