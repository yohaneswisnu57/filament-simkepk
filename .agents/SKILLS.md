# Handoff: Migrasi Project ke WSL2 + Docker Native

Dokumen ini untuk melanjutkan pekerjaan migrasi project Laravel (Sail) dari
`C:\Users\wisnu\Documents\htdocs\project-simkepk` (Windows filesystem) ke
`/home/wisnu/project-simkepk` (WSL2 Ubuntu native filesystem), karena bind-mount
Docker dari path Windows sangat lambat (scan vendor/ butuh ~8.5 detik vs 0ms di WSL native).

## Kondisi environment saat ini

- **WSL2 Ubuntu** sudah terinstall, user Linux: `wisnu`, home: `/home/wisnu`.
- **Docker Engine native** (bukan Docker Desktop) sudah terinstall DI DALAM Ubuntu via apt
  (`docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin`),
  service aktif via systemd (`systemctl status docker`). User `wisnu` sudah masuk grup `docker`.
- **Docker Desktop** (Windows) juga terinstall terpisah (v4.82.0, reinstalled dari awal) —
  dipakai untuk project lain di Windows (ada container `ukwms_postgres`, `ukwms_mysql`).
- Project baru sudah di-clone bersih dari GitHub (`https://github.com/yohaneswisnu57/filament-simkepk.git`)
  ke `/home/wisnu/project-simkepk`, branch `main`.
- `.env` sudah disalin ke lokasi baru, plus ditambahkan `WWWUSER=1000` dan `WWWGROUP=1000`.
- `composer install` sudah dijalankan (vendor/ ada), `npm install` + `npm run build` sudah jalan
  (public/build/ ada).
- `docker compose build` (image `sail-8.4/app`) sudah selesai.
- Migrasi database (`php artisan migrate --force`) sudah sukses di container mysql BARU
  (volume `project-simkepk_sail-mysql` fresh, **data lama TIDAK ikut pindah** — kalau butuh
  data lama, harus dump manual dari volume Docker Desktop yang lama sebelum dihapus).
- Sempat berhasil `curl http://localhost` → HTTP 200.

## Masalah terakhir yang BELUM selesai

Docker Desktop (Windows) dan Docker Engine native (Ubuntu) **bentrok** ketika
Docker Desktop auto-start dan WSL Integration untuk distro **Ubuntu** masih aktif —
begitu Docker Desktop nyala, container project ini (`project-simkepk-laravel.test-1`,
`project-simkepk-mysql-1`) langsung `Exited (255)`.

### Langkah yang harus diselesaikan (manual di GUI Windows):

1. Buka **Docker Desktop** → **Settings** → **Resources** → **WSL Integration**.
2. **Matikan** toggle untuk distro **Ubuntu** saja (biarkan integrasi default/lain tetap nyala
   kalau dipakai project Windows lain).
3. **Apply & Restart**.
4. Setelah itu, dari Ubuntu jalankan ulang:
   ```bash
   cd ~/project-simkepk
   docker compose up -d
   # tunggu mysql healthy:
   docker inspect --format "{{.State.Health.Status}}" project-simkepk-mysql-1
   curl -s -o /dev/null -w "%{http_code}\n" http://localhost   # harus 200
   ```

Kalau masih bentrok setelah toggle dimatikan, kemungkinan perlu juga:
- Set Docker Desktop supaya **tidak auto-start saat Windows boot** (Settings → General →
  uncheck "Start Docker Desktop when you sign in").
- Atau matikan Docker Desktop sepenuhnya kalau project ini yang sedang dikerjakan (native
  Docker Engine di Ubuntu jalan sendiri tanpa perlu Docker Desktop terbuka).

## Cara kerja sehari-hari setelah ini beres

Semua command project HARUS dijalankan dari dalam WSL Ubuntu, BUKAN dari
`C:\Users\wisnu\Documents\htdocs\project-simkepk` (folder lama, sudah tidak dipakai
untuk running, boleh dihapus kalau sudah yakin).

```bash
# masuk ke WSL Ubuntu
wsl -d Ubuntu

# lanjut ke project
cd ~/project-simkepk

# jalankan containers
docker compose up -d

# jalankan artisan/composer/npm via exec
docker compose exec laravel.test php artisan migrate
docker compose exec laravel.test composer install
docker compose exec laravel.test npm run build

# lihat log
docker compose logs -f laravel.test

# stop
docker compose down
```

Akses aplikasi tetap di `http://localhost` dari browser Windows (port sudah di-forward
otomatis oleh WSL2).

## Hal lain yang perlu diketahui

- File `.env` project baru **berbeda file** dari `.env` di folder Windows lama — kalau
  ubah kredensial, edit yang di `/home/wisnu/project-simkepk/.env` (via `wsl` atau
  `\\wsl$\Ubuntu\home\wisnu\project-simkepk\.env` dari Windows Explorer/VS Code).
- Backup dump database project LAIN (`ukwms_postgres`, `ukwms_mysql`) yang sempat
  dilakukan sebelum uninstall Docker Desktop lama ada di:
  `C:\Users\wisnu\Documents\docker_backup_20260720\ukwms_postgres.sql` dan
  `ukwms_mysql.sql`. Container itu sudah otomatis kebuat ulang oleh Docker Desktop
  baru dan datanya utuh (tidak perlu restore), tapi dump ini tetap ada sebagai
  jaga-jaga.
- Diagnosis awal soal performa (sebelum migrasi WSL) yang masih relevan:
  `.env` project ini pakai `CACHE_STORE=database`, `SESSION_DRIVER=database`,
  `QUEUE_CONNECTION=database` — ini bikin setiap request nambah round-trip ke
  MySQL. Pertimbangkan ganti ke `file` atau `redis` untuk local dev kalau masih
  terasa lambat setelah migrasi WSL selesai.
