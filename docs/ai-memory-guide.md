# ai-memory Guide — baca SEBELUM & SETELAH eksekusi + cara deploy

> Aturan pakai: **SEBELUM** menjalankan/menulis prompt, baca memori. **SETELAH** selesai eksekusi, tulis pembelajaran. Detail tiap langkah di bawah.

## 1. SEBELUM eksekusi (wajib dibaca dulu)

Sebelum mengerjakan prompt apa pun, cek memori proyek:

1. **`memory_query`** — cari kerjaan/putusan/gotcha relevan dengan prompt ini:
   ```json
   { "workspace": "default", "project": "perpus", "query": "<topik prompt>", "limit": 5 }
   ```
2. **`memory_recent`** — apa saja yang berubah belakangan (halaman terbaru).
3. **`memory_briefing`** — ringkasan terstruktur: jumlah sesi, aktivitas 7/30 hari, pending handoff.
4. **`memory_read_page`** — buka halaman penuh hasil pencarian yang relevan (bukan cuma snippet).
5. **`memory_handoff_list`** — ada tidak handoff OPEN dari sesi/agent lain yang belum diklaim.

Aturan: kalau halaman yang cocok ditemukan, BAca dulu sebelum menulis apa pun. Jangan terima snippet saja.

## 2. SETELAH eksekusi (tulis kembali)

Selesai mengerjakan prompt, lakukan:

1. **Evaluasi hasil** — apa yang berubah, apa yang diputuskan, apa yang gagal.
2. **`memory_write_page`** — tulis halaman durable HANYA kalau ada:
   - keputusan/arsitektur baru (dan alternatif yang ditolak),
   - aturan proyek ("jangan pakai X", "selalu Y"),
   - gotcha non-trivial (misal: `whereDate()` gagal di string `d-m-Y` → pakai `STR_TO_DATE`).
   ```json
   { "workspace": "default", "project": "perpus", "path": "<path>.md", "body": "<isi>" }
   ```
3. **Jangan tulis** untuk: progres rutin, catatan sesi (hook otomatis tangkap), catatan sementara. Itu mencemari memori.
4. Bila sesi panjang: **`memory_consolidate`** agar observasi jadi halaman wiki rapi.

Terapkan: prompt berikutnya dimulai dari langkah §1 lagi — siklus baca→eksekusi→tulis.

## 3. Cara Deploy (Hostinger via GitHub Actions)

> Deploy OTOMATIS, tanpa langkah manual. Pemicunya: **commit berisi literal `[deploy]`**.

### Langkah

1. **Commit dengan suffix `[deploy]`**:
   ```bash
   git add -A
   git commit -m "feat(namafitur): deskripsi

   - perubahan A
   - perubahan B

   [deploy]"
   ```
2. **Push**:
   ```bash
   git push origin main
   ```
3. **Monitor** GitHub Actions: https://github.com/septiananugrahh/perpus/actions
   - Trigger: `.github/workflows/laravel.yml`, step 9 cek `contains(head_commit.message, '[deploy]')`.
   - Tanpa `[deploy]` → run "skipped" (aman untuk dev, tidak deploy).
4. **Tunggu run selesai (green)** — composer, ziggy, key, npm build, rsync ke Hostinger, migrate, cache.
5. **Verifikasi produksi**: https://perpus.saicponorogo.com → menu Peminjaman → Riwayat Peminjaman
   - filter rentang (Hari Ini / Minggu Ini / Bulan Ini / custom),
   - "Tampilkan semua data",
   - infinite scroll (scroll bawah → halaman berikutnya termuat).

### Kalau butuh deploy ulang commit lama
Amend + force-push (tambah `[deploy]` ke pesan):
```bash
git commit --amend --no-edit --message "… [deploy]"
git push --force-with-lease origin main
```

## 4. Troubleshooting umum

| Masalah | Penyebab | Solusi |
|---------|----------|--------|
| 404 di `/peminjaman/riwayat` | vhost salah (`localhost` bukan `perpus.test`) | pakai `http://perpus.test` |
| `resp.data.data is undefined` | `next_page_url` nunjuk halaman Inertia HTML, bukan JSON | pastikan `riwayat()` timpa `next_page_url` ke `peminjaman.riwayat.data` |
| Filter tanggal hasil 0 baris | `whereDate()` di MySQL NULL untuk string `d-m-Y` | `STR_TO_DATE(tgl_pinjam, '%d-%m-%Y %H:%i')` / `DATE(STR_TO_DATE(...))` |
| `php artisan` gagal / exit 53 | binary PHP salah | `D:\laragon\bin\php\php-8.5.1-Win32-vs17-x64\php.exe` |

## 5. Referensi tool MCP (ai-memory)

| Tool | Guna |
|------|------|
| `memory_query` | telusuri kerjaan/putusan/gotcha lama |
| `memory_recent` | halaman terakhir diupdate |
| `memory_briefing` | ringkasan terstruktur proyek |
| `memory_explore` | digest prose bebas |
| `memory_read_page` | baca isi penuh satu halaman |
| `memory_write_page` | simpan pengetahuan durable |
| `memory_consolidate` | rangkum observasi sesi jadi wiki |
| `memory_lint` | audit kontradiksi / halaman basi |
| `memory_handoff_*` | kirim/terima handoff antar agent |

## 6. Scope proyek

Semua panggilan ai-memory untuk proyek ini WAJIB menyertakan:
```json
{ "workspace": "default", "project": "perpus" }
```
Jangan andalkan "last active project" server — bisa salah resolve (default/scratch).

## 7. Ringkas alur satu prompt

1. Baca: `memory_query` → `memory_recent` → `memory_read_page` (bila relevan).
2. Eksekusi prompt sesuai data.
3. Tulis: `memory_write_page` bila ada keputusan/aturan/gotcha baru.
4. Deploy (bila diminta): commit `[deploy]` → push → monitor Actions → verifikasi produksi.