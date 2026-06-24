# SanitaCheck

## Deskripsi Singkat
SanitaCheck adalah sistem monitoring kebersihan dan sanitasi fasilitas umum (seperti toilet, kantin, ruang tunggu, tempat cuci tangan, dan ruang kelas) di lingkungan fasilitas umum atau kesehatan. Sistem ini mencatat inspeksi sanitasi berkala, menganalisis status kelayakan (bersih, perlu dibersihkan, buruk), dan memberikan rekomendasi preventif pencegahan risiko kesehatan bagi masyarakat.

## Anggota Kelompok
1. Ceo Adyatma86 - NIM UAS 001 - Fullstack Developer & Team Leader
2. Anggota 2 - NIM UAS 002 - Frontend Designer
3. Anggota 3 - NIM UAS 003 - Backend Engineer & QA
4. Anggota 4 - NIM UAS 004 - Technical Writer & Deployment Specialist

## Fitur Aplikasi
- **Login Admin/Petugas:** Autentikasi aman untuk membedakan admin (pengelola fasilitas) dan petugas lapangan (pencatat inspeksi).
- **Dashboard Metrik:** Memantau total fasilitas, inspeksi harian, rasio kepatuhan higienis, dan indikator risiko kesehatan.
- **CRUD Fasilitas Umum:** Mengelola registrasi fasilitas baru, lokasi, penanggung jawab, dan status operasional.
- **CRUD Inspeksi Sanitasi:** Form inspeksi interaktif yang mencakup checklist air bersih, sabun, indikator bau tak sedap, dan catatan temuan.
- **REST API Sederhana:** Integrasi mulus antara backend Laravel dan frontend PHP Native.
- **Frontend PHP Native Responsif:** Halaman beranda, pencarian fasilitas, filter status kebersihan, dan detail riwayat serta rekomendasi kesehatan secara dinamis.

## Teknologi
- **Backend:** Laravel 11.x
- **Frontend:** PHP Native + HTML5 + CSS3 + Vanilla JavaScript
- **Database:** MySQL / MariaDB
- **Framework UI:** Tailwind CSS (via CDN) & Google Material Symbols Icons
- **Integrasi API:** Fetch API (AJAX)

## Cara Instalasi
1. **Clone repository:**
   ```bash
   git clone <url-repository>
   ```
2. **Jalankan composer install:**
   ```bash
   composer install
   ```
3. **Copy .env.example menjadi .env:**
   ```bash
   copy .env.example .env
   ```
4. **Atur database:**
   Buat database baru bernama `SanitaCheck` di MySQL local Anda (atau sesuaikan DB_DATABASE, DB_USERNAME, dan DB_PASSWORD di file `.env`).
5. **Jalankan migration dan seeder:**
   ```bash
   php artisan migrate:fresh --seed
   ```
6. **Jalankan server:**
   - Untuk backend Laravel: `php artisan serve` (berjalan di http://127.0.0.1:8000)
   - Untuk frontend PHP Native: Pindahkan folder `/phpnative` ke dalam direktori `/www` Laragon Anda (atau root server web Anda seperti XAMPP `/htdocs`), lalu buka http://localhost/phpnative/ di browser Anda.

## Akun Demo
- **Admin:**
  - Email: `admin@sanitacheck.com`
  - Password: `password123`
- **Petugas:**
  - Email: `petugas@sanitacheck.com`
  - Password: `password123`

## Link Deploy
- **Frontend:** http://localhost/phpnative
- **Backend/Admin:** http://127.0.0.1:8000

## Endpoint API
| Method | Endpoint | Keterangan |
| --- | --- | --- |
| GET | `/api/fasilitas` | Menampilkan seluruh daftar fasilitas umum aktif beserta status kebersihannya |
| GET | `/api/fasilitas/{id}/inspeksi` | Menampilkan riwayat laporan inspeksi sanitasi detail pada fasilitas tertentu |
| POST | `/api/inspeksi-sanitasi` | Menambahkan/mencatat laporan inspeksi sanitasi baru dari petugas lapangan |
| GET | `/api/fasilitas/status/{status}` | Memfilter daftar fasilitas umum berdasarkan status kebersihannya (`bersih`, `perlu dibersihkan`, `buruk`) |

## AI Usage Log
1. **Perancangan Migrasi & Skema Relasional:** AI membantu memetakan relasi One-to-Many antara model `User`, `Fasilitas`, dan `Inspeksi` untuk memastikan foreign keys terhubung dengan benar.
2. **Scaffolding API:** Memanfaatkan AI untuk mempercepat pembuatan `ApiController` yang memenuhi standar validasi request, formatting JSON response, dan filter relasi database.
3. **Frontend Interaksi Fetch API:** Pembuatan skrip `app.js` yang modular menggunakan vanilla JavaScript untuk integrasi ke endpoint Laravel API secara dinamis serta penyediaan fitur penggantian host API secara instan (API Config widget).
4. **Pembuatan Laporan & Dokumentasi:** AI digunakan dalam memformat README dan pembuatan checklist tugas `task.md`.

## Pembagian Tugas
- **Ceo Adyatma86 (NIM UAS 001):** Mengoordinasikan seluruh alur pengembangan, integrasi REST API antara backend Laravel dengan frontend PHP native, merancang skema database, dan mengimplementasikan fitur autentikasi serta role.
- **Anggota 2 (NIM UAS 002):** Mengembangkan desain mockup di folder `/design` ke dalam layout HTML/Tailwind CSS yang responsif untuk beranda, daftar data, pencarian, dan detail fasilitas.
- **Anggota 3 (NIM UAS 003):** Membuat migration file, model Laravel, dan controller CRUD untuk Panel Admin/Petugas, serta melakukan pengujian keamanan (password hashing & middleware protection).
- **Anggota 4 (NIM UAS 004):** Menyusun README.md, AI Usage Log, menyiapkan data dummy (Seeder), dan mempersiapkan file dokumentasi pengumpulan UAS.
