@echo off
title Menjalankan Aplikasi Buku Tamu UTS
echo =======================================================
echo    UTS TEKNOLOGI PLATFORM - PETRUS ADMO SAMON KW (25110027)
echo                 Aplikasi Buku Tamu Digital
echo =======================================================
echo.

:: 1. Cek apakah Docker daemon aktif
docker info >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [DOCKER TERDETEKSI] Menjalankan via Docker Compose...
    docker compose up -d
    echo.
    echo Aplikasi siap diakses di: http://localhost:8000
    start http://localhost:8000
    goto end
)

:: 2. Jika Docker belum aktif/WSL belum siap, jalankan via PHP Built-in Server + MySQL XAMPP
echo [DOCKER BELUM AKTIF / STANDALONE MODE]
echo Menjalankan aplikasi secara native dengan PHP dan Database MySQL...
echo.

:: Pastikan MySQL berjalan
tasklist /fi "imagename eq mysqld.exe" 2>NUL | find /i /n "mysqld.exe">NUL
if "%ERRORLEVEL%"=="1" (
    echo Menyalakan layanan MySQL XAMPP...
    if exist "C:\xampp\mysql_start.bat" (
        start "" /min "cmd.exe" /c "C:\xampp\mysql_start.bat"
        timeout /t 3 /nobreak >nul
    )
)

echo Membuka server aplikasi di port 8000...
start http://localhost:8000
php -S 0.0.0.0:8000 -t app/

:end
pause
