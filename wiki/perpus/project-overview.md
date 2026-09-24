# Perpus — Library Management System

**Project:** Laravel + Inertia + Vue3 library system
**Repository:** https://github.com/septiananugrahh/perpus.git
**Production URL:** https://perpus.saicponorogo.com
**Local:** D:/laragon/www/perpus

## Stack
- Laravel 11.x (PHP 8.4+)
- Vue 3 + Inertia.js
- MySQL (`perpus` database)
- Vite build
- Retro CSS design system (no Tailwind)

## PHP Binary
Path: `D:\laragon\bin\php\php-8.5.1-Win32-vs17-x64\php.exe`
- Invoke: `cmd /c "D:\laragon\bin\php\php-8.5.1-Win32-vs17-x64\php.exe artisan ..."`
- Do NOT use php-8.3.14 (fails composer platform check)

## MySQL Client
Path: `C:/xampp8/mysql/bin/mysql.exe`

## Data Model
- `PeminjamanBuku`: `tgl_pinjam`, `tgl_kembali` stored as STRINGS `d-m-Y H:i` or `j-n-Y G:i` (legacy unpadded)
- Model `PeminjamanBuku::parseTanggal()` handles both formats
- `IndukBuku`: book catalog, `judul_buku`, `id_buku`
- FK: `PeminjamanBuku.id_barang` → `IndukBuku.id_buku`

## Key Routes
- `GET /peminjaman` — active loans (Index.vue)
- `GET /peminjaman/riwayat` — loan history with date-range filter (Riwayat.vue)
- `GET /peminjaman/riwayat/data` — infinite-scroll JSON endpoint

## UI Pattern
- `AuthenticatedLayout` wrapper
- `RetroCard` for containers
- `RetroButton` variants: `primary`, `secondary`, `color`
- `.status-badge`, `.retro-table` for status display
- Indonesian labels