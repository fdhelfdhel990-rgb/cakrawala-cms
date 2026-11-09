@echo off
title EPerpus Cakrawala - Laravel Dev Server
echo ==================================================
echo Menjalankan Server Laravel EPerpus Cakrawala...
echo PHP Version: 8.5 (Project Runtime)
echo Akses Web: http://127.0.0.1:8000
echo ==================================================
"%~dp0tools\php\php.exe" artisan serve --host=127.0.0.1 --port=8000
pause
