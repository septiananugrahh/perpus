<!-- ai-memory:start -->
## Long-term memory (ai-memory)

This project uses [ai-memory](https://github.com/akitaonrails/ai-memory) for cross-session continuity.

- **workspace**: `default`
- **project**: `perpus`
- **session-aware**: No — must pass `workspace` + `project` on every project-scoped call.

### Deploy

Deploy to Hostinger via GitHub Actions CI/CD. Commit to `main` with `[deploy]` suffix in message:

```
git commit -m "feat(xxx): description [deploy]"
git push origin main
```

GitHub Actions `.github/workflows/laravel.yml` handles the rest automatically. No manual deploy needed.

### Rules
- PHP binary: `D:\laragon\bin\php\php-8.5.1-Win32-vs17-x64\php.exe` (NOT php-8.3.14)
- Invoke via: `cmd /c "D:\laragon\bin\php\php-8.5.1-Win32-vs17-x64\php.exe ..."`
- MySQL client: `C:/xampp8/mysql/bin/mysql.exe`
- DB: MySQL `perpus` — NOT the empty sqlite file
- Indonesian UI, retro design system, no Tailwind in new features
<!-- ai-memory:end -->
