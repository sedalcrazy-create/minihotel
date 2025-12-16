@echo off
chcp 65001 >nul
echo.
echo 🐳 Import کردن Docker Image...
echo.

REM Check if Docker is running
docker ps >nul 2>&1
if errorlevel 1 (
    echo ❌ خطا: Docker Desktop در حال اجرا نیست!
    echo.
    echo لطفاً Docker Desktop را راه‌اندازی کنید و دوباره امتحان کنید.
    pause
    exit /b 1
)

REM Find the tar file
set "TAR_FILE="
for %%f in (minihotel-image*.tar) do (
    set "TAR_FILE=%%f"
    goto :found
)

:found
if "%TAR_FILE%"=="" (
    echo ❌ خطا: فایل minihotel-image*.tar پیدا نشد!
    echo.
    echo لطفاً مطمئن شوید که فایل tar در همین پوشه است.
    pause
    exit /b 1
)

echo ✓ فایل پیدا شد: %TAR_FILE%
echo.
echo 📦 در حال Import کردن Image...
echo این کار ممکن است چند دقیقه طول بکشد...
echo.

docker load -i "%TAR_FILE%"

if errorlevel 1 (
    echo.
    echo ❌ خطا در Import!
    echo.
    echo راهنمای رفع مشکل:
    echo 1. مطمئن شوید Docker Desktop در حال اجرا است
    echo 2. فضای کافی روی دیسک دارید (حداقل 2 GB)
    echo 3. فایل tar سالم است و کامل کپی شده
    pause
    exit /b 1
)

echo.
echo ✅ موفق! Docker Image وارد شد.
echo.
echo 📋 مراحل بعدی:
echo 1. کپی .env: copy .env.example .env
echo 2. اجرا: docker-compose up -d
echo 3. راه‌اندازی: docker-compose exec app php artisan key:generate
echo 4. دیتابیس: docker-compose exec app php artisan migrate --seed
echo.
pause
