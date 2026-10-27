#!/usr/bin/env bash
set -euo pipefail
if ! command -v apt-get >/dev/null; then echo '[BLOCK] Hanya Debian/apt.'; exit 2; fi
if [ "$(id -u)" -eq 0 ]; then echo '[BLOCK] Jalankan sebagai user non-root dengan sudo terkonfigurasi.'; exit 2; fi
printf 'Akan memasang dependensi PHP-FPM/Composer/MariaDB client dan Caddy resmi di SISTEM Debian. Ketik INSTALL untuk lanjut: '
read -r answer
[ "$answer" = INSTALL ] || { echo '[BATAL] Tidak ada perubahan.'; exit 0; }
sudo apt-get update
sudo apt-get install -y ca-certificates curl gnupg debian-keyring debian-archive-keyring apt-transport-https unzip php-cli php-fpm php-mysql php-mbstring php-xml php-gd php-curl php-zip php-bcmath php-intl composer mariadb-client
if ! command -v caddy >/dev/null 2>&1; then
 echo '[INFO] Menambahkan repo resmi Caddy (Cloudsmith).'
 sudo mkdir -p /usr/share/keyrings
 curl -fsSL 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | sudo gpg --dearmor -o /usr/share/keyrings/caddy-stable-archive-keyring.gpg
 curl -fsSL 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | sudo tee /etc/apt/sources.list.d/caddy-stable.list >/dev/null
 sudo chmod o+r /usr/share/keyrings/caddy-stable-archive-keyring.gpg
 sudo apt-get update
 sudo apt-get install -y caddy
fi
printf '[OK] Dependensi instalasi selesai. Tinjau ./00_PRECHECK.sh, lalu composer install di direktori proyek.\n'
