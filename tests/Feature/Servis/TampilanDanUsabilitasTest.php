<?php

use App\Models\User;
use App\Modules\Servis\Models\Servis;

/*
|--------------------------------------------------------------------------
| UAT — perbaikan kegunaan (usability) dan bug tampilan
|--------------------------------------------------------------------------
|
| Skenario di berkas ini meniru langkah admin sungguhan di panel, dan
| menjaga agar bug tampilan yang pernah ditemukan (lihat AUDIT.md) tidak
| diam-diam kembali di kemudian hari.
|
*/

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->owner = User::factory()->owner()->create();
});

test('halaman data servis yang benar-benar kosong mengarahkan admin menambah data pertama', function () {
    // Database belum punya satu pun baris servis, dan admin belum mengetik
    // pencarian apa pun — ini kunjungan pertama, bukan pencarian yang gagal.
    $this->actingAs($this->admin)
        ->get('/servis')
        ->assertOk()
        ->assertSee('Belum ada data servis yang tercatat')
        ->assertSee('Tambah data servis pertama')
        ->assertDontSee('cocok dengan pencarian');
});

test('pencarian tanpa hasil menampilkan pesan berbeda dari database kosong', function () {
    Servis::factory()->create(['pelanggan' => 'Budi Santoso']);

    $this->actingAs($this->admin)
        ->get('/servis?search=NamaYangTidakAda')
        ->assertOk()
        ->assertSee('cocok dengan pencarian')
        ->assertSee('Hapus pencarian')
        ->assertDontSee('Belum ada data servis yang tercatat');
});

test('owner melihat pesan pemantauan alih-alih ajakan menambah data ketika daftar kosong', function () {
    $this->actingAs($this->owner)
        ->get('/servis')
        ->assertOk()
        ->assertSee('Belum ada data servis yang tercatat')
        ->assertDontSee('Tambah data servis pertama');
});

test('kotak pencarian tetap berfungsi lewat GET biasa tanpa AJAX', function () {
    // Ini adalah cek "tanpa JavaScript": kotak pencarian sekarang berada
    // di dalam form sungguhan, jadi permintaan GET langsung ke URL dengan
    // parameter search (persis seperti menekan Enter tanpa skrip berjalan)
    // harus tetap menyaring data dengan benar.
    Servis::factory()->create(['pelanggan' => 'Budi Santoso']);
    Servis::factory()->create(['pelanggan' => 'Siti Aminah']);

    $this->actingAs($this->admin)
        ->get('/servis?search=Budi')
        ->assertOk()
        ->assertSee('Budi Santoso')
        ->assertDontSee('Siti Aminah');
});

test('info hasil pencarian menyertakan kata kunci dan jumlah data', function () {
    Servis::factory()->count(3)->create(['pelanggan' => 'Budi Santoso']);

    $this->actingAs($this->admin)
        ->get('/servis?search=Budi')
        ->assertOk()
        ->assertSee('Hasil pencarian')
        ->assertSee('Budi')
        ->assertSee('3 data ditemukan');
});

test('warna status servis sama persis di Data Servis, Detail, Dashboard, dan Invoice', function () {
    // Regresi: sebelumnya status "Proses" berwarna biru di Data Servis dan
    // Detail Servis, tapi ungu di Dashboard dan Laporan — warna yang
    // berbeda-beda untuk arti yang sama persis di halaman yang berbeda.
    //
    // Invoice sekarang ikut memakai varian terang yang sama seperti halaman
    // lain (bukan lagi varian :dark) sejak kop bergradasi gelapnya diganti
    // desain dokumen yang lebih bersih dan ramah cetak.
    $servis = Servis::factory()->create(['status' => Servis::STATUS_PROSES]);

    $halIndex   = $this->actingAs($this->admin)->get('/servis')->getContent();
    $halShow    = $this->actingAs($this->admin)->get("/servis/{$servis->id}")->getContent();
    $halDash    = $this->actingAs($this->admin)->get('/dashboard')->getContent();
    $halInvoice = $this->actingAs($this->admin)->get("/servis/{$servis->id}/invoice")->getContent();

    foreach (['Data Servis' => $halIndex, 'Detail Servis' => $halShow, 'Invoice' => $halInvoice] as $nama => $html) {
        expect($html)->toContain('badge--proses');
    }

    expect($halDash)->toContain('badge--proses');
});

test('legenda pie chart statistik memakai warna yang sama persis dengan grafiknya', function () {
    $this->actingAs($this->owner)
        ->get('/statistik')
        ->assertOk()
        // Warna ini harus identik dengan WARNA di resources/js/statistik.js,
        // supaya legenda dan irisan pie yang sesungguhnya tidak berbeda warna.
        ->assertSee('#b45309', false)
        ->assertSee('#6d28d9', false)
        ->assertSee('#15803d', false);
});

