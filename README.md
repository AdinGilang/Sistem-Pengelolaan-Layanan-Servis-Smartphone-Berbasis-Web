# Sistem Pengelolaan Layanan Servis Smartphone Berbasis Web

Sistem Pengelolaan Layanan Servis Smartphone Berbasis Web merupakan aplikasi yang dikembangkan untuk membantu digitalisasi proses layanan servis pada Phone Repair. Sistem ini memudahkan pengelolaan data pelanggan, pencatatan servis, pemantauan status perbaikan, hingga pembuatan laporan dan invoice secara terkomputerisasi.

Aplikasi memiliki dua peran pengguna, yaitu Admin dan Owner. Admin dapat mengelola data pelanggan, data servis, status perbaikan, serta mencetak invoice servis. Sementara itu, Owner dapat memantau dashboard, laporan, statistik, dan aktivitas servis secara real-time tanpa hak untuk mengubah data.

Sistem dibangun menggunakan Laravel sebagai framework backend, MySQL sebagai database, serta menerapkan arsitektur **HMVC (Hierarchical Model-View-Controller)** untuk menghasilkan aplikasi yang terstruktur, mudah dikembangkan, dan mudah dipelihara.

Melalui proyek ini, saya mengimplementasikan berbagai konsep pengembangan web, seperti autentikasi pengguna, manajemen data berbasis CRUD, relasi database, pembuatan laporan, dashboard statistik, serta generasi invoice dalam format PDF.

---

## Fitur Utama

- [x] Login Multi Role (Admin & Owner)
- [x] Pelacakan Status Servis oleh pelanggan tanpa perlu akun
- [x] Dashboard Monitoring
- [x] Manajemen Data Pelanggan
- [x] Manajemen Data Servis
- [x] Input Kerusakan Smartphone
- [x] Update Status Perbaikan
- [x] Pencarian Data Servis secara langsung
- [x] Cetak Invoice PDF (kertas termal 58mm)
- [x] Laporan Servis (tampilan, PDF, Excel, cetak)
- [x] Statistik Servis
- [x] Pengaturan teks nota

---

## Teknologi yang Digunakan

| Lapisan | Teknologi |
|---------|-----------|
| Framework | Laravel 12 |
| Bahasa | PHP 8.3 |
| Database | MySQL 8 (SQLite untuk pengujian) |
| Tampilan | Blade, Tailwind CSS 3, Alpine.js |
| Build | Vite 7 |
| Grafik | Chart.js 4 (dibundel lokal) |
| PDF | DomPDF |
| Excel | Maatwebsite Excel |
| Pengujian | Pest 4 |

---

## Arsitektur

### HMVC — modul mandiri

Setiap fitur berdiri sebagai modul yang membawa seluruh lapisannya sendiri.

```
app/
├── Modules/                          Modul fitur (HMVC)
│   ├── Dashboard/
│   │   ├── Controllers/
│   │   ├── Views/
│   │   └── routes.php
│   ├── Servis/
│   │   ├── Controllers/              ServisController
│   │   ├── Models/                   Servis
│   │   ├── Requests/                 StoreServisRequest, UpdateServisRequest
│   │   ├── Policies/                 ServisPolicy
│   │   ├── Observers/                ServisObserver
│   │   ├── Services/                 ServisService
│   │   ├── Views/
│   │   └── routes.php
│   ├── Invoice/
│   ├── Laporan/                      + Exports/, Services/
│   ├── Statistik/                    + Services/
│   └── Setting/
│
├── Http/                             Inti bersama
│   ├── Controllers/Auth/             Scaffolding Laravel Breeze
│   ├── Controllers/                  Home, Legal, Sitemap, Profile
│   └── Middleware/                   RoleMiddleware, SecurityHeaders
├── Models/User.php
├── Providers/
│   ├── AppServiceProvider.php
│   └── ModuleServiceProvider.php     Pemuat modul otomatis
├── Support/
│   ├── Casts/EncryptedOrPlain.php
│   └── Database/DateExpression.php
└── Console/Commands/CreateUserCommand.php
```

