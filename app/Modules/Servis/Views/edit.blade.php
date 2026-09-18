<x-app-layout>
    <x-slot name="header">Edit Data Servis</x-slot>
    <x-slot name="backTo">{{ route('servis.index') }}</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-md rounded-xl p-8">

            <form method="POST" action="{{ route('servis.update', $servis) }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- ── SECTION: INFO SERVIS ── --}}
                <div style="border-left:4px solid #3b5bdb;padding-left:12px;margin-bottom:4px;">
                    <div style="font-size:13px;font-weight:700;color:#1a1f36;">Informasi Servis</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" required
                               value="{{ old('tanggal', $servis->tanggal?->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('tanggal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teknisi</label>
                        <select name="teknisi" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">— Pilih Teknisi —</option>
                            @foreach($teknisiList as $id => $name)
                                <option value="{{ $name }}" {{ old('teknisi', $servis->teknisi) == $name ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>


                {{-- ── SECTION: ESTIMASI PENGERJAAN ── --}}
                <div style="border-left:4px solid #0ca678;padding-left:12px;margin-top:8px;">
                    <div style="font-size:13px;font-weight:700;color:#1a1f36;">Estimasi Pengerjaan</div>
                    <div style="font-size:11px;color:#8a93b2;">Opsional — perkiraan waktu selesai perbaikan</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Estimasi Selesai
                        </label>
                        <input type="date" name="estimasi_selesai"
                               value="{{ old('estimasi_selesai', $servis->estimasi_selesai?->format('Y-m-d')) }}"
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
                                   value="{{ old('estimasi_hari', $servis->estimasi_hari) }}"
                                   min="0" max="365" placeholder="0"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   id="estimasi_hari">
                            <span class="text-sm text-gray-500 whitespace-nowrap">hari</span>
                            <input type="number" name="estimasi_jam"
                                   value="{{ old('estimasi_jam', $servis->estimasi_jam) }}"
                                   min="0" max="23" placeholder="0"
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

                {{-- Status estimasi (sudah lewat / tepat waktu / belum) --}}
                @php
                    $today = now()->startOfDay();
                    $estTgl = $servis->estimasi_selesai;
                    $selesai = $servis->status === 'Selesai';
                @endphp
                @if($estTgl)
                    <div style="border-radius:10px;padding:10px 14px;font-size:12px;display:flex;align-items:center;gap:8px;
                        {{ $selesai ? 'background:#f0fdf4;border:1px solid #86efac;color:#166534;' : ($estTgl->lt($today) ? 'background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;' : 'background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;') }}">
                        @if($selesai)
                            Servis sudah selesai dikerjakan.
                        @elseif($estTgl->lt($today))
                            Estimasi sudah terlewat {{ $today->diffInDays($estTgl) }} hari. Segera selesaikan!
                        @elseif($estTgl->isToday())
                            Estimasi selesai <strong>hari ini</strong>!
                        @else
                            Sisa waktu pengerjaan: <strong>{{ $today->diffInDays($estTgl) }} hari</strong>
                            (estimasi selesai {{ $estTgl->translatedFormat('d F Y') }})
                        @endif
                    </div>
                @endif

                {{-- ── SECTION: DATA PELANGGAN ── --}}
                <div style="border-left:4px solid #2f9e44;padding-left:12px;margin-top:8px;">
                    <div style="font-size:13px;font-weight:700;color:#1a1f36;">Data Pelanggan</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan <span class="text-red-500">*</span></label>
                    <input type="text" name="pelanggan" required
                           value="{{ old('pelanggan', $servis->pelanggan) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('pelanggan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <input type="text" name="alamat"
                               value="{{ old('alamat', $servis->alamat) }}" placeholder="Jl. Contoh No. 1"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                        <div class="flex">
                            {{-- Sebelumnya kotak ini kosong tanpa isi apa pun —
                                 sisa ikon yang tak sengaja ikut terhapus saat
                                 pembersihan emoji. --}}
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-green-600">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </span>
                            <input type="text" name="no_wa"
                                   value="{{ old('no_wa', $servis->no_wa) }}" placeholder="08123456789"
                                   inputmode="tel" autocomplete="tel"
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
                        <input type="text" name="merk_hp"
                               value="{{ old('merk_hp', $servis->merk_hp) }}" placeholder="Samsung, Xiaomi, iPhone..."
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe HP</label>
                        <input type="text" name="tipe_hp"
                               value="{{ old('tipe_hp', $servis->tipe_hp) }}" placeholder="Galaxy A54, Redmi Note 12..."
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Kelengkapan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelengkapan</label>
                    @php
                        $savedKelengkapan = old('kelengkapan', $servis->kelengkapan ?? []);
                        if (is_string($savedKelengkapan)) {
                            $savedKelengkapan = json_decode($savedKelengkapan, true) ?? [];
                        }
                    @endphp
                    <div class="flex flex-wrap gap-4">
                        @foreach(['SIM Card','Memori Card','Casing','Baterai','Charger','Earphone','Kardus'] as $item)
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="kelengkapan[]" value="{{ $item }}"
                                       {{ in_array($item, $savedKelengkapan) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600">
                                {{ $item }}
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- ── SECTION: POLA KUNCI ── --}}
                <div style="border-left:4px solid #9c36b5;padding-left:12px;margin-top:8px;">
                    <div style="font-size:13px;font-weight:700;color:#1a1f36;">Pola Kunci</div>
                    <div style="font-size:11px;color:#8a93b2;">Gambar ulang pola atau biarkan kosong jika tidak berubah</div>
                </div>

                <div class="flex flex-col items-center gap-4">
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

                    {{-- Sebelumnya pola yang lebih pendek dari 4 titik dibuang
                         diam-diam saat disimpan, tanpa peringatan apa pun. --}}
                    <p id="pola_kurang_msg" class="hidden text-xs" style="color:#e03131;">
                        Pola terlalu pendek — hubungkan minimal 4 titik agar tersimpan.
                    </p>

                    <input type="hidden" name="pola_kunci" id="pola_kunci" value="{{ old('pola_kunci', $servis->pola_kunci) }}">
                </div>


                {{-- ── SECTION: KATA SANDI (PIN) ── --}}
                <div style="border-left:4px solid #e03131;padding-left:12px;margin-top:8px;">
                    <div style="font-size:13px;font-weight:700;color:#1a1f36;">Kata Sandi / PIN</div>
                    <div style="font-size:11px;color:#8a93b2;">Kosongkan jika tidak ingin mengubah PIN yang sudah ada</div>
                </div>

                {{-- Info PIN status --}}
                @if($servis->kata_sandi)
                    <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;font-size:12px;color:#166534;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        PIN sudah terpasang. Isi field di bawah untuk menggantinya, atau kosongkan untuk mempertahankan PIN lama.
                    </div>
                @else
                    <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;font-size:12px;color:#9a3412;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 9.9-1"/>
                        </svg>
                        Belum ada PIN. Isi field di bawah untuk menambahkan.
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            PIN Baru
                            <span class="text-gray-400 font-normal text-xs">(4-6 digit angka)</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="kata_sandi" id="kata_sandi_input"
                                   inputmode="numeric" pattern="[0-9]{4,6}"
                                   maxlength="6" placeholder="{{ $servis->kata_sandi ? 'Isi untuk ganti PIN' : 'Contoh: 1234' }}"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-10"
                                   autocomplete="new-password">
                            <button type="button" onclick="togglePin('kata_sandi_input','eye_edit')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg id="eye_edit" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        @error('kata_sandi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Konfirmasi PIN Baru
                        </label>
                        <div class="relative">
                            <input type="password" id="kata_sandi_confirm"
                                   inputmode="numeric" pattern="[0-9]{4,6}"
                                   maxlength="6" placeholder="Ulangi PIN baru"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-10"
                                   autocomplete="new-password">
                            <button type="button" onclick="togglePin('kata_sandi_confirm','eye_confirm_edit')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg id="eye_confirm_edit" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        <div id="pin_match_msg" class="text-xs mt-1 hidden"></div>
                    </div>
                </div>

                {{-- ── SECTION: KERUSAKAN & STATUS ── --}}
                <div style="border-left:4px solid #e03131;padding-left:12px;margin-top:8px;">
                    <div style="font-size:13px;font-weight:700;color:#1a1f36;">Detail Kerusakan</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kerusakan <span class="text-red-500">*</span></label>
                    <textarea name="kerusakan" rows="4" required
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('kerusakan', $servis->kerusakan) }}</textarea>
                    @error('kerusakan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(old('status', $servis->status) === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya (Rp)</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Rp</span>
                            <input type="number" name="biaya"
                                   value="{{ old('biaya', $servis->biaya) }}" placeholder="0"
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
                        Update
                    </button>
                </div>

            </form>
        </div>
    </div>

<script>
const canvas  = document.getElementById('patternCanvas');
const ctx     = canvas.getContext('2d');
const SIZE    = 240, COLS = 3, PADDING = 50;
const STEP    = (SIZE - PADDING * 2) / (COLS - 1);
const R       = 14;

const dots = [];
for (let r = 0; r < 3; r++)
    for (let c = 0; c < 3; c++)
        dots.push({ id: r*3+c+1, x: PADDING+c*STEP, y: PADDING+r*STEP, active: false });

let pattern = [], drawing = false, mousePos = {x:0,y:0};

function draw() {
    ctx.clearRect(0, 0, SIZE, SIZE);
    if (pattern.length > 1) {
        ctx.beginPath();
        ctx.moveTo(dots[pattern[0]-1].x, dots[pattern[0]-1].y);
        for (let i = 1; i < pattern.length; i++)
            ctx.lineTo(dots[pattern[i]-1].x, dots[pattern[i]-1].y);
        ctx.strokeStyle = 'rgba(59,91,219,0.6)'; ctx.lineWidth = 3; ctx.lineJoin = 'round'; ctx.stroke();
        if (drawing) {
            const last = dots[pattern[pattern.length-1]-1];
            ctx.beginPath(); ctx.moveTo(last.x, last.y); ctx.lineTo(mousePos.x, mousePos.y);
            ctx.strokeStyle = 'rgba(59,91,219,0.3)'; ctx.lineWidth = 2; ctx.stroke();
        }
    }
    dots.forEach(dot => {
        ctx.beginPath(); ctx.arc(dot.x, dot.y, R+4, 0, Math.PI*2);
        ctx.fillStyle = dot.active ? 'rgba(59,91,219,0.15)' : 'rgba(200,200,200,0.2)'; ctx.fill();
        ctx.beginPath(); ctx.arc(dot.x, dot.y, R, 0, Math.PI*2);
        ctx.fillStyle = dot.active ? '#3b5bdb' : '#c0c8e0'; ctx.fill();
        ctx.beginPath(); ctx.arc(dot.x, dot.y, R-5, 0, Math.PI*2);
        ctx.fillStyle = dot.active ? 'rgba(255,255,255,0.3)' : 'rgba(255,255,255,0.6)'; ctx.fill();
    });
}

function getPos(e) {
    const rect = canvas.getBoundingClientRect();
    return { x: (e.clientX - rect.left) * (canvas.width/rect.width), y: (e.clientY - rect.top) * (canvas.height/rect.height) };
}
function nearDot(pos) {
    return dots.find(d => Math.hypot(d.x-pos.x, d.y-pos.y) < R+8) || null;
}
function updateUI() {
    document.getElementById('dotCount').textContent = pattern.length;
    document.getElementById('patternDisplay').textContent = pattern.length ? pattern.join('-') : '—';
    document.getElementById('pola_kunci').value = pattern.length >= 4 ? pattern.join('-') : '';

    const pesanKurang = document.getElementById('pola_kurang_msg');
    pesanKurang?.classList.toggle('hidden', !(pattern.length > 0 && pattern.length < 4));
}

canvas.addEventListener('mousedown', e => { e.preventDefault(); drawing=true; pattern=[]; dots.forEach(d=>d.active=false); const dot=nearDot(getPos(e)); if(dot){dot.active=true;pattern.push(dot.id);} updateUI(); draw(); });
canvas.addEventListener('mousemove', e => { if(!drawing)return; mousePos=getPos(e); const dot=nearDot(mousePos); if(dot&&!dot.active){dot.active=true;pattern.push(dot.id);updateUI();} draw(); });
canvas.addEventListener('mouseup', () => { drawing=false; draw(); });

function resetPattern() { pattern=[]; dots.forEach(d=>d.active=false); updateUI(); draw(); }

// Load existing pattern
const oldPola = "{{ old('pola_kunci', $servis->pola_kunci) }}";
if (oldPola) {
    oldPola.split('-').map(Number).forEach(id => {
        const dot = dots.find(d => d.id === id);
        if (dot) { dot.active=true; pattern.push(id); }
    });
    updateUI();
}
draw();
</script>


<script>
const tanggalInput   = document.querySelector('[name="tanggal"]');
const estimasiTgl  = document.getElementById('estimasi_selesai');
const estimasiHari = document.getElementById('estimasi_hari');
const estimasiJam  = document.getElementById('estimasi_jam');

function calcFromHari() {
    const tanggal = tanggalInput?.value;
    const hari    = parseInt(estimasiHari?.value) || 0;
    if (tanggal && hari >= 0) {
        const d = new Date(tanggal);
        d.setDate(d.getDate() + hari);
        estimasiTgl.value = d.toISOString().split('T')[0];
    }
}
function calcFromTgl() {
    const tanggal = tanggalInput?.value;
    if (tanggal && estimasiTgl?.value) {
        const t1   = new Date(tanggal);
        const t2   = new Date(estimasiTgl.value);
        const diff = Math.round((t2 - t1) / 86400000);
        if (diff >= 0) estimasiHari.value = diff;
    }
}
estimasiHari?.addEventListener('input', calcFromHari);
estimasiJam?.addEventListener('input',  calcFromHari);
estimasiTgl?.addEventListener('change', calcFromTgl);
tanggalInput?.addEventListener('change', function() {
    if (estimasiHari?.value || estimasiJam?.value) calcFromHari();
});
</script>


<script>
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

const pinInput   = document.getElementById('kata_sandi_input');
const pinConfirm = document.getElementById('kata_sandi_confirm');
const pinMsg     = document.getElementById('pin_match_msg');

function checkPinMatch() {
    const p1 = pinInput?.value;
    const p2 = pinConfirm?.value;
    if (!p2 && !p1) { pinMsg.classList.add('hidden'); return; }
    if (p1 && !p2)  { pinMsg.classList.add('hidden'); return; }
    pinMsg.classList.remove('hidden');
    if (p1 === p2) {
        pinMsg.textContent = 'PIN cocok';
        pinMsg.style.color = '#2f9e44';
    } else {
        pinMsg.textContent = 'PIN tidak cocok';
        pinMsg.style.color = '#e03131';
    }
}

[pinInput, pinConfirm].forEach(el => {
    el?.addEventListener('keypress', e => { if (!/[0-9]/.test(e.key)) e.preventDefault(); });
});

pinInput?.addEventListener('input', checkPinMatch);
pinConfirm?.addEventListener('input', checkPinMatch);

document.querySelector('form')?.addEventListener('submit', function(e) {
    const p1 = pinInput?.value;
    const p2 = pinConfirm?.value;
    if (p1 && p2 && p1 !== p2) {
        e.preventDefault();
        pinMsg.classList.remove('hidden');
        pinMsg.textContent = 'PIN tidak cocok — periksa kembali';
        pinMsg.style.color = '#e03131';
        pinConfirm.focus();
    }
});
</script>

</x-app-layout>