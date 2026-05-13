<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Servis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        * { font-family: 'Inter', sans-serif; }

        .fade-in { animation: fadeInUp 0.5s ease-out forwards; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .pulse-dot {
            animation: pulseDot 1.8s ease-in-out infinite;
        }
        @keyframes pulseDot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        input:focus { outline: none; }
    </style>
</head>

<body style="background: #f0f4ff; min-height: 100vh; padding: 0; margin: 0;">

<div class="max-w-lg mx-auto px-4 py-8">

    {{-- Top Bar --}}
    <div class="flex items-center gap-3 mb-6">
        <div style="width:44px; height:44px; background:#4f46e5; border-radius:14px;
                    display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="22" height="22" fill="none" stroke="white" stroke-width="2.2"
                 viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <rect x="5" y="2" width="14" height="20" rx="2"/>
                <circle cx="12" cy="17" r="1" fill="white" stroke="none"/>
            </svg>
        </div>
        <div>
            <div style="font-size:15px; font-weight:800; color:#1e1b4b; line-height:1.2;">
                Servis Smartphone
            </div>
            <div style="font-size:11px; color:#6366f1; font-weight:600;">
                Cek status perbaikan HP kamu
            </div>
        </div>
    </div>

    {{-- Search Card --}}
    <div style="background:#fff; border-radius:20px; padding:20px;
                border:1.5px solid #e0e7ff; margin-bottom:16px;">

        <div style="font-size:11px; font-weight:800; color:#4f46e5;
                    text-transform:uppercase; letter-spacing:.06em; margin-bottom:10px;">
            Masukkan kode servis
        </div>

        <form method="GET" action="{{ route('servis.cek') }}">
            <div class="flex flex-col sm:flex-row gap-3">
                <input
                    type="text"
                    name="kode"
                    value="{{ request('kode') }}"
                    placeholder="Contoh: SRV-2026-XXXXXX"
                    required
                    style="flex:1; padding:12px 16px; background:#f5f3ff;
                           border:1.5px solid #c7d2fe; border-radius:12px;
                           font-size:14px; color:#312e81; width:100%;"
                    onfocus="this.style.borderColor='#4f46e5'; this.style.boxShadow='0 0 0 3px #e0e7ff';"
                    onblur="this.style.borderColor='#c7d2fe'; this.style.boxShadow='none';"
                />
                <button
                    type="submit"
                    style="background:#4f46e5; color:#fff; border:none; border-radius:12px;
                           padding:12px 22px; font-size:14px; font-weight:700;
                           cursor:pointer; display:flex; align-items:center;
                           justify-content:center; gap:6px; white-space:nowrap;
                           transition: background .15s;"
                    onmouseover="this.style.background='#4338ca'"
                    onmouseout="this.style.background='#4f46e5'"
                >
                    <svg width="15" height="15" fill="none" stroke="currentColor"
                         stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Cek Sekarang
                </button>
            </div>
        </form>

        <div style="font-size:11px; color:#818cf8; text-align:center; margin-top:10px;">
            Kode servis ada di struk atau pesan WhatsApp dari staf kami
        </div>
    </div>

    {{-- Kode tidak ditemukan --}}
    @if(request('kode') && !$servis)
        <div style="background:#fff1f2; border:1.5px solid #fecdd3; border-radius:16px;
                    padding:16px 20px; display:flex; align-items:center; gap:12px;"
             class="fade-in">
            <div style="width:40px; height:40px; background:#ffe4e6; border-radius:12px;
                        display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#e11d48" stroke-width="2.5"
                     viewBox="0 0 24 24" stroke-linecap="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>
            <div>
                <div style="font-size:14px; font-weight:700; color:#9f1239;">Kode tidak ditemukan</div>
                <div style="font-size:12px; color:#fb7185; margin-top:2px;">
                    Periksa kembali kode servis kamu
                </div>
            </div>
        </div>
    @endif

    {{-- Hasil ditemukan --}}
    @if($servis)
        <div style="background:#fff; border-radius:20px; border:1.5px solid #e0e7ff;
                    overflow:hidden;" class="fade-in">

            {{-- Header card --}}
            <div style="background:#4f46e5; padding:18px 20px;
                        display:flex; align-items:center; gap:14px;">
                <div style="width:48px; height:48px; border-radius:14px;
                            background:rgba(255,255,255,.18);
                            display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="24" height="24" fill="none" stroke="white" stroke-width="2"
                         viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="5" y="2" width="14" height="20" rx="2"/>
                        <circle cx="12" cy="17" r="1" fill="white" stroke="none"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:14px; font-weight:800; color:#fff; letter-spacing:.03em;">
                        {{ $servis->kode_unik }}
                    </div>
                    <div style="font-size:12px; color:#c7d2fe; margin-top:3px;">
                        {{ $servis->merk_hp ?? '' }} {{ $servis->tipe_hp ?? '' }}
                    </div>
                </div>
            </div>

            {{-- Status badge strip --}}
            <div style="padding:12px 20px; display:flex; align-items:center;
                        justify-content:space-between; border-bottom:1px solid #e0e7ff;">
                <span style="font-size:10px; color:#818cf8; font-weight:700;
                             text-transform:uppercase; letter-spacing:.06em;">
                    Status saat ini
                </span>

                @if($servis->status == 'Menunggu')
                    <span style="background:#fef3c7; color:#92400e; padding:5px 14px;
                                 border-radius:20px; font-size:12px; font-weight:700;
                                 display:inline-flex; align-items:center; gap:6px;">
                        <span class="pulse-dot" style="width:7px; height:7px; border-radius:50%;
                              background:#f59e0b; display:inline-block;"></span>
                        Menunggu
                    </span>
                @elseif($servis->status == 'Proses')
                    <span style="background:#dbeafe; color:#1e40af; padding:5px 14px;
                                 border-radius:20px; font-size:12px; font-weight:700;
                                 display:inline-flex; align-items:center; gap:6px;">
                        <span class="pulse-dot" style="width:7px; height:7px; border-radius:50%;
                              background:#3b82f6; display:inline-block;"></span>
                        Sedang Diproses
                    </span>
                @else
                    <span style="background:#d1fae5; color:#065f46; padding:5px 14px;
                                 border-radius:20px; font-size:12px; font-weight:700;
                                 display:inline-flex; align-items:center; gap:6px;">
                        <span style="width:7px; height:7px; border-radius:50%;
                              background:#10b981; display:inline-block;"></span>
                        Selesai
                    </span>
                @endif
            </div>

            {{-- Detail fields --}}
            <div style="padding:16px 20px; display:flex; flex-direction:column; gap:14px;">

                <div>
                    <div style="font-size:10px; color:#a5b4fc; font-weight:700;
                                text-transform:uppercase; letter-spacing:.06em; margin-bottom:3px;">
                        Nama Pelanggan
                    </div>
                    <div style="font-size:15px; font-weight:700; color:#1e1b4b;">
                        {{ $servis->pelanggan }}
                    </div>
                </div>

                <div>
                    <div style="font-size:10px; color:#a5b4fc; font-weight:700;
                                text-transform:uppercase; letter-spacing:.06em; margin-bottom:3px;">
                        Jenis Kerusakan
                    </div>
                    <div style="font-size:15px; font-weight:700; color:#1e1b4b;">
                        {{ $servis->kerusakan }}
                    </div>
                </div>

                @if($servis->teknisi)
                <div>
                    <div style="font-size:10px; color:#a5b4fc; font-weight:700;
                                text-transform:uppercase; letter-spacing:.06em; margin-bottom:3px;">
                        Teknisi
                    </div>
                    <div style="font-size:15px; font-weight:700; color:#1e1b4b;">
                        {{ $servis->teknisi }}
                    </div>
                </div>
                @endif

            </div>

            {{-- Biaya --}}
            @if($servis->biaya)
            <div style="margin:0 20px 16px; background:#f5f3ff; border-radius:14px;
                        padding:14px 18px; display:flex; align-items:center;
                        justify-content:space-between;">
                <div>
                    <div style="font-size:10px; color:#6366f1; font-weight:700;
                                text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px;">
                        Total Biaya
                    </div>
                    <div style="font-size:22px; font-weight:800; color:#312e81;">
                        Rp {{ number_format($servis->biaya, 0, ',', '.') }}
                    </div>
                </div>
                <div style="width:44px; height:44px; background:#ede9fe; border-radius:12px;
                            display:flex; align-items:center; justify-content:center;">
                    <svg width="22" height="22" fill="none" stroke="#6366f1" stroke-width="2"
                         viewBox="0 0 24 24" stroke-linecap="round">
                        <rect x="2" y="5" width="20" height="14" rx="2"/>
                        <line x1="2" y1="10" x2="22" y2="10"/>
                    </svg>
                </div>
            </div>
            @endif

            {{-- Progress Steps --}}
            <div style="padding:0 20px 20px;">
                <div style="font-size:10px; color:#a5b4fc; font-weight:700;
                            text-transform:uppercase; letter-spacing:.06em; margin-bottom:14px;">
                    Progres Pengerjaan
                </div>

                @php
                    $step = 1;
                    if($servis->status == 'Proses') $step = 2;
                    if($servis->status == 'Selesai') $step = 3;
                @endphp

                {{-- Step 1 --}}
                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <div style="display:flex; flex-direction:column; align-items:center;">
                        <div style="width:26px; height:26px; border-radius:50%; flex-shrink:0;
                                    display:flex; align-items:center; justify-content:center;
                                    font-size:11px; font-weight:800;
                                    background:{{ $step >= 1 ? '#10b981' : '#e0e7ff' }};
                                    color:{{ $step >= 1 ? '#fff' : '#a5b4fc' }};">
                            @if($step > 1) ✓ @else 1 @endif
                        </div>
                        <div style="width:2px; height:24px;
                                    background:{{ $step > 1 ? '#10b981' : '#e0e7ff' }};"></div>
                    </div>
                    <div style="padding-top:4px; padding-bottom:12px;">
                        <div style="font-size:13px; font-weight:700;
                                    color:{{ $step >= 1 ? '#1e1b4b' : '#a5b4fc' }};">
                            HP diterima
                        </div>
                        <div style="font-size:11px; color:#818cf8; margin-top:1px;">
                            Perangkat sudah diterima staf
                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <div style="display:flex; flex-direction:column; align-items:center;">
                        <div style="width:26px; height:26px; border-radius:50%; flex-shrink:0;
                                    display:flex; align-items:center; justify-content:center;
                                    font-size:11px; font-weight:800;
                                    background:{{ $step == 2 ? '#4f46e5' : ($step > 2 ? '#10b981' : '#e0e7ff') }};
                                    color:{{ $step >= 2 ? '#fff' : '#a5b4fc' }};">
                            @if($step > 2) ✓ @else 2 @endif
                        </div>
                        <div style="width:2px; height:24px;
                                    background:{{ $step > 2 ? '#10b981' : '#e0e7ff' }};"></div>
                    </div>
                    <div style="padding-top:4px; padding-bottom:12px;">
                        <div style="font-size:13px; font-weight:700;
                                    color:{{ $step >= 2 ? '#1e1b4b' : '#a5b4fc' }};">
                            Sedang diperbaiki
                        </div>
                        <div style="font-size:11px; color:#818cf8; margin-top:1px;">
                            Teknisi sedang mengerjakan
                        </div>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <div style="display:flex; flex-direction:column; align-items:center;">
                        <div style="width:26px; height:26px; border-radius:50%; flex-shrink:0;
                                    display:flex; align-items:center; justify-content:center;
                                    font-size:11px; font-weight:800;
                                    background:{{ $step == 3 ? '#10b981' : '#e0e7ff' }};
                                    color:{{ $step == 3 ? '#fff' : '#a5b4fc' }};">
                            @if($step == 3) ✓ @else 3 @endif
                        </div>
                    </div>
                    <div style="padding-top:4px;">
                        <div style="font-size:13px; font-weight:700;
                                    color:{{ $step == 3 ? '#1e1b4b' : '#a5b4fc' }};">
                            Siap diambil
                        </div>
                        <div style="font-size:11px; color:#818cf8; margin-top:1px;">
                            HP selesai diperbaiki
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- Footer --}}
    <div style="text-align:center; font-size:11px; color:#a5b4fc; margin-top:24px;">
        © {{ date('Y') }} Servis Smartphone PHONE REPAIR
    </div>

</div>

</body>
</html>