`ModuleServiceProvider` memindai folder `app/Modules` lalu mendaftarkan
`routes.php` dan namespace view setiap modul secara otomatis. **Menambah modul
baru cukup dengan membuat folder** — tidak ada berkas konfigurasi yang perlu
disentuh.

View dipanggil dengan namespace modulnya:

```php
return view('servis::index', compact('servis'));
return view('laporan::pdf', $data);
```

### Pembagian tanggung jawab

```
Request → Route modul → Middleware (auth, role, throttle)
        → Form Request (validasi & otorisasi)
        → Controller (alur HTTP)
        → Service (query & aturan bisnis)
        → Model
        → View (murni menampilkan)
```

Tidak ada query database di dalam berkas Blade.

### Pembagian hak akses

| Kemampuan | Admin | Owner |
|-----------|:-----:|:-----:|
| Melihat dashboard | ✅ | ✅ |
| Melihat data servis | ✅ | ✅ |
| Menambah / mengubah / menghapus servis | ✅ | ❌ |
| Melihat & mencetak invoice | ✅ | ✅ |
| Melihat kredensial perangkat (PIN, pola kunci) | ✅ | ❌ |
| Pengaturan teks nota | ✅ | ❌ |
| Laporan & statistik | ❌ | ✅ |

Aturan ini ditegakkan `ServisPolicy` di sisi server, bukan sekadar
menyembunyikan tombol di tampilan.

---

## Cara Menjalankan

### Prasyarat

- PHP 8.2 atau lebih baru
- Composer 2
- Node.js 20 atau lebih baru
- MySQL 8 (atau MariaDB 10.6+)

### Langkah pemasangan

```bash
git clone https://github.com/AdinGilang/Sistem-Pengelolaan-Layanan-Servis-Smartphone-Berbasis-Web.git
cd Sistem-Pengelolaan-Layanan-Servis-Smartphone-Berbasis-Web

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Sunting `.env`, isi bagian database:

```env
DB_DATABASE=servis_app
DB_USERNAME=servis_app
DB_PASSWORD=kata-sandi-anda
```

Lalu:

```bash
php artisan migrate
php artisan db:seed      # membuat akun Admin dan Owner
npm run build
php artisan serve
```

Seeder menampilkan kata sandi akun yang dibuat **sekali** di terminal —
catat saat itu juga. Untuk menentukan sendiri kata sandinya, isi lebih dulu
`SEED_ADMIN_PASSWORD` dan `SEED_OWNER_PASSWORD` pada `.env`.

### Membuat akun tambahan

Pendaftaran mandiri sengaja ditutup (lihat [Keamanan](#keamanan)). Akun dibuat
lewat perintah:

```bash
php artisan user:create
php artisan user:create --name="Budi" --email=budi@toko.test --role=admin
```

Kata sandi tidak pernah diterima sebagai opsi baris perintah supaya tidak ikut
tersimpan di riwayat shell.

### Mode pengembangan

```bash
composer dev     # server + queue + log + vite dalam satu perintah
```

### Pengujian

```bash
php artisan test
```

112 test mencakup otorisasi antar peran, validasi, pembatasan laju, kebocoran
data di halaman publik, header keamanan, SEO, kegunaan panel admin (UAT), dan
rendering seluruh halaman.

---

## Keamanan

Beberapa keputusan yang perlu diketahui sebelum memasang di server:

1. **Pendaftaran mandiri ditutup secara bawaan.** Aplikasi ini hanya untuk staf
   internal. Nyalakan hanya bila memang dibutuhkan, misalnya saat demonstrasi:

   ```env
   AUTH_REGISTRATION_ENABLED=true
   ```

2. **Wajib diubah di server:**

   ```env
   APP_ENV=production
   APP_DEBUG=false
   LOG_LEVEL=error
   SESSION_SECURE_COOKIE=true
   ```

   `APP_DEBUG=true` di server membuat halaman galat menampilkan isi `.env`,
   jejak stack, dan potongan query database kepada siapa pun yang membukanya.

3. **Jangan memakai akun root untuk database.** Perintah SQL pembuatan pengguna
   terbatas tersedia di `.env.example`.

4. **Kredensial perangkat pelanggan.** PIN disimpan sebagai hash bcrypt satu
   arah — tidak dapat dikembalikan ke bentuk aslinya oleh siapa pun. Pola kunci
   disimpan terenkripsi karena teknisi perlu membacanya kembali.

5. **Halaman di balik login tidak diindeks** mesin pencari, lewat meta tag dan
   header `X-Robots-Tag`.

Rincian lengkap pemeriksaan dan perbaikan ada di [AUDIT.md](AUDIT.md).

---

## Pemasangan dengan Docker

```bash
docker build -t servis-app .
docker run -p 8080:80 \
  -e APP_KEY="base64:..." \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e DB_HOST=... -e DB_DATABASE=... -e DB_USERNAME=... -e DB_PASSWORD=... \
  -e RUN_MIGRATIONS=true \
  servis-app
