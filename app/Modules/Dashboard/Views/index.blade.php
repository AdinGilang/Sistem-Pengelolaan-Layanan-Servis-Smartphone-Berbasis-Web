@php
    $kartu = [
        [
            'label' => 'Total Servis',
            'nilai' => $ringkasan['total'],
            'ket'   => 'Seluruh unit tercatat',
            'warna' => 'blue',
            'ikon'  => 'M22 12h-4l-3 9L9 3l-3 9H2',
        ],
        [
            'label' => 'Menunggu',
            'nilai' => $ringkasan['menunggu'],
            'ket'   => 'Belum ditangani',
            'warna' => 'amber',
            'ikon'  => 'M12 6v6l4 2',
        ],
        [
            'label' => 'Proses',
            'nilai' => $ringkasan['proses'],
            'ket'   => 'Sedang dikerjakan',
            'warna' => 'violet',
            'ikon'  => 'M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z',
        ],
        [
            'label' => 'Selesai',
            'nilai' => $ringkasan['selesai'],
            'ket'   => 'Siap diambil',
            'warna' => 'green',
            'ikon'  => 'm5 12 5 5L20 7',
        ],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    @push('styles')
    <style>
        /*
         * Efek sorot kartu dulu ditulis sebagai atribut onmouseover /
         * onmouseout yang menimpa style elemen lewat JavaScript. Selain
         * memaksa browser menghitung ulang tata letak di setiap gerakan
         * kursor, cara itu juga menuntut CSP mengizinkan skrip inline.
         * Sekarang cukup satu transisi CSS.
         */

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
            margin-bottom: 26px;
        }

        .stat {
            position: relative;
            overflow: hidden;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            padding: 20px 22px;
            box-shadow: var(--shadow-sm);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .stat:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat__stripe {
            position: absolute;
            inset: 0 0 auto 0;
            height: 3px;
        }

        .stat__icon {
            width: 40px;
            height: 40px;
            border-radius: var(--r-md);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .stat__icon svg { width: 20px; height: 20px; }

        .stat__label {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin: 0;
        }

        .stat__value {
            font-family: var(--font-display);
            font-size: 34px;
            font-weight: 700;
            color: var(--navy);
            line-height: 1.1;
            margin: 4px 0 0;
            letter-spacing: -.02em;
        }

        .stat__note {
            font-size: 12px;
            color: var(--muted);
            margin: 6px 0 0;
        }

        .panel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 18px;
        }

        .panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .panel__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 22px 14px;
            border-bottom: 1px solid var(--line);
        }

        .panel__title {
            font-size: 14px;
            font-weight: 700;
            color: var(--navy);
            margin: 0;
        }

        .panel__sub {
            font-size: 12px;
            color: var(--muted);
            margin: 2px 0 0;
        }

        .panel__body { padding: 8px 22px 18px; }

        .antrean {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid var(--line);
            text-decoration: none;
        }

        .antrean:last-child { border-bottom: none; }

        .antrean__ikon {
            width: 36px;
            height: 36px;
            border-radius: var(--r-md);
            background: var(--bg);
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .antrean__ikon svg { width: 17px; height: 17px; }

        .antrean__nama {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--navy);
            margin: 0;
        }

        .antrean__ket {
            font-size: 12px;
            color: var(--muted);
            margin: 2px 0 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 32ch;
        }

        .bar { margin-bottom: 15px; }

        .bar__head {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .bar__label { font-weight: 600; color: var(--navy); }
        .bar__value { font-weight: 700; }

        .bar__track {
            height: 8px;
            background: var(--bg);
            border-radius: 99px;
            overflow: hidden;
        }

        .bar__fill {
            height: 100%;
            border-radius: 99px;
        }

        .kosong {
            padding: 34px 0;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
        }
    </style>
    @endpush

    {{-- ── KARTU RINGKASAN ── --}}
    <div class="stat-grid">
        @foreach ($kartu as $item)
            <article class="stat">
                <span class="stat__stripe" style="background: var(--{{ $item['warna'] }});"></span>
                <div class="stat__icon"
                     style="background: var(--{{ $item['warna'] }}-wash); color: var(--{{ $item['warna'] }});">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        @if ($item['label'] === 'Menunggu')<circle cx="12" cy="12" r="10"/>@endif
                        <path d="{{ $item['ikon'] }}"/>
                    </svg>
                </div>
                <p class="stat__label">{{ $item['label'] }}</p>
                <p class="stat__value">{{ number_format($item['nilai'], 0, ',', '.') }}</p>
                <p class="stat__note">{{ $item['ket'] }}</p>
            </article>
        @endforeach
    </div>

    <div class="panel-grid">

        {{-- ── DATA SERVIS TERBARU ── --}}
        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Data Servis Terbaru</h2>
                    <p class="panel__sub">{{ number_format($ringkasan['total'], 0, ',', '.') }} entri tercatat</p>
                </div>
                <a href="{{ route('servis.index') }}" class="btn btn--solid" style="padding:8px 14px;font-size:12.5px;">
                    Lihat Semua
                </a>
            </div>

            <div class="panel__body">
                @forelse ($recentServis as $item)
                    <a href="{{ route('servis.show', $item) }}" class="antrean">
                        <span class="antrean__ikon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/>
                            </svg>
                        </span>
                        <span style="min-width:0;flex:1;">
                            <span class="antrean__nama" style="display:block;">{{ $item->pelanggan }}</span>
                            <span class="antrean__ket" style="display:block;">{{ $item->kerusakan }}</span>
                        </span>
                        <x-status-badge :status="$item->status" />
                    </a>
                @empty
                    <p class="kosong">Belum ada data servis yang tercatat.</p>
                @endforelse
            </div>
        </section>

        {{-- ── RINGKASAN STATUS ── --}}
        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Ringkasan Status</h2>
                    <p class="panel__sub">Distribusi pekerjaan saat ini</p>
                </div>
            </div>

            <div class="panel__body" style="padding-top:16px;">
                @foreach ([
                    ['Menunggu', $ringkasan['menunggu'], $persentase['menunggu'], 'amber'],
                    ['Sedang Diproses', $ringkasan['proses'], $persentase['proses'], 'violet'],
                    ['Selesai', $ringkasan['selesai'], $persentase['selesai'], 'green'],
                ] as [$label, $jumlah, $persen, $warna])
                    <div class="bar">
                        <div class="bar__head">
                            <span class="bar__label">{{ $label }}</span>
                            <span class="bar__value" style="color: var(--{{ $warna }});">
                                {{ $persen }}% &middot; {{ $jumlah }} unit
                            </span>
                        </div>
                        <div class="bar__track"
                             role="progressbar"
                             aria-label="{{ $label }}"
                             aria-valuenow="{{ $persen }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            <div class="bar__fill" style="width: {{ $persen }}%; background: var(--{{ $warna }});"></div>
                        </div>
                    </div>
                @endforeach

                <div style="margin-top:20px;padding:14px;background:var(--bg);border-radius:var(--r-md);display:flex;align-items:center;gap:12px;">
                    <span style="width:38px;height:38px;border-radius:var(--r-md);background:var(--blue-wash);color:var(--blue);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
                        </svg>
                    </span>
                    <span>
                        <span style="display:block;font-size:13px;font-weight:600;color:var(--navy);">Tingkat penyelesaian</span>
                        <span style="display:block;font-size:12.5px;color:var(--muted);">
                            {{ $ringkasan['selesai'] }} dari {{ $ringkasan['total'] }} unit sudah selesai.
                        </span>
                    </span>
                </div>
            </div>
        </section>

    </div>
</x-app-layout>
