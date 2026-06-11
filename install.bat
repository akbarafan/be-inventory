@echo off
echo ============================================
echo  Instalasi Sistem Inventaris SMK Labschool
echo ============================================
echo.

REM Step 1: Enable GD extension in XAMPP php.ini
echo [1/6] Mengaktifkan ext-gd di PHP...
set PHP_INI=C:\xampp\php\php.ini
if exist "%PHP_INI%" (
    powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=gd', 'extension=gd' | Set-Content '%PHP_INI%'"
    echo     ext-gd berhasil diaktifkan
) else (
    echo     XAMPP tidak ditemukan di C:\xampp - aktifkan ext-gd manual di php.ini
)
echo.

REM Step 2: Install dependencies
echo [2/6] Menginstall dependencies (composer update)...
call composer update --ignore-platform-req=ext-gd --no-interaction
echo.

REM Step 3: Setup .env
echo [3/6] Menyiapkan file .env...
if not exist ".env" (
    copy .env.example .env
    echo     File .env dibuat
) else (
    echo     File .env sudah ada
)
echo.

REM Step 4: Generate app key
echo [4/6] Generate application key...
call php artisan key:generate
echo.

REM Step 5: Database setup
echo [5/6] Setup database...
echo.
echo     Pastikan MySQL XAMPP sudah berjalan!
echo     Buka phpMyAdmin dan buat database: inventory_smk
echo     Kemudian tekan Enter untuk melanjutkan...
pause > nul
call php artisan migrate --seed
echo.

REM Step 6: Storage link
echo [6/6] Storage link...
call php artisan storage:link
echo.

echo ============================================
echo  INSTALASI SELESAI!
echo ============================================
echo.
echo  Jalankan: php artisan serve
echo  Buka browser: http://localhost:8000
echo.
echo  Login default:
echo    Admin   - username: admin    / password: admin123
echo    User    - username: petugas  / password: user123
echo.
pause
