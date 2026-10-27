#!/usr/bin/env bash
set -u
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.." && pwd)"
[ -f "$ROOT/artisan" ] || { echo '[BLOCK] artisan tidak ditemukan; periksa lokasi proyek.'; exit 2; }
echo '=== PERPUSTAKAAN: DEBIAN PLATFORM CHECK (READ ONLY) ==='
echo "Root: $ROOT"
if [ -r /etc/os-release ]; then . /etc/os-release; echo "OS: $PRETTY_NAME"; fi
for c in php composer caddy mysql mariadb curl systemctl; do
 if command -v "$c" >/dev/null 2>&1; then echo "[OK] $c -> $(command -v "$c")"; else echo "[WARN] $c belum ditemukan"; fi
done
if command -v php >/dev/null 2>&1; then
 php -v | head -1
 php -r 'foreach(["pdo_mysql","mbstring","openssl","fileinfo","gd","xml","curl","zip"] as $e) {echo (extension_loaded($e)?"[OK] ":"[WARN] ")."ext-$e\n";}'
fi
if command -v composer >/dev/null 2>&1; then (cd "$ROOT" && composer check-platform-reqs --no-dev 2>&1 | tail -15) || echo '[WARN] Composer platform requirements belum lengkap'; fi
if [ -e "$ROOT/.env" ]; then echo '[OK] .env tersedia (nilai dirahasiakan)'; else echo '[BLOCK] .env belum tersedia'; fi
if [ -f "$ROOT/vendor/autoload.php" ]; then echo '[OK] vendor/autoload.php'; else echo '[WARN] vendor belum tersedia'; fi
if [ -e "$ROOT/app/Services/KioskPilotReturn.php" ]; then echo '[OK] Indikator PATCH 010'; else echo '[WARN] PATCH 010 belum terdeteksi'; fi
for p in /run/php/php*-fpm.sock; do [ -S "$p" ] && echo "[OK] PHP-FPM socket: $p"; done
if command -v caddy >/dev/null 2>&1; then caddy version | head -1; fi
echo '[INFO] Pemeriksa ini tidak menjalankan migrasi ataupun perubahan konfigurasi.'
echo '[GATE] Gunakan HTTPS trusted; Laravel 10 belum aman untuk publik sebelum upgrade/QA.'
