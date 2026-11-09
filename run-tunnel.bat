@echo off
title Cloudflare Tunnel - Cakrawala Digital Library
cd /d "%~dp0"

echo ======================================================================
echo   CLOUDFLARE TUNNEL - CAKRAWALA DIGITAL LIBRARY
echo ======================================================================
echo   Membuka jalur tunnel publik HTTPS untuk http://127.0.0.1:8000
echo.
echo   [PENTING]
echo   1. Pastikan server Laravel sudah aktif (jalankan run-server.bat)
echo   2. Tunggu beberapa detik sampai Cloudflare menampilkan link:
echo      -------------------------------------------------------
echo      ^|  https://xxxxxxxx.trycloudflare.com                  ^|
echo      -------------------------------------------------------
echo   3. Salin link tersebut:
echo      - Buka di browser: https://xxxxxxxx.trycloudflare.com/admin
echo      - Masukkan ke aplikasi Android (Profil -> Tentang Aplikasi -> Ubah Server)
echo.
echo   Tekan Ctrl + C jika ingin menghentikan tunnel.
echo ======================================================================
echo.

tools\cloudflared.exe tunnel --url http://127.0.0.1:8000
pause
