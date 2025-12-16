# راهنمای نصب و راه‌اندازی سیستم مدیریت خوابگاه بانک ملی

## پیش‌نیازها

### ویندوز 10 یا بالاتر
- Docker Desktop for Windows
- WSL2 (Windows Subsystem for Linux)
- حداقل 4GB RAM آزاد
- حداقل 10GB فضای دیسک

## نصب Docker روی ویندوز

### مرحله 1: فعال‌سازی WSL2

1. PowerShell را با دسترسی Administrator باز کنید
2. دستورات زیر را اجرا کنید:

```powershell
dism.exe /online /enable-feature /featurename:Microsoft-Windows-Subsystem-Linux /all /norestart
dism.exe /online /enable-feature /featurename:VirtualMachinePlatform /all /norestart
```

3. سیستم را restart کنید
4. WSL2 را به عنوان نسخه پیش‌فرض تنظیم کنید:

```powershell
wsl --set-default-version 2
```

### مرحله 2: نصب Docker Desktop

1. از لینک زیر Docker Desktop را دانلود کنید:
   https://desktop.docker.com/win/main/amd64/Docker%20Desktop%20Installer.exe

2. فایل نصب را اجرا کرده و تنظیمات پیش‌فرض را قبول کنید
3. پس از نصب، Docker Desktop را اجرا کنید
4. در تنظیمات Docker، WSL2 را فعال کنید

## دیپلوی آنلاین (با اینترنت)

### روش 1: Clone از Git

```bash
git clone <repository-url>
cd hotel
```

### روش 2: Build و Run

```bash
# Build کردن image
docker-compose build

# اجرای کانتینر
docker-compose up -d

# مشاهده لاگ‌ها
docker-compose logs -f
```

### دسترسی به سیستم

سیستم روی آدرس زیر در دسترس است:
- URL: http://localhost:8081
- Username: admin
- Password: admin123

## دیپلوی آفلاین (بدون اینترنت)

### مرحله 1: آماده‌سازی روی سیستم با اینترنت

```bash
cd /path/to/hotel

# Build کردن image
docker-compose build

# Export کردن image
docker save -o bankMelli-dormitory.tar hotel-app:latest

# فشرده‌سازی برای انتقال راحت‌تر
tar -czvf bankMelli-dormitory-full.tar.gz bankMelli-dormitory.tar database/ .env
```

### مرحله 2: انتقال به سیستم آفلاین

فایل `bankMelli-dormitory-full.tar.gz` را به سیستم هدف منتقل کنید (با فلش USB یا روش دیگر)

### مرحله 3: نصب روی سیستم آفلاین

```bash
# استخراج فایل‌ها
tar -xzvf bankMelli-dormitory-full.tar.gz

# Load کردن image در Docker
docker load -i bankMelli-dormitory.tar

# اجرای کانتینر
docker run -d \
  --name bankMelli-dormitory-app \
  -p 8081:80 \
  -v $(pwd)/database:/var/www/html/database \
  -v $(pwd)/.env:/var/www/html/.env \
  hotel-app:latest
```

## مدیریت کانتینر

### مشاهده وضعیت

```bash
# لیست کانتینرهای در حال اجرا
docker ps

# مشاهده لاگ‌ها
docker logs bankMelli-dormitory-app

# مشاهده لاگ‌های real-time
docker logs -f bankMelli-dormitory-app
```

### توقف و راه‌اندازی مجدد

```bash
# توقف کانتینر
docker stop bankMelli-dormitory-app

# راه‌اندازی مجدد
docker start bankMelli-dormitory-app

# Restart
docker restart bankMelli-dormitory-app
```

### حذف کانتینر

```bash
# توقف و حذف
docker stop bankMelli-dormitory-app
docker rm bankMelli-dormitory-app

# حذف image
docker rmi hotel-app:latest
```

## پشتیبان‌گیری

### پشتیبان از دیتابیس

```bash
# کپی فایل دیتابیس
docker cp bankMelli-dormitory-app:/var/www/html/database/database.sqlite ./backup/database-$(date +%Y%m%d).sqlite
```

