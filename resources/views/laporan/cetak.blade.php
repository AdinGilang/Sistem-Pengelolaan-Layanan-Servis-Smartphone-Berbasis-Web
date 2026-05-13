<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cetak Laporan — {{ $namaBulan }} {{ $tahun }}</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', sans-serif; font-size: 13px; color: #1a1f36; background: #fff; padding: 32px; }

  .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #1a1f36; }
  .header-left h1 { font-size: 20px; font-weight: 700; color: #1a1f36; }
  .header-left p  { font-size: 12px; color: #8a93b2; margin-top: 3px; }
  .header-right   { text-align: right; font-size: 12px; color: #8a93b2; }
  .header-right strong { display: block; font-size: 15px; color: #3b5bdb; font-weight: 700; }

  .stats { display: flex; gap: 14px; margin-bottom: 24px; }
  .stat-box { flex: 1; border: 1.5px solid #e8eaf0; border-radius: 8px; padding: 12px 14px; }
  .stat-box .label { font-size: 10px; font-weight: 600; color: #8a93b2; text-transform: uppercase; letter-spacing: .5px; }
  .stat-box .value { font-size: 28px; font-weight: 700; color: #1a1f36; margin-top: 2px; }
  .stat-box .sub   { font-size: 11px; color: #8a93b2; margin-top: 2px; }
  .stat-box.blue   { border-top: 3px solid #3b5bdb; }
  .stat-box.yellow { border-top: 3px solid #f59f00; }
  .stat-box.purple { border-top: 3px solid #9c36b5; }
  .stat-box.green  { border-top: 3px solid #2f9e44; }

  table { width: 100%; border-collapse: collapse; font-size: 12px; }
  thead tr { background: #1a1f36; color: #fff; }
  thead th { padding: 10px 12px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
  thead th.right { text-align: right; }
  tbody tr:nth-child(even) { background: #f7f8fc; }
  tbody tr { border-bottom: 1px solid #f0f2f7; }
  tbody td { padding: 10px 12px; }
  tbody td.right { text-align: right; }
  tbody td.muted { color: #8a93b2; font-size: 11px; }

  .badge-status { display: inline-block; font-size: 10px; font-weight: 600; padding: 2px 9px; border-radius: 20px; }
  .badge-menunggu { background: rgba(245,159,0,0.15); color: #d08700; }
  .badge-proses   { background: rgba(156,54,181,0.15); color: #8a2be2; }
  .badge-selesai  { background: rgba(47,158,68,0.15);  color: #2f9e44; }

  tfoot tr { background: #f0f2f7; border-top: 2px solid #e8eaf0; }
  tfoot td { padding: 11px 12px; font-weight: 700; font-size: 13px; }
  tfoot td.right { text-align: right; color: #2f9e44; }

  .footer { margin-top: 24px; text-align: center; font-size: 11px; color: #8a93b2; padding-top: 12px; border-top: 1px solid #e8eaf0; }

  .no-data { text-align: center; padding: 48px 0; color: #8a93b2; font-size: 14px; }

  .btn-print {
    display: inline-flex; align-items: center; gap: 7px;
    background: #1a1f36; color: #fff;
    padding: 9px 18px; border-radius: 8px;
    font-size: 13px; font-weight: 600;
    cursor: pointer; border: none; font-family: inherit;
    margin-bottom: 20px;
  }
  .btn-print:hover { background: #252c4a; }

  @media print {
    .btn-print { display: none; }
    body { padding: 16px; }
  }
</style>
</head>
<body>

  <button class="btn-print" onclick="window.print()">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="6 9 6 2 18 2 18 9"/>
      <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
      <rect x="6" y="14" width="12" height="8"/>
    </svg>
    Cetak Halaman Ini
  </button>

  <div class="header">
    <div class="header-left">
      <h1>Laporan Servis</h1>
      <p>ServisApp — dicetak {{ now()->format('d F Y, H:i') }}</p>
    </div>
    <div class="header-right">
      Periode
      <strong>{{ $namaBulan }} {{ $tahun }}</strong>
    </div>
  </div>

  <div class="stats">
    <div class="stat-box blue">
      <div class="label">Total Servis</div>
      <div class="value">{{ $totalServis }}</div>
      <div class="sub">{{ $namaBulan }} {{ $tahun }}</div>
    </div>
    <div class="stat-box yellow">
      <div class="label">Menunggu</div>
      <div class="value">{{ $totalMenunggu }}</div>
      <div class="sub">Belum ditangani</div>
    </div>
    <div class="stat-box purple">
      <div class="label">Proses</div>
      <div class="value">{{ $totalProses }}</div>
      <div class="sub">Sedang dikerjakan</div>
    </div>
    <div class="stat-box green">
      <div class="label">Total Pendapatan</div>
      <div class="value" style="font-size:18px;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
      <div class="sub">Dari servis selesai</div>
    </div>
  </div>

  @if($data->isEmpty())
    <div class="no-data">📋 Tidak ada data untuk periode {{ $namaBulan }} {{ $tahun }}</div>
  @else
    <table>
      <thead>
        <tr>
          <th style="width:32px;">No</th>
          <th>Kode</th>
          <th>Pelanggan</th>
          <th>Kerusakan</th>
          <th>Status</th>
          <th class="right">Biaya</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody>
        @foreach($data as $i => $item)
          @php
            $badgeClass = match($item->status) {
              'Menunggu' => 'badge-menunggu',
              'Proses'   => 'badge-proses',
              'Selesai'  => 'badge-selesai',
              default    => '',
            };
          @endphp
          <tr>
            <td class="muted">{{ $i + 1 }}</td>
            <td><span style="font-weight:600;color:#3b5bdb;font-size:11px;">{{ $item->kode_unik }}</span></td>
            <td style="font-weight:600;">{{ $item->pelanggan }}</td>
            <td class="muted">{{ $item->kerusakan }}</td>
            <td><span class="badge-status {{ $badgeClass }}">{{ $item->status }}</span></td>
            <td class="right" style="font-weight:600;">
              @if($item->biaya)
                Rp {{ number_format($item->biaya, 0, ',', '.') }}
              @else
                <span class="muted">—</span>
              @endif
            </td>
            <td class="muted">{{ $item->created_at->format('d/m/Y') }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5">Total Pendapatan (Servis Selesai)</td>
          <td class="right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  @endif

  <div class="footer">
    ServisApp &bull; Laporan {{ $namaBulan }} {{ $tahun }} &bull; Dicetak {{ now()->format('d/m/Y H:i') }}
  </div>

</body>
</html>