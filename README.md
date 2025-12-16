# 🏨 Mini Hotel - سیستم مدیریت خوابگاه بانک ملی

سیستم مدیریت خوابگاه اداره آموزش بانک ملی - کاملاً Local و Dockerized

## 🚀 نصب و راه‌اندازی با Docker

### پیش‌نیاز
- Docker Desktop (Windows/Mac) یا Docker Engine (Linux)
- Git

### مراحل نصب

#### 1️⃣ Clone کردن پروژه
```bash
git clone git@github.com:sedalcrazy-create/minihotel.git
cd minihotel
```

#### 2️⃣ ساخت و اجرای Container
```bash
docker-compose up -d --build
```

#### 3️⃣ نصب Dependencies
```bash
docker-compose exec app composer install
```

#### 4️⃣ ایجاد App Key
```bash
docker-compose exec app php artisan key:generate
```

#### 5️⃣ اجرای Migration ها
```bash
docker-compose exec app php artisan migrate --seed
```

#### 6️⃣ دسترسی به سیستم
```
http://localhost:8080
```

---

## 🔧 دستورات مفید

### دیدن لاگ‌ها
```bash
docker-compose logs -f app
```

### ورود به Container
```bash
docker-compose exec app sh
```

### خاموش کردن
```bash
docker-compose down
```

### خاموش کردن + پاک کردن دیتا
```bash
docker-compose down -v
```

### اجرای دستورات Artisan
```bash
docker-compose exec app php artisan [command]
```

---

## 🖥️ استقرار روی سرور Linux

### 1️⃣ روی سرور
```bash
git clone git@github.com:sedalcrazy-create/minihotel.git
cd minihotel
cp .env.example .env
# ویرایش .env برای Production
```

### 2️⃣ ساخت و اجرا
```bash
docker-compose -f docker-compose.prod.yml up -d --build
```

### 3️⃣ Nginx Reverse Proxy
```nginx
server {
    listen 80;
    server_name yourdomain.com;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

---

## 📊 ساختار پروژه

```
minihotel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Models/
│   └── Providers/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── database.sqlite
├── docker/
│   ├── nginx/
│   ├── php/
│   └── supervisor/
├── public/
├── resources/
│   └── views/
├── routes/
├── storage/
├── Dockerfile
└── docker-compose.yml
```

---

## 👥 کاربران پیش‌فرض

| نقش | ایمیل | رمز عبور |
|-----|-------|---------|
| مدیر سیستم | admin@bank.ir | password |
| اپراتور | operator@bank.ir | password |
| مدیر خوابگاه | manager@bank.ir | password |

---

## 📝 ویژگی‌ها

✅ مدیریت 132 تخت در 22 واحد
✅ سه نوع پذیرش (دوره کلاسی، همایش، ماموریت اداری)
✅ مدیریت وعده‌های غذایی
✅ مدیریت نظافت و تعمیرات
✅ داشبورد شماتیک تصویری
✅ گزارش‌های Excel
✅ Import پرسنل از Excel
✅ احراز هویت و سطح دسترسی
✅ رابط کاربری فارسی
✅ تاریخ شمسی

---

## 🔒 امنیت

- تمام رمزهای عبور Hash شده با bcrypt
- CSRF Protection
- XSS Prevention
- SQL Injection Prevention با Eloquent ORM
- Activity Logging
- Session Management

---

## 📦 تکنولوژی

- **Backend:** Laravel 11, PHP 8.2
- **Database:** SQLite
- **Frontend:** Blade Templates, Bootstrap 5 RTL
- **Date:** Jalali (Persian)
- **Excel:** Maatwebsite Excel
- **PDF:** DomPDF
- **Permissions:** Spatie Permission
- **Docker:** Alpine Linux, Nginx, PHP-FPM, Supervisor

---

## 🐛 رفع مشکلات

### Permission Errors
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Database Locked
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

### Container نمی‌سازد
```bash
docker-compose down
docker system prune -a
docker-compose up -d --build
```

---

## 📞 پشتیبانی

برای گزارش مشکلات از GitHub Issues استفاده کنید.

---

**توسعه داده شده برای اداره آموزش بانک ملی ایران** 🇮🇷
