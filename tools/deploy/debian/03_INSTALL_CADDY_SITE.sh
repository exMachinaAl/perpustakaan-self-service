#!/usr/bin/env bash
set -euo pipefail
HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SOURCE="${1:-}"
[ -n "$SOURCE" ] && [ -f "$SOURCE" ] || { echo 'Cara pakai: ./03_INSTALL_CADDY_SITE.sh tools/deploy/debian/generated/Caddyfile.nama'; exit 2; }
command -v caddy >/dev/null || { echo '[BLOCK] caddy belum terpasang'; exit 2; }
TARGET='/etc/caddy/perpustakaan.caddy'
MAIN='/etc/caddy/Caddyfile'
[ -e "$MAIN" ] || { echo '[BLOCK] /etc/caddy/Caddyfile belum ada'; exit 2; }
if sudo test -e "$TARGET"; then
 if sudo cmp -s "$SOURCE" "$TARGET"; then echo '[OK] Site identik sudah dipasang'; else echo '[BLOCK] Site sudah ada namun berbeda; tangani manual, tidak overwrite.'; exit 2; fi
fi
BACKUP="$HERE/_backups/site-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
sudo cp "$MAIN" "$BACKUP/Caddyfile.before" || { echo '[BLOCK] Gagal backup Caddyfile'; exit 2; }
echo "[INFO] Backup config OS: $BACKUP (di dalam proyek)"
echo "[INFO] Akan menambah konfigurasi site $TARGET dan import di Caddyfile; tidak membuka firewall otomatis."
printf 'Ketik TERAPKAN untuk lanjut: '; read -r yes
[ "$yes" = TERAPKAN ] || { echo '[BATAL] Tidak ada perubahan.'; exit 0; }
if ! sudo test -e "$TARGET"; then sudo install -o root -g root -m 0644 "$SOURCE" "$TARGET"; fi
if ! sudo grep -Fqx 'import /etc/caddy/perpustakaan.caddy' "$MAIN"; then
 printf '\nimport /etc/caddy/perpustakaan.caddy\n' | sudo tee -a "$MAIN" >/dev/null
fi
if ! sudo caddy validate --config "$MAIN" --adapter caddyfile; then
 echo '[BLOCK] Konfigurasi tidak valid. Memulihkan Caddyfile utama (site dibiarkan untuk tinjauan).'
 sudo cp "$BACKUP/Caddyfile.before" "$MAIN"
 exit 2
fi
sudo systemctl reload caddy || { echo '[ERROR] Gagal reload Caddy; cek journalctl -u caddy'; exit 2; }
echo '[OK] Caddy reload. Pastikan DNS, firewall, sertifikat, dan izin filesystem sudah sesuai.'
