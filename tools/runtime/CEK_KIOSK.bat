@echo off
setlocal DisableDelayedExpansion
call "%~dp0_common.bat"
echo ================================================================
echo   CEK KIOSK - ROUTE + PETUNJUK SCAN (READ ONLY)
echo ================================================================
if not defined PHP_EXE (echo [ERROR] PHP tidak ada.& pause& exit /b 2)
if not exist "%PROJECT_ROOT%\vendor\autoload.php" (echo [ERROR] vendor tidak ada.& pause& exit /b 2)
cd /d "%PROJECT_ROOT%" || exit /b 2
echo [1] Route kiosk publik / device
"%PHP_EXE%" artisan route:list --path=kiosk
echo [2] Route admin kiosk
"%PHP_EXE%" artisan route:list --path=admin/kiosk
echo.
echo Cara uji:
echo ADMIN: https://IP:8443/admin/kiosk/devices (login admin)
echo KIOSK: https://IP:8443/kiosk/setup (aktifkan pakai kode admin)
echo SCAN : https://IP:8443/kiosk (tidak perlu login admin)
echo QR   : /user/kiosk/pass lewat login siswa di perangkat lain
echo Pastikan HTTP 419/426/401/403/404 ditangani sesuai TROUBLESHOOTING.
echo Jangan aktifkan transaksi LIVE tanpa uji dan backup database.
pause
