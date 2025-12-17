# لاگ کارهای انجام شده - سیستم مدیریت خوابگاه بانک ملی

**تاریخ:** 2025-12-17
**توسعه‌دهنده:** Claude Code (Opus 4.5)

---

## 1. رفع مشکلات لاگین و احراز هویت

### مشکل: خطای CSRF 419
- **علت:** نبود فایل `config/session.php` و عدم تنظیم TrustProxies
- **راه‌حل:**
  - ایجاد `config/session.php` با تنظیمات مناسب
  - اضافه کردن `trustProxies` به `bootstrap/app.php`:
    ```php
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
    })
    ```

### مشکل: HTTP به جای HTTPS
- **علت:** CDN با HTTPS ولی اتصال داخلی HTTP
- **راه‌حل:** ایجاد `app/Providers/AppServiceProvider.php`:
  ```php
  public function boot(): void {
      if (request()->getHost() !== '37.152.174.87') {
          URL::forceScheme('https');
      }
  }
  ```
- ایجاد `bootstrap/providers.php` برای ثبت Provider

### مشکل: پسورد bcrypt
- **علت:** پسوردها در دیتابیس خراب شده بودند
- **راه‌حل:** بازنشانی پسوردها با دستور PHP در کانتینر

---

## 2. رفع مشکل لوگو داشبورد

### مشکل: لوگو نمایش داده نمی‌شد
- **علت:** URL هاردکد شده به `http://localhost:8081/logo.png`
- **راه‌حل:** تغییر به `{{ asset('logo.png') }}`

### مشکل: نبود متن بانک ملی
- **راه‌حل:** اضافه کردن در `layouts/app.blade.php`:
  ```html
  <span style="...">بانک ملی ایران</span>
  <span style="...">اداره کل آموزش</span>
  ```

### مشکل: پس‌زمینه سفید لوگو
- **راه‌حل:** اضافه کردن استایل:
  ```css
  background: linear-gradient(135deg, #f96c08 0%, #ff8534 100%);
  padding: 8px;
  border-radius: 8px;
  ```

---

## 3. ماژول‌های جدید

### 3.1 مدیریت مهمانان (Guests)
- **فایل‌ها:**
  - `app/Http/Controllers/GuestController.php`
  - `resources/views/guests/index.blade.php`
  - `resources/views/guests/create.blade.php`
  - `resources/views/guests/edit.blade.php`
  - `resources/views/guests/show.blade.php`
- **قابلیت‌ها:** CRUD کامل برای مهمانان خارجی
- **مسیر:** `/guests`

### 3.2 مدیریت وعده‌های غذایی (Meals)
- **فایل‌ها:**
  - `app/Http/Controllers/MealController.php`
  - `resources/views/meals/index.blade.php`
- **قابلیت‌ها:** ثبت صبحانه/ناهار/شام برای رزروهای فعال
- **مسیر:** `/meals`

### 3.3 مدیریت نظافت (Cleaning)
- **فایل‌ها:**
  - `app/Http/Controllers/CleaningController.php`
  - `resources/views/cleaning/index.blade.php`
  - `resources/views/cleaning/pending.blade.php`
- **قابلیت‌ها:** لاگ نظافت، لیست تخت‌های نیازمند نظافت
- **مسیر:** `/cleaning`

### 3.4 مدیریت تعمیرات (Maintenance)
- **فایل‌ها:**
  - `app/Http/Controllers/MaintenanceController.php`
  - `resources/views/maintenance/index.blade.php`
  - `resources/views/maintenance/create.blade.php`
  - `resources/views/maintenance/show.blade.php`
- **قابلیت‌ها:** ثبت درخواست تعمیر، تغییر وضعیت، تخصیص مسئول
- **مسیر:** `/maintenance`

### 3.5 گزارش‌ها (Reports)
- **فایل‌ها:**
  - `app/Http/Controllers/ReportController.php`
  - `resources/views/reports/index.blade.php`
  - `resources/views/reports/reservations.blade.php`
  - `resources/views/reports/occupancy.blade.php`
  - `resources/views/reports/meals.blade.php`
