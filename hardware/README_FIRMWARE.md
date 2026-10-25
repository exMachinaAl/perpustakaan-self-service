# PETUNJUK FIRMWARE ESP32 + RC522 — PATCH 002

## Persiapan hardware

ESP32 DevKit klasik: RC522 SDA/SS->GPIO5, SCK->GPIO18, MOSI->GPIO23, MISO->GPIO19, RST->GPIO22, 3.3V->3V3, GND->GND. IRQ dibiarkan kosong. **Jangan memberi RC522 daya 5V.** Gunakan kabel pendek dan ground yang sama.

## Arduino IDE

1. Install ESP32 board package (Espressif Systems); pilih **ESP32 Dev Module** untuk ESP32 DevKit klasik.
2. Library Manager -> install `MFRC522` (Miguel Balboa RFID), versi yang diuji secara statis pada proyek ini mengacu pada API seri 1.4.x, misalnya 1.4.12.
3. Pilih USB COM port, Upload Speed yang stabil (misalnya 115200). Jika tombol BOOT diperlukan ikuti prosedur board.
4. Buka `01_RC522_SELFTEST/01_RC522_SELFTEST.ino`, Upload. Buka Serial Monitor 115200; tap kartu 13.56 MHz. Pastikan VersionReg bukan `0x00`/`0xFF` dan UID terbaca.
5. Setelah tes dasar berhasil, buka `02_RC522_LARAVEL_SECURE/02_RC522_LARAVEL_SECURE.ino`.
6. Duplikasi `device_config.example.h` menjadi `device_config.h` **di folder 02_RC522_LARAVEL_SECURE**, isi Wi-Fi 2.4 GHz, `READER_ID`, `READER_SECRET`, origin server HTTPS, NTP, dan CA certificate PEM untuk server itu. **Jangan unggah `device_config.h` ke Git**.
7. Register perangkat pertama kali di root Laravel: `php artisan perpus:register-reader ESP32-01 "Meja Layanan 1"`. Simpan secret yang muncul sekali saja; bisa gunakan `php artisan perpus:reader-manage ESP32-01 rotate` jika pernah bocor.
8. Upload firmware online, Serial Monitor 115200. Heartbeat `HTTP 200` artinya Laravel menerima status reader. Tap kartu: `HTTP 202` artinya Laravel menerima scan (tidak otomatis meminjam). Buka `/admin/rfid/desk` dan `/admin/rfid` untuk enrol kartu.

## Sertifikat, host dan jam

- Server harus dapat diakses ESP32 dengan **HTTPS dan sertifikat CA yang benar**. `APP_URL` dan domain/proxy harus sesuai. Gunakan CA root/intermediate sesuai rantai yang dapat memverifikasi hostname.
- `localhost` atau `127.0.0.1` di ESP32 menunjuk ke ESP32, bukan komputer Laragon. Jangan pakai localhost di `API_ORIGIN`.
- Mode development tanpa HTTPS: lakukan hanya **self-test offline**. Untuk integrasi jaringan, sediakan reverse proxy TLS dengan hostname jaringan yang dikenali ESP32 (mis. domain internal dengan CA lokal terpasang di `ROOT_CA_PEM`). Dilarang mematikan verifikasi sertifikat atau mempublikasikan server PHP dev.
- ESP32 perlu NTP (waktu server/perangkat toleransi ±90 detik), atau NTP internal jika jaringan tidak memiliki akses internet.
- Waktu keliru → HTTP 401. Sertifikat salah/DNS gagal → status negatif. URL atau path salah → HTTP 404. Proxy HTTP upstream salah/TrustProxies → HTTP 403 di production.
- **UID kartu RC522 tidak anti-kloning.** Gunakan petugas untuk menyetujui transaksi, nonaktifkan kartu hilang, dan upgrade perangkat/kartu kriptografis untuk keamanan yang lebih tinggi.

## Tabel kode status

- `200` heartbeat OK
- `202` scan diterima (termasuk bila tertahan cooldown server)
- `401` kredensial/signature/NTP gagal
- `403` koneksi bukan HTTPS menurut aplikasi production
- `409` nonce digunakan ulang
- `422` isi heartbeat/UID invalid
- `429` rate limit
- `5xx` backend MySQL/Laravel bermasalah
- Negatif (`-1`, `-100`, dll): gangguan jaringan/TLS pada ESP32

## Privasi

Mode offline self-test mencetak UID untuk pengujian lokal. Firmware produksi tidak mencetak UID, secret atau identitas anggota. Jangan publikasikan serial log yang mengandung credential.
