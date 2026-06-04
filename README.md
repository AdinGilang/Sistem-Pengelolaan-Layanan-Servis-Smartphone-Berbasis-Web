# Sistem Pengelolaan Layanan Servis Smartphone Berbasis Web

Sistem ini merupakan aplikasi berbasis web yang dirancang untuk membantu proses pengelolaan layanan servis smartphone pada Phone Repair. Aplikasi dikembangkan untuk mempermudah pencatatan data servis, pengelolaan pelanggan, pemantauan status perbaikan, hingga pembuatan laporan servis secara lebih cepat, terstruktur, dan efisien.

Sistem memiliki dua hak akses pengguna, yaitu Admin dan Owner. Admin bertugas mengelola data servis, menginput kerusakan, memperbarui status perbaikan, serta mencetak invoice servis. Sementara itu, Owner dapat memantau dashboard, laporan, statistik, dan data servis tanpa memiliki hak untuk mengubah data.

Aplikasi dibangun menggunakan framework Laravel dengan database MySQL serta menerapkan konsep arsitektur MVC (Model View Controller). Metode pengembangan sistem menggunakan model Waterfall yang terdiri dari tahap analisis kebutuhan, desain sistem, implementasi, pengujian, dan maintenance.

## Fitur Utama

- [x] Login Multi Role (Admin & Owner)
- [x] Pelacakan Status Servis 
- [x] Dashboard Monitoring
- [x] Manajemen Data Pelanggan
- [x] Manajemen Data Servis
- [x] Input Kerusakan Smartphone
- [x] Update Status Perbaikan
- [x] Pencarian Data Servis
- [x] Cetak Invoice PDF
- [x] Laporan Servis
- [x] Statistik Servis

## Teknologi yang Digunakan

*Laravel 12
*PHP 8.x
*MySQL
*Bootstrap 5
*JavaScript
*Blade Template Engine
*DomPDF

## Arsitektur yang digunakan:
* MVC (Model View Controller)
* Authentication & Authorization
* Role Based Access Control (Admin & Owner)

### Login:
<img width="615" height="710" alt="image" src="https://github.com/user-attachments/assets/5355db14-f6e2-427f-a2c8-a511b8d8c3de" />

### Dashboard Admin:
*Dashboard

<img width="1917" height="871" alt="image" src="https://github.com/user-attachments/assets/ec4b7ca0-4127-47ab-afa2-ae128ec8c276" />

*Data Servis

<img width="1919" height="853" alt="image" src="https://github.com/user-attachments/assets/156b4854-86df-4a1d-a240-0af32d83b99e" />

*Detail Servis

<img width="633" height="797" alt="image" src="https://github.com/user-attachments/assets/7f722b31-e4b6-45eb-8fe5-bae6deadf696" />


*Invoice


<img width="364" height="841" alt="image" src="https://github.com/user-attachments/assets/016373e7-6f27-4d04-ab42-e83c3b6f450c" />

*noted: yang membedakan dashboard owner yaitu, terdapat fitur laporan servis dan statistik servis/penjualan.

### Dashboard Owner:
*Laporan Servis
<img width="1902" height="849" alt="image" src="https://github.com/user-attachments/assets/e0518205-5b4d-4990-a277-934647555b47" />
*Statistik Servis
*<img width="1890" height="839" alt="image" src="https://github.com/user-attachments/assets/2b84c930-485b-4389-a3f0-722039d67ccd" />


## Permasalahan:
Project ini dikembangkan sebagai tugas akhir/Skripsi untuk membantu digitalisasi proses layanan servis smartphone pada Phone Repair yang sebelumnya masih menggunakan pencatatan manual.
Melalui project ini saya mengimplementasikan:
- Authentication & Authorization
- CRUD Operations
- Relational Database Design
- PDF Generation
- Dashboard & Reporting
- MVC Architecture

## Tujuan Pengembangan
Sistem ini dibuat untuk meningkatkan efisiensi pengelolaan layanan servis smartphone, mengurangi kesalahan pencatatan data secara manual, serta mempermudah proses monitoring dan pelaporan data servis pada Phone Repair.


