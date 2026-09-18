# Hasil Audit & Perbaikan Sistem

Dokumen ini merangkum pemeriksaan menyeluruh terhadap kode Sistem Pengelolaan
Layanan Servis Smartphone, beserta perbaikan yang diterapkan.

Isinya dibagi menjadi:

1. [Bug yang ditemukan](#1-bug-yang-ditemukan)
2. [Masalah performa dan animasi berat](#2-masalah-performa-dan-animasi-berat)
3. [Perbaikan keamanan](#3-perbaikan-keamanan)
4. [Optimasi SEO](#4-optimasi-seo)
5. [Restrukturisasi ke HMVC](#5-restrukturisasi-ke-hmvc)
6. [Keputusan desain antarmuka](#6-keputusan-desain-antarmuka)
7. [Daftar periksa keamanan sebelum push](#7-daftar-periksa-keamanan-sebelum-push)
8. [Yang belum dikerjakan](#8-yang-belum-dikerjakan)

Seluruh perbaikan diverifikasi oleh **99 test otomatis** (`php artisan test`).

---

## 1. Bug yang ditemukan

### 1.1 Parah — menghentikan sistem

| # | Masalah | Lokasi lama | Dampak |
|---|---------|-------------|--------|
| B1 | Dua migration menambahkan kolom `estimasi_jam` yang sama | `2026_04_16_add_estimasi_to_servis_table.php` dan `2026_04_20_080935_add_estimasi_jam_to_servis_table.php` | `php artisan migrate` pada database kosong **selalu gagal** dengan galat *duplicate column*. Siapa pun yang meng-clone repositori ini tidak bisa menjalankannya. |
| B2 | `Dockerfile` kehilangan tanda `\` setelah `libpng-dev` | `Dockerfile` baris 22 | Instruksi `RUN` terpotong, baris berikutnya dibaca sebagai instruksi Dockerfile. **Build image selalu gagal.** |
| B3 | `MONTH()` dan `YEAR()` ditulis langsung di `selectRaw` | `StatistikController`, `LaporanController` | Kedua fungsi itu khas MySQL. Di SQLite halaman Laporan dan Statistik langsung galat — termasuk saat test dijalankan, karena `phpunit.xml` memang memakai SQLite. |

**Perbaikan:**
- B1 — migration kedua dibuat idempoten (`Schema::hasColumn` diperiksa dulu). Berkasnya tidak dihapus supaya riwayat migration pada database yang sudah berjalan tetap utuh.
- B2 — daftar paket dirapikan; Dockerfile juga ditulis ulang menjadi multi-stage dengan `composer install --no-dev`.
- B3 — dibuatkan `App\Support\Database\DateExpression` yang menghasilkan ekspresi sesuai driver aktif (MySQL, SQLite, PostgreSQL).

### 1.2 Salah hasil — data tampil keliru

| # | Masalah | Dampak |
|---|---------|--------|
| B4 | `kelengkapan` di-`json_encode()` manual di controller, lalu di-encode **sekali lagi** oleh cast `array` di model | Yang tersimpan adalah string JSON di dalam string JSON. Saat dibaca kembali bukan array, sehingga **daftar kelengkapan di halaman detail selalu tampil kosong** padahal datanya ada. |
| B5 | `LaporanExport::map()` memakai `static $no` | Variabel `static` melekat pada method, bukan instance. Bila export dipanggil dua kali dalam satu proses, **nomor urut lanjut dari export sebelumnya** alih-alih kembali ke 1. |
| B6 | `laporan/index.blade.php` menimpa `$namaBulan` dengan `translatedFormat('F')` | `APP_LOCALE` bernilai `en`, jadi judul laporan tampil **"March"** alih-alih "Maret" di antarmuka yang seluruhnya berbahasa Indonesia. |
| B7 | `ServisController::destroy()` mengirim `->with('error', ...)` | Layout lama hanya merender `session('success')`. Pesan "Data tidak ditemukan" **tidak pernah muncul** — pengguna melihat halaman berganti tanpa keterangan apa pun. |

### 1.3 Kode mati dan tidak terpakai

| # | Temuan |
|---|--------|
| B8 | `LaporanController::clearCache()` ada tetapi **tidak pernah didaftarkan ke route mana pun**. |
| B9 | `ServisController::cekForm()` ada tetapi tidak pernah dipanggil. |
| B10 | Route `POST /cek-servis` (`servis.cek.status`) terdaftar, tetapi formulirnya memakai `GET`. |
| B11 | `resources/views/servis/dashboard.blade.php` merujuk `$total`, `$menunggu`, `$proses`, `$selesai` yang **tidak pernah dikirim controller mana pun** — akan galat fatal bila sampai dirender. |
| B12 | `resources/views/layouts/navigation.blade.php` tidak direferensikan berkas mana pun. |

**Perbaikan:** `clearCache` kini punya route (`POST /laporan/segarkan`) dan tombol di antarmuka; sisanya dihapus.

### 1.4 Query di dalam view (pelanggaran MVC)

| # | Masalah | Dampak |
|---|---------|--------|
| B13 | `dashboard.blade.php` menjalankan **6 query Eloquent** di dalam blok `@php` | Logika bisnis bocor ke lapisan tampilan. `$recentServis` bahkan di-query **dua kali** — yang pertama tidak pernah dipakai. |
| B14 | `layouts/app.blade.php` memanggil `\App\Models\Servis::count()` | Satu query tambahan **pada setiap halaman** yang memakai layout, hanya untuk sebuah angka di lencana sidebar. |
| B15 | `invoice/pdf.blade.php` dan `invoice/cetak.blade.php` memanggil `Setting::get()` tiga kali | Template menembak database sendiri. |

**Perbaikan:** seluruh pengambilan data dipindahkan ke controller dan lapisan service. Angka status kini diambil dalam **satu query agregat**, lencana sidebar memakai view composer dengan cache satu menit, dan teks nota dikirim dari `InvoiceController`.

### 1.5 Bug lain

| # | Masalah |
|---|---------|
| B16 | `RoleMiddleware` membaca `auth()->user()->role` tanpa memeriksa apakah pengguna ada → galat 500 bila sesi kedaluwarsa. |
| B17 | Validasi `status` hanya `required|string` → nilai apa pun bisa tersimpan, merusak perhitungan laporan yang mencocokkan string persis. |
| B18 | `pola_kunci` ditempel langsung ke dalam literal string JavaScript. Nilai yang mengandung garis miring terbalik merusak sintaks skrip seluruh halaman. |
| B19 | Pencarian memakai `LIKE "%{$s}%"` tanpa menetralkan `%` dan `_` → mengetik `%` mengembalikan seluruh baris. |
| B20 | Penghapusan data servis bersifat permanen, padahal itu arsip transaksi. |
| B21 | Pencarian langsung tidak menjaga urutan permintaan → jawaban lambat dari pencarian lama bisa menimpa hasil terbaru. |

---

## 2. Masalah performa dan animasi berat

### 2.1 Animasi yang membebani perangkat

| # | Temuan | Mengapa berat |
|---|--------|---------------|
| P1 | **Tiga "blob" latar belakang** berukuran 600px, 500px, dan 350px dengan `filter: blur(80px)`, dianimasikan `drift 14s infinite alternate` | Blur radius sebesar itu memaksa GPU menggambar ulang area sangat luas, dan animasi `transform` + `scale` membuatnya terjadi **di setiap frame, tanpa henti**. Di laptop kelas bawah dan ponsel, kipas berputar dan baterai terkuras hanya untuk latar belakang dekoratif. |
| P2 | `.dashboard-card { animation: float 6s ease-in-out infinite; }` pada elemen ber-`box-shadow: 0 24px 60px rgba(...)` | Bayangan seluas itu harus dirasterisasi ulang mengikuti pergerakan kartu, terus-menerus selama halaman terbuka. |
| P3 | **Tidak ada dukungan `prefers-reduced-motion` sama sekali** | Pengguna yang mematikan animasi di sistem operasinya — termasuk yang sensitif terhadap gerakan — tetap menerima seluruh animasi. |

**Perbaikan:** blob dan animasi melayang dihapus seluruhnya. Kedalaman visual sekarang dibangun dari warna, garis, dan bayangan tipis statis. Satu aturan global `@media (prefers-reduced-motion: reduce)` mematikan seluruh animasi dan transisi bagi yang memintanya.

### 2.2 Skrip pihak ketiga

| # | Temuan | Dampak |
|---|--------|--------|
| P4 | `resources/views/servis/cek.blade.php` memuat `https://cdn.tailwindcss.com` | Itu adalah **kompiler Tailwind versi browser**, sekitar 300 KB JavaScript yang harus diunduh dan dijalankan lebih dulu, lalu menyusun CSS di sisi pengguna **pada setiap kunjungan**. Padahal ini halaman publik yang paling sering dibuka pelanggan, umumnya dari jaringan seluler. |
| P5 | Statistik memuat Chart.js dari `cdn.jsdelivr.net` **tanpa Subresource Integrity**, lewat `<script>` di tengah body | Halaman rusak bila CDN bermasalah, dan perubahan di sisi CDN pada halaman berisi data keuangan tidak akan terdeteksi. |
| P6 | `@import url(...)` untuk Google Fonts di dalam blok `<style>` | `@import` menunda render karena baru diminta setelah CSS induk selesai diurai. |

**Perbaikan:**
- P4 — CDN dihapus; halaman memakai CSS hasil build. Tidak ada lagi satu pun skrip pihak ketiga di halaman publik.
- P5 — Chart.js dipasang lewat npm dan dibundel Vite sebagai **entry terpisah** (`resources/js/statistik.js`), sehingga hanya diunduh pada halaman statistik, bukan di setiap halaman. Hanya komponen chart yang dipakai yang didaftarkan, agar bundelnya minimal.
- P6 — diganti `<link rel="preconnect">` + `<link rel="stylesheet">` di `<head>`.

### 2.3 Penanganan kejadian yang boros

| # | Temuan | Dampak |
|---|--------|--------|
| P7 | Sekitar **40 atribut `onmouseover` / `onmouseout`** yang menulis ulang `element.style` | Setiap gerakan kursor memicu perhitungan ulang gaya dan tata letak. Selain itu, Content Security Policy jadi terpaksa mengizinkan skrip inline. |
| P8 | `<select onchange="document.getElementById('filterForm').submit()">` | Bukan hanya soal performa — ini **bug aksesibilitas**. Pengguna keyboard yang menekan panah bawah sekali langsung memuat ulang halaman, sehingga pilihan berikutnya tidak pernah terjangkau. |
| P9 | Pencarian langsung tidak menampilkan indikator apa pun di area tabel | Halaman terasa macet, lalu tinggi konten melompat saat data datang. |

**Perbaikan:** seluruh efek sorot menjadi aturan CSS; dropdown filter memakai tombol "Terapkan" yang eksplisit; pencarian langsung kini menampilkan **skeleton loader** dan mengabaikan jawaban yang datang terlambat.

---

## 3. Perbaikan keamanan

### 3.1 Kritis

#### K1 — Siapa pun bisa mendaftar dan langsung menjadi Admin

`RegisteredUserController` memberi `'role' => 'admin'` kepada setiap pendaftar,
dan route `/register` terbuka untuk umum. Artinya siapa pun yang menemukan
alamat situs bisa membuat akun dengan **akses penuh ke seluruh data pelanggan**.

**Perbaikan:** pendaftaran mandiri dimatikan secara bawaan
(`AUTH_REGISTRATION_ENABLED=false`). Akun dibuat lewat
`php artisan user:create` atau seeder. Flag bisa dinyalakan sementara untuk
keperluan demonstrasi.

#### K2 — Owner sebenarnya bisa mengubah dan menghapus data

Seluruh route servis berada dalam satu grup `role:admin|owner`. Komentar di
`routes/web.php` menyebut *"proteksi aksi di view & controller"*, tetapi di
controller **tidak ada pemeriksaan apa pun** — pembatasannya hanya berupa
`@if($userRole === 'admin')` yang menyembunyikan tombol.

Akun Owner cukup mengirim `POST /servis` atau `DELETE /servis/1` secara
langsung untuk membuat dan menghapus data, bertentangan dengan aturan bisnis
yang menyatakan Owner memantau tanpa hak mengubah.

**Perbaikan:** dibuat `ServisPolicy` sebagai sumber kebenaran tunggal,
dipanggil `$this->authorize()` di setiap aksi controller, dan route dipecah
menjadi grup baca (admin + owner) dan grup tulis (admin). Tampilan kini
memakai `@can`, sehingga tombol dan izin sesungguhnya tidak mungkin berbeda.
Ditutup oleh 11 test di `tests/Feature/Servis/HakAksesServisTest.php`.

#### K3 — Peran bisa dinaikkan lewat mass assignment

Kolom `role` ikut di dalam `$fillable` milik model `User`. Satu field tambahan
pada body request berpeluang menentukan peran akun.

**Perbaikan:** `role` dikeluarkan dari `$fillable`; nilainya hanya diberikan
lewat penetapan eksplisit di kode yang sudah diotorisasi.

#### K4 — PIN perangkat bisa ditebak habis

Endpoint `servis/{id}/pin-verify` tidak punya pembatasan laju sama sekali.
PIN 4 digit hanya punya 10.000 kemungkinan — seluruhnya bisa dicoba dalam
hitungan menit.

**Perbaikan:** dibatasi 5 percobaan per menit per kombinasi pengguna dan unit,
ditambah `throttle:10,1` di level route. Akses juga dipersempit: hanya Admin
yang boleh membuka kredensial perangkat, karena Owner yang sifatnya memantau
tidak punya keperluan atasnya.

### 3.2 Penting

| # | Temuan | Perbaikan |
|---|--------|-----------|
| K5 | `pola_kunci` tersimpan sebagai teks biasa | Dienkripsi lewat cast `EncryptedOrPlain`, yang tetap toleran terhadap baris lama sehingga tidak memaksa migrasi data. |
| K6 | `.env.example` mengirim `APP_DEBUG=true` | Diubah menjadi production-safe, dengan keterangan bahwa `APP_DEBUG=true` di server membocorkan isi `.env` dan jejak stack kepada siapa pun yang membuka halaman galat. |
| K7 | Tidak ada satu pun header keamanan | Middleware `SecurityHeaders`: `Content-Security-Policy`, `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, dan `Strict-Transport-Security` pada koneksi HTTPS. |
| K8 | Galat tak tertangani menampilkan jejak stack | Handler di `bootstrap/app.php` merender halaman 500 netral saat `APP_DEBUG` mati; pesan aslinya tetap masuk log. Ditambah halaman galat 403, 404, 419, 429, 500, dan 503 berbahasa Indonesia. |
| K9 | Kebijakan kata sandi hanya bawaan Laravel (8 karakter) | Minimal 10 karakter, wajib huruf besar-kecil, angka, dan simbol; di production ditolak bila pernah muncul di kebocoran data publik (`uncompromised()`). |
| K10 | Lupa kata sandi dan atur ulang tanpa pembatasan laju | `throttle:5,1` pada `forgot-password` dan `reset-password`; menahan penyalahgunaan pengiriman surel sekaligus penebakan alamat surel mana yang terdaftar. |
| K11 | Sesi tidak dienkripsi, cookie tidak dipaksa aman | `SESSION_ENCRYPT=true` dan `SESSION_SECURE_COOKIE` didokumentasikan di `.env.example`; di production seluruh URL dipaksa `https`. |
| K12 | Halaman publik `/cek-servis` mengirim seluruh kolom baris servis ke view | Query kini hanya mengambil kolom yang memang perlu diketahui pelanggan. Alamat, nomor WhatsApp, pola kunci, dan PIN tidak pernah ikut. Diverifikasi test. |
| K13 | `/cek-servis` tanpa pembatasan laju | `throttle:20,1` untuk meredam penebakan kode servis massal. Panjang kode acak juga dinaikkan dari 6 menjadi 8 karakter. |
| K14 | `kode_unik` bisa ditentukan dari request | Dikeluarkan dari `$fillable`; nilainya selalu dibangkitkan server. |
| K15 | Bulan dan tahun laporan langsung menjadi kunci cache tanpa pembatasan | Divalidasi dan dinormalkan; tanpa ini cache store bisa dibanjiri entri sampah hanya dengan mengubah query string. |
| K16 | Teks pengaturan nota tersimpan apa adanya | `strip_tags()` diterapkan sebelum validasi, sehingga tidak ada markup yang tersimpan sejak awal. |
| K17 | Data sensitif berpeluang tercatat di log saat request gagal | `dontFlash` diperluas: `kata_sandi`, `pola_kunci`, dan `pin`. |
| K18 | Penghapusan permanen atas arsip transaksi | Diganti soft delete. |

---

## 4. Optimasi SEO

Kondisi sebelumnya: halaman depan hanya punya `<title>` berisi
`config('app.name')` — tanpa meta description, tanpa canonical, tanpa Open
Graph, tanpa structured data. `robots.txt` berisi `Disallow:` kosong, yang
berarti **mengizinkan mesin pencari menelusuri seluruh halaman panel
berisi data pelanggan**.

Yang diterapkan:

| Aspek | Penerapan |
|-------|-----------|
| Meta dasar | Komponen `<x-seo>`: title bertingkat, meta description (dipotong 155 karakter), keywords, author, canonical. |
| Berbagi tautan | Open Graph dan Twitter Card lengkap, agar pratinjau di WhatsApp dan media sosial tampil benar. |
| Structured data | JSON-LD `LocalBusiness`, `WebSite` dengan `SearchAction` (menunjuk ke pelacakan servis), dan `FAQPage`. Kolom alamat serta telepon hanya ikut bila diisi di `.env`, **sehingga tidak ada data karangan yang dikirim ke mesin pencari**. |
| Kontrol indeks | Halaman publik `index, follow`; seluruh halaman di balik login diberi `noindex, nofollow` lewat meta tag **dan** header `X-Robots-Tag`. |
| `robots.txt` | Ditulis ulang: hanya halaman publik yang diizinkan, seluruh jalur panel dilarang, sitemap dicantumkan. |
| `sitemap.xml` | Route dinamis di `/sitemap.xml`, berisi halaman publik saja. |
| Struktur konten | Satu `<h1>` per halaman, hierarki heading rapi, `alt` pada gambar, `lang="id"`, tautan lewati navigasi, dan `aria-label` pada elemen interaktif. |
| Konten | Bagian FAQ berisi pertanyaan nyata pelanggan — sekaligus bahan `FAQPage` untuk potongan kaya di hasil pencarian. |
| Halaman legal | Kebijakan Privasi dan Syarat & Ketentuan. Selain praktik baik SEO, sistem ini memang menyimpan data pribadi dan kredensial perangkat pelanggan. |

---

## 5. Restrukturisasi ke HMVC

Struktur lama menumpuk seluruh fitur di satu tempat: `app/Http/Controllers/`,
`app/Models/`, `resources/views/`. Sekarang setiap fitur berdiri sebagai modul
mandiri:

```
app/Modules/
├── Dashboard/    Controllers/ Views/ routes.php
├── Servis/       Controllers/ Models/ Requests/ Policies/ Observers/ Services/ Views/ routes.php
├── Invoice/      Controllers/ Views/ routes.php
├── Laporan/      Controllers/ Services/ Exports/ Views/ routes.php
├── Statistik/    Controllers/ Services/ Views/ routes.php
└── Setting/      Controllers/ Models/ Requests/ Views/ routes.php
```

`App\Providers\ModuleServiceProvider` memindai folder `app/Modules` dan
mendaftarkan `routes.php` serta namespace view setiap modul secara otomatis.
Menambah modul baru cukup membuat folder — tidak ada berkas konfigurasi yang
perlu disentuh.

View dipanggil dengan namespace modulnya: `view('servis::index')`,
`view('laporan::pdf')`.

`routes/web.php` kini tinggal 30 baris, berisi hal lintas modul saja.

Lapisan yang tetap berada di `app/` sebagai inti bersama: model `User`,
scaffolding autentikasi Laravel Breeze, middleware, dan helper di `app/Support`.
Pembagian ini disengaja — autentikasi adalah urusan kerangka kerja, bukan
fitur domain, dan merupakan pola HMVC yang lazim dipakai proyek Laravel.

Perpindahan berkas dilakukan dengan `git mv` sehingga **riwayat commit setiap
berkas tetap terlacak**.

---

## 6. Keputusan desain antarmuka

Dari 30 pola yang menjadi rujukan, berikut yang dinilai cocok dan tidak untuk
sistem ini. Pertimbangan utamanya: ini perangkat kerja yang dipakai staf
berjam-jam setiap hari, bukan halaman pemasaran.

### Diterapkan

| Pola | Alasan |
|------|--------|
| Ikon bergaya lucide | Sudah dipakai kode lama; kini diseragamkan (`stroke-width` 2, garis luar tanpa isian). |
| Sudut membulat lembut | Token `--r-sm/md/lg` (8/12/16px). |
| Bayangan tipis | Tiga tingkat kedalaman, tanpa bayangan berat. |
| Inter / Space Grotesk | Plus Jakarta Sans untuk teks, Space Grotesk untuk judul dan angka. |
| Tiga kartu fitur sebaris | Grid responsif; dipakai empat kartu karena memang ada empat fitur nyata. |
| Butir bertanda centang | Dibuat dengan CSS, bukan emoji, agar konsisten lintas sistem operasi dan tidak dibacakan pembaca layar sebagai "tanda centang tebal". |
| Garis warna di tepi kartu | Penanda status pada kartu dashboard. |
| Animasi sorot | Dipertahankan tetapi murni CSS dan tunduk pada `prefers-reduced-motion`. |
| Skeleton loader | **Ditambahkan** (daftar rujukan menyebut ketiadaannya sebagai kekurangan). |
| Halaman privasi & syarat layanan | **Ditambahkan**, karena sistem ini menyimpan data pribadi pelanggan. |

### Tidak diterapkan

| Pola | Alasan |
|------|--------|
| Radial orb / blob | **Dihapus** — penyebab utama beban render (lihat P1). |
| Jendela terminal | Hiasan titik merah-kuning-hijau ala macOS di kartu beranda dihapus; ini bukan perkakas pengembang. |
| Emoji | Seluruhnya dihapus (📱 📋 🔧 ✅ ❌ dan lainnya). Emoji tampil berbeda di tiap sistem operasi, ikut terbaca pembaca layar, dan menurunkan kesan profesional pada dokumen nota. |
| Gradien tajam, warna neon, warna pelangi | Menurunkan keterbacaan pada antarmuka padat data. |
| Efek kaca / liquid glass | Latar transparan berblur menurunkan kontras teks dan memakan daya render. |
| Testimoni palsu, tiga tingkat harga, bento grid, grid titik, ikon kilau, panah beranimasi | Tidak relevan; ini sistem operasional internal, bukan halaman penjualan produk. |
| Pola "bukan x, melainkan y" | Gaya penulisan pemasaran yang tidak pada tempatnya. |
| Latar putih murni | Dipakai `#f4f6fb` — lebih nyaman untuk pemakaian panjang. |
| Ungu dan hitam | Warna merek navy dan biru dipertahankan. |
| Demo produk palsu | **Diperbaiki** — angka di beranda dulu ditulis tetap di template (16, 7, 5, 4) dan tidak pernah berubah. Sekarang dibaca langsung dari database. |

---

## 7. Daftar periksa keamanan sebelum push

| # | Butir | Status | Keterangan |
|---|-------|:------:|------------|
| 1 | API key aman | ✅ | Tidak ada API key pihak ketiga di proyek ini. Seluruh nilai rahasia dibaca lewat `env()`. |
| 2 | `.env` tidak publik | ✅ | Terdaftar di `.gitignore`, dikecualikan di `.dockerignore`, dan document root diarahkan ke `public/`. Diverifikasi: `git ls-files` tidak memuat `.env`. |
| 3 | Tidak ada rahasia tertulis di kode | ✅ | Kata sandi seeder dipindah ke `.env` atau dibangkitkan acak. Tidak ada kredensial bawaan. |
| 4 | Cek rahasia di riwayat Git | ✅ | `git log --all -- .env` kosong; `.env` tidak pernah ter-commit. |
| 5 | Mode debug mati | ✅ | `.env.example` kini production-safe dengan keterangan; `php.ini` kontainer memaksa `display_errors = Off`. |
| 6 | Galat tidak bocor | ✅ | Handler exception + halaman galat khusus (K8). |
| 7 | Validasi input | ✅ | Form Request untuk setiap aksi tulis; `status` dibatasi daftar resmi; nomor WhatsApp dan pola kunci divalidasi pola. |
| 8 | Sanitasi input | ✅ | `prepareForValidation` merapikan spasi; `strip_tags` pada teks nota; wildcard `LIKE` dinetralkan. |
| 9 | Anti SQL injection | ✅ | Seluruhnya lewat Eloquent/Query Builder dengan parameter terikat. Tidak ada `DB::raw` yang menyusun nilai dari input. |
| 10 | Anti XSS | ✅ | Blade `{{ }}` (tidak ada `{!! !!}` pada data pengguna); data ke JavaScript lewat `@json`; header CSP. |
| 11 | Autentikasi di sisi server | ✅ | Middleware `auth` + `role` + `ServisPolicy` di controller. |
| 12 | Pemeriksaan hak akses pengguna | ✅ | Policy dipanggil di setiap aksi; diverifikasi test. |
| 13 | Peran admin aman | ✅ | `role` bukan mass assignable; pendaftaran mandiri ditutup (K1, K3). |
| 14 | Database tidak publik | 📋 | Tindakan sisi server. Panduan lengkap ditulis di `.env.example`: batasi bind ke localhost, jangan buka port 3306 ke internet. |
| 15 | Hak akses database ketat | 📋 | Perintah SQL pembuatan pengguna terbatas disediakan di `.env.example` (`SELECT, INSERT, UPDATE, DELETE` saja; hak DDL hanya sementara saat migrasi). |
| 16 | Hash kata sandi | ✅ | Cast `hashed` (bcrypt, 12 putaran). PIN perangkat juga di-hash; pola kunci dienkripsi. |
| 17 | Sesi aman | ✅ | Regenerasi ID saat masuk, invalidasi saat keluar, `HttpOnly`, `SameSite=Lax`, `SESSION_ENCRYPT=true`, `Secure` di HTTPS. |
| 18 | Atur ulang kata sandi aman | ✅ | Token bertanda waktu bawaan Laravel + pembatasan laju + kebijakan kata sandi kuat. |
| 19 | Batasi unggahan berkas | ➖ | **Tidak berlaku** — sistem ini tidak memiliki fitur unggah berkas. Batas `upload_max_filesize` tetap diperketat di konfigurasi PHP kontainer sebagai pertahanan berlapis. |
| 20 | Pindai berkas unggahan | ➖ | **Tidak berlaku** — alasan sama dengan butir 19. Fitur unggah sengaja tidak ditambahkan karena di luar cakupan permintaan. |

Keterangan: ✅ diterapkan di kode · 📋 didokumentasikan sebagai langkah sisi server · ➖ tidak berlaku

---

## 8. Yang belum dikerjakan

Disebutkan terbuka supaya jelas batas pekerjaan ini:

1. **CSP masih mengizinkan `'unsafe-inline'`** untuk skrip dan gaya. Banyak
   template membawa blok `<style>` dan `<script>` di dalam berkas Blade-nya
   sendiri. Menghapusnya sepenuhnya berarti menulis ulang hampir seluruh
   tampilan, dan berisiko besar untuk berkas skripsi. Perbaikan lanjutannya
   adalah memindahkan gaya ke berkas CSS dan memakai *nonce* per request.

2. **Beberapa atribut `onclick` masih tersisa** di `servis/create.blade.php`
   dan `servis/edit.blade.php`. Keduanya memanggil fungsi yang didefinisikan
   di halaman yang sama untuk papan pola kunci dan masukan PIN. Dibiarkan
   karena fungsinya berjalan normal dan pembongkarannya berisiko pada dua
   berkas terbesar di proyek.

3. **Butir 14 dan 15 daftar keamanan** adalah konfigurasi server, bukan kode.
   Langkahnya sudah ditulis di `.env.example`, tetapi harus dijalankan pada
   server tempat aplikasi dipasang.

4. **Data lama belum ikut dienkripsi.** Kolom `pola_kunci` pada baris yang
   sudah ada tetap berupa teks biasa sampai barisnya disimpan ulang. Cast
   `EncryptedOrPlain` memang dibuat toleran agar tidak ada data yang rusak.
   Bila ingin menyeragamkan sekarang, jalankan sekali:
   `Servis::withTrashed()->whereNotNull('pola_kunci')->each->save();`

5. **Kelengkapan yang terlanjur tersimpan ganda** (bug B4) tetap berbentuk
   JSON berlapis di baris lama. View sudah memasang penanganan cadangan
   sehingga tetap tampil benar, tetapi datanya baru rapi setelah baris
   tersebut disunting ulang.
