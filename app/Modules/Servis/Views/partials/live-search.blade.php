@push('scripts')
<script>
/**
 * Pencarian langsung pada daftar servis.
 *
 * Tiga hal yang diperbaiki dari versi sebelumnya:
 *
 * 1. Selama menunggu jawaban server, tabel kini menampilkan skeleton row.
 *    Sebelumnya isi tabel dibiarkan apa adanya lalu tiba-tiba diganti,
 *    sehingga halaman terasa macet dan tinggi kontennya meloncat.
 *
 * 2. Tombol bersihkan tidak lagi memakai atribut onclick di dalam HTML.
 *    Semua perilaku dipasang lewat addEventListener, sehingga Content
 *    Security Policy tidak perlu melonggarkan skrip inline untuk halaman ini.
 *
 * 3. Teks "Hasil pencarian: ... — N data ditemukan" dulu hanya dirender
 *    sekali saat halaman dimuat penuh dan tidak pernah ikut diperbarui oleh
 *    pencarian AJAX, sehingga informasinya cepat basi. Sekarang elemen
 *    #hasil-info ikut ditimpa setiap kali jawaban baru datang.
 *
 * Kotak pencarian sendiri kini berada di dalam <form id="filter-form">
 * yang sungguhan (bukan lagi berdiri sendiri di luar form apa pun), jadi
 * menekan Enter tetap mengirim pencarian nyata lewat GET biasa walau
 * skrip ini gagal dimuat sama sekali.
 */
(function () {
    const input       = document.getElementById('live-search');
    const clearBtn    = document.getElementById('clear-search');
    const icon        = document.getElementById('search-icon');
    const spinner     = document.getElementById('search-spinner');
    const hint        = document.getElementById('search-hint');
    const statusSel   = document.getElementById('filter-status');
    const tableBody   = document.getElementById('table-body');
    const pagination  = document.getElementById('pagination-wrapper');
    const hasilInfo   = document.getElementById('hasil-info');

    if (!input || !tableBody) {
        return;
    }

    const KOLOM = 8;
    let debounceTimer = null;
    let currentSearch = input.value.trim();
    let permintaanKe = 0;

    /** Skeleton sementara, setinggi kira-kira satu halaman data. */
    function tampilkanSkeleton() {
        const baris = Array.from({ length: 5 }, () => `
            <tr>
                <td colspan="${KOLOM}" class="px-2">
                    <div class="skeleton-row">
                        <div class="skeleton-bar" style="width:14%"></div>
                        <div class="skeleton-bar" style="width:20%"></div>
                        <div class="skeleton-bar" style="width:28%"></div>
                        <div class="skeleton-bar" style="width:16%"></div>
                        <div class="skeleton-bar" style="width:12%"></div>
                    </div>
                </td>
            </tr>`).join('');

        tableBody.innerHTML = baris;
        tableBody.setAttribute('aria-busy', 'true');
    }

    function mulaiMemuat() {
        icon?.classList.add('hidden');
        spinner?.classList.remove('hidden');

        if (hint) {
            hint.classList.remove('hidden');
            hint.textContent = 'Mencari...';
        }

        tampilkanSkeleton();
    }

    function selesaiMemuat() {
        icon?.classList.remove('hidden');
        spinner?.classList.add('hidden');
        hint?.classList.add('hidden');
        tableBody.removeAttribute('aria-busy');
    }

    function cari(nilai) {
        const status = statusSel ? statusSel.value : '';
        const url = new URL(window.location.href);

        url.searchParams.set('search', nilai);
        url.searchParams.set('status', status);
        url.searchParams.set('page', '1');

        window.history.replaceState({}, '', url.toString());

        mulaiMemuat();

        // Penanda urutan permintaan: jawaban yang datang terlambat dari
        // pencarian lama diabaikan agar tidak menimpa hasil terbaru.
        const nomor = ++permintaanKe;

        fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then((r) => r.text())
            .then((html) => {
                if (nomor !== permintaanKe) {
                    return;
                }

                const doc = new DOMParser().parseFromString(html, 'text/html');

                const bodyBaru = doc.getElementById('table-body');
                if (bodyBaru) {
                    tableBody.innerHTML = bodyBaru.innerHTML;
                }

                const pagBaru = doc.getElementById('pagination-wrapper');
                if (pagBaru && pagination) {
                    pagination.innerHTML = pagBaru.innerHTML;
                }

                const infoBaru = doc.getElementById('hasil-info');
                if (infoBaru && hasilInfo) {
                    hasilInfo.innerHTML = infoBaru.innerHTML;
                }

                selesaiMemuat();
            })
            .catch(() => {
                if (nomor !== permintaanKe) {
                    return;
                }

                tableBody.innerHTML = `
                    <tr><td colspan="${KOLOM}" class="text-center py-8 text-gray-500">
                        Gagal memuat data. Periksa koneksi Anda lalu coba lagi.
                    </td></tr>`;

                selesaiMemuat();
            });
    }

    input.addEventListener('input', function () {
        const nilai = this.value.trim();

        clearBtn?.classList.toggle('hidden', nilai === '');

        if (hint) {
            hint.classList.remove('hidden');
            hint.textContent = 'Mengetik...';
        }

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            if (nilai === currentSearch) {
                hint?.classList.add('hidden');
                return;
            }

            currentSearch = nilai;
            cari(nilai);
        }, 450);
    });

    input.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') {
            return;
        }

        e.preventDefault();
        clearTimeout(debounceTimer);
        currentSearch = this.value.trim();
        cari(currentSearch);
    });

    clearBtn?.addEventListener('click', function () {
        input.value = '';
        currentSearch = '';
        clearBtn.classList.add('hidden');
        cari('');
    });

    statusSel?.addEventListener('change', function () {
        cari(input.value.trim());
    });
})();

/**
 * Konfirmasi hapus. Pesannya dibawa atribut data-konfirmasi pada form,
 * bukan atribut onsubmit berisi JavaScript.
 */
document.querySelectorAll('form[data-konfirmasi]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        if (!window.confirm(form.dataset.konfirmasi)) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
