# SiPerjadin — SPT & SPPD

Aplikasi pendataan Surat Perintah Tugas (SPT) dan Surat Perintah Perjalanan Dinas (SPPD)
perjalanan dinas Dalam Daerah / Luar Daerah, beserta cetak PDF SPT/SPPD.

## Tech stack

- PHP ^8.3 — Laravel ^13.8 (artisan: `Laravel Framework 13.32.0`)
- Filament 5.7.6 (panel admin `/admin`)
- MySQL untuk development, SQLite didukung via template `.env`
- PDF: `barryvdh/laravel-dompdf` ^3.1
- Excel: `maatwebsite/excel` 4.0
- Frontend: Vite 8 + Tailwind CSS 4 (`npm run dev` / `npm run build`)
- Test: PHPUnit 12.5 (`php artisan test`)

## Requirement

- PHP 8.3+ dengan ekstensi standar Laravel
- Composer
- MySQL (untuk database development `spt_sppd` dan database test `spt_sppd_test`)
- Node.js + npm (hanya untuk build aset Vite)

## Install

```sh
git clone <url-repo> spt-sppd
cd spt-sppd
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
```

## Konfigurasi `.env`

Salin dari `.env.example`, lalu sesuaikan minimal:

```env
APP_URL=http://localhost
APP_LOCALE=id
```

### Database (development)

Template default memakai SQLite. Untuk MySQL (sesuai database
development `spt_sppd`):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spt_sppd
DB_USERNAME=root
DB_PASSWORD=
```

## Migration & seeder

```sh
php artisan migrate
php artisan db:seed
```

Seeder membuat akun admin development, data pegawai, kecamatan/desa,
kota tujuan, dan penandatangan. Kredensial seeder dikendalikan lewat
environment variable (lihat `.env.example`: `SEED_ADMIN_*`, `SEED_USER_*`).
Tanpa variable tersebut seeder memakai fallback lokal khusus development —
jangan memakai kredensial tersebut di luar lokal, dan jangan menuliskan
password ke dokumentasi/chat/log.

## Menjalankan aplikasi

```sh
php artisan serve
npm run dev   # terminal terpisah, untuk aset Vite saat development
```

Atau sekaligus (server + queue + log + vite):

```sh
composer dev
```

Login via `/login`. Panel admin Filament di `/admin` (khusus role admin).

## Menjalankan test

> Wajib memakai database test `spt_sppd_test`.
> Jangan pernah menjalankan test/migrate/seed terhadap database
> development `spt_sppd`.

`phpunit.xml` memaksa SQLite in-memory, tetapi sebagian migration memakai
sintaks khusus MySQL sehingga suite SQLite gagal. Jalankan test dengan
MySQL `spt_sppd_test` (PowerShell):

```powershell
$env:DB_CONNECTION='mysql'; $env:DB_DATABASE='spt_sppd_test'; $env:DB_URL=''; php artisan test
```

Atau via script composer (setelah env di atas diekspor):

```sh
composer test
```

## Struktur singkat

- `app/Http/Controllers` — `FormController` (CRUD via `/form`),
  `PerjalananDinasController` (listing `/dalam-daerah`, `/luar-daerah`),
  `BerandaController`, `SptPdfController`, `SppdPdfController`
- `app/Services` — `SppdSyncService` (sinkronisasi SPPD),
  `PenandatanganService`, `ScheduleOverlapService`
- `app/Enums/UserRole.php` — role `admin`/`user` (kolom DB tetap string)
- `routes/web.php` — route web (tanpa closure agar bisa `route:cache`)
- `resources/views` — Blade web + template PDF (`pdf/`)
- `database/migrations` — skema; `database/seeders` — data awal
- `tests/Feature`, `tests/Unit` — test suite
