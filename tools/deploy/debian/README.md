# Debian 13 — Deployment toolkit permanen

Ini alat deployment untuk proyek **yang sudah ada**, bukan source Laravel baru. Jalankan di direktori proyek `perpustakaan-full-secure` yang sudah dipindahkan dengan aman ke Debian.

1. `bash tools/deploy/debian/00_PRECHECK.sh` — preflight read-only.
2. `bash tools/deploy/debian/01_INSTALL_DEPENDENCIES.sh` — memasang paket OS setelah konfirmasi `INSTALL` (memerlukan sudo). **Tidak memasang database server**; boleh gunakan MariaDB/MySQL yang sudah ada. Jika database harus lokal, instal/konfigurasi `mariadb-server` tersendiri.
3. Pastikan PHP extensions, `.env`, vendor dan database siap. Jalankan `composer install --no-dev --optimize-autoloader` dan `php artisan migrate:status`, jangan `migrate:fresh`.
4. `bash tools/deploy/debian/02_CONFIGURE_HOSTNAME.sh` — membuat Caddyfile yang memakai domain tetap dan PHP-FPM.
5. Pastikan file permission untuk php-fpm dan Caddy (lihat dokumen), kemudian `bash tools/deploy/debian/03_INSTALL_CADDY_SITE.sh <file hasil langkah 4>`.
6. `bash tools/deploy/debian/04_HEALTHCHECK.sh`.

**Tidak** menggunakan `php artisan serve` untuk Debian produksi. Sertifikat dikelola Caddy secara otomatis, kunci CA disimpan dalam penyimpanan privat Caddy, bukan source proyek. Mode LAN `tls internal` perlu root CA dipercaya oleh perangkat klien dan DNS LAN. Mode publik memerlukan DNS publik, port 80/443, atau konfigurasi DNS-01 terpisah.

Perhatian: Laravel 10 lama mungkin tidak kompatibel dengan seluruh dependency PHP 8.4 dari Debian 13. Periksa `composer check-platform-reqs`, testing staging dan upgrade Laravel yang didukung **sebelum membuka ke internet**.
