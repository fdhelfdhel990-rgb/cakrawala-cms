@echo off
title Setup Global PHP 8.5 untuk Seluruh Sistem
echo ========================================================
echo   Pemasangan Global PHP 8.5 untuk Seluruh Proyek di PC
echo ========================================================
echo.
echo 1. Membuat folder global D:\php ...
if not exist "D:\php" mkdir "D:\php"

echo 2. Menyalin berkas runtime PHP 8.5 lengkap ke D:\php ...
xcopy /E /I /Y "%~dp0tools\php\*" "D:\php\" >nul

echo 3. Mendaftarkan D:\php ke Environment Variable PATH Windows...
powershell -NoProfile -Command "$curr = [System.Environment]::GetEnvironmentVariable('Path', 'User') -replace 'D:\\xampp\\php;?', '' -replace 'D:\\php;?', ''; [System.Environment]::SetEnvironmentVariable('Path', 'D:\php;' + $curr, 'User')"

echo.
echo ========================================================
echo [SUKSES] PHP 8.5 berhasil terpasang secara GLOBAL di:
echo          D:\php
echo.
echo Silakan tutup semua terminal / VS Code Anda, lalu buka
echo kembali. Sekarang di folder proyek MANAPUN, Anda bisa
echo langsung mengetik:
echo   php -v
echo   php artisan serve
echo ========================================================
echo.
pause
