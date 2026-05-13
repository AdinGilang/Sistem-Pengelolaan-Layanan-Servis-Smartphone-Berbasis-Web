<x-app-layout>
    <x-slot name="header">Tambah Data Servis</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-md rounded-xl p-8">

            <form method="POST" action="{{ route('servis.store') }}" class="space-y-6" id="formServis">
                @csrf

                {{-- ── SECTION: INFO SERVIS ── --}}
                <div style="border-left:4px solid #3b5bdb;padding-left:12px;margin-bottom:4px;">
                    <div style="font-size:13px;font-weight:700;color:#1a1f36;">Informasi Servis</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" required value="{{ old('tanggal', date('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('tanggal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teknisi</label>
                        <select name="teknisi" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">— Pilih Teknisi —</option>
                            @foreach($teknisiList as $id => $name)
                                <option value="{{ $name }}" {{ old('teknisi') == $name ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                {{-- ── SECTION: ESTIMASI PENGERJAAN ── --}}
                <div style="border-left:4px solid #0ca678;padding-left:12px;margin-top:8px;">
                    <div style="font-size:13px;font-weight:700;color:#1a1f36;">Estimasi Pengerjaan</div>
                    <div style="font-size:11px;color:#8a93b2;">Opsional — isi jika sudah diketahui perkiraan waktu selesai</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Estimasi Selesai
                        </label>
                        <input type="date" name="estimasi_selesai"
                               value="{{ old('estimasi_selesai') }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               id="estimasi_selesai">
                        @error('estimasi_selesai')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Durasi Estimasi
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="estimasi_hari"
                                   value="{{ old('estimasi_hari') }}"
                                   min="0" max="365"
                                   placeholder="0"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   id="estimasi_hari">
                            <span class="text-sm text-gray-500 whitespace-nowrap">hari</span>
                            <input type="number" name="estimasi_jam"
                                   value="{{ old('estimasi_jam') }}"
                                   min="0" max="23"
                                   placeholder="0"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   id="estimasi_jam">
                            <span class="text-sm text-gray-500 whitespace-nowrap">jam</span>
                        </div>
                        @error('estimasi_hari')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        @error('estimasi_jam')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Info estimasi otomatis --}}
                <div id="estimasi_info" class="hidden">
                    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:10px 14px;font-size:12px;color:#166534;display:flex;align-items:center;gap:8px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span id="estimasi_text">Estimasi selesai dalam <strong id="estimasi_durasi"></strong></span>
                    </div>
                </div>

                {{-- ── SECTION: DATA PELANGGAN ── --}}
                <div style="border-left:4px solid #2f9e44;padding-left:12px;margin-top:8px;">
                    <div style="font-size:13px;font-weight:700;color:#1a1f36;">Data Pelanggan</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan <span class="text-red-500">*</span></label>
                    <input type="text" name="pelanggan" required value="{{ old('pelanggan') }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('pelanggan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <input type="text" name="alamat" value="{{ old('alamat') }}" placeholder="Jl. Contoh No. 1"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">📱</span>
                            <input type="text" name="no_wa" value="{{ old('no_wa') }}" placeholder="08123456789"
                                   class="w-full border-gray-300 rounded-r-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                {{-- ── SECTION: DATA HP ── --}}
                <div style="border-left:4px solid #f59f00;padding-left:12px;margin-top:8px;">
                    <div style="font-size:13px;font-weight:700;color:#1a1f36;">Data Handphone</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Merk HP</label>
                        <input type="text" name="merk_hp" value="{{ old('merk_hp') }}" placeholder="Samsung, Xiaomi, iPhone..."
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe HP</label>
                        <input type="text" name="tipe_hp" value="{{ old('tipe_hp') }}" placeholder="Galaxy A54, Redmi Note 12..."
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Kelengkapan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelengkapan</label>
                    <div class="flex flex-wrap gap-4">
                        @foreach(['SIM Card','Memori Card','Casing','Baterai','Charger','Earphone','Kardus'] as $item)
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="kelengkapan[]" value="{{ $item }}"
                                       {{ in_array($item, old('kelengkapan', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600">
                                {{ $item }}
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- ── SECTION: POLA KUNCI ── --}}
                <div style="border-left:4px solid #9c36b5;padding-left:12px;margin-top:8px;">
                    <div style="font-size:13px;font-weight:700;color:#1a1f36;">Pola Kunci</div>
                    <div style="font-size:11px;color:#8a93b2;">Hubungkan minimal 4 titik untuk membuat pola</div>
                </div>

                <div class="flex flex-col items-center gap-4">
                    {{-- Canvas Pola --}}
                    <div style="background:#f0f2f7;border-radius:16px;padding:20px;display:inline-block;">
                        <canvas id="patternCanvas" width="240" height="240"
                                style="cursor:crosshair;border-radius:12px;background:#fff;display:block;"></canvas>
                    </div>

                    <div class="flex items-center gap-4">
                        <div style="font-size:13px;color:#555;">
                            Titik terhubung: <strong id="dotCount">0</strong>
                            &nbsp;|&nbsp;
                            Pola: <strong id="patternDisplay" style="color:#3b5bdb;">—</strong>
                        </div>
                        <button type="button" onclick="resetPattern()"
                                class="px-4 py-1.5 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">
                            Reset Pola
                        </button>
                    </div>

                    <input type="hidden" name="pola_kunci" id="pola_kunci" value="{{ old('pola_kunci') }}">
                </div>


                {{-- ── SECTION: KATA SANDI (PIN) ── --}}
                <div style="border-left:4px solid #e03131;padding-left:12px;margin-top:8px;">
                    <div style="font-size:13px;font-weight:700;color:#1a1f36;">Kata Sandi / PIN</div>
                    <div style="font-size:11px;color:#8a93b2;">Opsional — PIN angka 4-6 digit untuk keamanan data perangkat</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            PIN Perangkat
                            <span class="text-gray-400 font-normal text-xs">(4-6 digit angka)</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="kata_sandi" id="kata_sandi_input"
                                   inputmode="numeric" pattern="[0-9]{4,6}"
                                   maxlength="6" placeholder="Contoh: 1234"
                                   value="{{ old('kata_sandi') }}"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-10"
                                   autocomplete="new-password">
                            <button type="button" onclick="togglePin('kata_sandi_input','eye_create')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg id="eye_create" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        @error('kata_sandi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin menggunakan PIN</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Konfirmasi PIN
                        </label>
                        <div class="relative">
                            <input type="password" id="kata_sandi_confirm"
                                   inputmode="numeric" pattern="[0-9]{4,6}"
                                   maxlength="6" placeholder="Ulangi PIN"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-10"
                                   autocomplete="new-password">
                            <button type="button" onclick="togglePin('kata_sandi_confirm','eye_confirm')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg id="eye_confirm" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        <div id="pin_match_msg" class="text-xs mt-1 hidden"></div>
                    </div>
                </div>

                {{-- PIN dots preview --}}
                <div id="pin_preview" class="hidden">
                    <div style="display:flex;align-items:center;gap:6px;padding:8px 12px;background:#f8f9ff;border-radius:10px;width:fit-content;">
                        <span style="font-size:11px;color:#8a93b2;margin-right:4px;">PIN:</span>
                        <span id="pin_dots" style="letter-spacing:4px;font-size:18px;color:#1a1f36;"></span>
                    </div>
                </div>

                {{-- ── SECTION: KERUSAKAN & STATUS ── --}}
                <div style="border-left:4px solid #e03131;padding-left:12px;margin-top:8px;">
                    <div style="font-size:13px;font-weight:700;color:#1a1f36;">Detail Kerusakan</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kerusakan <span class="text-red-500">*</span></label>
                    <textarea name="kerusakan" rows="4" required
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('kerusakan') }}</textarea>
                    @error('kerusakan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="Menunggu" {{ old('status','Menunggu') == 'Menunggu' ? 'selected':'' }}>Menunggu</option>
                            <option value="Proses"   {{ old('status') == 'Proses'   ? 'selected':'' }}>Proses</option>
                            <option value="Selesai"  {{ old('status') == 'Selesai'  ? 'selected':'' }}>Selesai</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya (Rp)</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Rp</span>
                            <input type="number" name="biaya" value="{{ old('biaya') }}" placeholder="0"
                                   class="w-full border-gray-300 rounded-r-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('servis.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Batal</a>
                    <button type="submit"
                            class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>

{{-- ── PATTERN LOCK SCRIPT ── --}}
<script>
const canvas  = document.getElementById('patternCanvas');
const ctx     = canvas.getContext('2d');
const SIZE    = 240;
const COLS    = 3;
const PADDING = 50;
const STEP    = (SIZE - PADDING * 2) / (COLS - 1);
const R       = 14; // dot radius

// 1-9 grid positions (numbered left-to-right, top-to-bottom)
const dots = [];
for (let r = 0; r < 3; r++) {
    for (let c = 0; c < 3; c++) {
        dots.push({
            id: r * 3 + c + 1,
            x: PADDING + c * STEP,
            y: PADDING + r * STEP,
            active: false,
        });
    }
}

let pattern  = [];
let drawing  = false;
let mousePos = { x: 0, y: 0 };

function draw() {
    ctx.clearRect(0, 0, SIZE, SIZE);

    // Draw connecting lines
    if (pattern.length > 1) {
        ctx.beginPath();
        ctx.moveTo(dots[pattern[0]-1].x, dots[pattern[0]-1].y);
        for (let i = 1; i < pattern.length; i++) {
            ctx.lineTo(dots[pattern[i]-1].x, dots[pattern[i]-1].y);
        }
        ctx.strokeStyle = 'rgba(59,91,219,0.6)';
        ctx.lineWidth = 3;
        ctx.lineJoin = 'round';
        ctx.stroke();

        // Line to current mouse if drawing
        if (drawing) {
            const last = dots[pattern[pattern.length-1]-1];
            ctx.beginPath();
            ctx.moveTo(last.x, last.y);
            ctx.lineTo(mousePos.x, mousePos.y);
            ctx.strokeStyle = 'rgba(59,91,219,0.3)';
            ctx.lineWidth = 2;
            ctx.stroke();
        }
    }

    // Draw dots
    dots.forEach(dot => {
        // Outer ring
        ctx.beginPath();
        ctx.arc(dot.x, dot.y, R + 4, 0, Math.PI * 2);
        ctx.fillStyle = dot.active ? 'rgba(59,91,219,0.15)' : 'rgba(200,200,200,0.2)';
        ctx.fill();

        // Inner dot
        ctx.beginPath();
        ctx.arc(dot.x, dot.y, R, 0, Math.PI * 2);
        ctx.fillStyle = dot.active ? '#3b5bdb' : '#c0c8e0';
        ctx.fill();

        // Center highlight
        ctx.beginPath();
        ctx.arc(dot.x, dot.y, R - 5, 0, Math.PI * 2);
        ctx.fillStyle = dot.active ? 'rgba(255,255,255,0.3)' : 'rgba(255,255,255,0.6)';
        ctx.fill();
    });
}

function getPos(e) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
    return {
        x: (clientX - rect.left) * scaleX,
        y: (clientY - rect.top)  * scaleY,
    };
}

function nearDot(pos) {
    for (const dot of dots) {
        const dx = dot.x - pos.x;
        const dy = dot.y - pos.y;
        if (Math.sqrt(dx*dx + dy*dy) < R + 8) return dot;
    }
    return null;
}

function updateUI() {
    document.getElementById('dotCount').textContent    = pattern.length;
    document.getElementById('patternDisplay').textContent = pattern.length ? pattern.join('-') : '—';
    document.getElementById('pola_kunci').value        = pattern.length >= 4 ? pattern.join('-') : '';
}

canvas.addEventListener('mousedown', e => {
    e.preventDefault();
    drawing = true;
    pattern = [];
    dots.forEach(d => d.active = false);
    const pos = getPos(e);
    const dot = nearDot(pos);
    if (dot) { dot.active = true; pattern.push(dot.id); }
    updateUI(); draw();
});

canvas.addEventListener('mousemove', e => {
    if (!drawing) return;
    e.preventDefault();
    mousePos = getPos(e);
    const dot = nearDot(mousePos);
    if (dot && !dot.active) {
        dot.active = true;
        pattern.push(dot.id);
        updateUI();
    }
    draw();
});

canvas.addEventListener('mouseup', e => {
    drawing = false;
    draw();
});

// Touch support
canvas.addEventListener('touchstart', e => { e.preventDefault(); canvas.dispatchEvent(new MouseEvent('mousedown', { clientX: e.touches[0].clientX, clientY: e.touches[0].clientY })); }, { passive: false });
canvas.addEventListener('touchmove',  e => { e.preventDefault(); canvas.dispatchEvent(new MouseEvent('mousemove', { clientX: e.touches[0].clientX, clientY: e.touches[0].clientY })); }, { passive: false });
canvas.addEventListener('touchend',   e => { e.preventDefault(); canvas.dispatchEvent(new MouseEvent('mouseup')); }, { passive: false });

function resetPattern() {
    pattern = [];
    dots.forEach(d => d.active = false);
    updateUI();
    draw();
}

// Init from old value (jika ada validasi error)
const oldPola = "{{ old('pola_kunci') }}";
if (oldPola) {
    const ids = oldPola.split('-').map(Number);
    ids.forEach(id => {
        const dot = dots.find(d => d.id === id);
        if (dot) { dot.active = true; pattern.push(id); }
    });
    updateUI();
}

draw();
</script>


<script>
// ── Auto-hitung tanggal estimasi dari durasi hari ────────────────────
const tanggalInput    = document.querySelector('[name="tanggal"]');
const estimasiTgl    = document.getElementById('estimasi_selesai');
const estimasiHari   = document.getElementById('estimasi_hari');
const estimasiJam    = document.getElementById('estimasi_jam');
const estimasiInfo   = document.getElementById('estimasi_info');
const estimasiDurasi = document.getElementById('estimasi_durasi');

function updateEstimasi() {
    const tanggal = tanggalInput?.value;
    const hari    = parseInt(estimasiHari?.value) || 0;
    const jam     = parseInt(estimasiJam?.value)  || 0;

    if (tanggal && (hari > 0 || jam > 0)) {
        const d = new Date(tanggal);
        d.setDate(d.getDate() + hari);
        estimasiTgl.value = d.toISOString().split('T')[0];
        const opts = { day:'numeric', month:'long', year:'numeric' };
        let txt = '';
        if (hari > 0) txt += hari + ' hari ';
        if (jam  > 0) txt += jam  + ' jam ';
        estimasiDurasi.textContent = txt.trim() + ' (' + d.toLocaleDateString('id-ID', opts) + ')';
        estimasiInfo.classList.remove('hidden');
    } else if (estimasiTgl.value && tanggal) {
        const t1 = new Date(tanggal), t2 = new Date(estimasiTgl.value);
        const diff = Math.round((t2 - t1) / 86400000);
        if (diff >= 0) {
            estimasiHari.value = diff;
            const opts = { day:'numeric', month:'long', year:'numeric' };
            estimasiDurasi.textContent = diff + ' hari (' + t2.toLocaleDateString('id-ID', opts) + ')';
            estimasiInfo.classList.remove('hidden');
        } else { estimasiInfo.classList.add('hidden'); }
    } else { estimasiInfo.classList.add('hidden'); }
}

estimasiHari?.addEventListener('input', updateEstimasi);
estimasiJam?.addEventListener('input',  updateEstimasi);
estimasiTgl?.addEventListener('change', function() {
    const tanggal = tanggalInput?.value;
    if (tanggal && this.value) {
        const t1 = new Date(tanggal), t2 = new Date(this.value);
        const diff = Math.round((t2 - t1) / 86400000);
        if (diff >= 0) { estimasiHari.value = diff; updateEstimasi(); }
    }
});
tanggalInput?.addEventListener('change', () => { if (estimasiHari?.value || estimasiJam?.value) updateEstimasi(); });
if ((estimasiHari?.value || estimasiJam?.value) && tanggalInput?.value) updateEstimasi();
</script>


<script>
// ── PIN toggle show/hide ───────────────────────────────────────────
function togglePin(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const eye   = document.getElementById(eyeId);
    if (!input) return;
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    eye.innerHTML = isHidden
        ? `<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/>
           <path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/>
           <line x1="1" y1="1" x2="23" y2="23"/>`
        : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
           <circle cx="12" cy="12" r="3"/>`;
}

// ── PIN validation & match ─────────────────────────────────────────
const pinInput   = document.getElementById('kata_sandi_input');
const pinConfirm = document.getElementById('kata_sandi_confirm');
const pinMsg     = document.getElementById('pin_match_msg');
const pinPreview = document.getElementById('pin_preview');
const pinDots    = document.getElementById('pin_dots');

function updatePinPreview(val) {
    if (val.length >= 4) {
        pinDots.textContent = '●'.repeat(val.length);
        pinPreview.classList.remove('hidden');
    } else {
        pinPreview.classList.add('hidden');
    }
}

function checkPinMatch() {
    const p1 = pinInput?.value;
    const p2 = pinConfirm?.value;
    if (!p2) { pinMsg.classList.add('hidden'); return; }
    pinMsg.classList.remove('hidden');
    if (p1 === p2) {
        pinMsg.textContent = '✅ PIN cocok';
        pinMsg.style.color = '#2f9e44';
    } else {
        pinMsg.textContent = '❌ PIN tidak cocok';
        pinMsg.style.color = '#e03131';
    }
}

// Hanya boleh input angka
[pinInput, pinConfirm].forEach(el => {
    el?.addEventListener('keypress', e => {
        if (!/[0-9]/.test(e.key)) e.preventDefault();
    });
    el?.addEventListener('paste', e => {
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        if (!/^\d+$/.test(paste)) e.preventDefault();
    });
});

pinInput?.addEventListener('input', function() {
    updatePinPreview(this.value);
    checkPinMatch();
});
pinConfirm?.addEventListener('input', checkPinMatch);

// Cegah submit jika PIN tidak cocok
document.getElementById('formServis')?.addEventListener('submit', function(e) {
    const p1 = pinInput?.value;
    const p2 = pinConfirm?.value;
    if (p1 && p2 && p1 !== p2) {
        e.preventDefault();
        pinMsg.classList.remove('hidden');
        pinMsg.textContent = '❌ PIN tidak cocok — periksa kembali';
        pinMsg.style.color = '#e03131';
        pinConfirm.focus();
    }
});
</script>

</x-app-layout>