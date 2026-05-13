<x-app-layout>
    <x-slot name="header">Invoice</x-slot>

    <div class="max-w-3xl mx-auto space-y-4">

        {{-- Action Buttons --}}
        <div class="flex justify-end gap-3 print:hidden">
            <a href="{{ route('servis.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 text-sm font-medium">
                ← Kembali
            </a>
            <a href="{{ route('invoice.cetak', $servis) }}" target="_blank"
               class="px-4 py-2 bg-gray-800 text-white rounded-xl hover:bg-gray-700 text-sm font-medium inline-flex items-center gap-2">
                🖨️ Cetak
            </a>
            <a href="{{ route('invoice.pdf', $servis) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 text-sm font-medium inline-flex items-center gap-2">
                ⬇️ Download PDF
            </a>
        </div>

        {{-- Invoice Card --}}
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">

            {{-- Header --}}
            <div style="background:linear-gradient(135deg,#1a1f36 0%,#3b5bdb 100%);padding:32px 40px;">
                <div class="flex justify-between items-start">
                    <div>
                        <div style="font-size:22px;font-weight:800;color:#fff;letter-spacing:1px;">🔧 PHONE REPAIR</div>
                        <div style="color:rgba(255,255,255,0.7);font-size:12px;margin-top:4px;">Jasa Perbaikan Smartphone Profesional</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:28px;font-weight:800;color:#fff;letter-spacing:2px;">INVOICE</div>
                        <div style="font-family:monospace;color:#a5b4fc;font-size:13px;margin-top:4px;">{{ $servis->kode_unik }}</div>
                    </div>
                </div>

                <div style="height:1px;background:rgba(255,255,255,0.2);margin:20px 0;"></div>

                <div class="flex justify-between" style="color:rgba(255,255,255,0.85);font-size:12px;">
                    <div>
                        <div style="color:rgba(255,255,255,0.5);margin-bottom:2px;">Tanggal Masuk</div>
                        <div style="font-weight:600;">{{ $servis->tanggal ? $servis->tanggal->format('d F Y') : '—' }}</div>
                    </div>
                    <div style="text-align:center;">
                        <div style="color:rgba(255,255,255,0.5);margin-bottom:2px;">Status</div>
                        <div style="font-weight:600;">
                            @if($servis->status == 'Selesai')
                                <span style="background:rgba(74,222,128,0.25);color:#4ade80;padding:2px 12px;border-radius:20px;">Selesai</span>
                            @elseif($servis->status == 'Proses')
                                <span style="background:rgba(96,165,250,0.25);color:#60a5fa;padding:2px 12px;border-radius:20px;">Proses</span>
                            @else
                                <span style="background:rgba(251,191,36,0.25);color:#fbbf24;padding:2px 12px;border-radius:20px;">Menunggu</span>
                            @endif
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="color:rgba(255,255,255,0.5);margin-bottom:2px;">Teknisi</div>
                        <div style="font-weight:600;">{{ $servis->teknisi ?? '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div style="padding:32px 40px;">

                {{-- Data Pelanggan & HP --}}
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div>
                        <div style="font-size:10px;font-weight:700;letter-spacing:1px;color:#8a93b2;margin-bottom:10px;">DATA PELANGGAN</div>
                        <div style="font-size:16px;font-weight:700;color:#1a1f36;">{{ $servis->pelanggan }}</div>
                        @if($servis->alamat)
                            <div style="font-size:12px;color:#666;margin-top:4px;">{{ $servis->alamat }}</div>
                        @endif
                        @if($servis->no_wa)
                            <div style="font-size:12px;color:#16a34a;margin-top:4px;">📱 {{ $servis->no_wa }}</div>
                        @endif
                    </div>
                    <div>
                        <div style="font-size:10px;font-weight:700;letter-spacing:1px;color:#8a93b2;margin-bottom:10px;">DATA HANDPHONE</div>
                        <div style="font-size:16px;font-weight:700;color:#1a1f36;">{{ $servis->merk_hp ?? '—' }}</div>
                        <div style="font-size:12px;color:#666;margin-top:2px;">{{ $servis->tipe_hp ?? '' }}</div>
                        @php
                            $kel = $servis->kelengkapan;
                            if (is_string($kel)) $kel = json_decode($kel, true) ?? [];
                            $kel = $kel ?? [];
                        @endphp
                        @if(count($kel) > 0)
                            <div style="margin-top:8px;display:flex;flex-wrap:wrap;gap:4px;">
                                @foreach($kel as $item)
                                    <span style="background:#eff6ff;color:#3b5bdb;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:500;">{{ $item }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Kerusakan --}}
                <div style="background:#f8f9ff;border-radius:12px;padding:16px 20px;margin-bottom:24px;border-left:4px solid #3b5bdb;">
                    <div style="font-size:10px;font-weight:700;letter-spacing:1px;color:#8a93b2;margin-bottom:6px;">KERUSAKAN</div>
                    <div style="font-size:13px;color:#333;line-height:1.6;">{{ $servis->kerusakan }}</div>
                </div>

                {{-- Tabel Biaya --}}
                <table style="width:100%;border-collapse:collapse;margin-bottom:16px;">
                    <thead>
                        <tr style="background:#f1f3f9;">
                            <th style="padding:10px 14px;text-align:left;font-size:11px;color:#8a93b2;font-weight:600;letter-spacing:1px;border-radius:8px 0 0 8px;">DESKRIPSI</th>
                            <th style="padding:10px 14px;text-align:right;font-size:11px;color:#8a93b2;font-weight:600;letter-spacing:1px;border-radius:0 8px 8px 0;">JUMLAH</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom:1px solid #f0f0f0;">
                            <td style="padding:14px;font-size:13px;color:#333;">
                                <div style="font-weight:600;">Jasa Servis — {{ $servis->merk_hp }} {{ $servis->tipe_hp }}</div>
                                <div style="font-size:11px;color:#888;margin-top:2px;">{{ $servis->kerusakan }}</div>
                            </td>
                            <td style="padding:14px;text-align:right;font-size:13px;font-weight:600;color:#1a1f36;">
                                Rp {{ number_format($servis->biaya ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- Total --}}
                <div style="background:#1a1f36;border-radius:12px;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
                    <div style="color:rgba(255,255,255,0.7);font-size:13px;font-weight:600;letter-spacing:1px;">TOTAL PEMBAYARAN</div>
                    <div style="color:#fff;font-size:22px;font-weight:800;">
                        Rp {{ number_format($servis->biaya ?? 0, 0, ',', '.') }}
                    </div>
                </div>

                {{-- Footer --}}
                <div style="margin-top:32px;padding-top:20px;border-top:1px dashed #e0e0e0;display:flex;justify-content:space-between;align-items:center;">
                    <div style="font-size:11px;color:#aaa;">
                        Terima kasih telah mempercayakan perangkat Anda kepada kami.<br>
                        Garansi servis berlaku 7 hari setelah pengambilan.
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:10px;color:#aaa;margin-bottom:40px;">Tanda Tangan Teknisi</div>
                        <div style="font-size:11px;color:#555;border-top:1px solid #ccc;padding-top:4px;min-width:100px;">{{ $servis->teknisi ?? 'Teknisi' }}</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>