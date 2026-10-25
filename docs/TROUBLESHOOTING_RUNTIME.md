# Troubleshooting Operasional

| Gejala | Pemeriksaan dan tindakan |
|---|---|
| `php not recognized` | Jalankan `SETUP.bat`; tetapkan `PERPUS_PHP_EXE` atau PATH. File `php.ini` yang aktif: `php --ini`. |
| `pdo_mysql / gd / fileinfo MISSING` | Aktifkan extension pada `php.ini` milik PHP CLI yang benar; ulang `CHECK_SYSTEM.bat`. Jangan pakai `--ignore-platform-reqs`. |
| `vendor/autoload.php` tidak ada | Jalankan `composer install` di root proyek sesudah backup, dengan PHP yang kompatibel. |
| MySQL gagal | Cek `DB_CONNECTION=mysql`, server DB aktif dan kredensial lokal; **jangan kirim .env**. |
| `migration pending` | Lakukan backup DB dan review migrasi sebelum `php artisan migrate`; toolkit tidak menjalankan otomatis. |
| 8000/8443 already in use | Baca `DEBUG.bat`; hentikan instance tepat dengan Ctrl+C. Jangan `taskkill /IM php.exe`. |
| `Caddyfile invalid` | Periksa IP, path TLS, dan certificate/key. `START_HTTPS.bat` otomatis menjalankan `caddy validate`. |
| Kamera gagal dari HP | HTTPS trusted origin wajib; tes CA SAN, izin kamera OS/browser, jaringan sekolah/firewall. |
| Kiosk redirect `/login` | Jangan buka `/admin/kiosk/hub` sebagai scanner. Gunakan `/kiosk/setup`, aktivasi dari admin `/admin/kiosk/devices`. |
| 419 | Periksa cookie secure, APP_URL, HTTPS Caddy, origin, CSRF, APP_KEY. |
| 426 | Caddy tidak mengirim HTTPS trusted forwarded headers atau Laravel belum menganggap proxy tepercaya. |
| 401 / 403 | Perangkat belum terdaftar atau dicabut, atau mengakses route admin. |
| 404 | Jalankan `CEK_KIOSK.bat` untuk memastikan route yang benar terdaftar. |
| Setelah restart Wi-Fi berbeda | Periksa IPv4 dan certificate SAN, terbitkan ulang sertifikat IP baru (tanpa mengganti root CA). |
| Startup dari OneDrive lambat | Biarkan source sesuai kebijakan Anda, namun pastikan binary, private key dan CA root berada di `%LOCALAPPDATA%` non-OneDrive. |

**Alert wajib**: setiap error harus jelas, tidak memaksa pemasangan, tidak memodifikasi source sembarangan, dan tidak menampilkan secret. Screenshot log/diagnostik hanya dibagikan setelah menyembunyikan alamat internal dan token jika perlu.
