/**
 * Peningkatan progresif untuk halaman publik.
 *
 * Sengaja ditulis tanpa dependensi apa pun — bukan Alpine, bukan pustaka
 * animasi pihak ketiga — dan dibundel sebagai entry Vite terpisah dari
 * resources/js/app.js. Halaman publik (beranda, cek status) tidak perlu
 * memuat Alpine.js yang sebenarnya hanya dipakai panel admin.
 *
 * Semua fungsi di sini progresif: bila skrip gagal dimuat atau JavaScript
 * dimatikan, halaman tetap utuh dan bisa dipakai sepenuhnya. Animasi di
 * sini hiasan, bukan syarat supaya konten terlihat atau formulir jalan.
 */

/** Pengguna yang meminta animasi dikurangi lewat pengaturan sistemnya. */
const kurangiGerak = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ==========================================================================
   1. Ungkap saat digulir
   ========================================================================== */
(function () {
    if (!('IntersectionObserver' in window)) {
        return;
    }

    const elemen = document.querySelectorAll('.reveal');

    if (elemen.length === 0) {
        return;
    }

    const pengamat = new IntersectionObserver(
        function (daftar) {
            daftar.forEach(function (entri) {
                if (!entri.isIntersecting) {
                    return;
                }

                entri.target.classList.remove('reveal-ready');
                entri.target.classList.add('reveal-visible');
                // Berhenti mengamati begitu tampil: animasinya sekali saja,
                // elemen tidak akan menghilang lagi saat digulir naik-turun.
                pengamat.unobserve(entri.target);
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
    );

    elemen.forEach(function (el) {
        el.classList.add('reveal-ready');
        pengamat.observe(el);
    });
})();

/* ==========================================================================
   2. Angka statistik menghitung naik
   --------------------------------------------------------------------------
   Nilai akhirnya SELALU sama dengan angka dari basis data: yang dianimasikan
   hanya tampilannya. Angka final sudah tertulis sebagai isi elemen sejak
   server merender halaman, jadi tanpa JavaScript pun pengunjung membaca
   angka yang benar — bukan nol.
   ========================================================================== */
(function () {
    const angka = document.querySelectorAll('[data-hitung]');

    if (angka.length === 0) {
        return;
    }

    const format = new Intl.NumberFormat('id-ID');

    function jalankan(el) {
        const tujuan = parseInt(el.dataset.hitung, 10);

        if (!Number.isFinite(tujuan) || tujuan <= 0) {
            return;
        }

        const durasi = 700;
        const mulai = performance.now();

        function langkah(sekarang) {
            const lewat = Math.min((sekarang - mulai) / durasi, 1);
            // ease-out: cepat di awal lalu melambat, terasa lebih alami
            // daripada laju yang datar dari awal sampai akhir.
            const kemajuan = 1 - Math.pow(1 - lewat, 3);

            el.textContent = format.format(Math.round(tujuan * kemajuan));

            if (lewat < 1) {
                requestAnimationFrame(langkah);
            } else {
                // Tulis ulang angka finalnya secara eksplisit supaya tidak
                // ada selisih pembulatan di frame terakhir.
                el.textContent = format.format(tujuan);
            }
        }

        requestAnimationFrame(langkah);
    }

    if (kurangiGerak || !('IntersectionObserver' in window)) {
        return; // biarkan angka final apa adanya
    }

    const pengamat = new IntersectionObserver(
        function (daftar) {
            daftar.forEach(function (entri) {
                if (!entri.isIntersecting) {
                    return;
                }

                jalankan(entri.target);
                pengamat.unobserve(entri.target); // sekali saja
            });
        },
        { threshold: 0.4 }
    );

    angka.forEach(function (el) {
        pengamat.observe(el);
    });
})();

/* ==========================================================================
   3. Akordeon FAQ yang membuka/menutup halus
   --------------------------------------------------------------------------
   <details> bawaan membuka isinya seketika tanpa transisi. Di sini
   perpindahannya diambil alih supaya tingginya bisa dianimasikan, tapi
   elemen <details> tetap dipakai: tanpa JavaScript, FAQ masih bisa dibuka
   seperti biasa — hanya tanpa animasi.
   ========================================================================== */
(function () {
    const daftar = document.querySelectorAll('.faq__item');

    if (daftar.length === 0 || kurangiGerak) {
        return;
    }

    const DURASI = 260; // harus sejalan dengan transition di CSS

    daftar.forEach(function (item) {
        const ringkasan = item.querySelector('summary');
        const panel = item.querySelector('.faq__panel');

        if (!ringkasan || !panel) {
            return;
        }

        /**
         * Membereskan keadaan setelah animasi selesai.
         *
         * Dipanggil oleh transitionend ATAU oleh penghitung waktu cadangan,
         * mana pun yang lebih dulu. Cadangan itu penting: kalau
         * transitionend tidak pernah terkirim (perilaku antar browser untuk
         * grid-template-rows berbeda-beda), tanpa pengaman ini penanda
         * "sedang bergerak" akan tersangkut selamanya dan FAQ tidak bisa
         * dibuka-tutup lagi — animasi hiasan berubah jadi kerusakan fungsi.
         */
        function beres(tutup) {
            if (item.dataset.bergerak !== 'true') {
                return; // sudah dibereskan duluan
            }

            if (tutup) {
                item.open = false;
                delete item.dataset.menutup;
            }

            delete item.dataset.bergerak;
            // Dikembalikan ke nilai dari stylesheet supaya tinggi panel
            // ikut menyesuaikan kalau teksnya berubah atau layar diputar.
            panel.style.gridTemplateRows = '';
        }

        function pantau(tutup) {
            const cadangan = setTimeout(function () {
                beres(tutup);
            }, DURASI + 120);

            panel.addEventListener('transitionend', function selesai(ev) {
                // transitionend ikut naik dari elemen anak (transisi
                // opacity), jadi properti yang memicunya harus dicek.
                if (ev.propertyName !== 'grid-template-rows') {
                    return;
                }

                panel.removeEventListener('transitionend', selesai);
                clearTimeout(cadangan);
                beres(tutup);
            }, false);
        }

        ringkasan.addEventListener('click', function (e) {
            e.preventDefault();

            // Abaikan klik saat animasi sebelumnya masih berjalan, supaya
            // klik beruntun tidak meninggalkan panel di tinggi setengah.
            if (item.dataset.bergerak === 'true') {
                return;
            }

            item.dataset.bergerak = 'true';

            if (item.open) {
                item.dataset.menutup = 'true';
                panel.style.gridTemplateRows = '1fr';

                requestAnimationFrame(function () {
                    panel.style.gridTemplateRows = '0fr';
                });

                pantau(true);
            } else {
                item.open = true;
                panel.style.gridTemplateRows = '0fr';

                requestAnimationFrame(function () {
                    panel.style.gridTemplateRows = '1fr';
                });

                pantau(false);
            }
        });
    });
})();

/* ==========================================================================
   4. Keadaan memuat pada formulir pelacakan
   --------------------------------------------------------------------------
   Formulir tetap terkirim seperti biasa — tombolnya TIDAK dinonaktifkan,
   karena tombol submit yang di-disable sebelum pengiriman selesai justru
   bisa membatalkan pengirimannya di sebagian browser. Yang dilakukan di
   sini hanya menampilkan spinner dan mencegah klik ganda.
   ========================================================================== */
(function () {
    const formulir = document.querySelectorAll('form[data-loading]');

    if (formulir.length === 0) {
        return;
    }

    formulir.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const tombol = form.querySelector('[type="submit"]');

            if (!tombol) {
                return;
            }

            if (tombol.dataset.memuat === 'true') {
                e.preventDefault(); // cegah pengiriman ganda
                return;
            }

            tombol.dataset.memuat = 'true';
            tombol.setAttribute('aria-busy', 'true');

            if (!kurangiGerak) {
                tombol.classList.add('is-loading');
            }
        });
    });
})();
