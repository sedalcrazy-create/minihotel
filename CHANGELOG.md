# تاریخچه تغییرات سیستم مدیریت خوابگاه بانک ملی

## نسخه 1.0.0 - 1403/09/26 (2025-12-16)

### ✨ امکانات جدید

#### ماژول پرسنل
- ✅ افزودن کامل ماژول مدیریت پرسنل سازمان
- ✅ امکان ثبت دستی پرسنل با 19 فیلد اطلاعاتی
- ✅ امکان ورود گروهی پرسنل از طریق فایل اکسل
- ✅ امکان خروجی اکسل از لیست پرسنل
- ✅ تمپلیت اکسل با راهنمای کامل فارسی
- ✅ نمایش اطلاعات کامل پرسنل شامل:
  - اطلاعات شخصی (کد پرسنلی، کد ملی، نام، نام خانوادگی، نام پدر، جنسیت، تاریخ تولد)
  - اطلاعات استخدامی (وضعیت استخدام، ستاد/شعبه، دپارتمان، محل خدمت)
  - سایر اطلاعات (نسبت، شماره حساب، فوق العاده، وضعیت استخدام همسر)
- ✅ تاریخچه رزروهای هر پرسنل

#### ماژول رزرو
- ✅ مدیریت رزرو تخت‌ها
- ✅ 3 نوع پذیرش: دوره کلاسی، همایش، ماموریت اداری
- ✅ چک-این و چک-اوت
- ✅ تخصیص تخت به پرسنل یا مهمان
- ✅ ردیابی وضعیت تخت‌ها (آزاد، اشغال، نیاز به نظافت، در تعمیر)

#### ماژول احراز هویت
- ✅ سیستم لاگین امن
- ✅ 5 نقش کاربری: admin, operator, manager, cleaning_staff, maintenance_staff
- ✅ ثبت فعالیت‌های کاربران (Activity Log)

#### داشبورد
- ✅ آمار کلی تخت‌ها (کل، آزاد، اشغال، نیاز به نظافت، در تعمیر)
- ✅ نمودار وضعیت تخت‌ها
- ✅ لیست آخرین رزروها
- ✅ طراحی مدرن با Glass Morphism

### 🎨 طراحی و رابط کاربری
- ✅ طراحی کاملاً فارسی (RTL)
- ✅ استفاده از فونت IRANSans
- ✅ لوگوی متحرک بانک ملی با افکت‌های زیبا:
  - حلقه‌های چرخان
  - ذرات درخشان
  - افکت glow و blur
