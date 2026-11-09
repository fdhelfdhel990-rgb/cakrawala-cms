@echo off
title Laravel Server Cakrawala (Host 0.0.0.0)
cd /d "%~dp0"
echo ======================================================================
echo   Menjalankan Laravel Server Cakrawala
echo   - Emulator Android : http://10.0.2.2:8000
echo   - Browser Laptop   : http://localhost:8000 atau http://127.0.0.1:8000
echo   - HP Android Wi-Fi : http://192.168.0.108:8000
echo ======================================================================
php artisan serve --host=0.0.0.0 --port=8000
pause
