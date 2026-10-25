# Panduan Operasional — Laravel + Caddy + QR Kiosk

## A. Status dan prasyarat

- Target tunggal: `perpustakaan-full-secure`, **bukan** folder patch.
- PHP CLI 8.2+ dengan ekstensi aplikasi `pdo_mysql`, `openssl`, `mbstring`, `fileinfo`, `gd` (cek `composer check-platform-reqs` bila Composer tersedia).
- Dependensi `vendor` via `composer install` setelah backup; MySQL/MariaDB aktif; `.env` ada dan benar.
- `APP_KEY` **jangan diganti** pada proyek existing. Jangan pernah jalankan `migrate:fresh`.
- Caddy dan mkcert (Windows binaries resmi) di folder **di luar OneDrive**: `%LOCALAPPDATA%\Perpustakaan_TLS_LAB\bin`.
- Laragon tidak diwajibkan; PHP CLI independen diperbolehkan.

## B. Sekali ketika baru setup

1. Jalankan `tools/runtime/SETUP.bat` untuk membaca alert.
2. Jika PHP tidak ditemukan, set `PERPUS_PHP_EXE` pada terminal, misalnya `set "PERPUS_PHP_EXE=C:\Program Files\php-8.2.35-nts-Win32-vs16-x64\php.exe"`, atau perbaiki PATH.
3. Jika `vendor` hilang, jalankan `composer install` dari root proyek setelah menyiapkan Composer. **Installer PATCH 008 tidak menjalankan Composer maupun migrasi.**
4. Jalankan `tools/runtime/CHECK_SYSTEM.bat`, baca `storage/logs/runtime-toolkit/CHECK_SYSTEM_TERAKHIR.txt`.
5. Download `caddy.exe` dari https://caddyserver.com/download dan `mkcert.exe` dari https://github.com/FiloSottile/mkcert/releases (periksa penerbit/checksum), letakkan keduanya di `%LOCALAPPDATA%\Perpustakaan_TLS_LAB\bin`. Jangan menaruh executables yang diunduh di root Laravel.
6. Cari IPv4 server dengan `ipconfig`. Jalankan `tools/runtime/BUAT_HTTPS_LAN.bat`; masukkan IPv4 **server**, bukan HP. `mkcert -install` memasang CA untuk Windows user tersebut.
7. Buka `.env` secara lokal dan isi `APP_URL=https://IP-SERVER:8443`, `KIOSK_PHONE_BASE_URL=https://IP-SERVER:8443`, `SESSION_SECURE_COOKIE=true`, `APP_DEBUG=false` untuk pengujian lintas perangkat. Jika setting reverse-proxy Laravel belum benar, tangani mengikuti docs PATCH 006/007; jangan mengaktifkan `TrustProxies` wildcard sembarangan.
8. Dari root proyek jalankan `php artisan config:clear` setelah `.env` diedit. **Tidak melakukan cache config otomatis.**
9. Buka firewall TCP 8443 hanya untuk jaringan sekolah/LAN tepercaya sesuai kebijakan IT. Pastikan komputer server dan kiosk di jaringan yang bisa saling terhubung; beberapa Wi-Fi sekolah memakai client isolation.
10. Jalankan `tools/runtime/START.bat` dan baca status dari kedua CMD.

## C. Rutin harian

- Start: `START.bat`. Dua CMD akan terbuka. Jangan jalankan ulang selama server masih hidup di port sama.
- Admin: `https://IP-SERVER:8443/admin/kiosk/devices` (wajib akun admin).
- Kiosk baru: `https://IP-SERVER:8443/kiosk/setup` (kode aktivasi sekali pakai dari admin).
- Kiosk terdaftar: `https://IP-SERVER:8443/kiosk` (tanpa login admin).
- Siswa: login pribadi lalu buka `/user/kiosk/pass` untuk QR anggota.
- Stop: Ctrl+C pada **masing-masing jendela** (bukan kill-all PHP/Caddy).

## D. Jika browser/perangkat pindah jaringan

- IP server berubah: sertifikat mkcert **terikat SAN IP**; jalankan BUAT_HTTPS_LAN dengan IP baru. Jika `Caddyfile` lama ada, skrip **tidak menimpanya**; edit atau pindahkan file lama setelah backup manual lokal. Jangan menghapus root CA key demi membuat certificate baru.
- Kiosk yang telah diaktivasi menggunakan cookie HTTPS sesuai origin: jika origin IP/port berubah atau cookie dibersihkan, kemungkinan harus aktivasi ulang.
- Jika menggunakan HP: CA pengujian tidak otomatis dipercaya. Uji pada perangkat lab tersendiri; untuk sekolah produksi gunakan nama domain dan CA publik.

## E. Batas dan keamanan

`php artisan serve` adalah development server; **bukan** konfigurasi hosting publik. Tool ini tidak menjalankan migration, seeder, membuat pinjaman, atau menonaktifkan verifikasi sertifikat. Semua private key/CA berada di luar proyek utama dan OneDrive.
