<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice {{ $servis->kode_unik }}</title>
<style>
    * { margin:0; padding:0; box-sizing:border-box; }

    body {
        font-family: 'Times New Roman', Times, serif;
        font-size: 11px;
        font-weight: 800;
        color: #000;
        background: #e5e5e5;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .preview-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        min-height: 100vh;
    }

    .action-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 16px;
    }
    .btn {
        padding: 9px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        border: none;
        font-family: Arial, sans-serif;
    }
    .btn-print { background: #1a1f36; color: #fff; }
    .btn-close  { background: #e0e0e0; color: #333; }

    /* ── KERTAS PREVIEW (layar) ── */
    .page {
        width: 58mm;
        background: #fff;
        padding: 4mm 3mm;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        border-radius: 4px;
    }

    /* ══ HEADER ══ */
    .brand {
        text-align: center;
        font-size: 16px;
        font-weight: 900;
        letter-spacing: 1px;
        margin-bottom: 2px;
        color: #000;
    }
    .brand-sub {
        text-align: center;
        font-size: 11px;
        font-weight: 800;
        color: #000;
        margin-bottom: 6px;
    }

    /* ══ GARIS ══ */
    .divider-solid  { border-top: 1.5px solid #000; margin: 4px 0; }
    .divider-dashed { border-top: 1.5px dashed #000; margin: 4px 0; }

    /* ══ INVOICE TITLE ══ */
    .invoice-title {
        text-align: center;
        font-size: 15px;
        font-weight: 900;
        letter-spacing: 3px;
        margin: 4px 0 2px;
        color: #000;
    }
    .kode {
        text-align: center;
        font-size: 10px;
        font-family: 'Courier New', monospace;
        font-weight: 800;
        color: #000;
        margin-bottom: 4px;
    }

    /* ══ META — display:table ══ */
    .meta {
        display: table;
        width: 100%;
        font-size: 11px;
        font-weight: 800;
        margin-bottom: 2px;
        color: #000;
    }
    .meta .lbl {
        display: table-cell;
        text-align: left;
        font-weight: 800;
        color: #000;
        width: 45%;
    }
    .meta .val {
        display: table-cell;
        text-align: right;
        font-weight: 900;
        color: #000;
    }

    /* ══ SECTION LABEL ══ */
    .section-label {
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 1px;
        color: #000;
        text-transform: uppercase;
        margin: 5px 0 3px;
    }

    /* ══ ROW DATA — display:table ══ */
    .row {
        display: table;
        width: 100%;
        font-size: 11px;
        font-weight: 800;
        margin-bottom: 3px;
        color: #000;
    }
    .row .lbl {
        display: table-cell;
        text-align: left;
        font-size: 10px;
        font-weight: 800;
        color: #000;
        width: 40%;
        vertical-align: top;
    }
    .row .val {
        display: table-cell;
        text-align: right;
        font-weight: 900;
        color: #000;
        vertical-align: top;
        word-break: break-word;
    }

    /* ══ KERUSAKAN BOX ══ */
    .kerusakan-box {
        border: 1.5px solid #000;
        border-radius: 2px;
        padding: 4px 5px;
        font-size: 11px;
        font-weight: 800;
        color: #000;
        line-height: 1.5;
        margin: 4px 0;
    }

    /* ══ TABEL ══ */
    table         { width:100%; border-collapse:collapse; margin:4px 0; font-size:11px; font-weight:800; color:#000; }
    th            { font-size:10px; color:#000; font-weight:900; padding:2px 0; border-bottom:1.5px solid #000; text-align:left; }
    th:last-child { text-align:right; }
    td            { padding:3px 0; vertical-align:top; color:#000; }
    td:last-child { text-align:right; font-weight:900; white-space:nowrap; }
    .item-name    { font-weight:900; font-size:11px; color:#000; }
    .item-sub     { font-size:10px; font-weight:800; color:#000; margin-top:1px; }

    /* ══ TOTAL — plain, tanpa box ══ */
    .total-box {
        display: table;
        width: 100%;
        margin: 4px 0;
    }
    .total-label {
        display: table-cell;
        text-align: left;
        font-size: 13px;
        font-weight: 900;
        letter-spacing: 1px;
        color: #000;
        vertical-align: middle;
    }
    .total-amount {
        display: table-cell;
        text-align: right;
        font-size: 13px;
        font-weight: 900;
        color: #000;
        vertical-align: middle;
    }

    /* ══ FOOTER ══ */
    .footer-note {
        text-align: center;
        font-size: 11px;
        font-weight: 800;
        color: #000;
        line-height: 1.6;
        margin: 5px 0 3px;
    }

    /* ══ TTD ══ */
    .ttd-area  { text-align:center; margin-top:6px; font-size:10px; font-weight:800; color:#000; }
    .ttd-space { height:32px; }
    .ttd-line  { border-top:1.5px solid #000; padding-top:3px; display:inline-block; min-width:80px; font-size:11px; font-weight:900; color:#000; }

    /* ══════════════════════════════════════════
       @MEDIA PRINT — semua ukuran pakai pt
       agar presisi di printer thermal 203dpi
       58mm = 164pt lebar kertas
       Font aman thermal: 7pt–9pt (bold)
    ══════════════════════════════════════════ */
    @media print {
        body {
            background: #fff;
            font-size: 8pt;
            font-weight: 800;
        }
        .preview-wrapper { padding:0; background:#fff; }
        .action-bar { display:none !important; }

        .page {
            box-shadow: none;
            border-radius: 0;
            width: 58mm;
            padding: 2mm 2mm;
        }

        .brand          { font-size: 11pt; }
        .brand-sub      { font-size:  7pt; }
        .invoice-title  { font-size: 10pt; }
        .kode           { font-size:  7pt; }
        .meta           { font-size:  8pt; }
        .meta .lbl      { font-size:  8pt; }
        .meta .val      { font-size:  8pt; }
        .section-label  { font-size:  7pt; }
        .row            { font-size:  8pt; }
        .row .lbl       { font-size:  7pt; }
        .row .val       { font-size:  8pt; }
        .kerusakan-box  { font-size:  8pt; }
        table           { font-size:  8pt; }
        th              { font-size:  7pt; }
        .item-name      { font-size:  8pt; }
        .item-sub       { font-size:  7pt; }
        .total-label    { font-size:  9pt; }
        .total-amount   { font-size:  9pt; }
        .footer-note    { font-size:  7pt; }
        .ttd-area       { font-size:  7pt; }
        .ttd-line       { font-size:  7pt; }

        @page {
            size: 58mm auto;
            margin: 0;
        }
    }
</style>
</head>
<body>

<div class="preview-wrapper">

    {{-- Action Bar --}}
    <div class="action-bar">
        <button class="btn btn-print" onclick="window.print()">🖨️ Cetak Invoice</button>
        <button class="btn btn-close" onclick="window.close()">✕ Tutup</button>
    </div>

    {{-- Kertas 58mm --}}
    <div class="page">

        {{-- Brand --}}
        <div class="brand">&#128295; PHONE REPAIR</div>
        <div class="brand-sub">Jasa Perbaikan Smartphone Profesional</div>
        <div class="divider-solid"></div>

        {{-- Invoice Title --}}
        <div class="invoice-title">INVOICE</div>
        <div class="kode">{{ $servis->kode_unik }}</div>
        <div class="divider-dashed"></div>

        {{-- Meta --}}
        <div class="meta"><span class="lbl">Tanggal</span><span class="val">{{ $servis->tanggal ? $servis->tanggal->format('d/m/Y') : '-' }}</span></div>
        <div class="meta"><span class="lbl">Status</span><span class="val">{{ $servis->status }}</span></div>
        <div class="meta"><span class="lbl">Teknisi</span><span class="val">{{ $servis->teknisi ?? '-' }}</span></div>
        <div class="divider-dashed"></div>

        {{-- Pelanggan --}}
        <div class="section-label">Data Pelanggan</div>
        <div class="row"><div class="lbl">Nama</div><div class="val">{{ $servis->pelanggan }}</div></div>
        @if($servis->alamat)
            <div class="row"><div class="lbl">Alamat</div><div class="val">{{ $servis->alamat }}</div></div>
        @endif
        @if($servis->no_wa)
            <div class="row"><div class="lbl">WhatsApp</div><div class="val">{{ $servis->no_wa }}</div></div>
        @endif
        <div class="divider-dashed"></div>

        {{-- HP --}}
        <div class="section-label">Data Handphone</div>
        <div class="row"><div class="lbl">Merk</div><div class="val">{{ $servis->merk_hp ?? '-' }}</div></div>
        <div class="row"><div class="lbl">Tipe</div><div class="val">{{ $servis->tipe_hp ?? '-' }}</div></div>
        @php
            $kel = $servis->kelengkapan;
            if (is_string($kel)) $kel = json_decode($kel, true) ?? [];
            $kel = $kel ?? [];
        @endphp
        @if(count($kel) > 0)
            <div class="row"><div class="lbl">Kelengkapan</div><div class="val">{{ implode(', ', $kel) }}</div></div>
        @endif
        <div class="divider-dashed"></div>

        {{-- Kerusakan --}}
        <div class="section-label">Kerusakan</div>
        <div class="kerusakan-box">{{ $servis->kerusakan }}</div>

        {{-- Tabel --}}
        <table>
            <thead>
                <tr><th>Deskripsi</th><th>Harga</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-name">Jasa Servis</div>
                        <div class="item-sub">{{ $servis->merk_hp }} {{ $servis->tipe_hp }}</div>
                    </td>
                    <td>Rp {{ number_format($servis->biaya ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="divider-solid"></div>

        {{-- Total --}}
        <div class="total-box">
            <div class="total-label">TOTAL</div>
            <div class="total-amount">Rp {{ number_format($servis->biaya ?? 0, 0, ',', '.') }}</div>
        </div>

        <div class="divider-dashed"></div>

        {{-- Footer --}}
        @php
            $footerThanks  = App\Models\Setting::get('footer_thanks',     'Terima kasih atas kepercayaan Anda.');
            $footerGaransi = App\Models\Setting::get('garansi_servis',   'Garansi servis 7 hari setelah pengambilan.');
            $footerBatas   = App\Models\Setting::get('batas_pengambilan','Batas Pengambilan Maksimal 3 Bulan!');
        @endphp
        <div class="footer-note">
            {{ $footerThanks }}<br>
            {{ $footerGaransi }}<br>
            <strong>{{ $footerBatas }}</strong>
        </div>

        <div class="ttd-area">
            <div class="ttd-space"></div>
            <div class="ttd-line">{{ $servis->teknisi ?? 'Teknisi' }}</div>
        </div>

    </div>{{-- end .page --}}

</div>{{-- end .preview-wrapper --}}

</body>
</html>