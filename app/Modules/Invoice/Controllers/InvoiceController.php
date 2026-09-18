<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
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

        // Lebar 164.41pt menyesuaikan kertas thermal 58mm yang dipakai di toko.
        return Pdf::loadView('invoice::pdf', $this->payload($servis))
            ->setPaper([0, 0, 164.41, 800], 'portrait')
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
