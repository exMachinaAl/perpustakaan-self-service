@echo off
setlocal EnableExtensions
for %%I in ("%~dp0..\..") do set "PROJECT=%%~fI"
set "PHP_EXE="
for /f "delims=" %%P in ('where php.exe 2^>nul') do if not defined PHP_EXE set "PHP_EXE=%%P"
if not defined PHP_EXE if exist "C:\Program Files X\php-8.2.34-nts-Win32-vs16-x64\php.exe" set "PHP_EXE=C:\Program Files X\php-8.2.34-nts-Win32-vs16-x64\php.exe"
if not defined PHP_EXE if exist "C:\Program Files\php-8.2.35-nts-Win32-vs16-x64\php.exe" set "PHP_EXE=C:\Program Files\php-8.2.35-nts-Win32-vs16-x64\php.exe"
echo ==== READ-ONLY CHECK KIOSK CIRCULATION 009 ====
echo Project: "%PROJECT%"
if not exist "%PROJECT%\artisan" (echo [GAGAL] Root Laravel tidak ditemukan.&pause&exit /b 2)
if not defined PHP_EXE (echo [GAGAL] PHP CLI tidak ditemukan.&pause&exit /b 2)
if not exist "%PROJECT%\app\Services\KioskCheckoutPilot.php" (echo [GAGAL] PATCH 009 tidak ditemukan.&pause&exit /b 2)
echo [INFO] Tidak menjalankan migrasi, tidak mengubah database.
pushd "%PROJECT%"
"%PHP_EXE%" artisan route:list --path=admin/kiosk/pilot
"%PHP_EXE%" artisan migrate:status
popd
echo [INFO] Pengujian peminjaman nyata harus mengikuti docs\KIOSK_CIRCULATION_PILOT.md
pause
