<x-app-layout>
    <x-slot name="header">
        Laporan
    </x-slot>

    @php
        // $namaBulan datang dari LaporanController. Sebelumnya view menghitung
        // ulang sendiri dengan translatedFormat('F'), yang mengikuti APP_LOCALE
        // (bernilai "en"), sehingga judul laporan tampil "March" alih-alih
        // "Maret" meski seluruh antarmuka berbahasa Indonesia.
        $listBulan = [
            1  => 'Januari',  2  => 'Februari', 3  => 'Maret',
            4  => 'April',    5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',     8  => 'Agustus',   9  => 'September',
            10 => 'Oktober',  11 => 'November',  12 => 'Desember',
        ];
    @endphp

    @push('styles')
    <style>
        /* Efek sorot tombol ekspor dan baris tabel. Dulu ditulis sebagai
           atribut onmouseover/onmouseout yang menimpa style lewat JavaScript
           pada setiap gerakan kursor; sekarang cukup satu aturan CSS yang
           bekerja apa pun warna latar inline-nya. */
        .aksi-hover { transition: filter .18s ease, background-color .18s ease; }
        a.aksi-hover:hover { filter: brightness(.9); }
        tr.aksi-hover:hover { background: #fafbff; }
    </style>
    @endpush

    {{-- ── FILTER FORM ── --}}
    <form method="GET" action="{{ route('laporan.index') }}" id="filterForm">
        <div style="background:#fff;border-radius:14px;padding:18px 22px;box-shadow:0 4px 24px rgba(26,31,54,0.08);margin-bottom:22px;display:flex;align-items:flex-end;gap:14px;flex-wrap:wrap;">

            <div>
                <div style="font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Bulan</div>
                <select name="bulan"
                        style="padding:8px 12px;border-radius:8px;border:1.5px solid #e8eaf0;font-size:13px;color:#1a1f36;font-family:inherit;background:#fff;cursor:pointer;min-width:130px;">
                    @foreach($listBulan as $num => $nama)
                        <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <div style="font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Tahun</div>
                <select name="tahun"
                        style="padding:8px 12px;border-radius:8px;border:1.5px solid #e8eaf0;font-size:13px;color:#1a1f36;font-family:inherit;background:#fff;cursor:pointer;min-width:100px;">
                    @foreach($listTahun as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Dulu kedua dropdown di atas langsung mengirim form begitu
                 nilainya berubah. Bagi pengguna keyboard, menekan panah bawah
                 sekali saja sudah memuat ulang halaman sehingga pilihan
                 berikutnya tidak pernah terjangkau. Pengiriman kini eksplisit
                 lewat tombol ini. --}}
            <button type="submit"
                    class="aksi-hover"
                    style="align-self:flex-end;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;background:#1a2035;color:#fff;border:none;cursor:pointer;font-family:inherit;">
                Terapkan
            </button>

            <div style="flex:1;"></div>

            {{-- Export buttons --}}
            <a href="{{ route('laporan.excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
               style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:10px;font-size:13px;font-weight:600;background:#2f9e44;color:#fff;text-decoration:none;box-shadow:0 4px 12px rgba(47,158,68,0.3);transition:all .18s;"
               class="aksi-hover"
               >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <polyline points="8 13 12 17 16 13"/>
                    <line x1="12" y1="17" x2="12" y2="10"/>
                </svg>
                Export Excel
            </a>

            <a href="{{ route('laporan.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
               style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:10px;font-size:13px;font-weight:600;background:#e03131;color:#fff;text-decoration:none;box-shadow:0 4px 12px rgba(224,49,49,0.3);transition:all .18s;"
               class="aksi-hover"
               >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <polyline points="8 13 12 17 16 13"/>
                    <line x1="12" y1="17" x2="12" y2="10"/>
                </svg>
                Export PDF
            </a>

            <a href="{{ route('laporan.cetak', ['bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank"
               style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:10px;font-size:13px;font-weight:600;background:#3b5bdb;color:#fff;text-decoration:none;box-shadow:0 4px 12px rgba(59,91,219,0.3);transition:all .18s;"
               class="aksi-hover"
               >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                Cetak
            </a>

            {{-- Ringkasan laporan di-cache satu jam. Tombol ini membuangnya
                 bila data servis baru saja diperbaiki langsung di database.
                 Aksinya memakai POST karena mengubah keadaan server, sehingga
                 ikut terlindungi token CSRF. --}}
            <button type="submit"
                    form="form-segarkan"
                    class="aksi-hover"
                    style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:10px;font-size:13px;font-weight:600;background:#f1f3f9;color:#4a5578;border:1px solid #e4e8f2;cursor:pointer;font-family:inherit;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M21 3v5h-5"/>
                    <path d="M21 12a9 9 0 0 1-15 6.7L3 16"/><path d="M3 21v-5h5"/>
                </svg>
                Segarkan
            </button>

        </div>
    </form>

    {{-- Form tersembunyi untuk tombol Segarkan di atas. Diletakkan di luar
         form filter agar tidak menghasilkan form bersarang. --}}
    <form method="POST" action="{{ route('laporan.cache.clear') }}" id="form-segarkan">
        @csrf
        <input type="hidden" name="bulan" value="{{ $bulan }}">
        <input type="hidden" name="tahun" value="{{ $tahun }}">
    </form>

    {{-- ── STAT CARDS ── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:22px;">

        {{-- Total Servis --}}
        <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:0 4px 24px rgba(26,31,54,0.08);position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:#3b5bdb;border-radius:14px 14px 0 0;"></div>
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(59,91,219,0.1);color:#3b5bdb;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
            </div>
            <div style="font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Total Servis</div>
            <div style="font-size:36px;font-weight:700;color:#1a1f36;line-height:1.1;margin-top:4px;letter-spacing:-1px;">{{ $totalServis }}</div>
            <div style="font-size:12px;color:#8a93b2;margin-top:6px;">{{ $namaBulan }} {{ $tahun }}</div>
        </div>

        {{-- Menunggu --}}
        <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:0 4px 24px rgba(26,31,54,0.08);position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:#f59f00;border-radius:14px 14px 0 0;"></div>
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(245,159,0,0.1);color:#f59f00;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div style="font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Menunggu</div>
            <div style="font-size:36px;font-weight:700;color:#1a1f36;line-height:1.1;margin-top:4px;letter-spacing:-1px;">{{ $totalMenunggu }}</div>
            <div style="font-size:12px;color:#8a93b2;margin-top:6px;">Belum ditangani</div>
        </div>

        {{-- Proses --}}
        <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:0 4px 24px rgba(26,31,54,0.08);position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:#9c36b5;border-radius:14px 14px 0 0;"></div>
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(156,54,181,0.1);color:#9c36b5;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
                </svg>
            </div>
            <div style="font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Proses</div>
            <div style="font-size:36px;font-weight:700;color:#1a1f36;line-height:1.1;margin-top:4px;letter-spacing:-1px;">{{ $totalProses }}</div>
            <div style="font-size:12px;color:#8a93b2;margin-top:6px;">Sedang dikerjakan</div>
        </div>

        {{-- Total Pendapatan --}}
        <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:0 4px 24px rgba(26,31,54,0.08);position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:#2f9e44;border-radius:14px 14px 0 0;"></div>
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(47,158,68,0.1);color:#2f9e44;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                </svg>
            </div>
            <div style="font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Total Pendapatan</div>
            <div style="font-size:22px;font-weight:700;color:#1a1f36;line-height:1.2;margin-top:4px;letter-spacing:-0.5px;">
                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            </div>
            <div style="font-size:12px;color:#8a93b2;margin-top:6px;">Dari servis selesai</div>
        </div>

    </div>

    {{-- ── TABEL DATA ── --}}
    <div style="background:#fff;border-radius:14px;box-shadow:0 4px 24px rgba(26,31,54,0.08);overflow:hidden;">
        <div style="padding:18px 22px 14px;border-bottom:1px solid #f0f2f7;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:14px;font-weight:700;color:#1a1f36;">Detail Servis — {{ $namaBulan }} {{ $tahun }}</div>
                <div style="font-size:12px;color:#8a93b2;margin-top:1px;">{{ $totalServis }} data ditemukan</div>
            </div>
        </div>

        @if($data->isEmpty())
            <div style="padding:48px 0;text-align:center;color:#8a93b2;">
                <div style="font-size:36px;margin-bottom:10px;"></div>
                <div style="font-size:14px;font-weight:600;">Tidak ada data servis</div>
                <div style="font-size:12px;margin-top:4px;">untuk bulan {{ $namaBulan }} {{ $tahun }}</div>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f7f8fc;">
                            <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">No</th>
                            <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Kode</th>
                            <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Pelanggan</th>
                            <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Kerusakan</th>
                            <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Status</th>
                            <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Biaya</th>
                            <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $i => $item)
                            @php
                                $statusStyle = match($item->status) {
                                    'Menunggu' => 'background:rgba(245,159,0,0.12);color:#d08700;',
                                    'Proses'   => 'background:rgba(156,54,181,0.12);color:#8a2be2;',
                                    'Selesai'  => 'background:rgba(47,158,68,0.12);color:#2f9e44;',
                                    default    => 'background:#f0f2f7;color:#8a93b2;',
                                };
                            @endphp
                            <tr style="border-bottom:1px solid #f4f5f9;transition:background .15s;"
                                class="aksi-hover"
                                >
                                <td style="padding:12px 16px;font-size:13px;color:#8a93b2;">{{ $i + 1 }}</td>
                                <td style="padding:12px 16px;">
                                    <span style="font-size:12px;font-weight:600;color:#3b5bdb;background:rgba(59,91,219,0.08);padding:3px 8px;border-radius:6px;">
                                        {{ $item->kode_unik }}
                                    </span>
                                </td>
                                <td style="padding:12px 16px;font-size:13px;font-weight:600;color:#1a1f36;">{{ $item->pelanggan }}</td>
                                <td style="padding:12px 16px;font-size:13px;color:#4a5568;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $item->kerusakan }}</td>
                                <td style="padding:12px 16px;">
                                    <span style="font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;{{ $statusStyle }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td style="padding:12px 16px;font-size:13px;font-weight:600;color:#1a1f36;text-align:right;">
                                    @if($item->biaya)
                                        Rp {{ number_format($item->biaya, 0, ',', '.') }}
                                    @else
                                        <span style="color:#8a93b2;">—</span>
                                    @endif
                                </td>
                                <td style="padding:12px 16px;font-size:12px;color:#8a93b2;">{{ $item->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#f7f8fc;border-top:2px solid #e8eaf0;">
                            <td colspan="5" style="padding:12px 16px;font-size:13px;font-weight:700;color:#1a1f36;">Total Pendapatan (Selesai)</td>
                            <td style="padding:12px 16px;font-size:13px;font-weight:700;color:#2f9e44;text-align:right;">
                                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

</x-app-layout>
