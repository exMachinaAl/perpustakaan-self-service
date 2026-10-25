# HTTPS LAN — membuat sertifikat/key sendiri, langkah lengkap

## 1. Syarat

Unduh program asli `mkcert.exe` dari https://github.com/FiloSottile/mkcert/releases dan `caddy.exe` dari https://caddyserver.com/download. Letakkan keduanya di `%LOCALAPPDATA%\Perpustakaan_TLS_LAB\bin\`. Jangan simpan binary, sertifikat, atau private key di `OneDrive\...\perpustakaan-full-secure`.

Pastikan PC server dapat menjalankan Laravel pada `127.0.0.1:8000` (melalui `START_LARAVEL.bat`). Tentukan IPv4 PC melalui `ipconfig`; contoh `192.168.1.20` hanya ilustrasi.

## 2. Generate

Klik `tools/runtime/BUAT_HTTPS_LAN.bat` dan isi IPv4 PC. Alat melakukan setara perintah berikut (pada Windows CMD, dengan `CAROOT` dipindah ke folder lokal):

```bat
set "CAROOT=%LOCALAPPDATA%\Perpustakaan_TLS_LAB\ca"
mkcert -install
mkcert -cert-file "%LOCALAPPDATA%\Perpustakaan_TLS_LAB\certs\192.168.1.20\perpus.pem" -key-file "%LOCALAPPDATA%\Perpustakaan_TLS_LAB\certs\192.168.1.20\perpus-key.pem" 192.168.1.20 localhost 127.0.0.1
```

`perpus.pem` adalah sertifikat publik, `perpus-key.pem` adalah **private key server**, `rootCA.pem` adalah sertifikat CA pengujian, dan `rootCA-key.pem` adalah **private key CA yang paling sensitif**. Jangan unggah dua private key tersebut ke chat, Git, ataupun OneDrive.

## 3. Konfigurasi Caddy

Skrip menulis Caddyfile di `%LOCALAPPDATA%\Perpustakaan_TLS_LAB\Caddyfile` dan memvalidasinya menggunakan `caddy validate`. Contoh (gunakan alamat file Anda):

```caddyfile
https://192.168.1.20:8443 {
    tls "C:/Users/USER/AppData/Local/Perpustakaan_TLS_LAB/certs/192.168.1.20/perpus.pem" "C:/Users/USER/AppData/Local/Perpustakaan_TLS_LAB/certs/192.168.1.20/perpus-key.pem"
    reverse_proxy 127.0.0.1:8000
}
```

Caddy terminasi TLS dan mem-proxy ke localhost. **Jangan** menambahkan `tls_insecure_skip_verify`.

## 4. Konfigurasi Laravel (manual)

```ini
APP_URL=https://192.168.1.20:8443
KIOSK_PHONE_BASE_URL=https://192.168.1.20:8443
SESSION_SECURE_COOKIE=true
```

IP tersebut harus disesuaikan. Jalankan `php artisan config:clear` setelah perubahan. Jangan mengganti `APP_KEY`; cek setting proxy/trusted headers bila menemui redirect loop atau HTTP 426.

## 5. Mulai dan tes

- Jalankan `START.bat` (membuka Laravel serta Caddy dalam dua jendela).
- Pada PC server, akses `https://192.168.1.20:8443/admin/kiosk/hub`.
- Pada HP lab, akses `https://192.168.1.20:8443/kiosk/setup`.
- Browser harus **mempercayai** sertifikat dan izin kamera diberikan. `http://192.168.1.20` tidak cukup untuk `getUserMedia`.
- CA Windows tidak otomatis dipercaya Android/iOS. Instal `rootCA.pem` **hanya pada perangkat lab yang dikelola**, mengikuti panduan OS; jangan memasang CA privat massal di HP siswa. Jangan bagikan `rootCA-key.pem`.

## 6. Jika gagal

- `ERR_CERT_AUTHORITY_INVALID`: HP/PC belum mempercayai CA; gunakan perangkat lab atau sertifikat publik.
- `ERR_CERT_COMMON_NAME_INVALID`: IP sekarang berbeda dengan SAN; buat sertifikat untuk IP baru.
- `Failed to fetch`/timeout: periksa Windows Firewall port TCP 8443, client isolation Wi-Fi, kedua CMD berjalan.
- Kamera tidak ada: cek `window.isSecureContext`, izin kamera OS, HTTPS, dan apakah aplikasi dalam iframe terbatas.
- Port 8443 sudah dipakai: hentikan instance Caddy lama secara manual (Ctrl+C), bukan kill-all.

Untuk produksi publik, gunakan domain valid, sertifikat CA publik dan proses server PHP/web server yang sesuai; jangan memakai CA mkcert pengujian sebagai CA publik.
