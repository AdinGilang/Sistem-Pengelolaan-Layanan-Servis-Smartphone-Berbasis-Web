<?php

namespace App\Http\Controllers;

use App\Models\Servis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    private function namaBulan(int $bulan): string
    {
        $arr = [
            1  => 'Jan', 2  => 'Feb', 3  => 'Mar', 4  => 'Apr',
            5  => 'Mei', 6  => 'Jun', 7  => 'Jul', 8  => 'Agu',
            9  => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];
        return $arr[$bulan] ?? '-';
    }

    public function index(Request $request)
    {
        $tahun = (int) ($request->tahun ?? now()->year);

        // ── 1. Jumlah servis per bulan ───────────────────────────────────
        $servisPerBulan = Servis::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $tahun)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        // ── 2. Pendapatan per bulan (hanya status Selesai) ───────────────
        $pendapatanPerBulan = Servis::selectRaw('MONTH(created_at) as bulan, SUM(biaya) as total')
            ->whereYear('created_at', $tahun)
            ->where('status', 'Selesai')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        // ── 3. Distribusi status (semua tahun atau tahun dipilih) ────────
        $distribusiStatus = Servis::selectRaw('status, COUNT(*) as total')
            ->whereYear('created_at', $tahun)
            ->groupBy('status')
            ->pluck('total', 'status');

        // ── Build label & data array 12 bulan ────────────────────────────
        $labels           = [];
        $dataServis       = [];
        $dataPendapatan   = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[]         = $this->namaBulan($m);
            $dataServis[]     = $servisPerBulan[$m]     ?? 0;
            $dataPendapatan[] = $pendapatanPerBulan[$m] ?? 0;
        }

        // ── Status pie data ───────────────────────────────────────────────
        $statusLabels = ['Menunggu', 'Proses', 'Selesai'];
        $statusData   = [
            $distribusiStatus['Menunggu'] ?? 0,
            $distribusiStatus['Proses']   ?? 0,
            $distribusiStatus['Selesai']  ?? 0,
        ];

        // ── Summary cards ─────────────────────────────────────────────────
        $totalTahunIni      = Servis::whereYear('created_at', $tahun)->count();
        $totalPendapatan    = Servis::whereYear('created_at', $tahun)->where('status', 'Selesai')->sum('biaya');
        $totalSelesai       = Servis::whereYear('created_at', $tahun)->where('status', 'Selesai')->count();
        $rataPerBulan       = $totalTahunIni > 0 ? round($totalTahunIni / 12, 1) : 0;

        // ── Dropdown tahun ────────────────────────────────────────────────
        $tahunAwal = DB::table('servis')->selectRaw('YEAR(MIN(created_at)) as t')->value('t') ?? now()->year;
        $listTahun = range($tahunAwal, now()->year);

        return view('statistik.index', compact(
            'tahun', 'listTahun',
            'labels', 'dataServis', 'dataPendapatan',
            'statusLabels', 'statusData',
            'totalTahunIni', 'totalPendapatan', 'totalSelesai', 'rataPerBulan'
        ));
    }
}