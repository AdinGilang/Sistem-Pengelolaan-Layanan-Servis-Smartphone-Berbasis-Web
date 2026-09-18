<?php

namespace App\Modules\Servis\Services;

use App\Modules\Servis\Models\Servis;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Lapisan service modul Servis.
 *
 * Semua query dan aturan penulisan data dikumpulkan di sini supaya
 * controller tinggal mengatur alur HTTP dan view tidak lagi menembak
 * database sendiri seperti pada versi sebelumnya.
 */
class ServisService
{
    public function paginate(?string $search, ?string $status, int $perPage = 10): LengthAwarePaginator
    {
        return Servis::query()
            ->search($search)
            ->status($status)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Ringkasan jumlah per status dalam SATU query agregat.
     *
     * Versi lama memanggil count() empat kali dari dalam Blade, jadi setiap
     * membuka dashboard menembak database berulang kali.
     *
     * @return array{total:int, menunggu:int, proses:int, selesai:int}
     */
    public function ringkasanStatus(): array
    {
        $perStatus = Servis::query()
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $menunggu = (int) ($perStatus[Servis::STATUS_MENUNGGU] ?? 0);
        $proses   = (int) ($perStatus[Servis::STATUS_PROSES] ?? 0);
        $selesai  = (int) ($perStatus[Servis::STATUS_SELESAI] ?? 0);

        return [
            'total'    => (int) $perStatus->sum(),
            'menunggu' => $menunggu,
            'proses'   => $proses,
            'selesai'  => $selesai,
        ];
    }

    public function terbaru(int $limit = 5): Collection
    {
        return Servis::query()
            ->latest()
            ->take($limit)
            ->get(['id', 'kode_unik', 'pelanggan', 'kerusakan', 'status', 'created_at']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Servis
    {
        $servis = new Servis($this->normalize($data));
        $servis->kode_unik = Servis::generateKodeUnik();
        $servis->save();

        return $servis;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Servis $servis, array $data): Servis
    {
        $data = $this->normalize($data);

        // PIN kosong berarti "biarkan PIN lama", bukan "hapus PIN".
        if (blank($data['kata_sandi'] ?? null)) {
            unset($data['kata_sandi']);
        }

        $servis->update($data);

        return $servis;
    }

    /**
     * kelengkapan dibiarkan tetap array: model sudah punya cast "array"
     * sehingga Eloquent yang meng-encode ke JSON. Versi sebelumnya
     * memanggil json_encode manual lalu cast meng-encode sekali lagi,
     * membuat nilai tersimpan ganda dan gagal dibaca kembali.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalize(array $data): array
    {
        $data['kelengkapan'] = array_values(array_filter(
            (array) ($data['kelengkapan'] ?? []),
            static fn ($item) => filled($item),
        )) ?: null;

        return $data;
    }
}
