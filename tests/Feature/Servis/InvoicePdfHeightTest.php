<?php

use App\Models\User;
use App\Modules\Invoice\Services\InvoicePdfHeight;
use App\Modules\Servis\Models\Servis;

/*
|--------------------------------------------------------------------------
| Regresi: invoice PDF terpotong pada kertas thermal
|--------------------------------------------------------------------------
|
| InvoiceController sebelumnya memanggil setPaper([0, 0, 164.41, 800]) —
| tinggi 800pt TETAP untuk setiap invoice apa pun panjang isinya. Diverifikasi
| langsung dengan merender PDF: invoice pendek dan panjang sama-sama
| menghasilkan MediaBox 800pt persis, bukan dua nilai berbeda seperti
| seharusnya kalau tinggi memang mengikuti isi. Konten yang butuh lebih
| dari 800pt begitu saja hilang tanpa peringatan maupun halaman kedua.
|
| Test di berkas ini membaca byte PDF yang sungguhan dihasilkan (bukan
| menebak lewat mock) untuk memastikan tinggi kertas benar-benar mengikuti
| panjang konten, bukan lagi angka tetap.
|
*/

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

/**
 * Baca nilai tinggi (komponen keempat) dari /MediaBox pertama pada byte PDF.
 */
function tinggiMediaBoxPdf(string $isiPdf): float
{
    preg_match('/\/MediaBox\s*\[\s*[\d.]+\s+[\d.]+\s+[\d.]+\s+([\d.]+)\s*\]/', $isiPdf, $cocok);

    expect($cocok)->not->toBeEmpty('MediaBox tidak ditemukan pada berkas PDF.');

    return (float) $cocok[1];
}

test('estimator tidak pernah menghasilkan tinggi di bawah batas minimum', function () {
    $servis = Servis::factory()->create([
        'alamat' => null, 'no_wa' => null, 'kelengkapan' => null,
        'merk_hp' => null, 'tipe_hp' => null, 'kerusakan' => 'Mati total',
    ]);

    $tinggi = InvoicePdfHeight::untuk($servis, 'Terima kasih.', 'Garansi 7 hari.', 'Maksimal 3 bulan.');

    expect($tinggi)->toBeGreaterThanOrEqual(420.0);
});

test('invoice dengan konten lebih panjang mendapat kertas yang lebih tinggi', function () {
    $pendek = Servis::factory()->create([
        'alamat' => null, 'no_wa' => null, 'kelengkapan' => null,
        'kerusakan' => 'LCD pecah',
    ]);

    $panjang = Servis::factory()->create([
        'alamat'      => 'Jalan Sangat Panjang Nomor 123, RT 001 RW 002, Kelurahan Contoh Panjang, Kecamatan Uji Coba',
        'no_wa'       => '081234567890',
        'kelengkapan' => ['SIM Card', 'Memori Card', 'Casing', 'Baterai', 'Charger', 'Earphone', 'Kardus'],
        'kerusakan'   => str_repeat('LCD pecah parah, touchscreen tidak responsif, baterai kembung. ', 4),
    ]);

    $tinggiPendek  = InvoicePdfHeight::untuk($pendek, 'Terima kasih.', 'Garansi 7 hari.', 'Maksimal 3 bulan.');
    $tinggiPanjang = InvoicePdfHeight::untuk($panjang, 'Terima kasih.', 'Garansi 7 hari.', 'Maksimal 3 bulan.');

    expect($tinggiPanjang)->toBeGreaterThan($tinggiPendek);
});

test('teks footer yang dipanjangkan admin lewat Pengaturan ikut menambah tinggi', function () {
    $servis = Servis::factory()->create();

    $footerPendek  = InvoicePdfHeight::untuk($servis, 'Terima kasih.', 'Garansi 7 hari.', 'Maksimal 3 bulan.');
    $footerPanjang = InvoicePdfHeight::untuk(
        $servis,
        str_repeat('Terima kasih banyak atas kepercayaan Anda kepada kami selama ini. ', 3),
        'Garansi 7 hari.',
        'Maksimal 3 bulan.',
    );

    expect($footerPanjang)->toBeGreaterThan($footerPendek);
});

test('estimator tetap dibatasi batas atas yang masuk akal walau kerusakan sepanjang batas validasi', function () {
    $servis = Servis::factory()->create([
        'kerusakan' => str_repeat('a', 2000), // batas maksimum StoreServisRequest
    ]);

    $tinggi = InvoicePdfHeight::untuk($servis, 'Terima kasih.', 'Garansi 7 hari.', 'Maksimal 3 bulan.');

    expect($tinggi)->toBeLessThanOrEqual(4000.0);
});

test('PDF invoice sungguhan: kertas untuk konten panjang lebih tinggi dari konten pendek, tidak lagi sama-sama 800pt', function () {
    // Ini pengujian ujung-ke-ujung yang membaca byte PDF asli hasil route
    // /invoice/pdf, persis seperti bug ditemukan pertama kali: sebelum
    // perbaikan, kedua MediaBox di bawah ini akan bernilai identik 800.0
    // berapa pun panjang isinya.
    $pendek = Servis::factory()->create([
        'alamat' => null, 'no_wa' => null, 'kelengkapan' => null,
        'kerusakan' => 'LCD pecah', 'biaya' => 50000,
    ]);

    $panjang = Servis::factory()->create([
        'alamat'      => 'Jalan Sangat Panjang Nomor 123, RT 001 RW 002, Kelurahan Contoh Panjang, Kecamatan Uji Coba, Kota Pengujian',
        'no_wa'       => '081234567890',
        'kelengkapan' => ['SIM Card', 'Memori Card', 'Casing', 'Baterai', 'Charger', 'Earphone', 'Kardus'],
        'kerusakan'   => str_repeat('LCD pecah parah, touchscreen tidak responsif, baterai kembung, kamera buram. ', 5),
        'biaya'       => 1250000,
    ]);

    $isiPendek  = $this->actingAs($this->admin)->get("/servis/{$pendek->id}/invoice/pdf")->assertOk()->getContent();
    $isiPanjang = $this->actingAs($this->admin)->get("/servis/{$panjang->id}/invoice/pdf")->assertOk()->getContent();

    $tinggiPendek  = tinggiMediaBoxPdf($isiPendek);
    $tinggiPanjang = tinggiMediaBoxPdf($isiPanjang);

    expect($tinggiPanjang)->toBeGreaterThan($tinggiPendek)
        ->and($tinggiPendek)->not->toBe(800.0)
        ->and($tinggiPanjang)->not->toBe(800.0);
});
