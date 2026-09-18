<x-app-layout>
    <x-slot name="header">
        Statistik
    </x-slot>

    {{-- ── FILTER TAHUN ── --}}
    <form method="GET" action="{{ route('statistik.index') }}" id="filterForm">
        <div style="background:#fff;border-radius:14px;padding:16px 22px;box-shadow:0 4px 24px rgba(26,31,54,0.08);margin-bottom:22px;display:flex;align-items:center;gap:14px;">
            <label for="filter-tahun" style="font-size:13px;font-weight:600;color:#1a1f36;">Filter Tahun</label>
            <select name="tahun" id="filter-tahun"
                    style="padding:8px 14px;border-radius:8px;border:1.5px solid #e8eaf0;font-size:13px;color:#1a1f36;font-family:inherit;background:#fff;cursor:pointer;">
                @foreach($listTahun as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>

            {{-- Pengiriman form dibuat eksplisit; sebelumnya halaman langsung
                 dimuat ulang setiap kali nilai dropdown berubah, yang membuat
                 pemilihan dengan keyboard mustahil dilakukan. --}}
            <button type="submit"
                    style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;background:#1a2035;color:#fff;border:none;cursor:pointer;font-family:inherit;">
                Terapkan
            </button>
            <div style="font-size:12px;color:#8a93b2;">Menampilkan data tahun <strong style="color:#3b5bdb;">{{ $tahun }}</strong></div>
        </div>
    </form>

    {{-- ── SUMMARY CARDS ── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:22px;">

        <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:0 4px 24px rgba(26,31,54,0.08);position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:#3b5bdb;border-radius:14px 14px 0 0;"></div>
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(59,91,219,0.1);color:#3b5bdb;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div style="font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Total Servis {{ $tahun }}</div>
            <div style="font-size:32px;font-weight:700;color:#1a1f36;line-height:1.1;margin-top:4px;letter-spacing:-1px;">{{ $totalTahunIni }}</div>
            <div style="font-size:12px;color:#8a93b2;margin-top:4px;">Sepanjang tahun</div>
        </div>

        <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:0 4px 24px rgba(26,31,54,0.08);position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:#2f9e44;border-radius:14px 14px 0 0;"></div>
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(47,158,68,0.1);color:#2f9e44;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <div style="font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Total Pendapatan</div>
            <div style="font-size:20px;font-weight:700;color:#1a1f36;line-height:1.2;margin-top:4px;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            <div style="font-size:12px;color:#8a93b2;margin-top:4px;">Dari servis selesai</div>
        </div>

        <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:0 4px 24px rgba(26,31,54,0.08);position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:#2f9e44;border-radius:14px 14px 0 0;"></div>
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(47,158,68,0.1);color:#2f9e44;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div style="font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Servis Selesai</div>
            <div style="font-size:32px;font-weight:700;color:#1a1f36;line-height:1.1;margin-top:4px;letter-spacing:-1px;">{{ $totalSelesai }}</div>
            <div style="font-size:12px;color:#8a93b2;margin-top:4px;">Unit terselesaikan</div>
        </div>

        <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:0 4px 24px rgba(26,31,54,0.08);position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:#f59f00;border-radius:14px 14px 0 0;"></div>
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(245,159,0,0.1);color:#f59f00;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </div>
            <div style="font-size:11px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Rata-rata / Bulan</div>
            <div style="font-size:32px;font-weight:700;color:#1a1f36;line-height:1.1;margin-top:4px;letter-spacing:-1px;">{{ $rataPerBulan }}</div>
            <div style="font-size:12px;color:#8a93b2;margin-top:4px;">Unit per bulan</div>
        </div>

    </div>

    {{-- ── ROW 1: Line Chart + Pie Chart ── --}}
    <div style="display:grid;grid-template-columns:1.6fr 1fr;gap:18px;margin-bottom:18px;">

        {{-- Grafik Jumlah Servis per Bulan --}}
        <div style="background:#fff;border-radius:14px;box-shadow:0 4px 24px rgba(26,31,54,0.08);overflow:hidden;">
            <div style="padding:18px 22px 14px;border-bottom:1px solid #f0f2f7;">
                <div style="font-size:14px;font-weight:700;color:#1a1f36;">Jumlah Servis per Bulan</div>
                <div style="font-size:12px;color:#8a93b2;margin-top:1px;">Tren servis masuk sepanjang tahun {{ $tahun }}</div>
            </div>
            <div style="padding:20px 22px;">
                <canvas id="chartServis" height="110"></canvas>
            </div>
        </div>

        {{-- Grafik Distribusi Status --}}
        <div style="background:#fff;border-radius:14px;box-shadow:0 4px 24px rgba(26,31,54,0.08);overflow:hidden;">
            <div style="padding:18px 22px 14px;border-bottom:1px solid #f0f2f7;">
                <div style="font-size:14px;font-weight:700;color:#1a1f36;">Distribusi Status Servis</div>
                <div style="font-size:12px;color:#8a93b2;margin-top:1px;">Perbandingan status tahun {{ $tahun }}</div>
            </div>
            <div style="padding:20px 22px;display:flex;align-items:center;justify-content:center;">
                <canvas id="chartStatus" height="200" style="max-width:260px;"></canvas>
            </div>
            {{-- Legend.

                 Warna swatch di sini sebelumnya di-hardcode terpisah dari
                 warna yang benar-benar dipakai Chart.js (resources/js/
                 statistik.js), sehingga tidak cocok satu sama lain — admin
                 melihat kotak oranye terang di legenda untuk irisan pie
                 yang sebenarnya berwarna oranye kecoklatan. Sekarang
                 keduanya memakai hex yang sama persis. --}}
            <div style="padding:0 22px 18px;display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#1a1f36;">
                    <div style="width:12px;height:12px;border-radius:3px;background:#b45309;"></div> Menunggu ({{ $statusData[0] }})
                </div>
                <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#1a1f36;">
                    <div style="width:12px;height:12px;border-radius:3px;background:#6d28d9;"></div> Proses ({{ $statusData[1] }})
                </div>
                <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#1a1f36;">
                    <div style="width:12px;height:12px;border-radius:3px;background:#15803d;"></div> Selesai ({{ $statusData[2] }})
                </div>
            </div>
        </div>

    </div>

    {{-- ── ROW 2: Bar Chart Pendapatan ── --}}
    <div style="background:#fff;border-radius:14px;box-shadow:0 4px 24px rgba(26,31,54,0.08);overflow:hidden;">
        <div style="padding:18px 22px 14px;border-bottom:1px solid #f0f2f7;">
            <div style="font-size:14px;font-weight:700;color:#1a1f36;">Pendapatan per Bulan</div>
            <div style="font-size:12px;color:#8a93b2;margin-top:1px;">Total pendapatan dari servis selesai — tahun {{ $tahun }}</div>
        </div>
        <div style="padding:20px 22px;">
            <canvas id="chartPendapatan" height="90"></canvas>
        </div>
    </div>


    {{-- ── DATA GRAFIK ──
         Angka dikirim ke JavaScript lewat elemen bertipe application/json.
         Isinya diserialisasi @json sehingga karakter khusus aman, dan skrip
         grafiknya sendiri berupa berkas terpisah yang dibundel Vite, bukan
         skrip inline maupun unduhan dari CDN pihak ketiga. --}}
    @php
        $dataGrafik = [
            'labels'         => $labels,
            'dataServis'     => $dataServis,
            'dataPendapatan' => $dataPendapatan,
            'statusLabels'   => $statusLabels,
            'statusData'     => $statusData,
        ];
    @endphp
    <script type="application/json" id="statistik-data">@json($dataGrafik)</script>

    @push('scripts')
        @vite('resources/js/statistik.js')
    @endpush
</x-app-layout>
