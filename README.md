# PERPUSTAKAAN RFID + QR — FULL SECURITY BASELINE (20-10-2026)

**Kode sesi:** `GPT-0E7760-20261016-736F6E6428`
**Mode rilis:** SOURCE LENGKAP Laravel 10.50.2 + Blade, MySQL-only, hardening login/role/transaksi/QR, API RFID identifikasi UID.
**Sumber:** `perpustakaan(1).zip` (SHA256: `42b8263f1a48c591ded4758c1b7c775506dd78460548b3d3d916cf2baa303d9c`).

> BACA SEBELUM DIJALANKAN: Source ini lolos *lint* PHP dan audit statis, **BELUM TERUJI secara runtime di server MySQL, Laravel vendor, ESP32, dan reader fisik**. JANGAN langsung mengganti server produksi sebelum uji staging. Laravel 10 sudah melewati masa dukungan keamanan per 2026; migrasi ke Laravel supported major menjadi prioritas sebelum deployment publik.

## Ringkasan perubahan

1. Seluruh log `activity_logs`, `analytics_logs`, `search_history`, `notifications` kini model MySQL. Dependensi `mongodb/laravel-mongodb` dan `mongodb/mongodb` dihapus dari `composer.json` serta `composer.lock` (hash relevan Composer diperbarui). Tersedia `php artisan perpus:import-mongo-logs` untuk JSONL dump lama, opsional.
2. Grup `/admin` wajib auth+akun aktif+role admin; grup `/user` wajib auth+akun aktif+role user. Login dan formulir reset diberi rate limit.
3. Reset password Laravel Password Broker melalui **tautan email token valid**, tidak lagi `/reset-password/{email}`. Email disamarkan dalam respons. `remember_token` ditambahkan dengan migrasi forward-only.
4. Seeder admin **tidak** membuat password default. Provisioning hanya dari `BOOTSTRAP_ADMIN_EMAIL` dan `BOOTSTRAP_ADMIN_PASSWORD` (min. 12) saat dijalankan sekali.
5. `LoanWorkflow` melakukan perubahan stok dan status dalam transaksi dengan `lockForUpdate`; perubahan status dibatasi: `menunggu -> dipinjam/ditolak/dibatalkan -> [dipinjam -> dikembalikan]`. Pembayaran denda dengan lock, penolakan action berfungsi, hapus riwayat aktif/selesai diblokir.
6. QR berformat `LIBQR1:<token hex acak>` dengan token hanya disimpan sebagai hash, masa berlaku **3 menit**, **sekali pakai**, validasi `POST` admin + CSRF + rate-limit. Scanner tidak membuka URL sembarang dari QR. Scan hanya menampilkan detail, **tidak otomatis mengubah stok**.
7. Admin dapat menautkan 1 kartu RFID UID-hex aktif ke anggota dan mencabutnya melalui `/admin/rfid`. UID disimpan sebagai HMAC hash dan empat karakter terakhir untuk dukungan. **UID bisa dipalsukan**; ini cuma identifikasi saat petugas memeriksa kartu, **bukan autentikasi kriptografis**.
8. API ESP32 `POST /api/v1/reader/scan` dilindungi HMAC signature per device, nonce unik anti-replay, toleransi clock 90 detik, batas request, catatan scan. Secret disimpan terenkripsi dengan APP_KEY. Tidak ada firmware khusus karena model reader belum diberitahukan.
9. Rute laporan Excel didaftarkan, sumber anggota dipindahkan dari model `Anggota` yang tidak memiliki tabel menjadi `User`, casing `ProdiController.php` diperbaiki. Validasi stok nonnegatif dan buku bersejarah pinjaman tidak boleh dihapus.
10. File `.env`, `vendor`, `node_modules` dan rahasia server tidak dikemas. Sampel upload sumber tersimpan di `storage/app/public` dan diakses dengan symlink `php artisan storage:link`.

## Instalasi Windows / Laragon (STAGING DULU)