```

Build memakai tiga tahap: aset frontend, dependensi PHP tanpa paket
pengembangan, dan runtime Apache + PHP 8.3. Cache konfigurasi dibangun saat
kontainer mulai, bukan saat build, karena nilainya bergantung pada variabel
lingkungan di server.

---

## Tangkapan Layar

### Login
<img width="615" height="710" alt="Halaman login" src="https://github.com/user-attachments/assets/5355db14-f6e2-427f-a2c8-a511b8d8c3de" />

### Dashboard Admin
<img width="1917" height="871" alt="Dashboard admin" src="https://github.com/user-attachments/assets/ec4b7ca0-4127-47ab-afa2-ae128ec8c276" />

**Data Servis**
<img width="1919" height="853" alt="Daftar data servis" src="https://github.com/user-attachments/assets/156b4854-86df-4a1d-a240-0af32d83b99e" />

**Detail Servis**
<img width="633" height="797" alt="Detail servis" src="https://github.com/user-attachments/assets/7f722b31-e4b6-45eb-8fe5-bae6deadf696" />

**Invoice**
<img width="364" height="841" alt="Invoice servis" src="https://github.com/user-attachments/assets/016373e7-6f27-4d04-ab42-e83c3b6f450c" />

### Dashboard Owner

Yang membedakan dashboard Owner: tersedia fitur laporan servis dan statistik.

**Laporan Servis**
<img width="1902" height="849" alt="Laporan servis" src="https://github.com/user-attachments/assets/e0518205-5b4d-4990-a277-934647555b47" />

**Statistik Servis**
<img width="1890" height="839" alt="Statistik servis" src="https://github.com/user-attachments/assets/2b84c930-485b-4389-a3f0-722039d67ccd" />

> Catatan: tangkapan layar di atas diambil sebelum pembaruan antarmuka.

---

## Latar Belakang

Proyek ini dikembangkan sebagai tugas akhir/skripsi untuk membantu digitalisasi
proses layanan servis smartphone pada Phone Repair, yang sebelumnya masih
menggunakan pencatatan manual.

Konsep yang diimplementasikan:

- Authentication & Authorization berbasis peran
- CRUD Operations
- Relational Database Design
- PDF & Excel Generation
- Dashboard & Reporting
- Arsitektur HMVC
- Pengujian otomatis

## Tujuan Pengembangan

Sistem ini dibuat untuk meningkatkan efisiensi pengelolaan layanan servis
smartphone, mengurangi kesalahan pencatatan data secara manual, serta
mempermudah proses monitoring dan pelaporan data servis pada Phone Repair.
