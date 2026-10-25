@echo off
setlocal DisableDelayedExpansion
call "%~dp0_common.bat"
echo ================================================================
echo  PERPUSTAKAAN - START LARAVEL + HTTPS (LAB)
echo ================================================================
if not defined PHP_EXE (echo [ERROR] PHP belum ditemukan.& pause& exit /b 2)
if not exist "%PROJECT_ROOT%\vendor\autoload.php" (echo [ERROR] vendor belum tersedia.& pause& exit /b 2)
if not defined CADDY_EXE (echo [ERROR] Caddy belum ditemukan. Jalankan SETUP.bat.& pause& exit /b 2)
if not exist "%TLS_DIR%\Caddyfile" (echo [ERROR] TLS belum disiapkan. Jalankan BUAT_HTTPS_LAN.bat.& pause& exit /b 2)
"%CADDY_EXE%" validate --config "%TLS_DIR%\Caddyfile" --adapter caddyfile >nul 2>&1
if errorlevel 1 (echo [ERROR] Caddyfile invalid; batal start.& pause& exit /b 2)
echo [INFO] Membuka dua terminal terpisah; tutup masing-masing dengan Ctrl+C.
start "PERPUS Laravel 8000" cmd /k ""%~dp0START_LARAVEL.bat""
start "PERPUS Caddy HTTPS" cmd /k ""%~dp0START_HTTPS.bat""
echo [INFO] Jika port dipakai proses lain, baca error di terminal masing-masing.
echo [INFO] Kiosk: https://IP-SERVER:8443/kiosk
pause
