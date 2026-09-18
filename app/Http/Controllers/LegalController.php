<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Halaman kebijakan privasi dan syarat layanan.
 *
 * Sistem ini menyimpan data pribadi pelanggan (nama, alamat, nomor
 * WhatsApp, bahkan kredensial perangkat), jadi keterangan tentang data apa
 * yang dikumpulkan dan bagaimana diperlakukan memang wajib tersedia.
 */
class LegalController extends Controller
{
    public function privasi(): View
    {
        return view('legal.privasi');
    }

    public function syarat(): View
    {
        return view('legal.syarat');
    }
}