- ✅ رنگ‌بندی نارنجی-طلایی بانک ملی (#f96c08, #e37415)
- ✅ کارت‌های شیشه‌ای با backdrop-filter
- ✅ انیمیشن‌های hover و transition

### 🐳 Docker و دیپلوی
- ✅ Dockerfile بهینه شده با PHP 8.2-fpm-alpine
- ✅ docker-compose.yml با نام کانتینر: bankMelli-dormitory-app
- ✅ پورت 8081 (به دلیل تداخل با پروژه دیگر)
- ✅ راهنمای دیپلوی آفلاین برای ویندوز 10
- ✅ راهنمای کامل نصب و راه‌اندازی (DEPLOYMENT.md)
- ✅ راهنمای دیپلوی آفلاین (OFFLINE-DEPLOYMENT.md)

### 🗄️ دیتابیس
- ✅ 14 migration کامل
- ✅ 14 model با روابط کامل
- ✅ 3 seeder (Users, AdmissionTypes, Buildings)
- ✅ 132 تخت در 22 واحد، 2 بخش (شرقی و غربی)

### 🔧 تصحیحات امروز

#### تصحیح فیلدهای پرسنل
- 🔧 تغییر `national_id` به `national_code` در همه فایل‌ها
- 🔧 حذف فیلدهای اضافی: position, phone, email
- 🔧 اضافه کردن فیلدهای جدید:
  - father_name (نام پدر)
  - gender (جنسیت)
  - relation (نسبت)
  - account_number (شماره حساب)
  - service_location_code (کد محل خدمت)
  - service_location (محل خدمت)
  - department_code (کد دپارتمان)
  - main_or_branch (ستاد/شعبه)
  - funkefalat (فوق العاده)
  - partner_employment_status (وضعیت استخدام همسر)

#### تصحیح PersonnelController
- 🔧 Validation در store() و update()
- 🔧 تغییر employment_status از مقادیر انگلیسی به فارسی
- 🔧 اضافه کردن تمام فیلدهای spec

#### تصحیح Excel Import/Export
- 🔧 PersonnelImport: mapping کامل 19 فیلد
- 🔧 PersonnelExport: export کامل 19 فیلد
- 🔧 Template: تمپلیت با 19 ستون و راهنمای فارسی
- 🔧 تصحیح mergeCells از L به S (19 ستون)

#### تصحیح Views
- 🔧 personnel/index.blade.php: نمایش فیلدهای صحیح
- 🔧 personnel/create.blade.php: فرم کامل با 19 فیلد
- 🔧 personnel/show.blade.php: نمایش جزئیات و تاریخچه
- 🔧 personnel/edit.blade.php: ویرایش با 19 فیلد

### 📦 فایل‌های ایجاد شده

#### Controllers
- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/ReservationController.php`
- `app/Http/Controllers/PersonnelController.php` (با Excel import/export/template)

#### Models
- `app/Models/User.php`
- `app/Models/Personnel.php`
- `app/Models/Guest.php`
- `app/Models/Building.php`
- `app/Models/Unit.php`
- `app/Models/Room.php`
- `app/Models/Bed.php`
- `app/Models/AdmissionType.php`
- `app/Models/Reservation.php`
- `app/Models/Meal.php`
- `app/Models/CleaningLog.php`
- `app/Models/MaintenanceRequest.php`
- `app/Models/ActivityLog.php`

#### Views - Layouts
- `resources/views/layouts/app.blade.php`

#### Views - Auth
- `resources/views/auth/login.blade.php`

#### Views - Dashboard
- `resources/views/dashboard.blade.php`

#### Views - Reservations
- `resources/views/reservations/index.blade.php`
- `resources/views/reservations/create.blade.php`
- `resources/views/reservations/show.blade.php`

#### Views - Personnel
- `resources/views/personnel/index.blade.php`
- `resources/views/personnel/create.blade.php`
- `resources/views/personnel/show.blade.php`
- `resources/views/personnel/edit.blade.php`

#### Excel
- `app/Imports/PersonnelImport.php`
- `app/Exports/PersonnelExport.php`

#### Migrations
- `database/migrations/2024_01_01_000001_create_users_table.php`
- `database/migrations/2024_01_01_000002_create_personnel_table.php`
- `database/migrations/2024_01_01_000003_create_guests_table.php`
- `database/migrations/2024_01_01_000004_create_buildings_table.php`
- `database/migrations/2024_01_01_000005_create_units_table.php`
- `database/migrations/2024_01_01_000006_create_rooms_table.php`
- `database/migrations/2024_01_01_000007_create_beds_table.php`
- `database/migrations/2024_01_01_000008_create_admission_types_table.php`
- `database/migrations/2024_01_01_000009_create_reservations_table.php`
- `database/migrations/2024_01_01_000010_create_reservation_beds_table.php`
- `database/migrations/2024_01_01_000011_create_meals_table.php`
- `database/migrations/2024_01_01_000012_create_cleaning_logs_table.php`
- `database/migrations/2024_01_01_000013_create_maintenance_requests_table.php`
- `database/migrations/2024_01_01_000014_create_activity_logs_table.php`

#### Seeders
- `database/seeders/UserSeeder.php`
- `database/seeders/AdmissionTypeSeeder.php`
- `database/seeders/BuildingSeeder.php`

#### Documentation
- `README.md`
- `DEPLOYMENT.md` (راهنمای دیپلوی)
- `OFFLINE-DEPLOYMENT.md` (راهنمای دیپلوی آفلاین)
- `CHANGELOG.md` (این فایل)

#### Docker
- `Dockerfile`
- `docker-compose.yml`
- `.dockerignore`

### 🐛 رفع مشکلات

#### مشکلات حل شده در فازهای قبل
- ✅ خطای APP_KEY missing
- ✅ خطای دسترسی به دیتابیس (chmod 777)
- ✅ خطای جدول personnels (تنظیم protected $table)
- ✅ تداخل پورت 8080 (تغییر به 8081)
- ✅ خطای supervisor log directory

#### مشکلات حل شده امروز
- ✅ View [personnel.create] not found
- ✅ فیلدهای Excel با migration مطابقت نداشت
- ✅ فیلدهای نمایشی در index با migration مطابقت نداشت
- ✅ Validation controller با فیلدهای واقعی مطابقت نداشت

### 📊 آمار پروژه

- **تعداد Models:** 14
- **تعداد Migrations:** 14
- **تعداد Controllers:** 4
- **تعداد Views:** 10
- **تعداد Routes:** 12
- **تعداد Seeders:** 3
- **خطوط کد PHP:** ~3,000+
- **خطوط کد Blade:** ~1,500+
- **تعداد تخت در سیستم:** 132
- **تعداد واحد:** 22
- **تعداد کاربر پیش‌فرض:** 5

### 🚀 نحوه استفاده

```bash
# دیپلوی با docker-compose
docker-compose up -d

# مشاهده لاگ‌ها
docker-compose logs -f

# دسترسی به سیستم
http://localhost:8081

# ورود
Username: admin
Password: admin123
```

### 📝 یادداشت‌های مهم

1. ✅ تمام فیلدهای پرسنل با مستندات OpenSpec مطابقت کامل دارد
2. ✅ سیستم کاملاً آفلاین قابل استفاده است
3. ✅ تمام وابستگی‌ها در Docker image موجود است
4. ✅ دیتابیس SQLite برای سهولت backup و انتقال
5. ✅ رابط کاربری کاملاً فارسی و راست‌چین

### 🔜 امکانات آینده (برای نسخه‌های بعدی)

- [ ] گزارش‌گیری پیشرفته (Excel, PDF)
- [ ] مدیریت غذا و تعیین وعده‌ها
- [ ] ماژول نظافت با برنامه‌زمانی
- [ ] ماژول تعمیرات با سیستم درخواست
- [ ] نمودارهای تحلیلی اشغال تخت‌ها
- [ ] سیستم پیامک و اطلاع‌رسانی
- [ ] تقویم شمسی برای رزروها
- [ ] API برای اتصال به سیستم‌های دیگر

---

**سازمان:** اداره کل آموزش بانک ملی ایران
**توسعه‌دهنده:** Claude Code
**تاریخ انتشار:** 1403/09/26
