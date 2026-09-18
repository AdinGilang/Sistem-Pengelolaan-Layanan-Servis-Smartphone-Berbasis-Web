<?php

namespace App\Modules\Laporan\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Laporan\Exports\LaporanExport;
use App\Modules\Laporan\Services\LaporanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class LaporanController extends Controller
{
    public function __construct(private readonly LaporanService $laporan)
    {
    }

    public function index(Request $request): View
    {
        [$bulan, $tahun] = $this->periode($request);

        return view('laporan::index', $this->payload($bulan, $tahun) + [
            'listTahun' => $this->laporan->daftarTahun(),
        ]);
    }

    public function exportExcel(Request $request): Response
    {
        [$bulan, $tahun] = $this->periode($request);

        return Excel::download(
            new LaporanExport($this->laporan, $bulan, $tahun),
            $this->namaFile($bulan, $tahun, 'xlsx'),
        );
    }

    public function exportPdf(Request $request): Response
    {
        [$bulan, $tahun] = $this->periode($request);

        return Pdf::loadView('laporan::pdf', $this->payload($bulan, $tahun))
            ->setPaper('a4', 'landscape')
            ->download($this->namaFile($bulan, $tahun, 'pdf'));
    }

    public function cetak(Request $request): View
    {
        [$bulan, $tahun] = $this->periode($request);

        return view('laporan::cetak', $this->payload($bulan, $tahun));
    }

    /**
     * Membuang cache laporan periode berjalan.
     *
     * Pada versi sebelumnya method ini ada tetapi tidak pernah didaftarkan
     * ke route mana pun, jadi tombol "segarkan data" tidak punya tujuan.
     */
    public function clearCache(Request $request): RedirectResponse
    {
        [$bulan, $tahun] = $this->periode($request);

        $this->laporan->lupakanCache($bulan, $tahun);

        return redirect()
            ->route('laporan.index', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Cache laporan berhasil disegarkan.');
    }

    /**
     * @return array{0:int, 1:int}
     */
    private function periode(Request $request): array
    {
        $validated = $request->validate([
            'bulan' => ['nullable', 'integer', 'between:1,12'],
            'tahun' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        return $this->laporan->normalisasiPeriode(
            $validated['bulan'] ?? null,
            $validated['tahun'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(int $bulan, int $tahun): array
    {
        return $this->laporan->ringkasan($bulan, $tahun) + [
            'bulan'     => $bulan,
            'tahun'     => $tahun,
            'namaBulan' => $this->laporan->namaBulan($bulan),
        ];
    }

    private function namaFile(int $bulan, int $tahun, string $ekstensi): string
    {
        return sprintf('laporan-servis-%s-%d.%s', strtolower($this->laporan->namaBulan($bulan)), $tahun, $ekstensi);
    }
}
