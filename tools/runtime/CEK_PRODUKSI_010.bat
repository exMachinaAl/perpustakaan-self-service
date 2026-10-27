@echo off
setlocal EnableExtensions
for %%I in ("%~dp0..\..") do set "PROJECT=%%~fI"
set "REPORTDIR=%~dp0_reports"
if not exist "%REPORTDIR%" mkdir "%REPORTDIR%" >nul 2>nul
set "REPORT=%REPORTDIR%\READINESS_010.txt"
echo =======================================================
echo  PERPUSTAKAAN - PERIKSA KESIAPAN PRODUKSI (READ-ONLY)
echo =======================================================
if not exist "%PROJECT%\artisan" (
 echo [GAGAL] Root Laravel tidak ditemukan: %PROJECT%
 pause
 exit /b 2
)
set "PHP_EXE="
for /f "delims=" %%P in ('where php.exe 2^>nul') do if not defined PHP_EXE set "PHP_EXE=%%P"
if not defined PHP_EXE if exist "C:\Program Files\php-8.2.35-nts-Win32-vs16-x64\php.exe" set "PHP_EXE=C:\Program Files\php-8.2.35-nts-Win32-vs16-x64\php.exe"
if not defined PHP_EXE if exist "C:\Program Files X\php-8.2.34-nts-Win32-vs16-x64\php.exe" set "PHP_EXE=C:\Program Files X\php-8.2.34-nts-Win32-vs16-x64\php.exe"
if not defined PHP_EXE (
 echo [GAGAL] php.exe tidak ditemukan. Periksa PATH atau jalankan SETUP.bat.
 pause
 exit /b 2
)
pushd "%PROJECT%"
"%PHP_EXE%" artisan perpus:production-check >"%REPORT%" 2>&1
set "RESULT=%ERRORLEVEL%"
popd
type "%REPORT%"
echo.
echo [INFO] Laporan: %REPORT%
if not "%RESULT%"=="0" echo [PERINGATAN] Ditemukan blocker atau error pemeriksaan.
echo [INFO] PASS bukan jaminan siap produksi karena pemeriksaan fisik dan beban bersifat manual.
pause
exit /b %RESULT%
