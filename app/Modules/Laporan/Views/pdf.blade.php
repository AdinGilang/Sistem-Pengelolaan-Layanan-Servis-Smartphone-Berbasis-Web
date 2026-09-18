<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1a1f36; background: #fff; }

  .header { background: #1a1f36; color: #fff; padding: 18px 24px; margin-bottom: 20px; border-radius: 8px; }
  .header h1 { font-size: 18px; font-weight: 700; letter-spacing: -0.5px; }
  .header p  { font-size: 12px; color: rgba(255,255,255,0.65); margin-top: 3px; }
  .header .badge {
    display: inline-block;
    background: #3b5bdb;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 6px;
    margin-top: 6px;
  }

  .stats { display: flex; gap: 12px; margin-bottom: 20px; }
  .stat-box {
    flex: 1;
    border: 1.5px solid #e8eaf0;
    border-radius: 8px;
    padding: 12px 14px;
    text-align: center;
  }
  .stat-box .label { font-size: 10px; font-weight: 600; color: #8a93b2; text-transform: uppercase; letter-spacing: .5px; }
  .stat-box .value { font-size: 26px; font-weight: 700; color: #1a1f36; margin-top: 2px; }
  .stat-box .sub   { font-size: 11px; color: #8a93b2; margin-top: 2px; }
  .stat-box.blue   { border-top: 3px solid #3b5bdb; }
  .stat-box.yellow { border-top: 3px solid #f59f00; }
  .stat-box.purple { border-top: 3px solid #9c36b5; }
  .stat-box.green  { border-top: 3px solid #2f9e44; }

  table { width: 100%; border-collapse: collapse; font-size: 11px; }
  thead tr { background: #1a1f36; color: #fff; }
  thead th { padding: 9px 10px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
  thead th.right { text-align: right; }

  tbody tr:nth-child(even) { background: #f7f8fc; }
  tbody tr { border-bottom: 1px solid #f0f2f7; }
  tbody td { padding: 9px 10px; color: #1a1f36; }
  tbody td.right { text-align: right; }
  tbody td.muted { color: #8a93b2; }

  .badge-status {
    display: inline-block;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
  }
  .badge-menunggu { background: rgba(245,159,0,0.15); color: #d08700; }
  .badge-proses   { background: rgba(156,54,181,0.15); color: #8a2be2; }
  .badge-selesai  { background: rgba(47,158,68,0.15);  color: #2f9e44; }

  tfoot tr { background: #f0f2f7; }
  tfoot td { padding: 10px; font-weight: 700; font-size: 12px; }
  tfoot td.right { text-align: right; color: #2f9e44; }

  .footer { margin-top: 20px; text-align: right; font-size: 10px; color: #8a93b2; }
</style>
</head>
<body>

  {{-- HEADER --}}
  <div class="header">
    <h1>Laporan Servis</h1>
    <p>ServisApp — Dicetak pada {{ now()->format('d F Y, H:i') }}</p>
    <span class="badge">{{ $namaBulan }} {{ $tahun }}</span>
  </div>

  {{-- STAT BOXES --}}
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
      <div class="label">Pendapatan</div>
      <div class="value" style="font-size:16px;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
      <div class="sub">Dari servis selesai</div>
    </div>
  </div>

  {{-- TABLE --}}
  @if($data->isEmpty())
    <p style="text-align:center;color:#8a93b2;padding:32px 0;">Tidak ada data untuk periode ini.</p>
  @else
    <table>
      <thead>
        <tr>
          <th style="width:30px;">No</th>
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
            <td><span style="font-weight:600;color:#3b5bdb;">{{ $item->kode_unik }}</span></td>
            <td style="font-weight:600;">{{ $item->pelanggan }}</td>
            <td class="muted">{{ Str::limit($item->kerusakan, 35) }}</td>
            <td><span class="badge-status {{ $badgeClass }}">{{ $item->status }}</span></td>
            <td class="right">
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
    ServisApp &bull; Laporan {{ $namaBulan }} {{ $tahun }} &bull; {{ now()->format('d/m/Y H:i') }}
  </div>

</body>
</html>