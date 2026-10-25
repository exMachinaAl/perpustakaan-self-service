@echo off
setlocal DisableDelayedExpansion
call "%~dp0_common.bat"
if not exist "%PROJECT_ROOT%\artisan" (echo [ERROR] Proyek Laravel tidak ditemukan.& exit /b 2)
set "OUT_DIR=%PROJECT_ROOT%\storage\logs\runtime-toolkit"
if not exist "%OUT_DIR%" mkdir "%OUT_DIR%" >nul 2>nul
set "OUT=%OUT_DIR%\CHECK_SYSTEM_TERAKHIR.txt"
> "%OUT%" echo PERPUSTAKAAN - READ ONLY CHECK
>>"%OUT%" echo Project=%PROJECT_ROOT%
echo [INFO] Laporan: "%OUT%"
if not defined PHP_EXE (
  echo [ERROR] PHP tidak ditemukan.
  >>"%OUT%" echo ERROR: PHP tidak ditemukan
  exit /b 2
)
echo [INFO] Pemeriksaan PHP...
"%PHP_EXE%" -v >>"%OUT%" 2>&1
if errorlevel 1 (echo [ERROR] PHP gagal.& exit /b 2)
for %%E in (pdo_mysql openssl mbstring fileinfo gd) do call :CHECK_EXT %%E
if exist "%PROJECT_ROOT%\.env" (>>"%OUT%" echo OK: .env ada) else (>>"%OUT%" echo ERROR: .env tidak ditemukan& echo [ERROR] .env tidak ada)
if exist "%PROJECT_ROOT%\vendor\autoload.php" (>>"%OUT%" echo OK: vendor ada) else (>>"%OUT%" echo ERROR: vendor tidak ada& echo [ERROR] vendor Composer belum dipasang)
if defined CADDY_EXE (>>"%OUT%" echo OK: Caddy exe ditemukan) else (>>"%OUT%" echo WARN: Caddy exe tidak ditemukan)
if defined MKCERT_EXE (>>"%OUT%" echo OK: mkcert exe ditemukan) else (>>"%OUT%" echo WARN: mkcert exe tidak ditemukan)
if exist "%TLS_DIR%\Caddyfile" (>>"%OUT%" echo OK: TLS Caddyfile ditemukan) else (>>"%OUT%" echo WARN: TLS belum dikonfigurasi)
if exist "%TLS_DIR%\Caddyfile" if defined CADDY_EXE (
  "%CADDY_EXE%" validate --config "%TLS_DIR%\Caddyfile" --adapter caddyfile >>"%OUT%" 2>&1
  if errorlevel 1 (echo [ERROR] Caddyfile invalid. Periksa laporan.) else echo [OK] Caddyfile valid.
)
if exist "%PROJECT_ROOT%\vendor\autoload.php" (
  pushd "%PROJECT_ROOT%"
  >>"%OUT%" echo ------ LARAVEL VERSION ------
  "%PHP_EXE%" artisan --version >>"%OUT%" 2>&1
  >>"%OUT%" echo ------ KIOSK ROUTES ------
  "%PHP_EXE%" artisan route:list --path=kiosk >>"%OUT%" 2>&1
  >>"%OUT%" echo ------ ADMIN KIOSK ROUTES ------
  "%PHP_EXE%" artisan route:list --path=admin/kiosk >>"%OUT%" 2>&1
  >>"%OUT%" echo ------ MIGRATE STATUS (READ ONLY) ------
  "%PHP_EXE%" artisan migrate:status >>"%OUT%" 2>&1
  if errorlevel 1 echo [WARN] migrate:status gagal; cek koneksi MySQL dan laporan.
  popd
) else echo [WARN] Pemeriksaan artisan dilewati, vendor belum ada.
echo [SELESAI] Buka laporan: "%OUT%"
echo [PENTING] Pemeriksaan tidak menampilkan isi .env atau credential.
pause
exit /b 0
:CHECK_EXT
"%PHP_EXE%" -r "exit(extension_loaded('%~1') ? 0 : 1);" >nul 2>&1
if errorlevel 1 (
  echo [WARN] extension %~1 MISSING
  >>"%OUT%" echo WARN: extension %~1 MISSING
) else (
  echo [OK] extension %~1
  >>"%OUT%" echo OK: extension %~1
)
exit /b 0
