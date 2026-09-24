# ai-memory Guide — How to Read & Use

## Session Start

Before coding, check what's been done:

```bash
# Check status
php artisan ai-memory status

# See recent pages
php artisan ai-memory recent

# Explore project state
php artisan ai-memory explore
```

In agent context, read via MCP tools:
- `memory_query` — search for prior work, decisions, gotchas
- `memory_recent` — what changed lately
- `memory_briefing` — structured summary (counts, pending handoffs)
- `memory_read_page` — full page after finding via search

## When to Write Memory

Write durable pages **ONLY** when:
- User explicitly asks to remember something permanently
- Recording a project rule (e.g., "never use X", "always Y")
- Saving a decision (architecture, trade-off, rejected alternative)

**Do NOT** write memory for:
- Normal progress updates
- Session notes (already captured automatically by hooks)
- Temporary notes (use a local file or temporary note with TTL)

## Before Deployment (CI/CD)

1. Run tests locally if available: `npm run test`, `php artisan test`
2. Run lint: `npm run lint` (or linter config)
3. Check build: `npm run build`
4. Verify routes: `php artisan route:list --path=peminjaman`
5. Check PHP lint: `php -l app/Http/Controllers/PeminjamanController.php`
6. Test data endpoint: `curl http://perpus.test/peminjaman/riwayat/data?page=1`

## Commit & Deploy

### Step 1: Commit with [deploy] suffix

```bash
git commit -m "feat(peminjaman): riwayat peminjaman dengan filter rentang tanggal & infinite scroll

- Controller: riwayat() & riwayatData() pakai queryRiwayatFiltered()
  * tanpa parameter tanggal → tampilkan SEMUA peminjaman
  * tanggal_mulai + tanggal_selesai → filter rentang (dari/sampai)
  * hanya tanggal (legacy) → filter satu hari
  * parseDateInput() non-throwing, invalid input diabaikan

- Riwayat.vue baru (533 lines):
  * UI dua mode: checkbox 'Tampilkan semua data' / rentang Dari–Sampai
  * input type=date + tombol Terapkan + quick filter (Hari Ini/Minggu Ini/Bulan Ini)
  * infinite scroll loadMore() pakai axios ke /riwayat/data?page=N
  * defensive: same-origin normalize, Array guard, stop-loop on empty
  * subtitle filterLabel menampilkan rentang aktif

- Fix infinite scroll bug:
  * riwayat() override next_page_url → peminjaman.riwayat.data endpoint
  * sebelumnya pakai paginator default → /riwayat?page=2 (HTML) bukan JSON

- routes/web.php: tambah GET /peminjaman/riwayat/data → riwayatData
- CSS: .filter-card margin-bottom 16px jarak filter–tabel
- Build passes (npm run build), PHP lint clean

[deploy]"
```

### Step 2: Push to GitHub

```bash
git push origin main
```

### Step 3: Monitor CI/CD

1. Go to https://github.com/septiananugrahh/perpus/actions
2. Wait for "Laravel & Vue 3 CI/CD" to complete (green ✓)
3. Check "Deploy to Hostinger" step for any errors

### Step 4: Verify Production

1. Visit https://perpus.saicponorogo.com
2. Navigate to Peminjaman → Riwayat Peminjaman
3. Test: filter range (Hari Ini/Minggu Ini/Bulan Ini/custom), "Tampilkan semua data", scroll

## After Deployment

If production shows issues:

1. Check Laravel logs (Hostinger or via SSH)
2. Clear cache: `php artisan config:clear route:clear view:clear`
3. Re-run migrations if schema changed: `php artisan migrate`
4. If critical, rollback via git revert or force deploy old commit with [deploy] suffix

## Common Pitfalls

| Issue | Cause | Fix |
|-------|-------|-----|
| 404 on `/peminjaman/riwayat` | Vhost mismatch (`localhost` vs `perpus.test`) | Use correct domain; check nginx config |
| `resp.data.data is undefined` | `next_page_url` points to Inertia HTML instead of JSON data endpoint | Ensure controller `riwayat()` rebuilds `next_page_url` to `peminjaman.riwayat.data` |
| Date filter returns 0 rows | `whereDate()` on MySQL does NOT match `d-m-Y` string format | Use `STR_TO_DATE(tgl_pinjam, '%d-%m-%Y %H:%i')` or LIKE |
| PHP artisan fails (exit 53) | Wrong PHP binary | Use `D:\laragon\bin\php\php-8.5.1-Win32-vs17-x64\php.exe` |

## MCP Tools Reference

| Tool | Purpose |
|------|---------|
| `memory_query` | Search for prior work, decisions, gotchas |
| `memory_recent` | List most recently updated pages |
| `memory_status` | Check if ai-memory is healthy |
| `memory_briefing` | Structured snapshot (counts, windows, pending handoffs) |
| `memory_explore` | Open-ended prose digest |
| `memory_read_page` | Fetch full page body |
| `memory_write_page` | Save durable project knowledge |
| `memory_consolidate` | Compile session observations to wiki pages |
| `memory_lint` | Audit wiki for contradictions, stale guidance |

## AI Memory Scope

This project uses:
- **workspace**: `default`
- **project**: `perpus`

When querying AI Memory, pass `workspace` and `project` together:
```json
{
  "workspace": "default",
  "project": "perpus",
  "query": "deploy hostinger",
  "limit": 5
}
```

## Quick Command Reference

```bash
# Check ai-memory health
php artisan ai-memory status

# See what was recently updated
php artisan ai-memory recent

# Search for prior work
php artisan ai-memory query "deploy hostinger"

# Explore project state (prose digest)
php artisan ai-memory explore

# Lint wiki for contradictions
php artisan ai-memory lint

# Run forget sweep (delete stale pages)
php artisan ai-memory forget
```