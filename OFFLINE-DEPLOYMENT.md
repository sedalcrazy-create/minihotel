# 🔒 راهنمای نصب آفلاین (بدون اینترنت) - Windows 10

این راهنما برای نصب پروژه روی سیستم **Windows 10 بدون اینترنت** است.

---

## 📦 مرحله 1: آماده‌سازی روی سیستم با اینترنت

### 1.1 نصب Docker Desktop
اگر هنوز نصب نکرده‌اید:
1. دانلود: https://www.docker.com/products/docker-desktop/
2. نصب Docker Desktop
3. راه‌اندازی Docker

### 1.2 Clone پروژه
```bash
git clone git@github.com:sedalcrazy-create/minihotel.git
cd minihotel
```

### 1.3 ساخت Docker Image با همه چیز
```bash
docker-compose build
```
این کار تمام کتابخانه‌ها را دانلود و در Image قرار می‌دهد.

### 1.4 Export کردن Docker Image
```bash
# روی Windows PowerShell یا Git Bash
docker save -o minihotel-image.tar minihotel-app:latest
```

یا با اسکریپت آماده:
```bash
./scripts/export-image.sh
```

### 1.5 فایل‌های مورد نیاز برای انتقال
پوشه‌ها و فایل‌های زیر را روی **فلش مموری** یا **هارد اکسترنال** کپی کنید:

```
📦 فایل‌های مورد نیاز:
├── minihotel-image.tar         (حدود 500-800 MB)
├── docker-compose.yml
├── .env.example
├── database/
│   └── .gitkeep
└── scripts/
    └── import-image.bat
```

---

## 💾 مرحله 2: انتقال به سیستم آفلاین

1. فلش یا هارد اکسترنال را به سیستم **Windows 10 آفلاین** وصل کنید
2. فایل‌ها را در پوشه دلخواه (مثلاً `C:\minihotel`) کپی کنید

---

## 🖥️ مرحله 3: نصب روی Windows 10 آفلاین

### 3.1 نصب Docker Desktop (آفلاین)

**گزینه A: نصب از فایل آفلاین**
1. Docker Desktop Installer را از سیستم با اینترنت دانلود کنید:
   - دانلود: `Docker Desktop Installer.exe` (حدود 500 MB)
2. فایل را روی فلش کپی کنید
3. روی سیستم آفلاین نصب کنید

**نکته:** Docker Desktop برای Windows 10 نیاز به **WSL 2** دارد.

**نصب WSL 2 آفلاین:**
```powershell
# روی سیستم با اینترنت:
1. دانلود WSL2 Kernel Update
   لینک: https://aka.ms/wsl2kernel
2. کپی فایل wsl_update_x64.msi روی فلش
3. نصب روی سیستم آفلاین
```

### 3.2 Import کردن Docker Image

روی سیستم آفلاین، **PowerShell به عنوان Administrator** باز کنید:

```powershell
cd C:\minihotel

# Import کردن Image
docker load -i minihotel-image.tar
```

یا با اسکریپت:
```powershell
.\scripts\import-image.bat
```

### 3.3 ایجاد فایل .env
```powershell
copy .env.example .env
```

### 3.4 اجرای پروژه
```powershell
docker-compose up -d
```

### 3.5 راه‌اندازی اولیه
```powershell
# ایجاد کلید برنامه
docker-compose exec app php artisan key:generate

# اجرای Migration و Seeder
docker-compose exec app php artisan migrate --seed
```

### 3.6 دسترسی به سیستم
مرورگر را باز کنید و به آدرس زیر بروید:
```
http://localhost:8080
```

---

## 🔄 بروزرسانی آفلاین

اگر پروژه بروزرسانی شد:

### روی سیستم با اینترنت:
```bash
git pull
docker-compose build
docker save -o minihotel-image-v2.tar minihotel-app:latest
```

### روی سیستم آفلاین:
```powershell
# حذف Image قدیمی
docker-compose down
docker rmi minihotel-app:latest

# Import Image جدید
docker load -i minihotel-image-v2.tar

# اجرای مجدد
docker-compose up -d

# بروزرسانی دیتابیس
docker-compose exec app php artisan migrate
```

---

## 📊 حجم فایل‌ها

| فایل | حجم تقریبی |
|------|-----------|
| `minihotel-image.tar` | 500-800 MB |
| `Docker Desktop Installer` | 500 MB |
| `WSL2 Kernel Update` | 20 MB |
| **جمع کل** | **حدود 1-1.5 GB** |

---

## ✅ چک‌لیست نصب آفلاین

### روی سیستم با اینترنت:
- [ ] نصب Docker Desktop
- [ ] Clone پروژه
- [ ] Build Image (`docker-compose build`)
- [ ] Export Image (`docker save`)
- [ ] دانلود Docker Desktop Installer
- [ ] دانلود WSL2 Kernel Update
- [ ] کپی همه فایل‌ها روی فلش/هارد

### روی سیستم آفلاین (Windows 10):
- [ ] نصب WSL2 Kernel Update
- [ ] نصب Docker Desktop
- [ ] کپی فایل‌ها در پوشه `C:\minihotel`
- [ ] Import Image (`docker load`)
- [ ] کپی `.env.example` به `.env`
- [ ] اجرا (`docker-compose up -d`)
- [ ] Key Generate
- [ ] Migrate و Seed
- [ ] تست در مرورگر

---

## 🐛 رفع مشکلات

### Docker Desktop اجرا نمی‌شود
```
خطا: WSL 2 installation is incomplete
راه‌حل: نصب wsl_update_x64.msi
```

### Image Load نمی‌شود
```
خطا: Error response from daemon
راه‌حل:
1. بررسی سالم بودن فایل .tar
2. فضای کافی روی دیسک (حداقل 2 GB)
3. Docker Desktop در حال اجرا باشد
```

### Container Start نمی‌شود
```powershell
# بررسی لاگ‌ها
docker-compose logs

# Restart
docker-compose down
docker-compose up -d
```

---

## 📱 تماس و پشتیبانی

اگر مشکلی بود:
1. لاگ‌ها را Export کنید:
   ```powershell
   docker-compose logs > logs.txt
   ```
2. فایل `logs.txt` را ارسال کنید

---

## 🎯 نکات مهم

✅ **همه کتابخانه‌ها در Image است** - نیازی به اینترنت نیست
✅ **SQLite داخلی است** - نیازی به دیتابیس جداگانه نیست
✅ **تمام Dependencies نصب شده** - فقط Import و Run
✅ **Portable است** - روی هر سیستم Windows 10 کار می‌کند

---

**آماده برای استفاده آفلاین! 🚀**
