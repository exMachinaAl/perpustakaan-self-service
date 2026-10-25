# Operasional Perpustakaan Full Secure — START HERE

**Paket permanen:** PATCH 008 *Persistent Runtime Toolkit*, ditempatkan di `tools/runtime/`.

Jika folder PATCH lama dihapus, **skrip ini tetap ada** di proyek utama. Upgrade ini tidak mengubah transaksi, route, firmware, ataupun database.

## Cara harian

1. Jalankan `tools/runtime/CHECK_SYSTEM.bat` jika ada error atau setelah pindah komputer.
2. Jalankan `tools/runtime/START.bat` untuk membuka Laravel localhost:8000 dan Caddy HTTPS:8443 di dua CMD.
3. Buka `https://IP-SERVER:8443/admin/kiosk/devices` (admin) atau `/kiosk` (perangkat sudah diaktivasi).
4. Hentikan keduanya dengan **Ctrl+C pada masing-masing CMD**, jangan `taskkill /IM` yang bisa mematikan aplikasi lain.

## Instalasi / pemindahan komputer

1. Baca [petunjuk operasional](docs/OPERASIONAL_RUNTIME.md).
2. `tools/runtime/SETUP.bat` — inventaris kebutuhan; tidak menjalankan migrasi.
3. Pasang `mkcert.exe` dan `caddy.exe` dari situs resmi di `%LOCALAPPDATA%\Perpustakaan_TLS_LAB\bin` (di luar OneDrive).
4. `tools/runtime/BUAT_HTTPS_LAN.bat` — masukkan IPv4 Wi-Fi/Ethernet server; membuat CA lokal, sertifikat, private key, dan Caddyfile di **`%LOCALAPPDATA%\Perpustakaan_TLS_LAB`**.
5. Sesuaikan `APP_URL`, `KIOSK_PHONE_BASE_URL`, `SESSION_SECURE_COOKIE` pada `.env` **secara manual**, lalu jalankan `php artisan config:clear`.
6. `tools/runtime/START.bat`; uji kamera/QR pada jaringan tepercaya.

**Dokumentasi lain:** [TLS](docs/HTTPS_TLS_LAN.md), [Uji kiosk](docs/PENGUJIAN_QR_KIOSK.md), [Troubleshooting](docs/TROUBLESHOOTING_RUNTIME.md), dan [README tools](tools/runtime/README.md).

### Privasi / keamanan

- Jangan taruh private key, CA root key, `.env`, atau credential ESP32 dalam proyek/patch/OneDrive.
- `rootCA.pem` **bukan** `rootCA-key.pem`; yang kedua adalah rahasia dan jangan pernah dibagikan.
- HTTPS dari mkcert **hanya lab**, tidak otomatis dipercaya HP siswa. Gunakan domain sekolah dengan sertifikat publik tepercaya untuk produksi.
- Aplikasi masih berbasis Laravel 10; selesaikan upgrade ke rilis yang didukung dan audit keamanan sebelum membuka layanan publik.
- Peminjaman self-service tanpa petugas **belum otomatis diaktifkan** oleh toolkit ini.
