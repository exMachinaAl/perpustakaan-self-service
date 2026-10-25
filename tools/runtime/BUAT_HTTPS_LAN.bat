@echo off
setlocal DisableDelayedExpansion
call "%~dp0_common.bat"
echo ================================================================
echo  HTTPS/TLS LAN - mkcert + Caddy (LAB, bukan produksi publik)
echo ================================================================
if not defined MKCERT_EXE (echo [ERROR] mkcert.exe tidak ditemukan.& echo Simpan di "%TLS_DIR%\bin\mkcert.exe" atau atur PERPUS_MKCERT_EXE.& pause& exit /b 2)
if not defined CADDY_EXE (echo [ERROR] caddy.exe tidak ditemukan.& echo Simpan di "%TLS_DIR%\bin\caddy.exe" atau atur PERPUS_CADDY_EXE.& pause& exit /b 2)
if not defined PHP_EXE (echo [ERROR] PHP tidak ditemukan; diperlukan untuk memvalidasi IPv4.& pause& exit /b 2)
echo Ketik IPv4 LAN server, lihat ipconfig. Contoh 192.168.1.20
set /p "LAN_IP=IPv4 server: "
if not defined LAN_IP (echo [ERROR] IPv4 kosong.& exit /b 2)
"%PHP_EXE%" -r "exit(filter_var($argv[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? 0 : 1);" "%LAN_IP%" >nul 2>&1
if errorlevel 1 (echo [ERROR] IPv4 tidak valid.& pause& exit /b 2)
set "CAROOT=%TLS_DIR%\ca"
set "CERT_DIR=%TLS_DIR%\certs\%LAN_IP%"
if not exist "%CAROOT%" mkdir "%CAROOT%" || exit /b 2
if not exist "%CERT_DIR%" mkdir "%CERT_DIR%" || exit /b 2
if exist "%CERT_DIR%\perpus.pem" if not exist "%CERT_DIR%\perpus-key.pem" (echo [ERROR] Key private hilang untuk cert yang ada.& pause& exit /b 2)
if exist "%CERT_DIR%\perpus-key.pem" if not exist "%CERT_DIR%\perpus.pem" (echo [ERROR] Cert hilang untuk key yang ada.& pause& exit /b 2)
if exist "%CERT_DIR%\perpus.pem" if exist "%CERT_DIR%\perpus-key.pem" (
  echo [INFO] Sertifikat untuk IP ini sudah ada; tidak ditimpa.
) else (
  echo [ERROR] Sertifikat hanya sebagian tersedia; periksa manual. Tidak menimpa otomatis.
  pause
  exit /b 2
)
if not exist "%CERT_DIR%\perpus.pem" (
  echo [INFO] Membuat CA di luar proyek dan OneDrive, lalu memasang trust ke Windows...
  "%MKCERT_EXE%" -install
  if errorlevel 1 (echo [ERROR] mkcert -install gagal. & pause& exit /b 2)
  "%MKCERT_EXE%" -cert-file "%CERT_DIR%\perpus.pem" -key-file "%CERT_DIR%\perpus-key.pem" "%LAN_IP%" localhost 127.0.0.1
  if errorlevel 1 (echo [ERROR] Gagal membuat sertifikat.& pause& exit /b 2)
)
set "CERT_DIR_CADDY=%CERT_DIR:\=/%"
rem Prefer explicit existing config preservation.
if exist "%TLS_DIR%\Caddyfile" (
  echo [WARN] Caddyfile lama ada dan TIDAK akan ditimpa.
  echo Periksa apakah IP atau sertifikat berubah; edit secara manual jika perlu.
  findstr /i /c:"https://%LAN_IP%:8443" "%TLS_DIR%\Caddyfile" >nul
  if errorlevel 1 (echo [ERROR] Caddyfile memakai alamat lain; berhenti agar tidak salah mengarahkan pairing HP.& echo Cadangkan Caddyfile lalu perbarui alamat dan TLS path secara manual.& pause& exit /b 2)
) else (
  >"%TLS_DIR%\Caddyfile" echo https://%LAN_IP%:8443 {
  >>"%TLS_DIR%\Caddyfile" echo     tls "%CERT_DIR_CADDY%/perpus.pem" "%CERT_DIR_CADDY%/perpus-key.pem"
  >>"%TLS_DIR%\Caddyfile" echo     reverse_proxy 127.0.0.1:8000
  >>"%TLS_DIR%\Caddyfile" echo }
)
echo [INFO] Memvalidasi Caddyfile...
"%CADDY_EXE%" validate --config "%TLS_DIR%\Caddyfile" --adapter caddyfile
if errorlevel 1 (echo [ERROR] Config Caddy salah. Jangan lanjut START_HTTPS.& pause& exit /b 2)
echo [OK] Sertifikat/config tersedia di "%TLS_DIR%" (bukan proyek).
echo.
echo [PENTING] Isi .env SENDIRI: APP_URL=https://%LAN_IP%:8443
echo [PENTING] KIOSK_PHONE_BASE_URL=https://%LAN_IP%:8443
echo [PENTING] SESSION_SECURE_COOKIE=true, lalu php artisan config:clear.
echo [PENTING] CA Windows TIDAK otomatis dipercaya di HP.
echo Root CA publik untuk perangkat lab: "%CAROOT%\rootCA.pem"
echo JANGAN BAGIKAN rootCA-key.pem atau perpus-key.pem.
echo Lanjutkan dengan START.bat.
pause
exit /b 0
