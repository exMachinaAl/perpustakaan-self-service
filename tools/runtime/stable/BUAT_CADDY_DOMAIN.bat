@echo off
setlocal EnableDelayedExpansion
call "%~dp0..\_common.bat"
if not defined LOCALAPPDATA set "LOCALAPPDATA=%USERPROFILE%\AppData\Local"
set "OUT=%LOCALAPPDATA%\Perpustakaan_TLS_STABLE"
if not exist "%OUT%" mkdir "%OUT%" || goto :fail
set "HOST="
echo Pilih nama DOMAIN/STABLE HOSTNAME. Contoh: perpus.example.sch.id / perpus.home.arpa
set /p "HOST=Hostname (tanpa https://): "
if not defined HOST goto :fail
echo(!HOST!|findstr /R /C:"^[A-Za-z0-9][A-Za-z0-9.-]*[A-Za-z0-9]$" >nul
if errorlevel 1 goto :fail
if exist "%OUT%\Caddyfile" (echo [BLOCK] Caddyfile sudah ada: "%OUT%\Caddyfile". Tidak ditimpa.& goto :fail)
set "MODE="
echo 1 = Domain PUBLIK (HTTPS CA otomatis; DNS dan port 80/443 wajib)
echo 2 = LAN saja (Caddy CA internal; butuh DNS dan trust CA klien)
set /p "MODE=Pilih 1 atau 2: "
if not "!MODE!"=="1" if not "!MODE!"=="2" goto :fail
echo(!HOST! {>"%OUT%\Caddyfile"
if "!MODE!"=="2" echo     tls internal>>"%OUT%\Caddyfile"
echo     reverse_proxy 127.0.0.1:8000>>"%OUT%\Caddyfile"
echo }>>"%OUT%\Caddyfile"
echo [OK] Generated: "%OUT%\Caddyfile"
echo [INFO] Caddy manages certificates itself. No private key is generated in project or patch.
echo [INFO] Set APP_URL=https://!HOST! in .env manually; never change APP_KEY.
echo [INFO] Public domain requires public 80/443 and DNS pointing to this machine.
echo [INFO] LAN name requires local DNS and trusted Caddy root CA on clients.
echo [INFO] Jalankan START_DOMAIN_HTTPS.bat setelah konfigurasi DNS, caddy and Laravel selesai.
pause
exit /b 0
:fail
echo [BLOCK] Input invalid or Caddyfile already exists. No file overwritten.
pause
exit /b 2