1. **Backup** direktori Laravel lama, file `.env` (jangan disebarkan), folder upload, dump MySQL, serta MongoDB lama bila pernah dipakai. Stop aplikasi lama.
2. Ekstrak ZIP ini sebagai folder **baru** sejajar dengan folder proyek lama, JANGAN langsung overlay proyek produksi.
3. Salin file `.env` dari aplikasi lama ke folder full baru, periksa `DB_CONNECTION=mysql`, host, username, password, `APP_URL`, `APP_DEBUG=false`. **Jangan ganti `APP_KEY` jika menggunakan data terenkripsi**. Bila instalasi baru tanpa `.env`, salin `.env.example` lalu jalankan `php artisan key:generate` setelah Composer tersedia.
4. Periksa ekstensi PHP (`php -m` dan `php --ini`): `pdo_mysql`, `mbstring`, `openssl`, `gd`, `fileinfo`, `zip`, `intl` bila diminta dependensi. Ekstensi mongodb tidak lagi diperlukan untuk proyek ini.
5. Instal dependensi dari root: `composer install`. Lockfile disediakan dengan dua paket MongoDB dihapus; bila Composer mendeteksi konflik di lingkungan Anda, simpan output error dan perbaiki dependency **di staging**, bukan memakai `--ignore-platform-reqs`.
6. Jalankan `php artisan optimize:clear` → `php artisan migrate:status` → `php artisan migrate`. **JANGAN** `migrate:fresh`, karena menghapus data. Migrasi hanya menambah tabel/kolom, tidak memigrasi data log lama otomatis.
7. Jalankan `php artisan storage:link`. Semua file media dari ZIP lama yang diperoleh sudah dimasukkan ke `storage/app/public`. Jika ada upload di server lama yang tidak tercakup ZIP, copy manual secara aman ke folder ini.
8. Sebelum mencoba reset password dari UI, set SMTP sekolah di `.env`, misalnya `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`. `MAIL_MAILER=log` hanya untuk development (tautan reset masuk ke log, bukan ke email pengguna).
9. Buat admin awal HANYA jika **belum ada**: set sementara `BOOTSTRAP_ADMIN_EMAIL` + `BOOTSTRAP_ADMIN_PASSWORD` (min 12 karakter acak) di `.env`; jalankan `php artisan db:seed --class=UserSeeder`; lalu **hapus** kedua nilai tersebut dari `.env`. Jika akun admin sudah ada, tidak perlu seed. Password default dari ZIP asli tidak dibuat lagi, tetapi **password lama yang sudah ada di database tidak otomatis dirotasi**: ganti manual.
10. Jalankan `php artisan route:list`, `php artisan serve` untuk pengujian lokal; periksa login, akun diblokir, transaksi, QR, laporan, RFID. Konfigurasi production: HTTPS, `APP_DEBUG=false`, `APP_ENV=production`, `SESSION_SECURE_COOKIE=true`, SMTP, backup database, scheduler `php artisan schedule:run` terjadwal, web root harus menunjuk `/public` (bukan root Laravel), permissions storage yang sesuai.

## Backend ESP32 / RFID

**Daftarkan reader di server Laravel:**

```bash
php artisan perpus:register-reader ESP32-MEJA-01 "Reader Meja Sirkulasi"
```

Command menampilkan `READER_ID` dan `READER_SECRET` **sekali** di terminal. Simpan di provisioning perangkat secara aman, jangan ke source/Git. Jika bocor, cabut/rotasi kredensial dan daftarkan reader baru.

**HTTP:** `POST /api/v1/reader/scan` dengan body JSON persis contoh berikut:

```json
{"uid":"04AABBCC"}
```

Headers:

```
X-Reader-ID: ESP32-MEJA-01
X-Reader-Time: <UNIX_SECONDS>
X-Reader-Nonce: <RANDOM_16_TO_32_BYTES_HEX>
X-Reader-Signature: <HMAC_SHA256_HEX>
Content-Type: application/json
```

Canonical string tepat, memakai karakter newline `\n` (tanpa newline penutup):

```
POST
/api/v1/reader/scan
<UNIX_SECONDS>
<RANDOM_NONCE_HEX>
<SHA256_HEX_OF_RAW_HTTP_REQUEST_BODY>
```

