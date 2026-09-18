<?php

namespace App\Modules\Setting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Setting\Models\Setting;
use App\Modules\Setting\Requests\UpdateSettingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    /** Pengaturan yang boleh diubah beserta nilai bawaannya. */
    private const DEFAULTS = [
        'footer_thanks'     => 'Terima kasih atas kepercayaan Anda.',
        'garansi_servis'    => 'Garansi servis 7 hari setelah pengambilan.',
        'batas_pengambilan' => 'Batas Pengambilan Maksimal 3 Bulan!',
    ];

    public function index(): View
    {
        $settings = [];

        foreach (self::DEFAULTS as $key => $default) {
            $settings[$key] = Setting::get($key, $default);
        }

        return view('setting::index', compact('settings'));
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        // Hanya key yang terdaftar di DEFAULTS yang diproses, sehingga
        // request tidak bisa menyisipkan pengaturan baru sembarangan.
        foreach (array_keys(self::DEFAULTS) as $key) {
            Setting::set($key, $request->validated($key));
        }

        return redirect()
            ->route('setting.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}
