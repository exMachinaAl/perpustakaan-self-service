#!/usr/bin/env bash
set -euo pipefail
HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT="$(cd "$HERE/../../.." && pwd)"
[ -f "$ROOT/artisan" ] || { echo '[BLOCK] Jalankan setelah toolkit terpasang di proyek Laravel.'; exit 2; }
printf 'Hostname stabil (misal perpus.example.sch.id atau perpus.home.arpa): '
read -r DOMAIN
if ! [[ "$DOMAIN" =~ ^[a-zA-Z0-9][a-zA-Z0-9.-]*[a-zA-Z0-9]$ ]] || [[ "$DOMAIN" == *..* ]] || [[ "$DOMAIN" == *'/'* ]]; then echo '[BLOCK] Hostname tidak valid'; exit 2; fi
if [[ "$DOMAIN" == *.home.arpa || "$DOMAIN" == *.local || "$DOMAIN" == *.internal ]]; then
 MODE=lan
else
 echo 'Pilih mode: 1 = PUBLIC (ACME, DNS publik + port 80/443), 2 = LAN (CA internal Caddy)'
 read -r CHOICE
 case "$CHOICE" in 1) MODE=public;; 2) MODE=lan;; *) echo '[BATAL] Pilihan tidak valid'; exit 2;; esac
fi
SOCKET=''
for s in /run/php/php*-fpm.sock; do if [ -S "$s" ]; then SOCKET="$s"; break; fi; done
if [ -z "$SOCKET" ]; then echo '[BLOCK] PHP-FPM socket tidak ditemukan; jalankan 00_PRECHECK.sh dan start service php-fpm'; exit 2; fi
OUT="$HERE/generated/Caddyfile.$DOMAIN"
mkdir -p "$HERE/generated"
if [ -e "$OUT" ]; then echo "[BLOCK] Sudah ada, tidak ditimpa: $OUT"; exit 2; fi
{
 printf '%s {\n' "$DOMAIN"
 [ "$MODE" = lan ] && printf '    tls internal\n'
 printf '    root * "%s/public"\n' "$ROOT"
 printf '    encode zstd gzip\n'
 printf '    php_fastcgi unix/%s\n' "$SOCKET"
 printf '    file_server\n'
 printf '    header X-Content-Type-Options "nosniff"\n'
 printf '    header Referrer-Policy "same-origin"\n'
 printf '}\n'
} > "$OUT"
chmod 644 "$OUT"
if command -v caddy >/dev/null 2>&1; then caddy validate --config "$OUT" --adapter caddyfile || { echo '[BLOCK] Caddyfile gagal validasi'; exit 2; }; fi
cat <<EOF
[OK] Template: $OUT
Mode: $MODE
Tindakan berikutnya:
1. Pastikan DNS $DOMAIN mengarah ke server (IP boleh berubah bila DNS diperbarui).
2. Periksa APP_URL=https://$DOMAIN, SESSION_SECURE_COOKIE=true dalam .env; APP_KEY jangan diganti.
3. Jalankan 03_INSTALL_CADDY_SITE.sh "$OUT" untuk memasang konfigurasi SESUDAH diperiksa.
4. Untuk mode LAN, client harus percaya Caddy Local Authority (lihat panduan).
5. Untuk mode PUBLIC, port publik 80/443 dan DNS harus benar; untuk server LAN-only, gunakan DNS-01 dengan plugin Caddy atau tunnel/VPN tepercaya (konfigurasi terpisah).
EOF
