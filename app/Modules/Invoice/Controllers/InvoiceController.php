<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Invoice\Services\InvoicePdfHeight;
use App\Modules\Servis\Models\Servis;
use App\Modules\Setting\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    public function show(Servis $servis): View
    {
        $this->authorize('view', $servis);

        return view('invoice::show', $this->payload($servis));
    }

    public function pdf(Servis $servis): Response
    {
        $this->authorize('view', $servis);

        $payload = $this->payload($servis);

        // Tinggi kertas dihitung dari isi sesungguhnya — lihat penjelasan
        // lengkap di InvoicePdfHeight. Sebelumnya angka 800 ini tetap untuk
        // setiap invoice; begitu isinya lebih panjang dari yang muat di
        // 800pt, sisanya terpotong begitu saja tanpa peringatan maupun
        // halaman kedua. Lebar 164.41pt menyesuaikan kertas thermal 58mm
        // yang dipakai di toko.
        $tinggi = InvoicePdfHeight::untuk(
            $servis,
            $payload['footerThanks'],
            $payload['footerGaransi'],
            $payload['footerBatas'],
        );

        return Pdf::loadView('invoice::pdf', $payload)
            ->setPaper([0, 0, InvoicePdfHeight::LEBAR_PT, $tinggi], 'portrait')
            ->download('invoice-' . $servis->kode_unik . '.pdf');
    }

    public function cetak(Servis $servis): View
    {
        $this->authorize('view', $servis);

        return view('invoice::cetak', $this->payload($servis));
    }

    /**
     * Teks footer nota diambil di controller, bukan dari dalam Blade.
     *
     * Versi sebelumnya memanggil Setting::get() langsung di dalam view,
     * sehingga template ikut menembak database dan sulit diuji.
     *
     * @return array<string, mixed>
     */
    private function payload(Servis $servis): array
    {
        return [
            'servis'        => $servis,
            'footerThanks'  => Setting::get('footer_thanks', 'Terima kasih atas kepercayaan Anda.'),
            'footerGaransi' => Setting::get('garansi_servis', 'Garansi servis 7 hari setelah pengambilan.'),
            'footerBatas'   => Setting::get('batas_pengambilan', 'Batas Pengambilan Maksimal 3 Bulan!'),
        ];
    }
}