test('teks footer invoice mengikuti pengaturan tersimpan di semua tampilan invoice', function () {
    // Regresi: pratinjau invoice di layar sebelumnya menampilkan teks
    // footer yang ditulis tetap di kode, tidak peduli apa pun yang admin
    // simpan di halaman Pengaturan — padahal versi cetak dan PDF-nya benar.
    // batas_pengambilan bahkan tidak dirender sama sekali di pratinjau
    // layar, padahal sudah benar di versi cetak dan PDF.
    $this->actingAs($this->admin)->put('/setting', [
        'footer_thanks'     => 'Terima kasih khusus dari pengujian.',
        'garansi_servis'    => 'Garansi unik untuk pengujian.',
        'batas_pengambilan' => 'Batas unik untuk pengujian.',
    ])->assertRedirect();

    $servis = Servis::factory()->create();

    $this->actingAs($this->admin)
        ->get("/servis/{$servis->id}/invoice")
        ->assertOk()
        ->assertSee('Terima kasih khusus dari pengujian.')
        ->assertSee('Garansi unik untuk pengujian.')
        ->assertSee('Batas unik untuk pengujian.');
});

test('tautan kembali selalu tersedia di halaman detail, formulir, dan invoice', function () {
    // Sebelumnya satu-satunya tautan "Kembali" berada di paling bawah
    // halaman yang panjangnya ratusan baris — admin harus menggulir
    // seluruhnya untuk kembali ke daftar.
    $servis = Servis::factory()->create();

    $this->actingAs($this->admin)->get('/servis/create')->assertOk()->assertSee('Kembali', false);
    $this->actingAs($this->admin)->get("/servis/{$servis->id}/edit")->assertOk()->assertSee('Kembali', false);
    $this->actingAs($this->admin)->get("/servis/{$servis->id}")->assertOk()->assertSee('Kembali', false);
    $this->actingAs($this->admin)->get("/servis/{$servis->id}/invoice")->assertOk()->assertSee('Kembali ke Detail');
    $this->actingAs($this->admin)->get('/setting')->assertOk()->assertSee('Kembali', false);
});

test('kotak nomor whatsapp tidak lagi kosong tanpa isi di formulir tambah dan ubah', function () {
    // Regresi: sisa pembersihan emoji meninggalkan kotak prefiks kosong
    // tanpa ikon apa pun di depan input nomor WhatsApp.
    $servis = Servis::factory()->create();

    $this->actingAs($this->admin)
        ->get('/servis/create')
        ->assertOk()
        ->assertSee('viewBox="0 0 24 24" fill="currentColor"', false);

    $this->actingAs($this->admin)
        ->get("/servis/{$servis->id}/edit")
        ->assertOk()
        ->assertSee('viewBox="0 0 24 24" fill="currentColor"', false);
});

test('pengaturan nota tidak menampilkan pesan sukses dua kali', function () {
    // Regresi: layout panel sudah merender toast session('success') secara
    // global, tapi halaman Pengaturan dulu merendernya sekali lagi secara
    // terpisah, sehingga pesan yang sama muncul dua kali di layar.
    $respons = $this->actingAs($this->admin)->put('/setting', [
        'footer_thanks'     => 'Terima kasih.',
        'garansi_servis'    => 'Garansi 7 hari.',
        'batas_pengambilan' => 'Maksimal 3 bulan.',
    ]);

    $html = $this->actingAs($this->admin)->get('/setting')->getContent();

    expect(substr_count($html, 'Pengaturan berhasil disimpan'))->toBeLessThanOrEqual(1);
});

test('tanggal ditampilkan dalam bahasa Indonesia, bukan bahasa Inggris', function () {
    // Regresi: beberapa halaman memakai ->format('d F Y') yang selalu
    // menghasilkan nama bulan berbahasa Inggris apa pun locale aplikasinya,
    // berbeda dari halaman lain yang sudah benar memakai translatedFormat().
    $servis = Servis::factory()->create([
        'tanggal' => '2026-01-15',
        'no_wa'   => null,
    ]);

    $html = $this->actingAs($this->admin)->get("/servis/{$servis->id}")->getContent();

    expect($html)->toContain('Januari')->not->toContain('January');
});

test('label bulan dan tahun pada filter laporan tertaut ke dropdown-nya', function () {
    $this->actingAs($this->owner)
        ->get('/laporan')
        ->assertOk()
        ->assertSee('for="laporan-bulan"', false)
        ->assertSee('for="laporan-tahun"', false);
});