- **قابلیت‌ها:**
  - گزارش رزروها با فیلتر و خروجی Excel
  - گزارش اشغال تخت‌ها
  - گزارش وعده‌های غذایی
- **مسیر:** `/reports`

---

## 4. ویرایش رزرو

### تغییرات در Controller:
- اضافه کردن متد `edit()` به `ReservationController`
- اضافه کردن متد `update()` به `ReservationController`

### ویوی جدید:
- `resources/views/reservations/edit.blade.php`
- فرم با مقادیر پیش‌فرض از رزرو موجود
- تخت‌های رزرو فعلی به عنوان "آزاد" نمایش داده می‌شوند

### دکمه ویرایش:
- اضافه شده به `reservations/show.blade.php`
- اضافه شده به `reservations/index.blade.php`
- فقط برای رزروهای با وضعیت `pending` یا `confirmed`

---

## 5. به‌روزرسانی منوی ناوبری

در `layouts/app.blade.php` لینک‌های زیر اضافه شدند:
- مهمانان
- وعده‌های غذایی
- نظافت
- تعمیرات
- گزارش‌ها

---

## 6. مسیرهای جدید (Routes)

در `routes/web.php` اضافه شد:
```php
// Guests
Route::resource('guests', GuestController::class);

// Meals
Route::get('/meals', [MealController::class, 'index'])->name('meals.index');
Route::post('/meals', [MealController::class, 'store'])->name('meals.store');
Route::put('/meals/{meal}', [MealController::class, 'update'])->name('meals.update');
Route::delete('/meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');

// Cleaning
Route::get('/cleaning', [CleaningController::class, 'index'])->name('cleaning.index');
Route::get('/cleaning/pending', [CleaningController::class, 'pending'])->name('cleaning.pending');
Route::post('/cleaning', [CleaningController::class, 'store'])->name('cleaning.store');

// Maintenance
Route::resource('maintenance', MaintenanceController::class);
Route::put('/maintenance/{maintenance}/assign', [MaintenanceController::class, 'assign'])->name('maintenance.assign');

// Reports
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/reservations', [ReportController::class, 'reservations'])->name('reports.reservations');
Route::get('/reports/occupancy', [ReportController::class, 'occupancy'])->name('reports.occupancy');
Route::get('/reports/meals', [ReportController::class, 'meals'])->name('reports.meals');
```

---

## 7. کامیت‌های Git

| Hash | توضیحات |
|------|---------|
| `39faf99` | Add reservation edit functionality |
| `b9652fa` | Add complete module implementations: Guests, Meals, Cleaning, Maintenance, Reports |
| `bb191d1` | Fix logo URLs to use asset() helper |
| `7e2d8a0` | Add Linux deployment guide |
| `540ff33` | Initial commit |

---

## 8. دستورات دیپلوی سرور

```bash
# Pull از گیت
cd /var/www/hotel && git pull origin master

# کپی به کانتینر Docker
docker cp /var/www/hotel/. hotel-app:/var/www/html/

# پاکسازی کش
docker exec hotel-app php artisan config:clear
docker exec hotel-app php artisan view:clear

# تنظیم دسترسی‌ها
docker exec hotel-app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
docker exec hotel-app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
```

---

## 9. اطلاعات دسترسی

- **سرور:** `37.152.174.87`
- **دسترسی مستقیم:** `http://37.152.174.87:8082`
- **دامنه:** `https://hotel.darmanjoo.ir`
- **کانتینر Docker:** `hotel-app`
- **مسیر پروژه روی سرور:** `/var/www/hotel`
- **مسیر در کانتینر:** `/var/www/html`

---

## 10. کاربران پیش‌فرض

| نقش | ایمیل | رمز عبور |
|-----|-------|----------|
| مدیر سیستم | admin@bank.ir | password |
| اپراتور | operator@bank.ir | password |
| مدیر خوابگاه | manager@bank.ir | password |

---

**ریپازیتوری گیت:** https://github.com/sedalcrazy-create/minihotel.git
