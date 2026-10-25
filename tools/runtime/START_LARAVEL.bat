@echo off
setlocal DisableDelayedExpansion
call "%~dp0_common.bat"
if not defined PHP_EXE (echo [ERROR] PHP CLI tidak ditemukan.& pause& exit /b 2)
if not exist "%PROJECT_ROOT%\.env" (echo [ERROR] .env belum ada.& pause& exit /b 2)
if not exist "%PROJECT_ROOT%\vendor\autoload.php" (echo [ERROR] vendor belum ada; jalankan composer install sesuai README.& pause& exit /b 2)
echo [INFO] Laravel hanya listening di 127.0.0.1:8000.
echo [INFO] Jendela ini harus tetap terbuka. Ctrl+C untuk berhenti.
cd /d "%PROJECT_ROOT%" || exit /b 2
"%PHP_EXE%" artisan serve --host=127.0.0.1 --port=8000
echo [INFO] Laravel berhenti (atau gagal). Periksa pesan di atas.
pause
