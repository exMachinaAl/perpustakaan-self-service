@echo off
setlocal
call "%~dp0..\_common.bat"
echo === STABLE HOSTNAME TLS CHECK ===
echo Proyek: "%PROJECT_ROOT%"
if not defined CADDY_EXE (echo [WARN] Caddy tidak ditemukan. Jalankan tools\runtime\SETUP.bat.) else "%CADDY_EXE%" version
if not exist "%PROJECT_ROOT%\artisan" echo [BLOCK] Artisan tidak ditemukan.
if not exist "%PROJECT_ROOT%\.env" echo [BLOCK] .env tidak ditemukan.
if exist "%PROJECT_ROOT%\.env" findstr /b /i "APP_URL= KIOSK_PHONE_BASE_URL= SESSION_SECURE_COOKIE=" "%PROJECT_ROOT%\.env" | findstr /v /i "KEY= PASSWORD= SECRET="
echo [INFO] APP_URL harus memakai hostname stabil, bukan IP LAN sementara.
echo [INFO] Pastikan DNS hostname teresolusi di semua HP/kiosk.
echo [INFO] Tes: buka URL HTTPS di perangkat lain, pastikan sertifikat dipercaya (tidak ada peringatan).
pause
