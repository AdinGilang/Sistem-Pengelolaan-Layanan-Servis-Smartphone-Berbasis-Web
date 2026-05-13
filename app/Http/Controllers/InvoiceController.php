<?php

namespace App\Http\Controllers;

use App\Models\Servis;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function show(Servis $servis)
    {
        return view('invoice.show', compact('servis'));
    }

    public function pdf(Servis $servis)
    {
        $pdf = Pdf::loadView('invoice.pdf', compact('servis'))
            ->setPaper([0, 0, 164.41, 800], 'portrait');

        return $pdf->download('invoice-' . $servis->kode_unik . '.pdf');
    }

    public function cetak(Servis $servis)
    {
        return view('invoice.cetak', compact('servis'));
    }
}