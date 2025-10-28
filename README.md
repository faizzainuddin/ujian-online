<div align="center">

<img src="public/assets/img/trustexam-illustration.svg" alt="TrustExam" height="120" />

# TrustExam — Ujian Online (Laravel 12)

Ringan, cepat, dan ramah admin untuk kebutuhan ujian sekolah.

[![PHP](https://img.shields.io/badge/PHP-%5E8.2-777bb4?logo=php)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-ff2d20?logo=laravel)](https://laravel.com)
[![CI](https://github.com/faizzainuddin/ujian-online/actions/workflows/ci.yml/badge.svg)](https://github.com/faizzainuddin/ujian-online/actions/workflows/ci.yml)
[![Stars](https://img.shields.io/github/stars/faizzainuddin/ujian-online?style=social)](https://github.com/faizzainuddin/ujian-online/stargazers)
[![Issues](https://img.shields.io/github/issues/faizzainuddin/ujian-online)](https://github.com/faizzainuddin/ujian-online/issues)
[![Last commit](https://img.shields.io/github/last-commit/faizzainuddin/ujian-online)](https://github.com/faizzainuddin/ujian-online/commits/main)

</div>

## Daftar Isi

- Gambaran Umum
- Fitur
- Prasyarat
- Instalasi Cepat
- Kredensial Default
- Rute Utama
- Tech Stack
- Struktur Direktori
- Troubleshooting
- Roadmap

## Gambaran Umum

TrustExam adalah aplikasi web Ujian Online untuk kebutuhan pengelolaan ujian di sekolah. Fokus pada kesederhanaan: login admin, dashboard ringan, dan pondasi tabel inti siap dikembangkan.

## Fitur

- 🔐 Autentikasi admin berbasis sesi (dashboard di `/admin`).
- 🧱 Middleware `admin.auth` untuk proteksi halaman dan tombol logout.
- 🔤 Captcha statis (demo) untuk form login.
- 🌱 Seeder admin default untuk akses awal.
- 🗄️ Migrasi tabel inti: admin, guru, siswa, ujian, soal, hasil, jawaban.

## Prasyarat

- PHP `^8.2` dan Composer 2
- MySQL/MariaDB (contoh `.env` gunakan port `3307` via XAMPP)
- Node.js 18+ dan npm (opsional untuk Vite/Tailwind)

## Instalasi Cepat

1. Clone & masuk folder

```bash
git clone https://github.com/faizzainuddin/ujian-online.git
cd ujian-online
```

2. Pasang dependency & siapkan env

```bash
composer install
cp .env.example .env   # Windows: copy .env.example .env
php artisan key:generate
```

3. Konfigurasi database di `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=ujian_sekolah
DB_USERNAME=root
DB_PASSWORD=
```

4. Migrasi + seeder

```bash
php artisan migrate --seed
```

5. (Opsional) Asset dev/build

```bash
npm install
npm run dev   # atau npm run build
```

6. Jalankan

```bash
php artisan serve
# buka http://127.0.0.1:8000
```

## Kredensial Default

- Username: `admin`
- Password: `password123`
- Captcha demo: `vm9fe` (hard-coded di halaman login)

> Disarankan segera ganti password dan gunakan captcha dinamis untuk produksi.

## Rute Utama

- `/` — Halaman login
- `/login` — Submit login (POST)
- `/logout` — Logout (POST)
- `/admin` — Dashboard admin (terproteksi)

## Tech Stack

- Backend: Laravel 12 (PHP ^8.2)
- Frontend tooling: Vite, Tailwind CSS 4 (opsional)
- DB/Queue: MySQL/MariaDB, Queue driver `database`

## Struktur Direktori

- `resources/views/auth/login.blade.php` — Halaman login
- `resources/views/admin/dashboard.blade.php` — Dashboard admin
- `app/Http/Controllers/AdminAuthController.php` — Controller auth admin
- `app/Http/Middleware/AdminAuthenticated.php` — Middleware sesi admin
- `app/Models/Admin.php` — Model admin
- `database/migrations/*create_*_table.php` — Migrasi tabel
- `database/seeders/AdminSeeder.php` — Seeder admin default

## Troubleshooting

- Tidak bisa konek DB: pastikan port MySQL di `.env` (default contoh `3307` untuk XAMPP) sesuai dengan layanan Anda.
- Error migrasi: cek izin user DB, atau jalankan `php artisan migrate:fresh --seed` untuk reset lokal.
- Aset tidak termuat: jalankan `npm run dev` (pengembangan) atau `npm run build` (produksi) lalu refresh cache browser.

## Roadmap

- [ ] Captcha dinamis (bukan hard-coded)
- [ ] Manajemen pengguna (guru/siswa) dari dashboard
- [ ] Modul bank soal & penilaian otomatis
- [ ] Rekap hasil ujian (export CSV/PDF)

# Ujian Sekolah / Ujian Online (Laravel)

Proyek Laravel untuk aplikasi ujian dengan autentikasi berbasis peran (Admin/Guru/Siswa) dan modul manajemen data. Dokumen ini menjelaskan cara setup, menjalankan secara lokal, serta catatan penting untuk deployment.

## Prasyarat
- PHP 8.2+ dan Composer
- Node.js 18+ dan npm
- MySQL/MariaDB (XAMPP/WAMP juga boleh)

## Setup Cepat (Lokal)
```bash
# 1) Masuk ke folder proyek
cd C:\xampp\htdocs\ujian-sekolah

# 2) Pasang dependensi PHP
composer install

# 3) Salin env dan generate APP_KEY
copy .env.example .env   # di PowerShell/CMD Windows
php artisan key:generate

# 4) Konfigurasi database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306 (atau port MySQL Anda)
# DB_DATABASE=ujian_online
# DB_USERNAME=root
# DB_PASSWORD=

# 5) Migrasi (opsional: seed data awal jika tersedia)
php artisan migrate --seed

# 6) Pasang dependensi frontend & build asset Vite
npm install
npm run build   # atau: npm run dev (mode pengembangan)

# 7) Jalankan server pengembangan Laravel
php artisan serve   # http://127.0.0.1:8000
```

## Kredensial Demo
- Lihat file seeder untuk kredensial default: `database/seeders/AdminSeeder.php` dan `database/seeders/DatabaseSeeder.php`.
- Jika tidak yakin, Anda dapat membuat akun manual via Tinker:
```php
php artisan tinker
>>> use Illuminate\Support\Facades\Hash; use App\Models\User;
>>> User::updateOrCreate(['username'=>'admin'], ['name'=>'Admin System','password'=>Hash::make('password'),'role'=>'ADMIN']);
```

## Perintah Berguna
- Bersihkan cache/config/view: `php artisan cache:clear && php artisan config:clear && php artisan view:clear`
- Tampilkan daftar route: `php artisan route:list`
- Ulang migrasi + seed: `php artisan migrate:fresh --seed`
- Menjalankan Vite dev server: `npm run dev`

## Struktur Direktori Singkat
- `app/` – kode aplikasi (Controllers, Models, Middleware)
- `resources/views/` – Blade templates (mis. login, dashboard admin)
- `public/` – aset publik dan build Vite (`public/build`)
- `database/migrations` – skema tabel; `database/seeders` – data contoh

## Tips Deployment (ringkas)
- Set `.env` produksi (APP_KEY, APP_ENV=production, APP_DEBUG=false)
- Jalankan: `php artisan migrate --force`
- Build asset: `npm ci && npm run build`
- Optimasi: `php artisan optimize`
- Pastikan `storage/` dan `bootstrap/cache/` writable

## Catatan
- Jangan commit file rahasia: `.env`, kredensial, dll. Gunakan `.env.example` sebagai template.
- Jika menggunakan sesi/cache berbasis database, jalankan migrasi tabel terkait (`session`, `cache`, dll) atau ubah driver ke `file` di `.env`.
