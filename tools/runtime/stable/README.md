# Stable TLS / Hostname (Windows)

Gunakan `BUAT_CADDY_DOMAIN.bat` untuk membuat Caddyfile di `%LOCALAPPDATA%\Perpustakaan_TLS_STABLE`; lalu `START_LARAVEL.bat` (toolkit lama) dan `START_DOMAIN_HTTPS.bat`.

Tidak ada private key dalam proyek/patch. Untuk nama `perpus.home.arpa`, DNS router harus diarahkan ke server (sesuaikan di setiap jaringan) dan CA Caddy harus dipercaya oleh klien. Untuk domain publik, pasang DNS A/AAAA/DDNS benar dan pastikan ACME dapat mencapai port 80/443; jika hanya LAN tanpa port publik, gunakan DNS-01 dengan plugin Caddy atau pendekatan akses privat yang sesuai, bukan menganggap sertifikat publik muncul otomatis.

**Jangan gunakan IP dari `ipconfig` sebagai nama host pada Caddyfile.** Bila IP pindah, update DNS, bukan ganti sertifikat. Jika Wi-Fi/network berganti, DNS harus tersedia di jaringan itu. Alternatif lebih stabil untuk multi-lokasi adalah domain publik dengan DNS dinamis atau VPN terkelola.

Buka `CEK_DOMAIN.bat` untuk cek dasar. Ekspos aplikasi lama ke Internet hanya setelah Laravel/dependency di-upgrade, hak akses diaudit dan keamanan dites.
