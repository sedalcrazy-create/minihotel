# راهنمای نصب و راه‌اندازی روی سرور لینوکس

این راهنما برای نصب سیستم مدیریت خوابگاه بانک ملی روی سرورهای لینوکس (Ubuntu/Debian/CentOS) است.

## پیش‌نیازها

### نیازمندی‌های سخت‌افزاری
- CPU: حداقل 2 Core
- RAM: حداقل 4GB
- Storage: حداقل 20GB فضای آزاد
- Network: دسترسی به پورت 80 یا 443

### نیازمندی‌های نرم‌افزاری
- سیستم عامل: Ubuntu 20.04+ / Debian 11+ / CentOS 8+
- Docker Engine 20.10+
- Docker Compose 2.0+
- دسترسی root یا sudo

## نصب Docker روی لینوکس

### Ubuntu / Debian

```bash
# به‌روزرسانی package list
sudo apt update

# نصب وابستگی‌ها
sudo apt install -y apt-transport-https ca-certificates curl gnupg lsb-release

# اضافه کردن Docker GPG key
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# اضافه کردن Docker repository
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# نصب Docker
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# اجازه استفاده از Docker بدون sudo
sudo usermod -aG docker $USER

# لاگ‌اوت و لاگ‌این مجدد برای اعمال تغییرات
```

### CentOS / RHEL

```bash
# نصب وابستگی‌ها
sudo yum install -y yum-utils

# اضافه کردن Docker repository
sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo

# نصب Docker
sudo yum install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# راه‌اندازی Docker
sudo systemctl start docker
sudo systemctl enable docker

# اجازه استفاده از Docker بدون sudo
sudo usermod -aG docker $USER
```

### تست نصب

```bash
# بررسی نسخه Docker
docker --version
docker compose version

# تست اجرای کانتینر
docker run hello-world
```

## روش 1: دیپلوی با اینترنت (Production)

### مرحله 1: کپی فایل‌های پروژه

```bash
# ایجاد دایرکتوری پروژه
sudo mkdir -p /opt/bankmelli-dormitory
cd /opt/bankmelli-dormitory

# کپی فایل‌های پروژه (از طریق git یا scp)
# روش 1: با Git
git clone <repository-url> .

# روش 2: با SCP (از ماشین دیگر)
# scp -r /path/to/hotel/* user@server:/opt/bankmelli-dormitory/
```

### مرحله 2: تنظیمات محیطی

```bash
# کپی فایل .env
cp .env.example .env

# ویرایش فایل .env
nano .env
```

تنظیمات پیشنهادی برای production:

```env
APP_NAME="سیستم مدیریت خوابگاه"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

SESSION_DRIVER=file
SESSION_LIFETIME=120

LOG_CHANNEL=daily
LOG_LEVEL=error
```

### مرحله 3: تولید APP_KEY

```bash
# اجرای موقت کانتینر برای تولید key
docker run --rm -v $(pwd):/app php:8.2-cli php /app/artisan key:generate

# یا به صورت دستی در فایل .env یک رشته 32 کاراکتری base64
```

### مرحله 4: Build و اجرا

```bash
# Build کردن image
docker compose build

# اجرای کانتینر در background
docker compose up -d

# مشاهده لاگ‌ها
docker compose logs -f
```

### مرحله 5: راه‌اندازی دیتابیس

```bash
# ورود به کانتینر
docker exec -it bankMelli-dormitory-app sh

# اجرای migrations
php artisan migrate --force

# اجرای seeders
php artisan db:seed --force

# خروج از کانتینر
exit

# تنظیم مجوزهای دیتابیس
sudo chmod 666 database/database.sqlite
sudo chmod 777 database
```

## روش 2: دیپلوی آفلاین (بدون اینترنت)

### مرحله 1: آماده‌سازی روی سیستم با اینترنت

```bash
# Build کردن image
docker compose build

# Export کردن image
docker save -o bankMelli-dormitory.tar hotel-app:latest

# بسته‌بندی کامل پروژه
tar -czf bankmelli-deployment.tar.gz \
  bankMelli-dormitory.tar \
  .env.example \
  database/ \
  docker-compose.yml \
  LINUX-DEPLOYMENT.md
```

### مرحله 2: انتقال به سرور

```bash
# با SCP
scp bankmelli-deployment.tar.gz user@server:/tmp/

# یا با USB/Network share
```

### مرحله 3: نصب روی سرور آفلاین

