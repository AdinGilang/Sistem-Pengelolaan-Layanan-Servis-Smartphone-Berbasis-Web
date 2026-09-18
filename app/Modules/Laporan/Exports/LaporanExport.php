<?php

namespace App\Modules\Laporan\Exports;

use App\Modules\Laporan\Services\LaporanService;
use App\Modules\Servis\Models\Servis;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    /**
     * Penomoran baris.
     *
     * Sebelumnya memakai "static $no" di dalam method map(). Variabel static
     * itu melekat pada method, bukan pada instance, sehingga nomor urut tidak
     * pernah kembali ke 1 bila export dipanggil dua kali dalam satu proses
     * (misalnya di queue worker atau di dalam test).
     */
    private int $nomor = 0;

    public function __construct(
        private readonly LaporanService $laporan,
        private readonly int $bulan,
        private readonly int $tahun,
    ) {
    }

    public function collection(): Collection
    {
        return $this->laporan->query($this->bulan, $this->tahun)->get();
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'No',
            'Kode Servis',
            'Tanggal',
            'Pelanggan',
            'Perangkat',
            'Kerusakan',
            'Teknisi',
            'Status',
            'Biaya (Rp)',
        ];
    }

    /**
     * @param  Servis  $row
     * @return list<mixed>
     */
    public function map($row): array
    {
        $this->nomor++;

        return [
            $this->nomor,
            $row->kode_unik,
            $row->created_at?->format('d/m/Y'),
            $row->pelanggan,
            trim(($row->merk_hp ?? '') . ' ' . ($row->tipe_hp ?? '')) ?: '-',
            $row->kerusakan,
            $row->teknisi ?: '-',
            $row->status,
            (int) ($row->biaya ?? 0),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B5BDB'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan ' . $this->laporan->namaBulan($this->bulan) . ' ' . $this->tahun;
    }
}
