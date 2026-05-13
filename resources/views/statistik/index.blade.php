<x-app-layout>
    <x-slot name="header">
        Statistik
    </x-slot>

    @php
        $namaBulanPanjang = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
        ];
    @endphp

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    {{-- ── FILTER TAHUN ── --}}
    <form method="GET" action="{{ route('statistik.index') }}" id="filterForm">
        <div style="background:#fff;border-radius:14px;padding:16px 22px;box-shadow:0 4px 24px rgba(26,31,54,0.08);margin-bottom:22px;display:flex;align-items:center;gap:14px;">
            <div style="font-size:13px;font-weight:600;color:#1a1f36;">Filter Tahun:</div>
            <select name="tahun" onchange="document.getElementById('filterForm').submit()"
                    style="padding:8px 14px;border-radius:8px;border:1.5px solid #e8eaf0;font-size:13px;color:#1a1f36;font-family:inherit;background:#fff;cursor:pointer;">
                @foreach($listTahun as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
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
                <div style="font-size:14px;font-weight:700;color:#1a1f36;">📊 Jumlah Servis per Bulan</div>
                <div style="font-size:12px;color:#8a93b2;margin-top:1px;">Tren servis masuk sepanjang tahun {{ $tahun }}</div>
            </div>
            <div style="padding:20px 22px;">
                <canvas id="chartServis" height="110"></canvas>
            </div>
        </div>

        {{-- Grafik Distribusi Status --}}
        <div style="background:#fff;border-radius:14px;box-shadow:0 4px 24px rgba(26,31,54,0.08);overflow:hidden;">
            <div style="padding:18px 22px 14px;border-bottom:1px solid #f0f2f7;">
                <div style="font-size:14px;font-weight:700;color:#1a1f36;">🥧 Distribusi Status Servis</div>
                <div style="font-size:12px;color:#8a93b2;margin-top:1px;">Perbandingan status tahun {{ $tahun }}</div>
            </div>
            <div style="padding:20px 22px;display:flex;align-items:center;justify-content:center;">
                <canvas id="chartStatus" height="200" style="max-width:260px;"></canvas>
            </div>
            {{-- Legend --}}
            <div style="padding:0 22px 18px;display:flex;gap:16px;justify-content:center;">
                <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#1a1f36;">
                    <div style="width:12px;height:12px;border-radius:3px;background:#f59f00;"></div> Menunggu ({{ $statusData[0] }})
                </div>
                <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#1a1f36;">
                    <div style="width:12px;height:12px;border-radius:3px;background:#9c36b5;"></div> Proses ({{ $statusData[1] }})
                </div>
                <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#1a1f36;">
                    <div style="width:12px;height:12px;border-radius:3px;background:#2f9e44;"></div> Selesai ({{ $statusData[2] }})
                </div>
            </div>
        </div>

    </div>

    {{-- ── ROW 2: Bar Chart Pendapatan ── --}}
    <div style="background:#fff;border-radius:14px;box-shadow:0 4px 24px rgba(26,31,54,0.08);overflow:hidden;">
        <div style="padding:18px 22px 14px;border-bottom:1px solid #f0f2f7;">
            <div style="font-size:14px;font-weight:700;color:#1a1f36;">💰 Pendapatan per Bulan</div>
            <div style="font-size:12px;color:#8a93b2;margin-top:1px;">Total pendapatan dari servis selesai — tahun {{ $tahun }}</div>
        </div>
        <div style="padding:20px 22px;">
            <canvas id="chartPendapatan" height="90"></canvas>
        </div>
    </div>

    {{-- ── CHART.JS SCRIPTS ── --}}
    <script>
        const labels = @json($labels);
        const dataServis = @json($dataServis);
        const dataPendapatan = @json($dataPendapatan);
        const statusLabels = @json($statusLabels);
        const statusData = @json($statusData);

        // ── 1. Line Chart: Jumlah Servis per Bulan ──────────────────────
        new Chart(document.getElementById('chartServis'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Servis',
                    data: dataServis,
                    borderColor: '#3b5bdb',
                    backgroundColor: 'rgba(59,91,219,0.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#3b5bdb',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.parsed.y} servis`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#8a93b2', font: { size: 11 } },
                        grid: { color: '#f0f2f7' }
                    },
                    x: {
                        ticks: { color: '#8a93b2', font: { size: 11 } },
                        grid: { display: false }
                    }
                }
            }
        });

        // ── 2. Donut Chart: Distribusi Status ────────────────────────────
        new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: ['#f59f00', '#9c36b5', '#2f9e44'],
                    borderColor: ['#fff', '#fff', '#fff'],
                    borderWidth: 3,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed} unit`
                        }
                    }
                }
            }
        });

        // ── 3. Bar Chart: Pendapatan per Bulan ───────────────────────────
        new Chart(document.getElementById('chartPendapatan'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan',
                    data: dataPendapatan,
                    backgroundColor: 'rgba(47,158,68,0.15)',
                    borderColor: '#2f9e44',
                    borderWidth: 2,
                    borderRadius: 6,
                    hoverBackgroundColor: 'rgba(47,158,68,0.3)',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` Rp ${ctx.parsed.y.toLocaleString('id-ID')}`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#8a93b2',
                            font: { size: 11 },
                            callback: val => 'Rp ' + val.toLocaleString('id-ID')
                        },
                        grid: { color: '#f0f2f7' }
                    },
                    x: {
                        ticks: { color: '#8a93b2', font: { size: 11 } },
                        grid: { display: false }
                    }
                }
            }
        });
    </script>

</x-app-layout>