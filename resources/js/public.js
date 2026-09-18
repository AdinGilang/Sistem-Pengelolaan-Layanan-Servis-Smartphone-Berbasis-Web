/**
 * Peningkatan progresif untuk halaman publik: elemen berkelas ".reveal"
 * diberi sedikit animasi masuk begitu pertama kali terlihat saat digulir.
 *
 * Sengaja ditulis tanpa dependensi apa pun — bukan Alpine, bukan pustaka
 * animasi pihak ketiga — dan dibundel sebagai entry Vite terpisah dari
 * resources/js/app.js. Halaman publik (beranda, cek status) tidak perlu
 * memuat Alpine.js yang sebenarnya hanya dipakai panel admin.
 *
 * Progresif berarti: bila skrip ini gagal dimuat, browser pengunjung tidak
 * mendukung IntersectionObserver, atau JavaScript dimatikan, elemen
 * ".reveal" tetap tampil penuh sejak awal (lihat aturan .reveal di
 * app.css). Animasi ini murni hiasan, bukan syarat supaya konten terlihat.
 */
(function () {
    if (!('IntersectionObserver' in window)) {
        return;
    }

    var elemen = document.querySelectorAll('.reveal');

    if (elemen.length === 0) {
        return;
    }

    var pengamat = new IntersectionObserver(
        function (daftar) {
            daftar.forEach(function (entri) {
                if (!entri.isIntersecting) {
                    return;
                }

                entri.target.classList.remove('reveal-ready');
                entri.target.classList.add('reveal-visible');
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
