<?php

namespace App\Modules\Invoice\Services;

use App\Modules\Servis\Models\Servis;

/**
 * Menghitung tinggi kertas thermal 58mm untuk invoice PDF berdasarkan
 * isi sesungguhnya, bukan angka tetap.
 *
 * Root cause bug "invoice terpotong": InvoiceController sebelumnya memanggil
 * ->setPaper([0, 0, 164.41, 800]) — tinggi 800pt TETAP untuk setiap invoice,
 * berapa pun panjang isinya. CSS di pdf.blade.php menulis
 * `@page { size: 58mm auto; }` seolah tinggi menyesuaikan konten secara
 * otomatis, tapi itu tidak pernah benar-benar berfungsi: DomPDF hanya
 * memakai ukuran dari CSS @page bila nilainya berhasil diuraikan menjadi
 * pasangan [lebar, tinggi] yang konkret. Untuk `auto`, nilainya tetap
 * berupa string "auto" apa adanya dan diabaikan — Dompdf::setPaper() dari
 * kode PHP-lah yang selalu menang. Sudah diverifikasi langsung: me-render
 * invoice pendek maupun panjang dengan setPaper(800) sama-sama menghasilkan
 * satu halaman MediaBox 800pt persis — bukan dua nilai berbeda seperti
 * seharusnya kalau tinggi memang mengikuti isi. Invoice yang butuh lebih
 * dari 800pt begitu saja terpotong tanpa peringatan maupun halaman kedua.
 *
 * Solusi di sini menghitung perkiraan tinggi dari isi sesungguhnya —
 * jumlah baris opsional yang terisi, dan estimasi baris terbungkus untuk
 * teks yang panjangnya bisa bervariasi (alamat, kelengkapan, kerusakan,
 * teks footer dari Pengaturan). Perkiraannya sengaja dilebihkan
 * (over-provisioned): salah menyisakan sedikit kertas kosong di ujung jauh
 * lebih baik daripada memotong data — apalagi ini dokumen yang diserahkan
 * ke pelanggan.
 */
class InvoicePdfHeight
{
    /** Lebar kertas thermal dalam poin (58mm). */
    public const LEBAR_PT = 164.41;

    /** Batas bawah — invoice paling ringkas pun tidak pernah lebih pendek dari ini. */
    private const TINGGI_MINIMUM = 420.0;

    /**
     * Batas atas untuk berjaga-jaga dari input ekstrem (kerusakan boleh
     * sampai 2000 karakter menurut validasi). Bukan batas yang realistis
     * tercapai, hanya jaring pengaman supaya nilainya tetap masuk akal.
     */
    private const TINGGI_MAKSIMUM = 4000.0;

    /** Tinggi satu baris teks pada badan invoice (font ~7.5pt, line-height ~1.5). */
    private const TINGGI_BARIS = 11.0;

    /** Tinggi satu baris teks footer (font ~7pt, sedikit lebih rapat). */
    private const TINGGI_BARIS_FOOTER = 9.5;

    /** Perkiraan jumlah karakter yang muat dalam satu baris pada lebar 52mm terpakai. */
    private const KARAKTER_PER_BARIS = 30;
    private const KARAKTER_PER_BARIS_FOOTER = 34;

    /**
     * Elemen tetap yang selalu ada di setiap invoice apa pun isinya: kop,
     * judul, tiga baris meta, label setiap seksi, garis pemisah, tabel,
     * kotak total, dan area tanda tangan. Angka ini dikalibrasi longgar
     * dari struktur pdf.blade.php, bukan diukur presisi piksel.
     */
    private const OVERHEAD_TETAP = 300.0;

    /** Margin halaman atas + bawah (4mm masing-masing sisi). */
    private const MARGIN_HALAMAN = 22.7;

    public static function untuk(Servis $servis, string $footerThanks, string $footerGaransi, string $footerBatas): float
    {
        $tinggi = self::OVERHEAD_TETAP + self::MARGIN_HALAMAN;

        // Baris opsional pada blok "Data Pelanggan" dan "Data Handphone" —
        // masing-masing hanya dirender bila field-nya terisi, persis
        // seperti kondisi @if pada pdf.blade.php.
        if (filled($servis->alamat)) {
            $tinggi += self::baris($servis->alamat);
        }

        if (filled($servis->no_wa)) {
            $tinggi += self::TINGGI_BARIS;
        }

        $kelengkapan = self::kelengkapan($servis);
        if ($kelengkapan !== []) {
            $tinggi += self::baris(implode(', ', $kelengkapan));
        }

        // Baris "Jasa Servis — merk tipe" di dalam tabel rincian biaya.
        $itemSub = trim(($servis->merk_hp ?? '') . ' ' . ($servis->tipe_hp ?? ''));
        $tinggi += self::baris($itemSub ?: '-');

        // Kotak kerusakan: teksnya sering paling panjang di seluruh dokumen.
        $tinggi += self::baris((string) $servis->kerusakan);

        // Tiga baris footer — masing-masing bisa dipanjangkan admin lewat
        // Pengaturan Nota (maksimal 255 karakter per field).
        $tinggi += self::barisFooter($footerThanks)
            + self::barisFooter($footerGaransi)
            + self::barisFooter($footerBatas);

        return (float) max(self::TINGGI_MINIMUM, min(self::TINGGI_MAKSIMUM, $tinggi));
    }

    /**
     * Tinggi yang dibutuhkan satu blok teks badan invoice, dengan estimasi
     * jumlah baris terbungkus dari panjang karakternya.
     */
    private static function baris(string $teks): float
    {
        $jumlahBaris = max(1, (int) ceil(mb_strlen($teks) / self::KARAKTER_PER_BARIS));

        return $jumlahBaris * self::TINGGI_BARIS;
    }

    private static function barisFooter(string $teks): float
    {
        $jumlahBaris = max(1, (int) ceil(mb_strlen($teks) / self::KARAKTER_PER_BARIS_FOOTER));

        return $jumlahBaris * self::TINGGI_BARIS_FOOTER;
    }

    /**
     * @return list<string>
     */
    private static function kelengkapan(Servis $servis): array
    {
        $kelengkapan = $servis->kelengkapan;

        if (is_string($kelengkapan)) {
            $kelengkapan = json_decode($kelengkapan, true) ?? [];
        }

        return is_array($kelengkapan) ? array_values($kelengkapan) : [];
    }
}
