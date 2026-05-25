<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

# SIKEPK (Sistem Informasi Kelayakan Protokol Etik)

SIKEPK adalah sebuah sistem manajemen pengajuan dan peninjauan protokol etik penelitian kesehatan berbasis web. Aplikasi ini dibangun dengan framework **Laravel 12** dan **Filament PHP v4**, dirancang untuk mempermudah alur kerja Komite Etik Penelitian Kesehatan dalam memproses pengajuan protokol dari peneliti hingga mendapatkan persetujuan (Ethical Clearance).

---

## ✨ Fitur Utama

- **Multi-Panel System:** Memiliki panel khusus untuk setiap peran (Admin, Reviewer, dan Peneliti/User).
- **Role-Based Access Control (RBAC):** Hak akses berlapis menggunakan Spatie Permission dan Filament Shield. Tersedia peran: `super_admin`, `admin`, `sekertaris`, `reviewer`, dan `user`.
- **Alur Pengajuan Protokol:** Peneliti dapat mengunggah form pengajuan, dokumen pernyataan, dan bukti pembayaran.
- **Manajemen Kelompok Reviewer:** Admin dapat membagi reviewer ke dalam kelompok-kelompok kerja yang dipimpin oleh seorang Ketua.
- **Review System:** Penugasan protokol ke kelompok reviewer tertentu untuk diberikan catatan/komentar kelayakan.
- **Tracking Status Real-time:** Memantau tahapan protokol mulai dari Pending, In Review, Under Revision, Approved, hingga Rejected.
- **Impersonate (Login As):** Super Admin dapat menyamar (*login as*) menjadi akun user/reviewer manapun untuk keperluan *troubleshooting* dengan aman (melewati pengecekan MFA).
- **Notifikasi Terintegrasi:** Notifikasi database *real-time* untuk setiap aktivitas penting.

---

## 🛠️ Tech Stack

- **Backend:** Laravel 12.x, PHP 8.4+
- **Frontend / Admin Panel:** Filament PHP v4, Livewire v3, Alpine.js
- **Styling:** Tailwind CSS v4
- **Database:** SQLite (Default) / MySQL / PostgreSQL
- **Build Tool:** Vite 7.x
- **Package Penting:**
  - `spatie/laravel-permission`
  - `bezhansalleh/filament-shield`
  - `spatie/laravel-activitylog`

---

## 🚀 Instalasi & Menjalankan Project Lokal

### Prasyarat
- PHP >= 8.4
- Composer
- Node.js & NPM
- Ekstensi PHP: `mbstring`, `xml`, `pdo`, `sqlite3` (atau database pilihan)

### Langkah-Langkah

1. **Clone repositori ini:**
   ```bash
   git clone https://github.com/username-anda/simkepk-filament.git
   cd simkepk-filament
   ```

2. **Install dependensi PHP & Node.js:**
   ```bash
   composer install
   npm install
   ```

3. **Salin file environment & generate app key:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database (.env):**
   Ubah pengaturan `.env` sesuai dengan database Anda. Secara bawaan Laravel akan menggunakan SQLite:
   ```env
   DB_CONNECTION=sqlite
   ```

5. **Migrasi database dan jalankan Seeder:**
   ```bash
   php artisan migrate:fresh --seed
   ```
   *(Perintah ini juga akan meng-generate permission dari Filament Shield dan membuat akun bawaan jika ada di seeder).*

6. **Build asset untuk frontend (Tailwind & Filament):**
   ```bash
   npm run build
   ```

7. **Jalankan server lokal:**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses di `http://localhost:8000`.

---

## 🚪 Akses Login (Panel Entry Points)

Sistem ini memisahkan jalur *login* sesuai dengan panel yang dituju:

- **Panel Admin / Super Admin:** `http://localhost:8000/admin`
- **Panel Reviewer / Tim Komite Etik:** `http://localhost:8000/reviewer`
- **Panel Peneliti (User Umum):** `http://localhost:8000/user`

---

## 🏗️ Struktur & Pola Pengembangan (Development)

Untuk *developer* yang ingin berkontribusi, harap perhatikan hal-hal berikut:

- **Filament Custom Schemas:** Project ini memisahkan komponen formulir dan tabel ke dalam *class* Schema tersendiri (berada di folder `app/Filament/Resources/.../Schemas` dan `Tables`).
- **Observer:** Aksi yang mengikuti perubahan pada `Protocol` (seperti *create*, *update*, *delete*) dikelola di `app/Observers/ProtocolObserver.php`.
- **Soft Deletes:** Hampir semua entitas utama (`User`, `Protocol`, `ReviewerKelompok`, `Document`) menggunakan metode Soft Delete. Harap perhatikan hal ini jika membuat *query* khusus.
- **Keamanan Impersonate:** Telah disiapkan *middleware* khusus (`BypassMfaIfImpersonated`) dan modifikasi autentikasi sesi pada Filament agar Super Admin dapat melakukan *Login As* tanpa mengalami kendala *logout* paksa atau terjebak di *page* MFA.
- **Code Formatting:** Jalankan perintah `vendor/bin/pint` sebelum melakukan *commit* untuk menjaga standar kerapian *code*.

---

## 📝 Lisensi

Meskipun framework utamanya (Laravel & Filament) bersifat *open-sourced* di bawah lisensi MIT, lisensi kode sumber (*source code*) pada aplikasi manajemen SIKEPK ini tunduk pada kebijakan hak cipta organisasi/institusi pemilik repository ini.

---
*Dibuat untuk mempermudah proses Komite Etik Penelitian Kesehatan.*
