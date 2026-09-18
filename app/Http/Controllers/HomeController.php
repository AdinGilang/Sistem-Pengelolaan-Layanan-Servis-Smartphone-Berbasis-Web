<?php

namespace App\Http\Controllers;

use App\Modules\Servis\Services\ServisService;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly ServisService $servis)
    {
    }

    /**
     * Halaman depan publik.
     *
     * Angka yang tampil di hero diambil dari data nyata, bukan lagi angka
     * yang ditulis tangan di dalam template. Hasilnya di-cache 5 menit agar
     * halaman publik tidak menembak database pada setiap kunjungan.
     */
    public function __invoke(): View
    {
        $ringkasan = Cache::remember(
            'beranda.ringkasan',
            now()->addMinutes(5),
            fn (): array => $this->servis->ringkasanStatus(),
        );

        return view('welcome', compact('ringkasan'));
    }
}
