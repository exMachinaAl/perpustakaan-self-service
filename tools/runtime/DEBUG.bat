@echo off
setlocal DisableDelayedExpansion
call "%~dp0_common.bat"
echo ================================================================
echo  DEBUG RUNTIME - lokal / teknisi saja
echo ================================================================
if not defined PHP_EXE (echo [ERROR] PHP tidak ditemukan.& pause& exit /b 2)
"%PHP_EXE%" -v
"%PHP_EXE%" --ini
echo.
echo [INFO] Tes port TCP lokal 8000:
netstat -ano -p TCP | findstr /C:":8000"
echo [INFO] Tes port TCP HTTPS 8443:
netstat -ano -p TCP | findstr /C:":8443"
echo [INFO] Diagnostik lengkap READ ONLY: CHECK_SYSTEM.bat
echo [INFO] Log Laravel: "%PROJECT_ROOT%\storage\logs"
echo [WARN] Jangan salin .env, sertifikat private key, rootCA-key, atau token ke chat.
echo [WARN] Debug ini tidak menampilkan secret dan tidak mengubah data.
pause
