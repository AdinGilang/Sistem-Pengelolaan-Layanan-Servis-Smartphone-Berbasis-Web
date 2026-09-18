@push('scripts')
<script>
/**
 * Kredensial perangkat pada halaman detail servis.
 *
 * Perbaikan dibanding versi sebelumnya:
 *
 * 1. Pola kunci dikirim ke JavaScript melalui direktif json milik Blade,
 *    bukan ditempel langsung ke dalam literal string. Nilai yang mengandung
 *    tanda petik atau garis miring terbalik dulu bisa merusak sintaks skrip
 *    di halaman ini.
 *
 * 2. Semua perilaku dipasang dengan addEventListener, bukan atribut onclick.
 *
 * 3. Jawaban server yang bukan 200 (PIN salah, terlalu sering mencoba,
 *    akses ditolak) kini ditangani. Sebelumnya respon gagal tetap dibaca
 *    sebagai JSON sukses sehingga pesannya tidak pernah muncul.
 */
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const servisId = @json($servis->id);

    /* ── Gambar pola kunci ───────────────────────────────────────────── */
    const canvas = document.getElementById('patternCanvas');
    const polaMentah = @json($servis->pola_kunci);

    if (canvas && polaMentah) {
        const ctx = canvas.getContext('2d');
        const UKURAN = 180;
        const SISI = 3;
        const TEPI = 38;
        const JARAK = (UKURAN - TEPI * 2) / (SISI - 1);
        const JARI = 11;

        const titik = [];
        for (let baris = 0; baris < SISI; baris++) {
            for (let kolom = 0; kolom < SISI; kolom++) {
                titik.push({
                    id: baris * SISI + kolom + 1,
                    x: TEPI + kolom * JARAK,
                    y: TEPI + baris * JARAK,
                });
            }
        }

        const pola = String(polaMentah)
            .split('-')
            .map(Number)
            .filter((n) => Number.isInteger(n) && n >= 1 && n <= 9);

        ctx.clearRect(0, 0, UKURAN, UKURAN);

        if (pola.length > 1) {
            ctx.beginPath();
            const awal = titik.find((t) => t.id === pola[0]);
            ctx.moveTo(awal.x, awal.y);

            for (let i = 1; i < pola.length; i++) {
                const t = titik.find((d) => d.id === pola[i]);
                ctx.lineTo(t.x, t.y);
            }

            ctx.strokeStyle = 'rgba(59,91,219,.55)';
            ctx.lineWidth = 2.5;
            ctx.lineJoin = 'round';
            ctx.stroke();
        }

        titik.forEach((t) => {
            const aktif = pola.includes(t.id);

            ctx.beginPath();
            ctx.arc(t.x, t.y, JARI + 3, 0, Math.PI * 2);
            ctx.fillStyle = aktif ? 'rgba(59,91,219,.12)' : 'rgba(200,200,200,.15)';
            ctx.fill();

            ctx.beginPath();
            ctx.arc(t.x, t.y, JARI, 0, Math.PI * 2);
            ctx.fillStyle = aktif ? '#3b5bdb' : '#d0d5e8';
            ctx.fill();

            if (aktif) {
                ctx.fillStyle = '#fff';
                ctx.font = 'bold 9px system-ui, sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(String(t.id), t.x, t.y);
            }
        });
    }

    /* ── Verifikasi PIN ──────────────────────────────────────────────── */
    const tombolLihat  = document.getElementById('pin_toggle_trigger');
    const kotakVerify  = document.getElementById('pin_verify_box');
    const inputVerify  = document.getElementById('pin_verify_input');
    const pesanVerify  = document.getElementById('pin_verify_msg');
    const tampilanPin  = document.getElementById('pin_display');
    const labelToggle  = document.getElementById('pin_toggle_text');

    if (!tombolLihat) {
        return;
    }

    let sudahTerverifikasi = false;

    function tampilkanPesan(teks, warna) {
        if (!pesanVerify) {
            return;
        }

        pesanVerify.classList.remove('hidden');
        pesanVerify.textContent = teks;
        pesanVerify.style.color = warna;
    }

    tombolLihat.addEventListener('click', function () {
        if (sudahTerverifikasi) {
            bukaKeterangan();
            return;
        }

        kotakVerify?.classList.remove('hidden');
        inputVerify?.focus();
    });

    function bukaKeterangan() {
        fetch(`/servis/${servisId}/pin-show`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
            .then((r) => r.json().then((data) => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                if (!ok) {
                    tampilkanPesan(data.message ?? 'Tidak dapat menampilkan PIN.', '#b91c1c');
                    return;
                }

                if (tampilanPin) {
                    tampilanPin.textContent = 'PIN cocok';
                    tampilanPin.style.fontSize = '16px';
                    tampilanPin.style.letterSpacing = 'normal';
                }

                if (labelToggle) {
                    labelToggle.textContent = 'Terverifikasi';
                }

                tampilkanPesan(data.message ?? '', '#15803d');
            })
            .catch(() => tampilkanPesan('Gagal menghubungi server.', '#b91c1c'));
    }

    document.getElementById('pin_verify_submit')?.addEventListener('click', kirimVerifikasi);

    document.getElementById('pin_verify_cancel')?.addEventListener('click', function () {
        kotakVerify?.classList.add('hidden');

        if (inputVerify) {
            inputVerify.value = '';
        }

        pesanVerify?.classList.add('hidden');
    });

    inputVerify?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            kirimVerifikasi();
        }
    });

    function kirimVerifikasi() {
        const nilai = inputVerify?.value ?? '';

        if (!/^[0-9]{4,6}$/.test(nilai)) {
            tampilkanPesan('PIN harus berupa 4 sampai 6 digit angka.', '#b45309');
            return;
        }

        fetch(`/servis/${servisId}/pin-verify`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ pin: nilai }),
        })
            .then((r) => r.json().then((data) => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                if (!ok || !data.success) {
                    tampilkanPesan(data.message ?? 'PIN salah.', '#b91c1c');

                    if (inputVerify) {
                        inputVerify.value = '';
                        inputVerify.focus();
                    }

                    return;
                }

                sudahTerverifikasi = true;
                tampilkanPesan('PIN cocok.', '#15803d');

                setTimeout(function () {
                    kotakVerify?.classList.add('hidden');
                    bukaKeterangan();
                }, 500);
            })
            .catch(() => tampilkanPesan('Gagal menghubungi server.', '#b91c1c'));
    }
})();
</script>
@endpush
