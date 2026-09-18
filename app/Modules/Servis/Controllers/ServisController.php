<?php

namespace App\Modules\Servis\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Servis\Models\Servis;
use App\Modules\Servis\Requests\StoreServisRequest;
use App\Modules\Servis\Requests\UpdateServisRequest;
use App\Modules\Servis\Services\ServisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class ServisController extends Controller
{
    public function __construct(private readonly ServisService $servis)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Servis::class);

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:20'],
        ]);

        return view('servis::index', [
            'servis'   => $this->servis->paginate($validated['search'] ?? null, $validated['status'] ?? null),
            'statuses' => Servis::STATUSES,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Servis::class);

        return view('servis::create', [
            'teknisiList' => $this->teknisiList(),
            'statuses'    => Servis::STATUSES,
        ]);
    }

    public function store(StoreServisRequest $request): RedirectResponse
    {
        $servis = $this->servis->create($request->validated());

        return redirect()
            ->route('servis.index')
            ->with('success', "Data servis {$servis->kode_unik} berhasil ditambahkan.");
    }

    public function show(Servis $servis): View
    {
        $this->authorize('view', $servis);

        return view('servis::show', compact('servis'));
    }

    public function edit(Servis $servis): View
    {
        $this->authorize('update', $servis);

        return view('servis::edit', [
            'servis'      => $servis,
            'teknisiList' => $this->teknisiList(),
            'statuses'    => Servis::STATUSES,
        ]);
    }

    public function update(UpdateServisRequest $request, Servis $servis): RedirectResponse
    {
        $this->servis->update($servis, $request->validated());

        return redirect()
            ->route('servis.index')
            ->with('success', "Data servis {$servis->kode_unik} berhasil diperbarui.");
    }

    public function destroy(Servis $servis): RedirectResponse
    {
        $this->authorize('delete', $servis);

        $kode = $servis->kode_unik;
        $servis->delete();

        return redirect()
            ->route('servis.index')
            ->with('success', "Data servis {$kode} berhasil dihapus.");
    }

    /**
     * Halaman publik pelacakan status.
     *
     * Hanya kolom yang memang perlu diketahui pelanggan yang diambil dari
     * database. Alamat, nomor WhatsApp, pola kunci, dan PIN tidak pernah
     * ikut terkirim ke halaman publik.
     */
    public function cekStatus(Request $request): View
    {
        $validated = $request->validate([
            'kode' => ['nullable', 'string', 'max:40'],
        ]);

        $kode = trim((string) ($validated['kode'] ?? ''));

        $servis = $kode === '' ? null : Servis::query()
            ->where('kode_unik', $kode)
            ->first([
                'kode_unik', 'pelanggan', 'merk_hp', 'tipe_hp',
                'kerusakan', 'teknisi', 'status', 'biaya', 'estimasi_selesai',
            ]);

        return view('servis::cek', [
            'servis' => $servis,
            'dicari' => $kode !== '',
        ]);
    }

    /**
     * Verifikasi PIN perangkat sebelum kredensial ditampilkan ke teknisi.
     *
     * Dibatasi 5 percobaan per menit per kombinasi user dan unit. Tanpa
     * pembatasan ini PIN 4-6 digit habis ditebak hanya dalam hitungan menit.
     */
    public function pinVerify(Request $request, Servis $servis): JsonResponse
    {
        $this->authorize('viewCredentials', $servis);

        $validated = $request->validate([
            'pin' => ['required', 'digits_between:4,6'],
        ]);

        $key = 'pin-verify:' . $request->user()->id . ':' . $servis->id;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan. Coba lagi dalam ' . RateLimiter::availableIn($key) . ' detik.',
            ], 429);
        }

        if (! $servis->hasPin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unit ini tidak memiliki PIN.',
            ], 404);
        }

        if (! Hash::check($validated['pin'], $servis->getAttributes()['kata_sandi'])) {
            RateLimiter::hit($key, 60);

            return response()->json([
                'success' => false,
                'message' => 'PIN salah.',
            ], 422);
        }

        RateLimiter::clear($key);
        $request->session()->put($this->pinSessionKey($servis), true);

        return response()->json(['success' => true]);
    }

    /**
     * PIN disimpan sebagai hash bcrypt satu arah sehingga secara teknis tidak
     * bisa dikembalikan ke bentuk aslinya. Endpoint ini hanya mengonfirmasi
     * bahwa PIN yang tadi dimasukkan teknisi memang cocok.
     */
    public function pinShow(Request $request, Servis $servis): JsonResponse
    {
        $this->authorize('viewCredentials', $servis);

        if (! $request->session()->get($this->pinSessionKey($servis))) {
            return response()->json(['message' => 'PIN belum diverifikasi.'], 403);
        }

        return response()->json([
            'verified' => true,
            'message'  => 'PIN cocok. Nilai aslinya tidak ditampilkan karena tersimpan sebagai hash satu arah.',
        ]);
    }

    /**
     * Kunci sesi diikat ke kode unik, bukan id berurutan, supaya status
     * verifikasi tidak bisa ditebak atau dipakai ulang antar unit.
     */
    private function pinSessionKey(Servis $servis): string
    {
        return 'pin_verified.' . $servis->kode_unik;
    }

    /**
     * @return Collection<int, string>
     */
    private function teknisiList(): Collection
    {
        return User::query()
            ->where('role', User::ROLE_ADMIN)
            ->orderBy('name')
            ->pluck('name', 'id');
    }
}