Signature = `hex(HMAC-SHA256(READER_SECRET, canonical_string))`. Body yang dikirim harus sama persis dengan body yang di-hash. Wajib HTTPS dan jam ESP32 tersinkronisasi NTP. Nonce tidak boleh diulang oleh reader yang sama. Respon aman: `identified` boolean, `user_id`, `display_name`, dan `action: verify_at_desk`. API **tidak** mengubah peminjaman/denda/stok.

**Catatan penting RFID:** pada mode UID biasa (RC522/PN532 dasar), clone UID bisa lolos lookup walau transport API aman. Untuk kartu anti-kloning dibutuhkan **autentikasi DESFire/secure-element berbasis challenge-response**, reader yang benar-benar mendukung perintah tersebut dan manajemen kunci. Tidak dapat diselesaikan hanya dengan hashing UID atau menambahkan token pada ESP32. Karena jenis reader/kartu belum diketahui, rilis ini menyertakan integrasi UID berpengamanan server, *bukan* klaim anti-clone penuh. Firmware spesifik pin SPI/I2C dan UI realtime pending menunggu detail perangkat.

## Uji fungsional dan keamanan wajib

- Login user biasa ke `/admin/...` -> 403; login admin ke `/user/...` -> 403.
- Akun `diblokir`/`tidak_aktif` ditolak saat login dan bila sesi masih aktif.
- Reset password tanpa token / token kedaluwarsa / token dipakai ulang ditolak; email reset dikirim jika SMTP benar.
- Stok 1, dua admin menyetujui peminjaman secara bersamaan: tepat satu penurunan; stok tidak negatif (uji konkuren di MySQL/InnoDB).
- Approval dua kali/pengembalian dua kali: stok tidak dihitung ganda.
- QR valid hanya 3 menit dan satu kali; URL eksternal dan ID tebakan ditolak oleh endpoint scan POST.
- Kartu UID invalid/tidak terdaftar/dicabut ditolak lookup; perangkat tanpa signature atau nonce replay ditolak.
- Laporan Excel bisa diunduh, CRUD buku/anggota lama berjalan, media dapat dibaca.
- `php artisan test`: ada feature tests baru memakai SQLite in-memory. **Belum dieksekusi saat pembangunan ZIP** karena `vendor/` dan ekstensi database di lingkungan pembangun tidak tersedia; pengujian concurrency perlu MySQL sungguhan.

## Batasan yang diketahui (jangan anggap siap produksi)

- Laravel 10 tidak lagi mendapat patch keamanan resmi. Upgrade framework adalah pekerjaan penting berikutnya; jangan menjadikan rilis ini bukti kelayakan produksi publik.
- UI masih memakai Tailwind CDN dan scanner html5-qrcode dari unpkg, sehingga butuh internet, CSP ketat dan bundling aset lokal belum diterapkan.
- Alur `VerifikasiPeminjamanController` adalah kode lama yang **tidak terdaftar pada route aktif**; tidak digunakan oleh integrasi baru.
- RFID per eksemplar buku / tata letak rak / firmware device belum diimplementasikan. Yang tersedia adalah **kartu anggota** dan API reader (identifikasi UID).
- Tidak ada bukti testing hardware atau server fisik dari ZIP ini. Wajib uji keamanan, backup, restore, race condition dan integrasi perangkat sebelum penggunaan operasional.
- Inisialisasi MySQL nyata, konfigurasi SMTP, akun admin baru, dan provisioning ESP32 harus dilakukan oleh operator sekolah, bukan melalui source ZIP yang memuat secrets.

## Catatan rollback

Hentikan versi baru, restore source dan dump MySQL hasil backup. Jangan otomatis rollback migrasi yang telah menerima data transaksi/log baru karena dapat menyebabkan kehilangan data. Simpan arsip JSONL MongoDB lama bila riwayatnya penting. Setelah rilis full ini lolos tes, gunakan sebagai baseline untuk patch berikutnya.
# perpustakaan-self-service