```bash
# استخراج فایل‌ها
cd /opt
sudo mkdir -p bankmelli-dormitory
cd bankmelli-dormitory
sudo tar -xzf /tmp/bankmelli-deployment.tar.gz

# Load کردن Docker image
sudo docker load -i bankMelli-dormitory.tar

# تنظیمات .env
cp .env.example .env
nano .env

# اجرای کانتینر
docker run -d \
  --name bankMelli-dormitory-app \
  --restart unless-stopped \
  -p 80:80 \
  -v $(pwd)/database:/var/www/html/database \
  -v $(pwd)/.env:/var/www/html/.env \
  hotel-app:latest

# راه‌اندازی دیتابیس
docker exec -it bankMelli-dormitory-app php artisan migrate --force
docker exec -it bankMelli-dormitory-app php artisan db:seed --force
sudo chmod 666 database/database.sqlite
sudo chmod 777 database
```

## پیکربندی Nginx Reverse Proxy (اختیاری)

اگر می‌خواهید سیستم را با domain و SSL راه‌اندازی کنید:

### نصب Nginx

```bash
# Ubuntu/Debian
sudo apt install -y nginx

# CentOS/RHEL
sudo yum install -y nginx
```

### پیکربندی

```bash
sudo nano /etc/nginx/sites-available/bankmelli-dormitory
```

محتوای فایل:

```nginx
server {
    listen 80;
    server_name your-domain.com;

    location / {
        proxy_pass http://localhost:8081;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

فعال‌سازی:

```bash
# Ubuntu/Debian
sudo ln -s /etc/nginx/sites-available/bankmelli-dormitory /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

# CentOS/RHEL
sudo nginx -t
sudo systemctl reload nginx
```

## نصب SSL با Let's Encrypt

```bash
# نصب Certbot
# Ubuntu/Debian
sudo apt install -y certbot python3-certbot-nginx

# CentOS/RHEL
sudo yum install -y certbot python3-certbot-nginx

# دریافت گواهی SSL
sudo certbot --nginx -d your-domain.com

# تست تمدید خودکار
sudo certbot renew --dry-run
```

## پیکربندی Firewall

### UFW (Ubuntu/Debian)

```bash
# فعال‌سازی UFW
sudo ufw enable

# اجازه HTTP و HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp  # SSH

# بررسی وضعیت
sudo ufw status
```

### Firewalld (CentOS/RHEL)

```bash
# فعال‌سازی Firewalld
sudo systemctl start firewalld
sudo systemctl enable firewalld

# اجازه HTTP و HTTPS
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --permanent --add-service=ssh
sudo firewall-cmd --reload

# بررسی وضعیت
sudo firewall-cmd --list-all
```

## Systemd Service (راه‌اندازی خودکار)

برای راه‌اندازی خودکار با systemd:

```bash
sudo nano /etc/systemd/system/bankmelli-dormitory.service
```

محتوای فایل:

```ini
[Unit]
Description=Bank Melli Dormitory Management System
Requires=docker.service
After=docker.service

[Service]
Type=oneshot
RemainAfterExit=yes
WorkingDirectory=/opt/bankmelli-dormitory
ExecStart=/usr/bin/docker compose up -d
ExecStop=/usr/bin/docker compose down
TimeoutStartSec=0

[Install]
WantedBy=multi-user.target
```

فعال‌سازی:

```bash
sudo systemctl daemon-reload
sudo systemctl enable bankmelli-dormitory
sudo systemctl start bankmelli-dormitory
sudo systemctl status bankmelli-dormitory
```

## مدیریت و نگهداری

### مشاهده لاگ‌ها

```bash
# لاگ Docker
docker logs bankMelli-dormitory-app

# لاگ real-time
docker logs -f bankMelli-dormitory-app

# لاگ Laravel
docker exec -it bankMelli-dormitory-app cat storage/logs/laravel.log

# لاگ Nginx
docker exec -it bankMelli-dormitory-app cat /var/log/nginx/access.log
docker exec -it bankMelli-dormitory-app cat /var/log/nginx/error.log
```

### پشتیبان‌گیری

```bash
# اسکریپت پشتیبان‌گیری خودکار
cat > /opt/backup-dormitory.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/opt/backups/dormitory"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# پشتیبان دیتابیس
docker exec bankMelli-dormitory-app cat /var/www/html/database/database.sqlite > $BACKUP_DIR/database_$DATE.sqlite

# پشتیبان .env
cp /opt/bankmelli-dormitory/.env $BACKUP_DIR/env_$DATE

# حذف بکاپ‌های قدیمی‌تر از 30 روز
find $BACKUP_DIR -name "*.sqlite" -mtime +30 -delete

echo "Backup completed: $DATE"
EOF

chmod +x /opt/backup-dormitory.sh

