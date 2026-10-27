# PATCH 011 — Stable Hostname TLS + Debian 13 Starter

Patch ini menambahkan alat platform permanen untuk **proyek perpustakaan-full-secure yang sudah ada**. Tidak membuat aplikasi baru, tidak memodifikasi kode transaksi, migrasi, database, file .env maupun kunci HTTPS. Upgrade Windows menggunakan BAT satu Enter dan upgrade Debian menggunakan SH satu Enter (jika source sudah dipindahkan ke Debian).

**Mulai dari:** `docs/DEPLOYMENT_STABLE_TLS_DEBIAN_WINDOWS.md`

- Windows: `tools/runtime/stable/BUAT_CADDY_DOMAIN.bat`, `START_DOMAIN_HTTPS.bat`, `CEK_DOMAIN.bat`.
- Debian: `tools/deploy/debian/00_PRECHECK.sh` sampai `04_HEALTHCHECK.sh`.
- Starter Debian juga disediakan sebagai ZIP terpisah jika ingin mempersiapkan server sebelum menyalin source aplikasi.
- Mode domain publik: sertifikat otomatis Caddy, **jika** DNS publik dan validasi ACME tersedia.
- Mode LAN: hostname DNS lokal tetap + `tls internal`, membutuhkan pemasangan CA tepercaya di perangkat lab.

**Tidak ada solusi TLS universal untuk semua Wi-Fi tanpa DNS atau pengelolaan hostname.** Saat pindah jaringan, DNS harus mengikuti server. Hindari mengganti IP di sertifikat setiap kali.

Aplikasi sudah menggunakan Laravel 12; uji perangkat, beban, backup/restore, dan keamanan operasional tetap wajib sebelum produksi.
