<?php

namespace App\Http\Controllers;

use App\Models\Servis;
use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    // Durasi cache dalam menit
    const CACHE_TTL = 60;

    // ── Helper: nama bulan Bahasa Indonesia ──────────────────────────────
    private function namaBulan(int $bulan): string
    {
        $arr = [
            1  => 'Januari',   2  => 'Februari',  3  => 'Maret',
            4  => 'April',     5  => 'Mei',        6  => 'Juni',
            7  => 'Juli',      8  => 'Agustus',    9  => 'September',
            10 => 'Oktober',   11 => 'November',   12 => 'Desember',
        ];
        return $arr[$bulan] ?? 'Bulan';
    }

    // ── Helper: ambil data servis (dengan cache) ──────────────────────────
    private function getDataServis(int $bulan, int $tahun): array
    {
        $cacheKey = "laporan_servis_{$bulan}_{$tahun}";

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () use ($bulan, $tahun) {
            $data = Servis::whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->orderBy('created_at', 'desc')
                ->get();

            return [
                'data'            => $data,
                'totalServis'     => $data->count(),
                'totalSelesai'    => $data->where('status', 'Selesai')->count(),
                'totalProses'     => $data->where('status', 'Proses')->count(),
                'totalMenunggu'   => $data->where('status', 'Menunggu')->count(),
                'totalPendapatan' => $data->where('status', 'Selesai')->sum('biaya'),
            ];
        });
    }

    // ── Helper: ambil tahun awal (dengan cache) ───────────────────────────
    private function getTahunAwal(): int
    {
        return Cache::remember('laporan_tahun_awal', now()->addMinutes(self::CACHE_TTL), function () {
            return DB::table('servis')
                ->selectRaw('YEAR(MIN(created_at)) as tahun_awal')
                ->value('tahun_awal') ?? now()->year;
        });
    }

    // ── index ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        $cacheKey    = "laporan_servis_{$bulan}_{$tahun}";
        $fromCache   = Cache::has($cacheKey); // cek apakah data dari cache
        $result      = $this->getDataServis($bulan, $tahun);

        $data            = $result['data'];
        $totalServis     = $result['totalServis'];
        $totalSelesai    = $result['totalSelesai'];
        $totalProses     = $result['totalProses'];
        $totalMenunggu   = $result['totalMenunggu'];
        $totalPendapatan = $result['totalPendapatan'];

        $tahunAwal = $this->getTahunAwal();
        $listTahun = range($tahunAwal, now()->year);
        $namaBulan = $this->namaBulan($bulan);

        return view('laporan.index', compact(
            'data', 'bulan', 'tahun', 'namaBulan',
            'totalServis', 'totalSelesai', 'totalProses', 'totalMenunggu',
            'totalPendapatan', 'listTahun', 'fromCache'
        ));
    }

    // ── exportExcel ───────────────────────────────────────────────────────
    // Export Excel tidak di-cache karena file download langsung
    public function exportExcel(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        $namaBulan = $this->namaBulan($bulan);
        $filename  = "laporan-servis-{$namaBulan}-{$tahun}.xlsx";

        return Excel::download(new LaporanExport($bulan, $tahun), $filename);
    }

    // ── exportPdf ─────────────────────────────────────────────────────────
    public function exportPdf(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        $result          = $this->getDataServis($bulan, $tahun);
        $data            = $result['data'];
        $totalServis     = $result['totalServis'];
        $totalSelesai    = $result['totalSelesai'];
        $totalProses     = $result['totalProses'];
        $totalMenunggu   = $result['totalMenunggu'];
        $totalPendapatan = $result['totalPendapatan'];
        $namaBulan       = $this->namaBulan($bulan);

        $pdf = Pdf::loadView('laporan.pdf', compact(
            'data', 'bulan', 'tahun', 'namaBulan',
            'totalServis', 'totalSelesai', 'totalProses', 'totalMenunggu',
            'totalPendapatan'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("laporan-servis-{$namaBulan}-{$tahun}.pdf");
    }

    // ── cetak ─────────────────────────────────────────────────────────────
    public function cetak(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        $result          = $this->getDataServis($bulan, $tahun);
        $data            = $result['data'];
        $totalServis     = $result['totalServis'];
        $totalSelesai    = $result['totalSelesai'];
        $totalProses     = $result['totalProses'];
        $totalMenunggu   = $result['totalMenunggu'];
        $totalPendapatan = $result['totalPendapatan'];
        $namaBulan       = $this->namaBulan($bulan);

        return view('laporan.cetak', compact(
            'data', 'bulan', 'tahun', 'namaBulan',
            'totalServis', 'totalSelesai', 'totalProses', 'totalMenunggu',
            'totalPendapatan'
        ));
    }

    // ── clearCache (manual flush via route) ───────────────────────────────
    public function clearCache(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        Cache::forget("laporan_servis_{$bulan}_{$tahun}");
        Cache::forget('laporan_tahun_awal');

        return redirect()
            ->route('laporan.index', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('cache_cleared', true);
    }
}