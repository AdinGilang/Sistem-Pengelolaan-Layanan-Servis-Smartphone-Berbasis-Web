<?php

namespace App\Modules\Servis\Observers;

use App\Modules\Laporan\Services\LaporanService;
use App\Modules\Servis\Models\Servis;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Menjaga cache laporan tetap konsisten dengan data yang sebenarnya.
 */
class ServisObserver
{
    public function __construct(private readonly LaporanService $laporan)
    {
    }

    public function created(Servis $servis): void
    {
        $this->bersihkan($servis->created_at);
    }

    public function updated(Servis $servis): void
    {
        $this->bersihkan($servis->created_at);

        // Bila created_at dipindah ke bulan lain, cache bulan asal ikut dibuang
        // supaya angka laporan bulan lama tidak tertinggal basi.
        if ($servis->wasChanged('created_at') && $servis->getOriginal('created_at')) {
            $this->bersihkan(Carbon::parse($servis->getOriginal('created_at')));
        }
    }

    public function deleted(Servis $servis): void
    {
        $this->bersihkan($servis->created_at);
    }

    public function restored(Servis $servis): void
    {
        $this->bersihkan($servis->created_at);
    }

    private function bersihkan(?CarbonInterface $tanggal): void
    {
        $tanggal ??= Carbon::now();

        $this->laporan->lupakanCache($tanggal->month, $tanggal->year);
    }
}
