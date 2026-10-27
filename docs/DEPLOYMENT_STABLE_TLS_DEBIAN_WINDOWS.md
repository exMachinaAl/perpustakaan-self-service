# Deployment Perpustakaan — HTTPS berbasis hostname (Windows + Debian 13)

## Mengapa sertifikat IP sering berubah?

Sertifikat yang dibuat untuk `192.168.x.x` hanya valid untuk IP tersebut. Pindah Wi-Fi mengubah IP dan sering menyebabkan nama di sertifikat tidak sesuai. Solusi adalah mengakses sistem memakai **nama host tetap** dan mengubah DNS apabila server berpindah IP. Sertifikat Caddy terikat ke hostname, bukan IP jaringan yang sedang dipakai.

## Mode A — Direkomendasikan untuk HP siswa: domain publik tepercaya

Gunakan domain sekolah, misalnya `perpus.sekolah.example` (ganti dengan domain nyata). Konfigurasikan DNS A/AAAA ke alamat publik saat ini, gunakan DDNS jika berubah, dan pastikan ACME dapat mencapai port 80/443 agar Caddy menerbitkan dan memperbarui sertifikat otomatis. Jangan membuka layanan sebelum framework dan keamanan aplikasi siap. Jika server hanya di LAN/CGNAT dan port tidak dapat diteruskan, pengujian memakai DNS-01 butuh plugin Caddy untuk provider DNS atau arsitektur tunnel/VPN; paket standar **tidak** otomatis menyediakan DNS-01.

Jika ingin aplikasi tetap privat tetapi sertifikat publik dipercaya oleh HP, gunakan domain yang Anda kendalikan + DNS-01 challenge + split-horizon DNS yang mengarah ke IP lokal; ini memerlukan plugin yang sesuai dan pengaturan DNS yang belum termasuk paket.

## Mode B — Pengujian LAN tanpa internet: `perpus.home.arpa`

Pasang DNS lokal di router/sekolah agar `perpus.home.arpa` mengarah ke IP server yang benar dan diperbarui ketika berubah (idealnya DHCP reservation untuk server tetap). Caddy `tls internal` membuat CA privat dengan sertifikat untuk nama stabil. **CA root harus dipercaya secara eksplisit di setiap komputer/HP kiosk**. Ini tidak cocok untuk siswa publik dengan perangkat tak terkelola; jangan minta semua siswa memasang CA privat sembarangan.

## Windows (PHP artisan serve sementara untuk lab)

Jalankan `tools/runtime/stable/BUAT_CADDY_DOMAIN.bat`; pilih hostname dan mode; jalankan `tools/runtime/START_LARAVEL.bat` (lab) dan `tools/runtime/stable/START_DOMAIN_HTTPS.bat`. Caddyfile dan kunci internal Caddy tetap di `%LOCALAPPDATA%`, di luar OneDrive proyek. Untuk jaringan LAN mode PUBLIC, port 80/443 mungkin terblokir oleh NAT; perhatikan firewall Windows. APP_URL dan KIOSK_PHONE_BASE_URL harus `https://hostname`, dan `SESSION_SECURE_COOKIE=true`. Hati-hati: login memerlukan cookie domain yang konsisten, akses jangan bercampur IP/HTTP.

## Debian 13 (PHP-FPM + Caddy sebagai service)

Pindahkan proyek Laravel yang sudah ada secara aman (source dan backup DB, bukan meng-clone proyek kosong) ke `/srv/perpustakaan-full-secure` atau lokasi lain, dengan `.env` tidak dibagikan. Jalankan `00_PRECHECK.sh`, `01_INSTALL_DEPENDENCIES.sh`, uji Composer requirements, pastikan DB, lalu `02_CONFIGURE_HOSTNAME.sh`. Script menghasilkan Caddyfile mengarah ke `public/` dan PHP-FPM socket. Setelah memeriksa domain/folder, jalankan `03_INSTALL_CADDY_SITE.sh <generated-file>` dan `04_HEALTHCHECK.sh`.

Gunakan user deployment non-root, bukan Composer root. Atur izin path: PHP-FPM (www-data) perlu membaca aplikasi dan menulis `storage/` dan `bootstrap/cache/`; akun `caddy` perlu akses baca `public/` dan traverse parent directories saja. Jangan `chmod -R 777`. Jangan pernah menaruh `.env`, DB dump, atau kunci TLS di `public/`. Untuk akses publik: pasang firewall, jangan buka port DB 3306, backup terenkripsi dan uji restore.

## Sertifikat di Debian

`tls internal` menyimpan kunci CA di penyimpanan privat Caddy (`/var/lib/caddy/.local/share/caddy/pki/authorities/local` untuk instalasi systemd umum). Root CA publik untuk klien dapat diekspor secara hati-hati, **jangan salin kunci private CA**. Mode domain publik membuat sertifikat ACME otomatis, tidak perlu mkcert. Path data bisa berbeda sesuai konfigurasi sistem.

## Validasi wajib

1. `getent hosts nama-domain` (Debian) atau `nslookup nama-domain` (Windows/HP jika tersedia).
2. Buka `https://nama-domain` di browser PC dan HP, pastikan tidak ada TLS warning dan URL sama dengan APP_URL.
3. Kamera/browser kiosk diberi izin, tidak mengembalikan redirect login karena cookie domain atau secure-cookie salah.
4. `/kiosk/setup` dapat diakses tanpa login admin, setelah aktivasi `/kiosk` terautentikasi sebagai perangkat.
5. QR siswa dan QR buku dideteksi, urutan scan dua arah bekerja, ulang token ditolak.
6. Tidak mengubah stok jika mode validasi; pilot nyata harus diawasi.
7. `php artisan migrate:status`, cek log Laravel (`storage/logs`), log Caddy `journalctl -u caddy`.
8. Coba putuskan jaringan, restart service dan hidupkan lagi; catat prosedur restore backup.

## Perhatian kompatibilitas

Proyek masih Laravel 10 menurut pemeriksaan sebelumnya, yang telah berakhir masa dukungan keamanannya. Debian 13 memakai PHP 8.4 pada instalasi umum; dependency lama dapat bermasalah pada PHP 8.4. Gunakan staging dan verifikasi Composer + tes regresi sebelum porting produksi. Toolkit ini **tidak** mengubah framework, DB, atau fitur peminjaman.
