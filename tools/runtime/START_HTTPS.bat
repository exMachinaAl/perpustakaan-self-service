@echo off
setlocal DisableDelayedExpansion
call "%~dp0_common.bat"
if not defined CADDY_EXE (echo [ERROR] caddy.exe tidak ditemukan. Jalankan SETUP.bat.& pause& exit /b 2)
if not exist "%TLS_DIR%\Caddyfile" (echo [ERROR] Caddyfile belum ada. Jalankan BUAT_HTTPS_LAN.bat.& pause& exit /b 2)
"%CADDY_EXE%" validate --config "%TLS_DIR%\Caddyfile" --adapter caddyfile
if errorlevel 1 (echo [ERROR] Konfigurasi HTTPS invalid.& pause& exit /b 2)
echo [INFO] Membuka HTTPS Caddy pada alamat yang ada di Caddyfile.
echo [INFO] Ctrl+C pada jendela ini untuk berhenti. Jangan jalankan lebih dari satu instance.
"%CADDY_EXE%" run --config "%TLS_DIR%\Caddyfile" --adapter caddyfile
echo [INFO] Caddy berhenti / gagal.
pause
