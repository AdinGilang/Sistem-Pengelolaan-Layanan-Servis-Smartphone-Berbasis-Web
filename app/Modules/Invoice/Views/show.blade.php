<x-app-layout>
    <x-slot name="header">Invoice</x-slot>
    <x-slot name="backTo">{{ route('servis.show', $servis) }}</x-slot>
    <x-slot name="backLabel">Kembali ke Detail</x-slot>

    {{--
        Desain sebelumnya memakai kop bergradasi navy-ke-biru dan kotak total
        berlatar gelap penuh — pola visual yang lazim dipakai templat dashboard
        generik, tapi terasa janggal untuk sebuah invoice: dokumen bisnis yang
        akan dicetak dan diserahkan ke pelanggan. Invoice sungguhan (nota dari
        toko, faktur dari penyedia layanan) umumnya nyaris monokrom — hitam di
        atas putih, satu warna aksen dipakai seperlunya, hierarki dibentuk dari
        ukuran dan bobot huruf, bukan dari kotak warna-warni.

        Halaman ini ditulis ulang mengikuti konvensi itu: kop surat sederhana,
        garis pemisah tipis, tabel rincian biaya yang benar-benar berupa tabel,
        dan blok total yang ditegaskan lewat garis dan ukuran huruf — bukan
        kotak gelap. Hasilnya juga lebih ramah dicetak: tidak ada gradasi yang
        berubah jadi blok abu-abu kusam di atas kertas.
    --}}
    @push('styles')
    <style>
        .inv-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 16px;
        }

        .inv-doc {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .inv-doc__head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            padding: 32px 36px 24px;
        }

        .inv-brand__name {
            font-family: var(--font-display);
            font-size: 19px;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: -.01em;
        }

        .inv-brand__tagline {
            font-size: 12px;
            color: var(--muted);
            margin-top: 3px;
        }

        .inv-doc__title {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: .06em;
            text-align: right;
        }

        .inv-doc__number {
            font-family: ui-monospace, 'SF Mono', Menlo, Consolas, monospace;
            font-size: 12.5px;
            color: var(--blue);
            font-weight: 600;
            text-align: right;
            margin-top: 4px;
        }

        .inv-strip {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            padding: 16px 36px;
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
            background: var(--bg);
        }

        .inv-strip__item:nth-child(2) { text-align: center; }
        .inv-strip__item:nth-child(3) { text-align: right; }

        .inv-label {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .inv-value { font-size: 13.5px; font-weight: 600; color: var(--navy); }
        .inv-value--lg { font-size: 16px; }
        .inv-value--muted { font-size: 12.5px; font-weight: 500; color: var(--muted); }

        .inv-body { padding: 28px 36px 32px; }

        .inv-parties {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            margin-bottom: 26px;
        }

        .inv-parties p { margin: 3px 0 0; }

        .inv-issue { margin-bottom: 26px; }

        .inv-issue p {
            font-size: 13.5px;
            color: var(--text);
            line-height: 1.6;
            margin: 6px 0 0;
        }

        .inv-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        .inv-table thead th {
            text-align: left;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--muted);
            padding: 0 0 8px;
            border-bottom: 2px solid var(--navy);
        }

        .inv-table thead th:last-child { text-align: right; }

        .inv-table tbody td {
            padding: 14px 0;
            border-bottom: 1px solid var(--line);
            vertical-align: top;
        }

        .inv-table tbody td:last-child {
            text-align: right;
            font-weight: 600;
            color: var(--navy);
            white-space: nowrap;
        }

        .inv-item__title { font-weight: 600; color: var(--navy); }
        .inv-item__sub { font-size: 12px; color: var(--muted); margin-top: 2px; }

        .inv-totals {
            display: flex;
            justify-content: flex-end;
            margin-top: 4px;
        }

        .inv-totals__box { width: 100%; max-width: 260px; }

        .inv-totals__row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 10px 0;
        }

        .inv-totals__row--grand {
            border-top: 2px solid var(--navy);
            margin-top: 2px;
        }

        .inv-totals__row--grand span:first-child {
            font-size: 13px;
            font-weight: 700;
            color: var(--navy);
        }

        .inv-totals__row--grand span:last-child {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 700;
            color: var(--blue);
        }

        .inv-foot {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 24px;
            margin-top: 36px;
            padding-top: 20px;
            border-top: 1px dashed var(--line);
        }

        .inv-foot__notes {
            font-size: 11.5px;
            color: var(--muted);
            line-height: 1.7;
            max-width: 34ch;
        }

        .inv-foot__notes strong { color: var(--text); font-weight: 600; }

        .inv-sign { text-align: center; flex-shrink: 0; }

        .inv-sign__label {
            font-size: 10.5px;
            color: var(--muted);
            margin-bottom: 44px;
        }

        .inv-sign__name {
            font-size: 12px;
            color: var(--text);
            font-weight: 600;
            border-top: 1px solid var(--line);
            padding-top: 6px;
            min-width: 120px;
        }

        @media (max-width: 640px) {
            .inv-doc__head { flex-direction: column; gap: 14px; }
            .inv-doc__title, .inv-doc__number { text-align: left; }
            .inv-strip { grid-template-columns: 1fr; gap: 12px; }
            .inv-strip__item:nth-child(2), .inv-strip__item:nth-child(3) { text-align: left; }
            .inv-parties { grid-template-columns: 1fr; }
            .inv-foot { flex-direction: column; align-items: flex-start; }
        }

        /* Andai halaman ini dicetak langsung (Ctrl+P) alih-alih lewat tombol
           Cetak yang menuju /invoice/cetak, dokumennya tetap tampil rapi:
           tanpa bayangan kartu yang di atas kertas hanya jadi noda abu-abu. */
        @media print {
            .inv-doc { box-shadow: none; border: none; }
        }
    </style>
    @endpush

    <div class="max-w-3xl mx-auto">

        <div class="inv-actions print:hidden">
            <a href="{{ route('invoice.cetak', $servis) }}" target="_blank" class="btn btn--ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                Cetak
            </a>
            <a href="{{ route('invoice.pdf', $servis) }}" class="btn btn--solid">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Unduh PDF
            </a>
        </div>

        <article class="inv-doc">

            {{-- Kop surat --}}
            <div class="inv-doc__head">
                <div>
                    <div class="inv-brand__name">Phone Repair</div>
                    <div class="inv-brand__tagline">Jasa perbaikan smartphone profesional</div>
                </div>
                <div>
                    <div class="inv-doc__title">Invoice</div>
                    <div class="inv-doc__number">{{ $servis->kode_unik }}</div>
                </div>
            </div>

            {{-- Info ringkas --}}
            <div class="inv-strip">
                <div class="inv-strip__item">
                    <div class="inv-label">Tanggal</div>
                    <div class="inv-value">{{ $servis->tanggal ? $servis->tanggal->translatedFormat('d F Y') : '—' }}</div>
                </div>
                <div class="inv-strip__item">
                    <div class="inv-label">Status</div>
                    <x-status-badge :status="$servis->status" />
                </div>
                <div class="inv-strip__item">
                    <div class="inv-label">Teknisi</div>
                    <div class="inv-value">{{ $servis->teknisi ?? '—' }}</div>
                </div>
            </div>

            <div class="inv-body">

                {{-- Pelanggan & perangkat --}}
                <div class="inv-parties">
                    <div>
                        <div class="inv-label">Pelanggan</div>
                        <div class="inv-value inv-value--lg">{{ $servis->pelanggan }}</div>
                        @if($servis->alamat)
                            <p class="inv-value--muted">{{ $servis->alamat }}</p>
                        @endif
                        @if($servis->no_wa)
                            <p class="inv-value--muted">{{ $servis->no_wa }}</p>
                        @endif
                    </div>
                    <div>
                        <div class="inv-label">Perangkat</div>
                        <div class="inv-value inv-value--lg">{{ $servis->merk_hp ?? '—' }}</div>
                        @if($servis->tipe_hp)
                            <p class="inv-value--muted">{{ $servis->tipe_hp }}</p>
                        @endif
                        @php
                            $kel = $servis->kelengkapan;
                            if (is_string($kel)) $kel = json_decode($kel, true) ?? [];
                            $kel = $kel ?? [];
                        @endphp
                        @if(count($kel) > 0)
                            <p class="inv-value--muted">Kelengkapan: {{ implode(', ', $kel) }}</p>
                        @endif
                    </div>
                </div>

                {{-- Keluhan --}}
                <div class="inv-issue">
                    <div class="inv-label">Keluhan / kerusakan</div>
                    <p>{{ $servis->kerusakan }}</p>
                </div>

                {{-- Rincian biaya --}}
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th>Deskripsi</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="inv-item__title">
                                    Jasa servis — {{ trim(($servis->merk_hp ?? '') . ' ' . ($servis->tipe_hp ?? '')) ?: 'Perangkat' }}
                                </div>
                                <div class="inv-item__sub">{{ $servis->kerusakan }}</div>
                            </td>
                            <td>Rp {{ number_format($servis->biaya ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="inv-totals">
                    <div class="inv-totals__box">
                        <div class="inv-totals__row inv-totals__row--grand">
                            <span>Total</span>
                            <span>Rp {{ number_format($servis->biaya ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{--
                    Sebelumnya bagian ini hanya menampilkan footerThanks dan
                    footerGaransi — footerBatas (batas waktu pengambilan) sudah
                    dikirim InvoiceController dan tampil benar di versi cetak
                    serta PDF, tapi terlewat di pratinjau layar ini. Ketiganya
                    sekarang konsisten di seluruh tampilan invoice.
                --}}
                <div class="inv-foot">
                    <p class="inv-foot__notes">
                        {{ $footerThanks }}<br>
                        {{ $footerGaransi }}<br>
                        <strong>{{ $footerBatas }}</strong>
                    </p>
                    <div class="inv-sign">
                        <div class="inv-sign__label">Tanda tangan teknisi</div>
                        <div class="inv-sign__name">{{ $servis->teknisi ?? 'Teknisi' }}</div>
                    </div>
                </div>

            </div>
        </article>
    </div>
</x-app-layout>
