<?php

namespace App\Modules\Statistik\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Laporan\Services\LaporanService;
use App\Modules\Statistik\Services\StatistikService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatistikController extends Controller
{
    public function __construct(
        private readonly StatistikService $statistik,
        private readonly LaporanService $laporan,
    ) {
    }

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'tahun' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        [, $tahun] = $this->laporan->normalisasiPeriode(null, $validated['tahun'] ?? null);

        return view('statistik::index', $this->statistik->untukTahun($tahun) + [
            'tahun'     => $tahun,
            'listTahun' => $this->laporan->daftarTahun(),
        ]);
    }
}
