#!/usr/bin/env bash
set -u
HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT="$(cd "$HERE/../../.." && pwd)"
REPORT_DIR="$HERE/_reports"; mkdir -p "$REPORT_DIR"
STAMP="$(date +%Y%m%d-%H%M%S)"
LOG="$REPORT_DIR/check-$STAMP.log"
{
 echo '=== PERPUSTAKAAN DEBIAN CHECK - NON MUTATING ==='
 date -Is
 echo "Project: $ROOT"
 for t in php composer caddy mysql; do command -v "$t" >/dev/null && echo "[OK] $t" || echo "[WARN] $t missing"; done
 for s in caddy mariadb; do systemctl is-active "$s" 2>/dev/null || true; done
 for s in /run/php/php*-fpm.sock; do [ -S "$s" ] && echo "[OK] $s"; done
 (cd "$ROOT" && php artisan --version && php artisan route:list --path=kiosk && php artisan migrate:status) 2>&1 || echo '[WARN] Artisan/DB readiness check failed'
 echo 'Manual: validate browser HTTPS padlock, QR camera permission, scanner kiosk, backup restore.'
} | tee "$LOG"
echo "Laporan: $LOG"
