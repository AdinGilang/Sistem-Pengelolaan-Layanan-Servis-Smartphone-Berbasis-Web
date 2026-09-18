<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Servis\Services\ServisService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly ServisService $servis)
    {
    }

    /**
     * Sebelumnya dashboard hanya berupa closure di routes/web.php yang
     * mengembalikan view, lalu view itu sendiri yang menjalankan enam query
     * Eloquent. Sekarang seluruh pengambilan data dilakukan di sini sehingga
     * template murni menampilkan data yang sudah siap.
     */
    public function index(): View
    {
        $ringkasan = $this->servis->ringkasanStatus();

        return view('dashboard::index', [
            'ringkasan'    => $ringkasan,
            'recentServis' => $this->servis->terbaru(),
            'persentase'   => $this->persentase($ringkasan),
        ]);
    }

    /**
     * @param  array{total:int, menunggu:int, proses:int, selesai:int}  $ringkasan
     * @return array{menunggu:int, proses:int, selesai:int}
     */
    private function persentase(array $ringkasan): array
    {
        $total = $ringkasan['total'];

        $hitung = static fn (int $jumlah): int => $total > 0 ? (int) round($jumlah / $total * 100) : 0;

        return [
            'menunggu' => $hitung($ringkasan['menunggu']),
            'proses'   => $hitung($ringkasan['proses']),
            'selesai'  => $hitung($ringkasan['selesai']),
        ];
    }
}
