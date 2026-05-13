<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'footer_thanks'    => Setting::get('footer_thanks',    'Terima kasih atas kepercayaan Anda.'),
            'garansi_servis'   => Setting::get('garansi_servis',   'Garansi servis 7 hari setelah pengambilan.'),
            'batas_pengambilan'=> Setting::get('batas_pengambilan','Batas Pengambilan Maksimal 3 Bulan!'),
        ];

        return view('setting.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'footer_thanks'     => 'required|string|max:255',
            'garansi_servis'    => 'required|string|max:255',
            'batas_pengambilan' => 'required|string|max:255',
        ]);

        Setting::set('footer_thanks',     $request->footer_thanks);
        Setting::set('garansi_servis',    $request->garansi_servis);
        Setting::set('batas_pengambilan', $request->batas_pengambilan);

        return redirect()->route('setting.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}