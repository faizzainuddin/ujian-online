# Konvensi Commit (Conventional Commits)

Format umum:

```
<type>(<scope>): <subject>

<body>

<footer>
```

Type yang diizinkan:

- feat: fitur baru
- fix: perbaikan bug
- docs: dokumentasi
- refactor: perubahan kode tanpa fitur/bug
- perf: peningkatan performa
- style: formatting/white-space (tanpa perubahan logika)
- test: menambah/memperbaiki test
- build: perubahan build tool/deps
- ci: perubahan CI
- chore: tugas rutin (config, housekeeping)
- revert: revert commit sebelumnya

Contoh:

- `feat(auth): tambah captcha dinamis di form login`
- `fix(admin): perbaiki validasi username saat login`
- `docs: update README dengan langkah instalasi`
- `ci: jalankan pint --test di workflow`

Catatan:

- Gunakan bahasa yang ringkas, deskriptif.
- `subject` huruf kecil, tanpa titik di akhir.
- Gunakan `body` untuk konteks tambahan atau breaking changes.

