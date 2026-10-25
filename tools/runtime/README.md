# Tools Runtime (permanen)

| File | Cara pakai |
|---|---|
| `SETUP.bat` | Cek kesiapan komponen, tampilkan urutan persiapan |
| `CHECK_SYSTEM.bat` | Periksa PHP/extensions/vendor/Caddy/route/migrasi tanpa mengubah data; report di `storage/logs/runtime-toolkit` |
| `BUAT_HTTPS_LAN.bat` | Buat CA/sertifikat/key dan Caddyfile lab di `%LOCALAPPDATA%`, tidak di proyek |
| `START.bat` | Buka Laravel localhost:8000 dan Caddy HTTPS di dua terminal |
| `START_LARAVEL.bat` | Hanya jalankan Laravel untuk tes di PC server |
| `START_HTTPS.bat` | Hanya jalankan Caddy HTTPS, perlu backend localhost:8000 |
| `CEK_KIOSK.bat` | Periksa route kiosk dan tampilkan URL/prosedur aktivasi |
| `DEBUG.bat` | Tampilkan info PHP/port tanpa membocorkan konfigurasi sensitif |
| `STOP_INFO.bat` | Petunjuk shutdown tanpa mematikan proses milik proyek lain |
| `_common.bat` | File internal untuk deteksi executable (jangan dijalankan langsung) |

Bisa override tool: `PERPUS_PHP_EXE`, `PERPUS_CADDY_EXE`, `PERPUS_MKCERT_EXE` di terminal pemanggil. Tidak perlu mengubah `.bat` untuk pindah lokasi tool.

**Peringatan:** `artisan serve` + Caddy pada port 8443 ditujukan untuk pengujian LAN. Untuk layanan produksi sekolah, gunakan PHP-FPM/FastCGI atau web server produksi dengan supervisi layanan, sertifikat publik, dan HTTPS terkelola.
