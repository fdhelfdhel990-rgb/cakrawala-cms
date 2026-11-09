@echo off
title Launch Online - Cakrawala Digital Library
cd /d "%~dp0"

echo ======================================================================
echo   MEMULAI SERVER LOKAL DAN CLOUDFLARE TUNNEL (1-CLICK RUN)
echo ======================================================================
echo.
echo 1. Menjalankan Laravel Server (Port 8000)...
start "Laravel Server (0.0.0.0:8000)" cmd /k "run-server.bat"

echo.
echo 2. Menunggu server Laravel siap (3 detik)...
timeout /t 3 /nobreak >nul

echo.
echo 3. Menjalankan Cloudflare Tunnel publik HTTPS...
start "Cloudflare Tunnel" cmd /k "run-tunnel.bat"

echo.
echo ======================================================================
echo   [SUKSES] Dua jendela telah dibuka:
echo   1. "Laravel Server"     : Menjalankan backend PHP & MySQL
echo   2. "Cloudflare Tunnel"  : Menghasilkan link HTTPS publik
echo.
echo   Lihat link 'https://...trycloudflare.com' di jendela Cloudflare Tunnel
echo   dan bagikan kepada dosen atau penguji!
echo ======================================================================
echo.
pause
