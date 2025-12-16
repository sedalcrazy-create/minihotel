@echo off
chcp 65001 >nul
echo.
echo 🚀 راه‌اندازی سریع MiniHotel
echo.

REM Check if Docker is running
docker ps >nul 2>&1
if errorlevel 1 (
    echo ❌ Docker Desktop در حال اجرا نیست!
    echo لطفاً ابتدا Docker Desktop را باز کنید.
    pause
    exit /b 1
)

echo ✓ Docker در حال اجرا است
echo.

REM Check if .env exists
if not exist ".env" (
    echo 1️⃣ ایجاد فایل .env...
    copy .env.example .env >nul
    echo ✓ فایل .env ایجاد شد
    echo.
)

REM Start containers
echo 2️⃣ راه‌اندازی Container ها...
docker-compose up -d

if errorlevel 1 (
    echo ❌ خطا در راه‌اندازی!
    pause
    exit /b 1
)

echo ✓ Container ها راه‌اندازی شدند
echo.

REM Wait for containers to be ready
echo ⏳ صبر کنید...
timeout /t 5 /nobreak >nul

REM Generate app key if not exists
findstr /C:"APP_KEY=" .env | findstr /C:"APP_KEY=$" >nul
if not errorlevel 1 (
    echo 3️⃣ ایجاد کلید برنامه...
    docker-compose exec -T app php artisan key:generate --ansi
    echo ✓ کلید ایجاد شد
    echo.
)

REM Check if database needs migration
echo 4️⃣ بررسی دیتابیس...
if not exist "database\database.sqlite" (
    echo   ایجاد دیتابیس و اجرای Migration ها...
    docker-compose exec -T app php artisan migrate --seed --force
    echo ✓ دیتابیس آماده شد
) else (
    echo ✓ دیتابیس موجود است
)

echo.
echo ✅ همه چیز آماده است!
echo.
echo 🌐 دسترسی به سیستم:
echo    http://localhost:8080
echo.
echo 👥 کاربران پیش‌فرض:
echo    مدیر: admin@bank.ir / password
echo    اپراتور: operator@bank.ir / password
echo.
echo 📋 دستورات مفید:
echo    • خاموش کردن: docker-compose down
echo    • مشاهده لاگ: docker-compose logs -f
echo    • ورود به Container: docker-compose exec app sh
echo.
pause