یا اگر از volume استفاده می‌کنید:

```bash
cp database/database.sqlite backup/database-$(date +%Y%m%d).sqlite
```

### بازیابی از پشتیبان

```bash
# توقف کانتینر
docker stop bankMelli-dormitory-app

# بازگرداندن دیتابیس
cp backup/database-20231216.sqlite database/database.sqlite

# اجازه نوشتن
chmod 666 database/database.sqlite
chmod 777 database

# راه‌اندازی مجدد
docker start bankMelli-dormitory-app
```

## به‌روزرسانی سیستم

### روش 1: Build مجدد

```bash
# دریافت آخرین تغییرات (اگر از git استفاده می‌کنید)
git pull

# توقف کانتینر فعلی
docker-compose down

# Build و اجرای نسخه جدید
docker-compose build --no-cache
docker-compose up -d
```

### روش 2: به‌روزرسانی آفلاین

```bash
# Load کردن image جدید
docker load -i bankMelli-dormitory-new.tar

# توقف و حذف کانتینر قدیمی
docker stop bankMelli-dormitory-app
docker rm bankMelli-dormitory-app

# اجرای کانتینر جدید
docker-compose up -d
```

## عیب‌یابی

### کانتینر شروع نمی‌شود

```bash
# مشاهده لاگ‌ها
docker logs bankMelli-dormitory-app

# بررسی وضعیت
docker inspect bankMelli-dormitory-app
```

### خطای پورت در حال استفاده

```bash
# پیدا کردن پروسسی که از پورت 8081 استفاده می‌کند
netstat -ano | findstr :8081

# تغییر پورت در docker-compose.yml
ports:
  - "8082:80"  # به جای 8081
```

### خطای دسترسی به دیتابیس

```bash
# ورود به کانتینر
docker exec -it bankMelli-dormitory-app sh

# بررسی مجوزها
ls -la /var/www/html/database/

# تنظیم مجوزها
chmod 666 /var/www/html/database/database.sqlite
chmod 777 /var/www/html/database/
```

### پاک کردن فایل‌های log

```bash
# ورود به کانتینر
docker exec -it bankMelli-dormitory-app sh

# پاک کردن logs
cd /var/www/html/storage/logs
rm -f *.log

# خروج
exit
```

## اطلاعات کاربران پیش‌فرض

سیستم با 5 کاربر پیش‌فرض نصب می‌شود:

| نام کاربری | رمز عبور | نقش |
|------------|----------|-----|
| admin | admin123 | مدیر سیستم |
| operator | operator123 | اپراتور |
| manager | manager123 | مدیر |
| cleaning | cleaning123 | نظافت |
| maintenance | maintenance123 | تعمیرات |

**⚠️ توجه:** پس از نصب حتماً رمز عبور کاربر admin را تغییر دهید!

## مشخصات سیستم

- **زبان برنامه‌نویسی:** PHP 8.2
- **فریمورک:** Laravel 11
- **دیتابیس:** SQLite
- **وب سرور:** Nginx
- **تعداد تخت:** 132 عدد
- **تعداد واحد:** 22 واحد
- **ظرفیت هر اتاق:** 6 تخت

## پشتیبانی

در صورت بروز مشکل، لطفاً موارد زیر را بررسی کنید:

1. Docker Desktop در حال اجرا باشد
2. WSL2 فعال باشد
3. پورت 8081 توسط برنامه دیگری استفاده نشود
4. حداقل 4GB RAM آزاد داشته باشید
5. لاگ‌های Docker را بررسی کنید

برای مشاهده لاگ‌های دقیق:

```bash
# لاگ‌های Laravel
docker exec -it bankMelli-dormitory-app cat storage/logs/laravel.log

# لاگ‌های Nginx
docker exec -it bankMelli-dormitory-app cat /var/log/nginx/access.log
docker exec -it bankMelli-dormitory-app cat /var/log/nginx/error.log
```

---

**نسخه سند:** 1.0
**تاریخ:** 1403/09/26
**سازمان:** اداره کل آموزش بانک ملی ایران
