<x-app-layout>
    <x-slot name="header">Detail Servis</x-slot>

    @php $userRole = auth()->user()->role ?? 'admin'; @endphp

    <div class="max-w-4xl mx-auto space-y-6">

        {{-- ── HEADER CARD ── --}}
        <div class="bg-white shadow-md rounded-2xl p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-2xl font-bold text-gray-800">{{ $servis->pelanggan }}</span>
                        @if($servis->status == 'Menunggu')
                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">Menunggu</span>
                        @elseif($servis->status == 'Proses')
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Proses</span>
                        @else
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Selesai</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-4 text-sm text-gray-500 flex-wrap">
                        <span class="font-mono font-semibold text-indigo-600">{{ $servis->kode_unik }}</span>
                        @if($servis->tanggal)
                            <span>📅 {{ $servis->tanggal->format('d F Y') }}</span>
                        @endif
                        @if($servis->teknisi)
                            <span>👨‍🔧 {{ $servis->teknisi }}</span>
                        @endif
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-xs text-gray-400 mb-1">Total Biaya</div>
                    <div class="text-2xl font-bold text-gray-800">
                        Rp {{ number_format($servis->biaya ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- ── CARD: DATA PELANGGAN ── --}}
            <div class="bg-white shadow-md rounded-2xl p-6 space-y-4">
                <div style="border-left:4px solid #2f9e44;padding-left:10px;">
                    <div class="font-bold text-gray-800 text-sm">Data Pelanggan</div>
                </div>

                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Nama</p>
                    <p class="font-semibold text-gray-800">{{ $servis->pelanggan }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Alamat</p>
                    <p class="text-gray-700">{{ $servis->alamat ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Nomor WhatsApp</p>
                    @if($servis->no_wa)
                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $servis->no_wa)) }}"
                           target="_blank"
                           class="inline-flex items-center gap-1.5 text-green-600 hover:underline font-medium">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            {{ $servis->no_wa }}
                        </a>
                    @else
                        <p class="text-gray-400">—</p>
                    @endif
                </div>
            </div>

            {{-- ── CARD: DATA HP ── --}}
            <div class="bg-white shadow-md rounded-2xl p-6 space-y-4">
                <div style="border-left:4px solid #f59f00;padding-left:10px;">
                    <div class="font-bold text-gray-800 text-sm">Data Handphone</div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Merk</p>
                        <p class="font-semibold text-gray-800">{{ $servis->merk_hp ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Tipe</p>
                        <p class="font-semibold text-gray-800">{{ $servis->tipe_hp ?? '—' }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-gray-400 mb-1.5">Kelengkapan</p>
                    @php
                        $kelengkapan = $servis->kelengkapan;
                        if (is_string($kelengkapan)) $kelengkapan = json_decode($kelengkapan, true) ?? [];
                        $kelengkapan = $kelengkapan ?? [];
                        $semua = ['SIM Card','Memori Card','Casing','Baterai','Charger','Earphone','Kardus'];
                    @endphp
                    <div class="flex flex-wrap gap-2">
                        @foreach($semua as $item)
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                {{ in_array($item, $kelengkapan)
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-100 text-gray-400 line-through' }}">
                                {{ $item }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── CARD: KERUSAKAN ── --}}
            <div class="bg-white shadow-md rounded-2xl p-6 space-y-4">
                <div style="border-left:4px solid #e03131;padding-left:10px;">
                    <div class="font-bold text-gray-800 text-sm">Detail Kerusakan</div>
                </div>

                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Deskripsi Kerusakan</p>
                    <p class="text-gray-700 leading-relaxed">{{ $servis->kerusakan }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Status</p>
                        @if($servis->status == 'Menunggu')
                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">Menunggu</span>
                        @elseif($servis->status == 'Proses')
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Proses</span>
                        @else
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Selesai</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Biaya</p>
                        <p class="font-bold text-gray-800">Rp {{ number_format($servis->biaya ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- ── CARD: POLA KUNCI ── --}}
            <div class="bg-white shadow-md rounded-2xl p-6 space-y-4">
                <div style="border-left:4px solid #9c36b5;padding-left:10px;">
                    <div class="font-bold text-gray-800 text-sm">Pola Kunci</div>
                </div>

                @if($servis->pola_kunci)
                    <div class="flex flex-col items-center gap-3">
                        <div style="background:#f0f2f7;border-radius:14px;padding:16px;display:inline-block;">
                            <canvas id="patternCanvas" width="180" height="180"
                                    style="border-radius:10px;background:#fff;display:block;"></canvas>
                        </div>
                        <div style="font-size:12px;color:#555;">
                            Urutan: <strong style="color:#3b5bdb;font-family:monospace;">{{ $servis->pola_kunci }}</strong>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-6 text-gray-400">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-2 opacity-40">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <p class="text-sm">Tidak ada pola kunci</p>
                    </div>
                @endif
            </div>

        </div>


            {{-- ── CARD: ESTIMASI PENGERJAAN ── --}}
            <div class="bg-white shadow-md rounded-2xl p-6 space-y-4 md:col-span-2">
                <div style="border-left:4px solid #0ca678;padding-left:10px;">
                    <div class="font-bold text-gray-800 text-sm">Estimasi Pengerjaan</div>
                </div>

                @php
                    $today   = now()->startOfDay();
                    $estTgl  = $servis->estimasi_selesai;
                    $selesai = $servis->status === 'Selesai';
                @endphp

                @if($estTgl || $servis->estimasi_hari)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        {{-- Tanggal masuk --}}
                        <div style="background:#f8f9ff;border-radius:10px;padding:12px 14px;">
                            <div class="text-xs text-gray-400 mb-1">Tanggal Masuk</div>
                            <div class="font-semibold text-gray-800 text-sm">
                                {{ $servis->tanggal ? $servis->tanggal->format('d F Y') : '—' }}
                            </div>
                        </div>

                        {{-- Estimasi selesai --}}
                        <div style="background:#f8f9ff;border-radius:10px;padding:12px 14px;">
                            <div class="text-xs text-gray-400 mb-1">Estimasi Selesai</div>
                            <div class="font-semibold text-gray-800 text-sm">
                                {{ $estTgl ? $estTgl->format('d F Y') : '—' }}
                            </div>
                            @if($servis->estimasi_hari || $servis->estimasi_jam)
                                <div class="text-xs text-gray-400 mt-0.5">
                                    @if($servis->estimasi_hari) {{ $servis->estimasi_hari }} hari @endif
                                    @if($servis->estimasi_jam)  {{ $servis->estimasi_jam }} jam @endif
                                    pengerjaan
                                </div>
                            @endif
                        </div>

                        {{-- Status estimasi --}}
                        <div style="border-radius:10px;padding:12px 14px;
                            {{ $selesai ? 'background:#f0fdf4;' : ($estTgl && $estTgl->lt($today) ? 'background:#fff7ed;' : 'background:#eff6ff;') }}">
                            <div class="text-xs mb-1"
                                style="{{ $selesai ? 'color:#166534;' : ($estTgl && $estTgl->lt($today) ? 'color:#9a3412;' : 'color:#1e40af;') }}">
                                Status Estimasi
                            </div>
                            @if($selesai)
                                <div class="font-semibold text-sm" style="color:#166534;">✅ Selesai Dikerjakan</div>
                            @elseif(!$estTgl)
                                <div class="font-semibold text-sm text-gray-500">— Belum ada estimasi</div>
                            @elseif($estTgl->lt($today))
                                <div class="font-semibold text-sm" style="color:#9a3412;">
                                    ⚠️ Terlambat {{ $today->diffInDays($estTgl) }} hari
                                </div>
                            @elseif($estTgl->isToday())
                                <div class="font-semibold text-sm" style="color:#92400e;">🔔 Selesai Hari Ini!</div>
                            @else
                                <div class="font-semibold text-sm" style="color:#1e40af;">
                                    🕐 Sisa {{ $today->diffInDays($estTgl) }} hari
                                </div>
                            @endif
                        </div>

                    </div>

                    {{-- Progress bar --}}
                    @if($servis->tanggal && $estTgl && !$selesai)
                        @php
                            $totalHari  = $servis->tanggal->diffInDays($estTgl);
                            $sudahHari  = $servis->tanggal->diffInDays($today);
                            $persen     = $totalHari > 0 ? min(100, round(($sudahHari / $totalHari) * 100)) : 0;
                            $barColor   = $persen >= 100 ? '#e03131' : ($persen >= 75 ? '#f59f00' : '#0ca678');
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Progress waktu pengerjaan</span>
                                <span>{{ $persen }}%</span>
                            </div>
                            <div style="background:#e5e7eb;border-radius:999px;height:8px;overflow:hidden;">
                                <div style="width:{{ $persen }}%;background:{{ $barColor }};height:100%;border-radius:999px;transition:width 0.5s;"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>{{ $servis->tanggal->format('d M Y') }}</span>
                                <span>{{ $estTgl->format('d M Y') }}</span>
                            </div>
                        </div>
                    @elseif($selesai)
                        <div style="background:#f0fdf4;border-radius:8px;padding:8px 12px;font-size:12px;color:#166534;text-align:center;">
                            ✅ Servis telah diselesaikan dengan status <strong>Selesai</strong>
                        </div>
                    @endif

                @else
                    <div class="flex items-center gap-2 text-gray-400 text-sm py-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Belum ada estimasi pengerjaan yang ditentukan.
                    </div>
                @endif
            </div>


            {{-- ── CARD: KATA SANDI / PIN ── --}}
            <div class="bg-white shadow-md rounded-2xl p-6 space-y-4">
                <div style="border-left:4px solid #e03131;padding-left:10px;">
                    <div class="font-bold text-gray-800 text-sm">Kata Sandi / PIN</div>
                </div>

                @if($servis->kata_sandi)
                    <div>
                        <p class="text-xs text-gray-400 mb-2">PIN Perangkat</p>

                        {{-- PIN tersimpan — tampil sebagai dots --}}
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div id="pin_display"
                                 style="font-size:24px;letter-spacing:6px;color:#1a1f36;font-weight:700;min-width:100px;">
                                ● ● ● ●
                            </div>
                            <button type="button" onclick="toggleShowPin()"
                                    id="pin_toggle_btn"
                                    style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:11px;color:#555;background:#f9fafb;cursor:pointer;">
                                <svg id="pin_eye_icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <span id="pin_toggle_text">Lihat PIN</span>
                            </button>
                        </div>

                        {{-- Verifikasi PIN sebelum tampil --}}
                        <div id="pin_verify_box" class="hidden mt-3">
                            <div style="background:#f8f9ff;border:1px solid #e0e4ff;border-radius:10px;padding:14px;">
                                <p class="text-xs text-gray-500 mb-2">Masukkan PIN untuk verifikasi:</p>
                                <div class="flex items-center gap-2">
                                    <input type="password" id="pin_verify_input"
                                           inputmode="numeric" maxlength="6"
                                           placeholder="PIN"
                                           class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm w-32 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                                           autocomplete="off">
                                    <button type="button" onclick="verifyPin()"
                                            class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                                        Verifikasi
                                    </button>
                                    <button type="button" onclick="cancelVerify()"
                                            class="px-3 py-1.5 bg-gray-200 text-gray-600 rounded-lg text-sm hover:bg-gray-300">
                                        Batal
                                    </button>
                                </div>
                                <p id="pin_verify_msg" class="text-xs mt-2 hidden"></p>
                            </div>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 flex items-center gap-1 mt-1">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        PIN disimpan terenkripsi
                    </div>
                @else
                    <div class="flex items-center gap-2 text-gray-400 text-sm py-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 9.9-1"/>
                        </svg>
                        Tidak ada PIN yang terpasang
                    </div>
                @endif
            </div>

        {{-- ── ACTION BUTTONS ── --}}
        <div class="flex justify-end gap-3 pb-6">
            <a href="{{ route('servis.index') }}"
               class="px-5 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 font-medium">
                ← Kembali
            </a>
            @if($userRole === 'admin')
                <a href="{{ route('servis.edit', $servis) }}"
                   class="px-5 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition">
                    ✏️ Edit Data
                </a>
            @endif
        </div>

    </div>

{{-- Pattern display script --}}
@if($servis->pola_kunci)
<script>
(function() {
    const canvas  = document.getElementById('patternCanvas');
    const ctx     = canvas.getContext('2d');
    const SIZE    = 180, COLS = 3, PADDING = 38;
    const STEP    = (SIZE - PADDING * 2) / (COLS - 1);
    const R       = 11;

    const dots = [];
    for (let r = 0; r < 3; r++)
        for (let c = 0; c < 3; c++)
            dots.push({ id: r*3+c+1, x: PADDING+c*STEP, y: PADDING+r*STEP });

    const pattern = "{{ $servis->pola_kunci }}".split('-').map(Number).filter(Boolean);

    ctx.clearRect(0, 0, SIZE, SIZE);

    // Lines
    if (pattern.length > 1) {
        ctx.beginPath();
        const first = dots.find(d => d.id === pattern[0]);
        ctx.moveTo(first.x, first.y);
        for (let i = 1; i < pattern.length; i++) {
            const d = dots.find(dot => dot.id === pattern[i]);
            ctx.lineTo(d.x, d.y);
        }
        ctx.strokeStyle = 'rgba(59,91,219,0.5)';
        ctx.lineWidth = 2.5;
        ctx.lineJoin = 'round';
        ctx.stroke();
    }

    // Dots
    dots.forEach(dot => {
        const active = pattern.includes(dot.id);

        ctx.beginPath();
        ctx.arc(dot.x, dot.y, R + 3, 0, Math.PI * 2);
        ctx.fillStyle = active ? 'rgba(59,91,219,0.12)' : 'rgba(200,200,200,0.15)';
        ctx.fill();

        ctx.beginPath();
        ctx.arc(dot.x, dot.y, R, 0, Math.PI * 2);
        ctx.fillStyle = active ? '#3b5bdb' : '#d0d5e8';
        ctx.fill();

        ctx.beginPath();
        ctx.arc(dot.x, dot.y, R - 4, 0, Math.PI * 2);
        ctx.fillStyle = active ? 'rgba(255,255,255,0.25)' : 'rgba(255,255,255,0.5)';
        ctx.fill();

        // Number label
        if (active) {
            ctx.fillStyle = '#fff';
            ctx.font = 'bold 9px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(dot.id, dot.x, dot.y);
        }
    });
})();
</script>
@endif


<script>
let pinVisible = false;
let pinVerified = false;
const servisId = {{ $servis->id }};

function toggleShowPin() {
    if (pinVerified) {
        // Sudah terverifikasi → toggle show/hide langsung
        pinVisible = !pinVisible;
        const display = document.getElementById('pin_display');
        const text    = document.getElementById('pin_toggle_text');
        const eye     = document.getElementById('pin_eye_icon');
        if (pinVisible) {
            // Fetch actual PIN via AJAX
            fetch(`/servis/${servisId}/pin-show`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest',
                           'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' }
            }).then(r => r.json()).then(data => {
                if (data.pin) {
                    display.textContent = data.pin;
                    display.style.fontSize = '20px';
                    display.style.letterSpacing = '4px';
                }
            });
            text.textContent = 'Sembunyikan';
            eye.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/>
                             <path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/>
                             <line x1="1" y1="1" x2="23" y2="23"/>`;
        } else {
            display.textContent = '● ● ● ●';
            display.style.fontSize = '24px';
            display.style.letterSpacing = '6px';
            text.textContent = 'Lihat PIN';
            eye.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                             <circle cx="12" cy="12" r="3"/>`;
        }
        return;
    }
    // Belum terverifikasi → tampilkan form verifikasi
    document.getElementById('pin_verify_box').classList.remove('hidden');
    document.getElementById('pin_verify_input').focus();
}

function verifyPin() {
    const input = document.getElementById('pin_verify_input').value;
    const msg   = document.getElementById('pin_verify_msg');
    if (!input) return;

    fetch(`/servis/${servisId}/pin-verify`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ pin: input })
    }).then(r => r.json()).then(data => {
        msg.classList.remove('hidden');
        if (data.success) {
            pinVerified = true;
            msg.textContent = '✅ PIN benar';
            msg.style.color = '#2f9e44';
            setTimeout(() => {
                document.getElementById('pin_verify_box').classList.add('hidden');
                pinVisible = false;
                toggleShowPin();
            }, 600);
        } else {
            msg.textContent = '❌ PIN salah';
            msg.style.color = '#e03131';
            document.getElementById('pin_verify_input').value = '';
            document.getElementById('pin_verify_input').focus();
        }
    });
}

function cancelVerify() {
    document.getElementById('pin_verify_box').classList.add('hidden');
    document.getElementById('pin_verify_input').value = '';
    document.getElementById('pin_verify_msg').classList.add('hidden');
}

// Enter key pada input PIN verify
document.getElementById('pin_verify_input')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); verifyPin(); }
    if (!/[0-9]/.test(e.key)) e.preventDefault();
});
</script>

</x-app-layout>