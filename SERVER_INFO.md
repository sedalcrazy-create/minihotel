# 📋 اطلاعات سرور و نحوه اتصال

## 🖥️ مشخصات سرور

- **آدرس IP:** `37.152.174.87`
- **سیستم عامل:** Ubuntu 22.04.5 LTS
- **دسترسی SSH:** root
- **رمز عبور:** `UJIr3a9UyH#b`

---

## 🔐 نحوه اتصال SSH

### Windows (Git Bash / PowerShell)
```bash
ssh root@37.152.174.87
# سپس رمز عبور را وارد کنید: UJIr3a9UyH#b
```

### استفاده از sshpass (اتوماتیک)
```bash
sshpass -p 'UJIr3a9UyH#b' ssh -o StrictHostKeyChecking=no root@37.152.174.87
```

---

## 🌐 دامنه‌ها و URL ها

### پروژه Hotel
- **URL عمومی:** https://hotel.darmanjoo.ir
- **پورت داخلی:** 8082
- **مسیر سرور:** `/var/www/hotel`
- **کانتینر Docker:** `hotel-app`

### سایر پروژه‌ها روی سرور
- **miniapp:** https://miniapp.darmanjoo.ir (پورت 8081)
- **n8n:** https://n8n.darmanjoo.ir (پورت 5678)
- **ria:** https://ria.jafamhis.ir (پورت 8083)

---

## 🐳 مدیریت Docker

### دستورات اصلی پروژه Hotel

```bash
# ورود به سرور
ssh root@37.152.174.87

# رفتن به دایرکتوری پروژه
cd /var/www/hotel

# مشاهده وضعیت کانتینرها
docker ps

# مشاهده لاگ‌ها
docker logs hotel-app -f

# ری‌استارت کانتینر
docker restart hotel-app

# وارد شدن به کانتینر
docker exec -it hotel-app sh

# اجرای دستورات Laravel
docker exec hotel-app php artisan cache:clear
docker exec hotel-app php artisan config:clear
docker exec hotel-app php artisan route:clear
docker exec hotel-app php artisan view:clear
```

### Build و Deploy مجدد

```bash
cd /var/www/hotel

# متوقف کردن کانتینرها
docker compose down

# Build و اجرای مجدد
docker compose up -d --build

# مشاهده لاگ‌ها
docker compose logs -f
```

---

## 🔧 Portainer (مدیریت Docker با رابط گرافیکی)

- **URL:** https://37.152.174.87:9443
- **دسترسی:** از طریق IP مستقیم سرور

---

## 📁 ساختار پروژه روی سرور

```
/var/www/hotel/
├── app/                      # کد اپلیکیشن Laravel
├── database/
│   └── database.sqlite       # دیتابیس SQLite
├── docker/
│   ├── nginx/               # تنظیمات Nginx
│   └── php/                 # تنظیمات PHP-FPM
├── docker-compose.yml       # تنظیمات Docker Compose
├── .env                     # تنظیمات محیط (production)
└── storage/
    └── logs/                # لاگ‌های Laravel
```

---

## 🌐 تنظیمات Nginx

### فایل کانفیگ Nginx برای hotel.darmanjoo.ir

**مسیر:** `/etc/nginx/sites-available/hotel.darmanjoo.ir`

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name hotel.darmanjoo.ir;

    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Content-Security-Policy "frame-ancestors 'self' https://*.bale.ai" always;

    location / {
        proxy_pass http://localhost:8082;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
    }
}
```

### فعال‌سازی کانفیگ

```bash
# ساخت لینک symbolic
ln -s /etc/nginx/sites-available/hotel.darmanjoo.ir /etc/nginx/sites-enabled/

# تست کانفیگ
nginx -t

# ری‌استارت Nginx
systemctl restart nginx
```

---

## 🔒 CDN و SSL

- **CDN:** Parspack
- **IP CDN:** 185.208.173.3
- **SSL:** توسط CDN مدیریت می‌شود
- **تنظیمات Nginx:** فقط HTTP (پورت 80) - CDN به HTTPS تبدیل می‌کند

**نکته:** تنظیمات دقیقاً مشابه `miniapp.darmanjoo.ir` است

---

## 💾 دیتابیس

### اطلاعات دیتابیس Hotel
- **نوع:** SQLite
- **مسیر:** `/var/www/html/database/database.sqlite`
- **دسترسی:** از طریق کانتینر Docker

### دسترسی به دیتابیس

```bash
# ورود به دیتابیس از داخل کانتینر
docker exec -it hotel-app sqlite3 /var/www/html/database/database.sqlite

# مشاهده جداول
.tables

# کوئری نمونه
SELECT * FROM users;

# خروج
.exit
```

### بکاپ دیتابیس

```bash
# بکاپ گرفتن
docker exec hotel-app cp /var/www/html/database/database.sqlite /var/www/html/database/backup_$(date +%Y%m%d_%H%M%S).sqlite

