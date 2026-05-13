<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }

    /* 58mm = ~164px at 72dpi. DomPDF uses 96dpi → 58mm ≈ 219px usable */
    @page {
        margin: 4mm 3mm;
        size: 58mm auto;
    }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 8px;
        color: #000;
        background: #fff;
        width: 52mm;
    }

    /* ── HEADER ── */
    .brand {
        text-align: center;
        font-size: 11px;
        font-weight: bold;
        letter-spacing: 1px;
        margin-bottom: 2px;
    }
    .brand-sub {
        text-align: center;
        font-size: 7px;
        color: #555;
        margin-bottom: 6px;
    }
    .divider-solid  { border-top: 1px solid #000; margin: 4px 0; }
    .divider-dashed { border-top: 1px dashed #555; margin: 4px 0; }

    .invoice-title {
        text-align: center;
        font-size: 10px;
        font-weight: bold;
        letter-spacing: 3px;
        margin: 4px 0 2px;
    }
    .kode {
        text-align: center;
        font-size: 7px;
        font-family: monospace;
        color: #333;
        margin-bottom: 4px;
    }

    /* ── META ROW ── */
    .meta { font-size: 7.5px; margin-bottom: 2px; }
    .meta span { float: right; font-weight: bold; }
    .meta::after { content: ''; display: table; clear: both; }

    /* ── SECTION LABEL ── */
    .section-label {
        font-size: 7px;
        font-weight: bold;
        letter-spacing: 1px;
        color: #555;
        text-transform: uppercase;
        margin: 5px 0 3px;
    }

    /* ── DATA ROW ── */
    .row { font-size: 7.5px; margin-bottom: 2px; }
    .row .label { color: #666; font-size: 7px; }
    .row .val   { font-weight: bold; }

    /* ── KELENGKAPAN ── */
    .kel { font-size: 7px; color: #333; }

    /* ── KERUSAKAN ── */
    .kerusakan-box {
        border: 1px dashed #aaa;
        border-radius: 2px;
        padding: 4px 5px;
        font-size: 7.5px;
        color: #333;
        line-height: 1.5;
        margin: 4px 0;
    }

    /* ── TABLE ── */
    table { width: 100%; border-collapse: collapse; margin: 4px 0; font-size: 7.5px; }
    th { font-size: 7px; color: #555; font-weight: bold; text-align: left; padding: 2px 0; border-bottom: 1px solid #ccc; }
    th:last-child { text-align: right; }
    td { padding: 3px 0; vertical-align: top; }
    td:last-child { text-align: right; font-weight: bold; white-space: nowrap; }
    .item-name { font-weight: bold; font-size: 7.5px; }
    .item-sub  { font-size: 6.5px; color: #777; margin-top: 1px; }

    /* ── TOTAL ── */
    .total-box {
        background: #1a1f36;
        color: #fff;
        padding: 5px 6px;
        border-radius: 3px;
        margin: 4px 0;
        display: table;
        width: 100%;
    }
    .total-label  { display: table-cell; font-size: 7px; font-weight: bold; vertical-align: middle; }
    .total-amount { display: table-cell; font-size: 10px; font-weight: bold; text-align: right; vertical-align: middle; }

    /* ── FOOTER ── */
    .footer-note {
        text-align: center;
        font-size: 7px;
        color: #777;
        line-height: 1.6;
        margin: 4px 0;
    }
    .ttd-area {
        text-align: center;
        margin-top: 6px;
        font-size: 7px;
    }
    .ttd-space { height: 32px; }
    .ttd-line  { border-top: 1px solid #000; padding-top: 2px; font-size: 7.5px; display: inline-block; min-width: 80px; }
</style>
</head>
<body>

{{-- Brand --}}
<div class="brand">&#128295; PHONE REPAIR</div>
<div class="brand-sub">Jasa Perbaikan Smartphone Profesional</div>
<div class="divider-solid"></div>

{{-- Invoice Title --}}
<div class="invoice-title">INVOICE</div>
<div class="kode">{{ $servis->kode_unik }}</div>
<div class="divider-dashed"></div>

{{-- Meta --}}
<div class="meta">Tanggal <span>{{ $servis->tanggal ? $servis->tanggal->format('d/m/Y') : '-' }}</span></div>
<div class="meta">Status   <span>{{ $servis->status }}</span></div>
<div class="meta">Teknisi  <span>{{ $servis->teknisi ?? '-' }}</span></div>
<div class="divider-dashed"></div>

{{-- Data Pelanggan --}}
<div class="section-label">Data Pelanggan</div>
<div class="row"><div class="label">Nama</div><div class="val">{{ $servis->pelanggan }}</div></div>
@if($servis->alamat)
    <div class="row"><div class="label">Alamat</div><div class="val">{{ $servis->alamat }}</div></div>
@endif
@if($servis->no_wa)
    <div class="row"><div class="label">WhatsApp</div><div class="val">{{ $servis->no_wa }}</div></div>
@endif
<div class="divider-dashed"></div>

{{-- Data HP --}}
<div class="section-label">Data Handphone</div>
<div class="row"><div class="label">Merk</div><div class="val">{{ $servis->merk_hp ?? '-' }}</div></div>
<div class="row"><div class="label">Tipe</div><div class="val">{{ $servis->tipe_hp ?? '-' }}</div></div>
@php
    $kel = $servis->kelengkapan;
    if (is_string($kel)) $kel = json_decode($kel, true) ?? [];
    $kel = $kel ?? [];
@endphp
@if(count($kel) > 0)
    <div class="row"><div class="label">Kelengkapan</div><div class="val kel">{{ implode(', ', $kel) }}</div></div>
@endif
<div class="divider-dashed"></div>

{{-- Kerusakan --}}
<div class="section-label">Kerusakan</div>
<div class="kerusakan-box">{{ $servis->kerusakan }}</div>

{{-- Tabel Biaya --}}
<table>
    <thead>
        <tr>
            <th>Deskripsi</th>
            <th>Harga</th>
        </tr>
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
    $footerThanks    = App\Models\Setting::get('footer_thanks',     'Terima kasih atas kepercayaan Anda.');
    $footerGaransi   = App\Models\Setting::get('garansi_servis',   'Garansi servis 7 hari setelah pengambilan.');
    $footerBatas     = App\Models\Setting::get('batas_pengambilan','Batas Pengambilan Maksimal 3 Bulan!');
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

</body>
</html>