# اضافه کردن به crontab (هر روز ساعت 2 صبح)
(crontab -l 2>/dev/null; echo "0 2 * * * /opt/backup-dormitory.sh") | crontab -
```

### بازیابی از پشتیبان

```bash
# توقف کانتینر
docker stop bankMelli-dormitory-app

# بازگرداندن دیتابیس
cp /opt/backups/dormitory/database_YYYYMMDD_HHMMSS.sqlite /opt/bankmelli-dormitory/database/database.sqlite

# تنظیم مجوزها
chmod 666 /opt/bankmelli-dormitory/database/database.sqlite
chmod 777 /opt/bankmelli-dormitory/database

# راه‌اندازی مجدد
docker start bankMelli-dormitory-app
```

### به‌روزرسانی سیستم

```bash
# دریافت آخرین تغییرات
cd /opt/bankmelli-dormitory
git pull  # یا کپی فایل‌های جدید

# پشتیبان‌گیری
docker exec bankMelli-dormitory-app cat /var/www/html/database/database.sqlite > /tmp/backup_pre_update.sqlite

# Build و راه‌اندازی مجدد
docker compose down
docker compose build --no-cache
docker compose up -d

# بررسی لاگ‌ها
docker logs -f bankMelli-dormitory-app
```

## مانیتورینگ

### نصب Portainer (اختیاری)

```bash
docker volume create portainer_data

docker run -d \
  --name portainer \
  --restart unless-stopped \
  -p 9000:9000 \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v portainer_data:/data \
  portainer/portainer-ce:latest
```

دسترسی: http://server-ip:9000

### بررسی استفاده منابع

```bash
# مصرف منابع کانتینر
docker stats bankMelli-dormitory-app

# فضای دیسک
df -h
docker system df

# حافظه و CPU
free -h
top
```

## عیب‌یابی

### کانتینر شروع نمی‌شود

```bash
# بررسی لاگ‌های Docker
docker logs bankMelli-dormitory-app

# بررسی وضعیت کانتینر
docker inspect bankMelli-dormitory-app

# تست manual
docker run -it --rm hotel-app:latest sh
```

### خطای Permission Denied

```bash
# تنظیم مجوزهای دیتابیس
cd /opt/bankmelli-dormitory
sudo chown -R 1000:1000 database/
sudo chmod 777 database/
sudo chmod 666 database/database.sqlite
```

### خطای پورت در حال استفاده

```bash
# پیدا کردن پروسس
sudo netstat -tulpn | grep :80
sudo lsof -i :80

# Kill کردن پروسس
sudo kill -9 <PID>

# یا تغییر پورت در docker-compose.yml
ports:
  - "8081:80"
```

### مشکل در اتصال به دیتابیس

```bash
# ورود به کانتینر
docker exec -it bankMelli-dormitory-app sh

# بررسی فایل دیتابیس
ls -la /var/www/html/database/
sqlite3 /var/www/html/database/database.sqlite "SELECT count(*) FROM users;"

# اجرای مجدد migrations
php artisan migrate:fresh --seed --force
```

## امنیت

### تنظیمات امنیتی پیشنهادی

```bash
# تغییر رمز عبور کاربر admin
docker exec -it bankMelli-dormitory-app php artisan tinker
# در tinker:
# $user = User::where('username', 'admin')->first();
# $user->password = Hash::make('NewSecurePassword123!');
# $user->save();
# exit

# غیرفعال کردن debug در production
# در .env:
# APP_DEBUG=false

# محدود کردن دسترسی به IP خاص (در Nginx)
# allow 192.168.1.0/24;
# deny all;
```

### SELinux (CentOS/RHEL)

```bash
# اگر SELinux فعال است
sudo semanage fcontext -a -t container_file_t "/opt/bankmelli-dormitory(/.*)?"
sudo restorecon -Rv /opt/bankmelli-dormitory
```

## تست و اطمینان از عملکرد

```bash
# تست سلامت کانتینر
docker exec bankMelli-dormitory-app php artisan --version

# تست اتصال به دیتابیس
docker exec bankMelli-dormitory-app php artisan migrate:status

# تست از خارج
curl -I http://localhost

# تست با domain
curl -I http://your-domain.com
```

## پشتیبانی و مشاوره

برای مشکلات فنی:

1. لاگ‌های Docker را بررسی کنید
2. وضعیت کانتینر را چک کنید
3. مجوزهای فایل‌ها را بررسی کنید
4. فایروال و SELinux را چک کنید

---

**نسخه سند:** 1.0
**تاریخ:** 1403/09/26
**سازمان:** اداره کل آموزش بانک ملی ایران
