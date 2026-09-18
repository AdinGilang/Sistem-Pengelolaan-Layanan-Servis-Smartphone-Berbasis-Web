<?php

namespace App\Modules\Statistik\Services;

use App\Modules\Servis\Models\Servis;
use App\Support\Database\DateExpression;

class StatistikService
{
    private const NAMA_BULAN_SINGKAT = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    /**
     * Seluruh angka statistik satu tahun.
     *
     * Dikumpulkan lewat dua query agregat, bukan sembilan query terpisah
     * seperti versi sebelumnya yang memanggil count() dan sum() berulang kali
     * untuk tiap kartu ringkasan.
     *
     * @return array<string, mixed>
     */
    public function untukTahun(int $tahun): array
    {
        $bulanan     = $this->rekapBulanan($tahun);
        $perStatus   = $this->rekapStatus($tahun);

        $labels         = [];
        $dataServis     = [];
        $dataPendapatan = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $labels[]         = self::NAMA_BULAN_SINGKAT[$bulan];
            $dataServis[]     = (int) ($bulanan[$bulan]['jumlah'] ?? 0);
            $dataPendapatan[] = (int) ($bulanan[$bulan]['pendapatan'] ?? 0);
        }

        $totalTahunIni = array_sum($dataServis);
        $totalSelesai  = (int) ($perStatus[Servis::STATUS_SELESAI]['jumlah'] ?? 0);

        return [
            'labels'          => $labels,
            'dataServis'      => $dataServis,
            'dataPendapatan'  => $dataPendapatan,
            'statusLabels'    => Servis::STATUSES,
            'statusData'      => array_map(
                static fn (string $status): int => (int) ($perStatus[$status]['jumlah'] ?? 0),
                Servis::STATUSES,
            ),
            'totalTahunIni'   => $totalTahunIni,
            'totalSelesai'    => $totalSelesai,
            'totalPendapatan' => array_sum($dataPendapatan),
            'rataPerBulan'    => $totalTahunIni > 0 ? round($totalTahunIni / 12, 1) : 0,
        ];
    }

    /**
     * Jumlah unit dan pendapatan per bulan dalam satu query.
     *
     * @return array<int, array{jumlah:int, pendapatan:int}>
     */
    private function rekapBulanan(int $tahun): array
    {
        $bulan = DateExpression::month();

        return Servis::query()
            ->selectRaw("{$bulan} as bulan, COUNT(*) as jumlah")
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN COALESCE(biaya, 0) ELSE 0 END) as pendapatan',
                [Servis::STATUS_SELESAI],
            )
            ->whereYear('created_at', $tahun)
            ->groupByRaw($bulan)
            ->get()
            ->keyBy('bulan')
            ->map(static fn ($row): array => [
                'jumlah'     => (int) $row->jumlah,
                'pendapatan' => (int) $row->pendapatan,
            ])
            ->all();
    }

    /**
     * @return array<string, array{jumlah:int}>
     */
    private function rekapStatus(int $tahun): array
    {
        return Servis::query()
            ->selectRaw('status, COUNT(*) as jumlah')
            ->whereYear('created_at', $tahun)
            ->groupBy('status')
            ->get()
            ->keyBy('status')
            ->map(static fn ($row): array => ['jumlah' => (int) $row->jumlah])
            ->all();
    }
}