test('kolom aksi Data Servis berupa tombol ikon dengan label yang bisa diakses, bukan tautan teks polos', function () {
    // Regresi: sebelumnya "Detail Invoice Edit Hapus" ditumpuk sebagai
    // tautan teks polos tanpa jarak jelas. Setiap tombol sekarang harus
    // tetap punya nama yang bisa diumumkan pembaca layar (aria-label/title)
    // meski teksnya sendiri digantikan ikon.
    $servis = Servis::factory()->create();

    $html = $this->actingAs($this->admin)->get('/servis')->getContent();

    expect($html)->toContain('class="aksi-btn aksi-btn--blue"')
        ->toContain('class="aksi-btn aksi-btn--purple"')
        ->toContain('class="aksi-btn aksi-btn--amber"')
        ->toContain('class="aksi-btn aksi-btn--red"')
        ->toContain('aria-label="Lihat detail ' . $servis->kode_unik . '"')
        ->toContain('aria-label="Hapus data ' . $servis->kode_unik . '"');
});

test('owner tanpa izin ubah/hapus hanya melihat tombol lihat dan invoice', function () {
    // Dicocokkan lewat atribut class lengkap pada elemennya, bukan sekadar
    // substring nama kelas — blok <style> di halaman ini juga menuliskan
    // nama kelas tersebut sebagai selector CSS, jadi substring saja selalu
    // "ditemukan" pada halaman mana pun terlepas dari tombol mana yang
    // sungguh-sungguh dirender.
    Servis::factory()->create();

    $html = $this->actingAs($this->owner)->get('/servis')->getContent();

    expect($html)->toContain('class="aksi-btn aksi-btn--blue"')
        ->toContain('class="aksi-btn aksi-btn--purple"')
        ->not->toContain('class="aksi-btn aksi-btn--amber"')
        ->not->toContain('class="aksi-btn aksi-btn--red"');
});

test('badge peran di topbar tetap kontras di atas latar putih', function () {
    // Regresi: warna badge peran dirancang untuk latar navy sidebar (teks
    // biru muda di atas biru transparan). Badge yang sama juga dipakai di
    // topbar berlatar putih, membuatnya pucat dan sulit dibaca di sana.
    $html = $this->actingAs($this->admin)->get('/dashboard')->getContent();

    expect($html)->toContain('.topbar .role-badge.admin');
});

test('pratinjau invoice berupa dokumen bersih, bukan kop bergradasi', function () {
    // Regresi desain: versi sebelumnya memakai kop bergradasi navy-ke-biru
    // dan kotak "TOTAL PEMBAYARAN" berlatar gelap penuh — pola visual
    // templat dashboard generik yang janggal untuk dokumen yang akan
    // dicetak dan diserahkan ke pelanggan.
    $servis = Servis::factory()->create(['biaya' => 150000]);

    $html = $this->actingAs($this->admin)->get("/servis/{$servis->id}/invoice")->getContent();

    expect($html)
        ->not->toContain('linear-gradient')
        ->toContain('Rp 150.000')
        ->toContain('inv-doc');
});

test('kartu ringkasan Laporan dan Statistik menyempit di layar HP', function () {
    // Regresi: keempat kartu ringkasan di kedua halaman ini dulu dipaksa
    // 4 kolom tetap (grid-template-columns:repeat(4,1fr)) tanpa satu pun
    // media query di seluruh berkas. Di layar HP (~375px), tiap kartu jadi
    // sekitar 85px lebar — angka seperti "Rp 15.000.000" meluber atau
    // terpotong.
    $laporan   = $this->actingAs($this->owner)->get('/laporan')->getContent();
    $statistik = $this->actingAs($this->owner)->get('/statistik')->getContent();

    expect($laporan)
        ->toContain('class="laporan-stat-grid"')
        ->toContain('@media (max-width: 720px)');

    expect($statistik)
        ->toContain('class="statistik-stat-grid"')
        ->toContain('class="statistik-chart-row"')
        ->toContain('@media (max-width: 720px)');
});

test('kotak pencarian Data Servis tidak lagi menampilkan garis tepi dobel saat difokuskan', function () {
    // Regresi: pembungkus kotak pencarian mengubah warna border DAN
    // memunculkan ring (box-shadow) biru sekaligus saat difokuskan — dua
    // efek fokus terpisah yang tampil bersamaan sehingga terlihat seperti
    // dua garis tepi biru bertumpuk (dilaporkan lewat tangkapan layar HP).
    // Sekarang border dibiarkan netral dan hanya ring yang berubah.
    $admin = $this->admin;

    $html = $this->actingAs($admin)->get('/servis')->getContent();

    expect($html)
        ->not->toContain('focus-within:border-blue-400')
        ->toContain('focus-within:ring-2 focus-within:ring-blue-400');
});
