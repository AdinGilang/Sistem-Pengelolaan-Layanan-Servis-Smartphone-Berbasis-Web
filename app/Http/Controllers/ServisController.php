<?php

namespace App\Http\Controllers;

use App\Models\Servis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ServisController extends Controller
{
    public function index(Request $request)
    {
        $servis = Servis::query()
            ->when($request->search, fn($q, $s) => $q->where('pelanggan', 'like', "%{$s}%"))
            ->when($request->status,  fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('servis.index', compact('servis'));
    }

    public function create()
    {
        $teknisiList = User::where('role', 'admin')->pluck('name', 'id');
        return view('servis.create', compact('teknisiList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal'           => 'required|date',
            'estimasi_selesai'  => 'nullable|date|after_or_equal:tanggal',
            'estimasi_hari'     => 'nullable|integer|min:0|max:365',
            'estimasi_jam'      => 'nullable|integer|min:0|max:23',
            'pelanggan'         => 'required|string|max:255',
            'alamat'            => 'nullable|string|max:255',
            'no_wa'             => 'nullable|string|max:20',
            'merk_hp'           => 'nullable|string|max:100',
            'tipe_hp'           => 'nullable|string|max:100',
            'kelengkapan'       => 'nullable|array',
            'teknisi'           => 'nullable|string|max:100',
            'pola_kunci'        => 'nullable|string|max:50',
            'kata_sandi'        => 'nullable|digits_between:4,6',
            'kerusakan'         => 'required|string',
            'status'            => 'required|string',
            'biaya'             => 'nullable|numeric',
        ]);

        if (!empty($validated['kelengkapan'])) {
            $validated['kelengkapan'] = json_encode($validated['kelengkapan']);
        }

        // Enkripsi PIN jika diisi
        if (!empty($validated['kata_sandi'])) {
            $validated['kata_sandi'] = Hash::make($validated['kata_sandi']);
        } else {
            $validated['kata_sandi'] = null;
        }

        do {
            $kode = 'SRV-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (Servis::where('kode_unik', $kode)->exists());

        $validated['kode_unik'] = $kode;

        Servis::create($validated);

        return redirect()->route('servis.index')->with('success', 'Data servis berhasil ditambahkan.');
    }

    public function show(Servis $servis)
    {
        return view('servis.show', compact('servis'));
    }

    public function edit(Servis $servis)
    {
        $teknisiList = User::where('role', 'admin')->pluck('name', 'id');
        return view('servis.edit', compact('servis', 'teknisiList'));
    }

    public function update(Request $request, Servis $servis)
    {
        $validated = $request->validate([
            'tanggal'           => 'required|date',
            'estimasi_selesai'  => 'nullable|date|after_or_equal:tanggal',
            'estimasi_hari'     => 'nullable|integer|min:0|max:365',
            'estimasi_jam'      => 'nullable|integer|min:0|max:23',
            'pelanggan'         => 'required|string|max:255',
            'alamat'            => 'nullable|string|max:255',
            'no_wa'             => 'nullable|string|max:20',
            'merk_hp'           => 'nullable|string|max:100',
            'tipe_hp'           => 'nullable|string|max:100',
            'kelengkapan'       => 'nullable|array',
            'teknisi'           => 'nullable|string|max:100',
            'pola_kunci'        => 'nullable|string|max:50',
            'kata_sandi'        => 'nullable|digits_between:4,6',
            'kerusakan'         => 'required|string',
            'status'            => 'required|string',
            'biaya'             => 'nullable|numeric',
        ]);

        if (!empty($validated['kelengkapan'])) {
            $validated['kelengkapan'] = json_encode($validated['kelengkapan']);
        } else {
            $validated['kelengkapan'] = null;
        }

        // Hanya update PIN jika field diisi
        // Jika dikosongkan → pertahankan PIN lama
        if (!empty($validated['kata_sandi'])) {
            $validated['kata_sandi'] = Hash::make($validated['kata_sandi']);
        } else {
            // Jangan overwrite PIN lama jika field kosong
            unset($validated['kata_sandi']);
        }

        $servis->update($validated);

        return redirect()->route('servis.index')->with('success', 'Data servis berhasil diperbarui.');
    }

    public function cekForm()
    {
        return view('servis.cek');
    }

    public function cekStatus(Request $request)
    {
        $servis = null;
        if ($request->filled('kode')) {
            $servis = Servis::where('kode_unik', trim($request->kode))->first();
        }
        return view('servis.cek', compact('servis'));
    }

    /**
     * Verifikasi PIN — dipanggil via AJAX dari halaman show
     */
    public function pinVerify(Request $request, Servis $servis)
    {
        $pin = $request->input('pin');

        if (!$servis->kata_sandi) {
            return response()->json(['success' => false, 'message' => 'Tidak ada PIN']);
        }

        $valid = Hash::check((string) $pin, $servis->kata_sandi);

        if ($valid) {
            // Simpan di session agar tidak perlu verifikasi ulang selama sesi
            session(['pin_verified_' . $servis->id => true]);
        }

        return response()->json(['success' => $valid]);
    }

    public function pinShow(Request $request, Servis $servis)
    {
        // Cek session verifikasi
        if (!session('pin_verified_' . $servis->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json([
            'pin' => '(PIN terenkripsi — tidak dapat ditampilkan)',
            'info' => 'PIN disimpan menggunakan bcrypt hash'
        ]);
    }

    public function destroy($id)
    {
        $servis = Servis::find($id);
        if (!$servis) {
            return redirect()->route('servis.index')->with('error', 'Data tidak ditemukan.');
        }
        $servis->delete();
        return redirect()->route('servis.index')->with('success', 'Data servis berhasil dihapus.');
    }
}