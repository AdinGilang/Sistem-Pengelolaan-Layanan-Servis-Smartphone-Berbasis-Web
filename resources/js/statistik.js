/**
 * Grafik halaman statistik.
 *
 * Chart.js sekarang di-bundel bersama aplikasi, bukan diambil dari
 * cdn.jsdelivr.net seperti sebelumnya. Dengan begitu:
 *
 *   - tampilan halaman tidak ikut rusak kalau CDN sedang bermasalah;
 *   - tidak ada pihak ketiga yang bisa mengganti isi skrip yang berjalan
 *     di halaman berisi data keuangan (versi lama juga tanpa Subresource
 *     Integrity, jadi perubahan di sisi CDN tidak akan terdeteksi);
 *   - Content Security Policy tidak perlu mengizinkan domain luar.
 *
 * Berkas ini adalah entry Vite terpisah sehingga Chart.js hanya diunduh
 * pada halaman statistik, bukan pada setiap halaman aplikasi.
 */

import {
    Chart,
    BarController,
    BarElement,
    CategoryScale,
    DoughnutController,
    Filler,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
} from 'chart.js';

// Hanya komponen yang dipakai yang didaftarkan, agar ukuran bundel minimal.
Chart.register(
    BarController,
    BarElement,
    CategoryScale,
    DoughnutController,
    Filler,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
);

const WARNA = {
    biru:   '#3b5bdb',
    kuning: '#b45309',
    ungu:   '#6d28d9',
    hijau:  '#15803d',
    garis:  '#eef1f8',
    label:  '#5c6580',
};

/** Hormati preferensi pengguna yang mematikan animasi di sistemnya. */
const animasiMati = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const dasar = {
    responsive: true,
    maintainAspectRatio: true,
    animation: animasiMati ? false : { duration: 400 },
    plugins: {
        legend: { display: false },
    },
};

const skalaDasar = (formatY) => ({
    y: {
        beginAtZero: true,
        ticks: { color: WARNA.label, font: { size: 11 }, callback: formatY },
        grid: { color: WARNA.garis },
    },
    x: {
        ticks: { color: WARNA.label, font: { size: 11 } },
        grid: { display: false },
    },
});

const rupiah = (nilai) => 'Rp ' + Number(nilai).toLocaleString('id-ID');

function bacaData() {
    const simpul = document.getElementById('statistik-data');

    if (!simpul) {
        return null;
    }

    try {
        return JSON.parse(simpul.textContent);
    } catch (e) {
        return null;
    }
}

function gambar() {
    const data = bacaData();

    if (!data) {
        return;
    }

    const kanvasServis = document.getElementById('chartServis');
    const kanvasStatus = document.getElementById('chartStatus');
    const kanvasPendapatan = document.getElementById('chartPendapatan');

    if (kanvasServis) {
        new Chart(kanvasServis, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Jumlah servis',
                    data: data.dataServis,
                    borderColor: WARNA.biru,
                    backgroundColor: 'rgba(59,91,219,.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: WARNA.biru,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: .35,
                }],
            },
            options: {
                ...dasar,
                plugins: {
                    ...dasar.plugins,
                    tooltip: {
                        callbacks: { label: (ctx) => ` ${ctx.parsed.y} unit servis` },
                    },
                },
                scales: skalaDasar((nilai) => (Number.isInteger(nilai) ? nilai : '')),
            },
        });
    }

    if (kanvasStatus) {
        new Chart(kanvasStatus, {
            type: 'doughnut',
            data: {
                labels: data.statusLabels,
                datasets: [{
                    data: data.statusData,
                    backgroundColor: [WARNA.kuning, WARNA.ungu, WARNA.hijau],
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 6,
                }],
            },
            options: {
                ...dasar,
                cutout: '65%',
                plugins: {
                    ...dasar.plugins,
                    tooltip: {
                        callbacks: { label: (ctx) => ` ${ctx.label}: ${ctx.parsed} unit` },
                    },
                },
            },
        });
    }

    if (kanvasPendapatan) {
        new Chart(kanvasPendapatan, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Pendapatan',
                    data: data.dataPendapatan,
                    backgroundColor: 'rgba(21,128,61,.16)',
                    borderColor: WARNA.hijau,
                    borderWidth: 2,
                    borderRadius: 6,
                    hoverBackgroundColor: 'rgba(21,128,61,.3)',
                }],
            },
            options: {
                ...dasar,
                plugins: {
                    ...dasar.plugins,
                    tooltip: {
                        callbacks: { label: (ctx) => ' ' + rupiah(ctx.parsed.y) },
                    },
                },
                scales: skalaDasar(rupiah),
            },
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', gambar);
} else {
    gambar();
}
