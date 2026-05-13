<?php

namespace App\Observers;

use App\Models\Servis;
use Illuminate\Support\Facades\Cache;

class ServisObserver
{
    /**
     * Hapus cache laporan untuk bulan & tahun yang relevan.
     * Dipanggil saat data servis dibuat, diupdate, atau dihapus.
     */
    private function forgetLaporanCache(Servis $servis): void
    {
        $bulan = $servis->created_at->month;
        $tahun = $servis->created_at->year;

        Cache::forget("laporan_servis_{$bulan}_{$tahun}");
        Cache::forget('laporan_tahun_awal');
    }

    public function created(Servis $servis): void
    {
        $this->forgetLaporanCache($servis);
    }

    public function updated(Servis $servis): void
    {
        $this->forgetLaporanCache($servis);

        // Jika created_at berubah (pindah bulan), hapus cache bulan lama juga
        if ($servis->wasChanged('created_at') && $servis->getOriginal('created_at')) {
            $originalDate = \Carbon\Carbon::parse($servis->getOriginal('created_at'));
            Cache::forget("laporan_servis_{$originalDate->month}_{$originalDate->year}");
        }
    }

    public function deleted(Servis $servis): void
    {
        $this->forgetLaporanCache($servis);
    }
}