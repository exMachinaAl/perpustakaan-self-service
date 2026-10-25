@echo off
setlocal DisableDelayedExpansion
call "%~dp0_common.bat"
echo ================================================================
echo   PERPUSTAKAAN - SETUP RUNTIME [TIDAK MENGUBAH DATABASE]
echo ================================================================
if not exist "%PROJECT_ROOT%\artisan" (echo [ERROR] Root Laravel tidak ditemukan.& exit /b 2)
echo [OK] Folder proyek: "%PROJECT_ROOT%"
if not exist "%PROJECT_ROOT%\.env" (echo [ERROR] .env tidak ditemukan. Konfigurasikan dari .env.example dahulu.) else echo [OK] .env tersedia; isinya tidak ditampilkan.
if not exist "%PROJECT_ROOT%\vendor\autoload.php" (echo [ERROR] vendor belum siap. Jalankan composer install di root proyek.) else echo [OK] Vendor Composer tersedia.
if defined PHP_EXE (echo [OK] PHP ditemukan: "%PHP_EXE%") else echo [ERROR] PHP CLI belum ditemukan. Set PERPUS_PHP_EXE atau PATH.
if defined CADDY_EXE (echo [OK] Caddy: "%CADDY_EXE%") else echo [WARN] Caddy belum ada. Tempatkan di "%TLS_DIR%\bin\caddy.exe"
if defined MKCERT_EXE (echo [OK] mkcert: "%MKCERT_EXE%") else echo [WARN] mkcert belum ada. Tempatkan di "%TLS_DIR%\bin\mkcert.exe"
if exist "%TLS_DIR%\Caddyfile" (echo [OK] TLS Caddyfile sudah dibuat.) else echo [WARN] HTTPS LAN belum dibuat. Jalankan BUAT_HTTPS_LAN.bat.
echo.
echo Urutan:
echo 1. Pasang PHP, ekstensi, Composer vendor, MySQL.
echo 2. Jalankan CHECK_SYSTEM.bat dan baca hasil.
echo 3. Jalankan BUAT_HTTPS_LAN.bat untuk sertifikat lokal.
echo 4. Periksa APP_URL/KIOSK_PHONE_BASE_URL dan SESSION_SECURE_COOKIE di .env.
echo 5. Jalankan START.bat, lalu CEK_KIOSK.bat.
echo.
echo Dokumentasi: "%PROJECT_ROOT%\README-RUNTIME.md"
echo Tidak ada composer install, migrate, key:generate, atau setup TLS otomatis.
pause
exit /b 0
