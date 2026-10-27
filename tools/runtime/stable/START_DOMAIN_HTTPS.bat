@echo off
setlocal
call "%~dp0..\_common.bat"
if not defined CADDY_EXE (echo [BLOCK] Caddy.exe tidak ditemukan.& pause& exit /b 2)
if not defined LOCALAPPDATA set "LOCALAPPDATA=%USERPROFILE%\AppData\Local"
set "CFG=%LOCALAPPDATA%\Perpustakaan_TLS_STABLE\Caddyfile"
if not exist "%CFG%" (echo [BLOCK] Caddyfile belum ada. Jalankan BUAT_CADDY_DOMAIN.bat.& pause& exit /b 2)
"%CADDY_EXE%" validate --config "%CFG%" --adapter caddyfile
if errorlevel 1 (echo [BLOCK] Invalid Caddyfile.& pause& exit /b 2)
echo [INFO] Pastikan Laravel sudah berjalan menggunakan START_LARAVEL.bat.
echo [INFO] Untuk sertifikat publik, DNS dan port 80/443 harus reachable.
echo [INFO] Jangan jalankan Caddy lain pada port yang sama.
"%CADDY_EXE%" run --config "%CFG%" --adapter caddyfile
pause