# کپی به سیستم محلی
scp root@37.152.174.87:/var/www/hotel/database/database.sqlite ./hotel_backup.sqlite
```

---

## 👤 اطلاعات کاربران پیش‌فرض

| ایمیل | رمز عبور | نقش |
|------|---------|-----|
| admin@bank.ir | password | مدیر سیستم |
| operator@bank.ir | password | اپراتور |
| manager@bank.ir | password | مدیر خوابگاه |
| cleaning@bank.ir | password | نظافت |
| maintenance@bank.ir | password | تعمیرات |

---

## 📝 لاگ‌ها

### مشاهده لاگ‌های Laravel

```bash
# لاگ Laravel
docker exec hotel-app tail -f /var/www/html/storage/logs/laravel.log

# لاگ Nginx
docker logs hotel-app 2>&1 | grep nginx

# لاگ PHP-FPM
docker exec hotel-app tail -f /var/log/php_errors.log
```

### لاگ‌های Nginx روی سرور

```bash
# Access log
tail -f /var/log/nginx/access.log | grep hotel

# Error log
tail -f /var/log/nginx/error.log | grep hotel
```

---

## 🚀 دستورات متداول

### آپلود فایل‌ها به سرور

```bash
# آپلود یک فایل
scp file.txt root@37.152.174.87:/var/www/hotel/

# آپلود دایرکتوری
scp -r ./folder root@37.152.174.87:/var/www/hotel/

# آپلود با استثنا کردن node_modules و vendor
rsync -avz --exclude 'node_modules' --exclude 'vendor' --exclude '.git' \
  ./ root@37.152.174.87:/var/www/hotel/
```

### تنظیم مجوزها

```bash
# دسترسی storage و cache
docker exec hotel-app chown -R www-data:www-data /var/www/html/storage
docker exec hotel-app chown -R www-data:www-data /var/www/html/bootstrap/cache
docker exec hotel-app chmod -R 775 /var/www/html/storage
docker exec hotel-app chmod -R 775 /var/www/html/bootstrap/cache
```

### بروزرسانی Composer Dependencies

```bash
docker exec hotel-app composer install --no-dev --optimize-autoloader
```

---

## ⚠️ نکات مهم

1. **CDN فعال است:** تمام درخواست‌ها از طریق Parspack CDN عبور می‌کنند
2. **TRUSTED_PROXIES:** در `.env` روی `*` تنظیم شده است
3. **APP_ENV:** در production روی `production` تنظیم شود
4. **APP_DEBUG:** در production باید `false` باشد
5. **دیتابیس SQLite:** قابل حمل و آسان برای بکاپ
6. **پورت 8082:** نباید با پروژه‌های دیگر تداخل داشته باشد

---

## 🔄 مراحل Deploy جدید

در صورت نیاز به deploy نسخه جدید:

```bash
# 1. آپلود فایل‌ها
rsync -avz --exclude 'node_modules' --exclude 'vendor' --exclude '.git' --exclude 'database/database.sqlite' \
  ./ root@37.152.174.87:/var/www/hotel/

# 2. اتصال به سرور
ssh root@37.152.174.87

# 3. رفتن به دایرکتوری پروژه
cd /var/www/hotel

# 4. نصب dependencies
docker exec hotel-app composer install --no-dev --optimize-autoloader

# 5. پاک کردن کش‌ها
docker exec hotel-app php artisan config:clear
docker exec hotel-app php artisan route:clear
docker exec hotel-app php artisan view:clear
docker exec hotel-app php artisan cache:clear

# 6. تنظیم مجوزها
docker exec hotel-app chown -R www-data:www-data /var/www/html/storage
docker exec hotel-app chmod -R 775 /var/www/html/storage

# 7. ری‌استارت کانتینر
docker restart hotel-app
```

---

## 📞 پشتیبانی و عیب‌یابی

### چک کردن سلامت سرویس‌ها

```bash
# وضعیت کانتینرها
docker ps

# استفاده از منابع
docker stats hotel-app

# بررسی دیسک
df -h

# بررسی RAM
free -h

# پینگ دامنه
ping hotel.darmanjoo.ir
```

### رایج‌ترین مشکلات

1. **سایت لود نمی‌شود:**
   - چک کنید کانتینر در حال اجراست: `docker ps`
   - لاگ‌ها را بررسی کنید: `docker logs hotel-app`

2. **خطای 502 Bad Gateway:**
   - کانتینر را ری‌استارت کنید: `docker restart hotel-app`
   - Nginx را ری‌استارت کنید: `systemctl restart nginx`

3. **لاگین کار نمی‌کند:**
   - کش را پاک کنید
   - Session storage permissions را چک کنید

4. **تغییرات اعمال نمی‌شود:**
   - OPcache را کلیر کنید: `docker restart hotel-app`
   - کش Laravel را پاک کنید

---

**تاریخ ایجاد:** 2025-12-17
**آخرین بروزرسانی:** 2025-12-17
**نسخه:** 1.0.0
