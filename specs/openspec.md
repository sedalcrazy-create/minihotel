# 📘 OpenSpec - Bank Melli Dormitory Management System
# 📘 OpenSpec - سیستم مدیریت خوابگاه بانک ملی

**Version / نسخه:** 1.0
**Date / تاریخ:** 2025
**Technology Stack / تکنولوژی:** Laravel 11, SQLite, Blade Templates
**UI Language / زبان رابط کاربری:** Persian (Farsi) / فارسی
**Calendar / تقویم:** Jalali (Persian) / شمسی

---

## 📊 1. Database Models / مدل‌های دیتابیس

### 1.1 Table: `users` (System Users / کاربران سیستم)

| Column / ستون | Type / نوع | Nullable | Default / پیش‌فرض | Description / توضیحات |
|---------------|-----------|----------|-------------------|----------------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Unique ID / شناسه یکتا |
| `name` | VARCHAR(255) | NO | - | User name / نام کاربر |
| `email` | VARCHAR(255) | NO | - | Email (unique) / ایمیل (یکتا) |
| `password` | VARCHAR(255) | NO | - | Hashed password / رمز عبور (hash شده) |
| `role` | ENUM | NO | 'operator' | Role: `admin`, `operator`, `manager`, `cleaning_staff`, `maintenance_staff` / نقش |
| `is_active` | BOOLEAN | NO | 1 | Active/Inactive / فعال/غیرفعال |
| `created_at` | TIMESTAMP | YES | NULL | Creation time / زمان ایجاد |
| `updated_at` | TIMESTAMP | YES | NULL | Update time / زمان بروزرسانی |

**Indexes / ایندکس‌ها:**
- PRIMARY KEY: `id`
- UNIQUE: `email`
- INDEX: `role`, `is_active`

---

### 1.2 Table: `personnel` (Bank Personnel / پرسنل بانک)

| Column / ستون | Type / نوع | Nullable | Default / پیش‌فرض | Description / توضیحات |
|---------------|-----------|----------|-------------------|----------------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Unique ID / شناسه یکتا |
| `employment_code` | VARCHAR(50) | NO | - | Employment code (unique) / کد استخدامی (یکتا) |
| `first_name` | VARCHAR(100) | NO | - | First name / نام |
| `last_name` | VARCHAR(100) | NO | - | Last name / نام خانوادگی |
| `birth_year` | SMALLINT | NO | - | Birth year / سال تولد |
| `birth_month` | TINYINT | NO | - | Birth month / ماه تولد |
| `birth_day` | TINYINT | NO | - | Birth day / روز تولد |
| `national_code` | VARCHAR(10) | NO | - | National ID (unique) / کد ملی (یکتا) |
| `father_name` | VARCHAR(100) | YES | NULL | Father name / نام پدر |
| `relation` | VARCHAR(50) | YES | NULL | Relation / نسبت |
| `account_number` | VARCHAR(50) | YES | NULL | Bank account number / شماره حساب |
| `service_location_code` | VARCHAR(50) | YES | NULL | Service location code / کد محل خدمت |
| `service_location` | VARCHAR(255) | YES | NULL | Service location / محل خدمت |
| `department_code` | VARCHAR(50) | YES | NULL | Department code / کد اداره امور |
| `department` | VARCHAR(255) | YES | NULL | Department / اداره امور |
| `employment_status` | VARCHAR(100) | NO | - | Status: `فعال`, `بازنشسته`, `فوتی`, `اخراج`, `انتقال` |
| `main_or_branch` | VARCHAR(50) | YES | NULL | Main or branch / اصلی-فرعی |
| `funkefalat` | VARCHAR(255) | YES | NULL | Funkefalat field / funkefalat |
| `partner_employment_status` | VARCHAR(100) | YES | NULL | Partner employment status / وضعیت خدمت همکار |
| `gender` | ENUM | NO | 'male' | Gender: `male`, `female` / جنسیت |
| `is_active` | BOOLEAN | NO | 1 | Active/Inactive (auto from status) / فعال/غیرفعال |
| `created_at` | TIMESTAMP | YES | NULL | Creation time / زمان ایجاد |
| `updated_at` | TIMESTAMP | YES | NULL | Update time / زمان بروزرسانی |

**Indexes / ایندکس‌ها:**
- PRIMARY KEY: `id`
- UNIQUE: `employment_code`, `national_code`
- INDEX: `is_active`, `employment_status`

**Business Rules / قوانین کسب‌وکار:**
- `is_active = 1` only if `employment_status = 'فعال'` / فقط زمانی که وضعیت خدمت فعال باشد
- Inactive personnel cannot create new reservations / پرسنل غیرفعال نمی‌توانند رزرو جدید ایجاد کنند

---

### 1.3 Table: `guests` (Miscellaneous Guests / مهمان‌های متفرقه)

| Column / ستون | Type / نوع | Nullable | Default / پیش‌فرض | Description / توضیحات |
|---------------|-----------|----------|-------------------|----------------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Unique ID / شناسه یکتا |
| `full_name` | VARCHAR(255) | NO | - | Full name / نام کامل |
| `national_code` | VARCHAR(10) | YES | NULL | National ID / کد ملی |
| `phone` | VARCHAR(20) | YES | NULL | Phone number / شماره تماس |
| `email` | VARCHAR(255) | YES | NULL | Email / ایمیل |
| `reason` | TEXT | YES | NULL | Reason for stay / دلیل اقامت |
| `organization` | VARCHAR(255) | YES | NULL | Organization / سازمان |
| `created_at` | TIMESTAMP | YES | NULL | Creation time / زمان ایجاد |
| `updated_at` | TIMESTAMP | YES | NULL | Update time / زمان بروزرسانی |

**Indexes / ایندکس‌ها:**
- PRIMARY KEY: `id`
- INDEX: `national_code`, `phone`

---

### 1.4 Table: `buildings` (Buildings/Dormitories / ساختمان‌ها/خوابگاه‌ها)

| Column / ستون | Type / نوع | Nullable | Default / پیش‌فرض | Description / توضیحات |
|---------------|-----------|----------|-------------------|----------------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Unique ID / شناسه یکتا |
| `name` | VARCHAR(255) | NO | - | Building name / نام ساختمان |
| `code` | VARCHAR(50) | NO | - | Building code (unique) / کد ساختمان (یکتا) |
| `description` | TEXT | YES | NULL | Description / توضیحات |
| `is_active` | BOOLEAN | NO | 1 | Active/Inactive / فعال/غیرفعال |
| `created_at` | TIMESTAMP | YES | NULL | Creation time / زمان ایجاد |
| `updated_at` | TIMESTAMP | YES | NULL | Update time / زمان بروزرسانی |

**Indexes / ایندکس‌ها:**
- PRIMARY KEY: `id`
- UNIQUE: `code`

**Default / پیش‌فرض:** One building named "Main Dormitory" / یک ساختمان با نام "خوابگاه اصلی"

---

### 1.5 Table: `units` (Units / واحدها)

| Column / ستون | Type / نوع | Nullable | Default / پیش‌فرض | Description / توضیحات |
|---------------|-----------|----------|-------------------|----------------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Unique ID / شناسه یکتا |
| `building_id` | BIGINT UNSIGNED | NO | - | Building ID (FK) / شناسه ساختمان |
| `number` | SMALLINT | NO | - | Unit number (1-22) / شماره واحد |
| `section` | ENUM | NO | 'east' | Section: `east` (1-12), `west` (13-22) / بخش: شرقی، غربی |
| `is_active` | BOOLEAN | NO | 1 | Active/Inactive / فعال/غیرفعال |
| `created_at` | TIMESTAMP | YES | NULL | Creation time / زمان ایجاد |
| `updated_at` | TIMESTAMP | YES | NULL | Update time / زمان بروزرسانی |

**Indexes / ایندکس‌ها:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `building_id` REFERENCES `buildings(id)` ON DELETE CASCADE
- UNIQUE: `building_id`, `number`
- INDEX: `section`, `is_active`

**Business Rules / قوانین:**
- Units 1-12 → `section = 'east'` / واحد 1-12 → بخش شرقی
- Units 13-22 → `section = 'west'` / واحد 13-22 → بخش غربی

---

### 1.6 Table: `rooms` (Rooms / اتاق‌ها)

| Column / ستون | Type / نوع | Nullable | Default / پیش‌فرض | Description / توضیحات |
|---------------|-----------|----------|-------------------|----------------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Unique ID / شناسه یکتا |
| `unit_id` | BIGINT UNSIGNED | NO | - | Unit ID (FK) / شناسه واحد |
| `number` | SMALLINT | NO | - | Room number / شماره اتاق |
| `capacity` | TINYINT | NO | 6 | Capacity (beds count) / ظرفیت (تعداد تخت) |
| `is_active` | BOOLEAN | NO | 1 | Active/Inactive / فعال/غیرفعال |
| `created_at` | TIMESTAMP | YES | NULL | Creation time / زمان ایجاد |
| `updated_at` | TIMESTAMP | YES | NULL | Update time / زمان بروزرسانی |

**Indexes / ایندکس‌ها:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `unit_id` REFERENCES `units(id)` ON DELETE CASCADE
- UNIQUE: `unit_id`, `number`
- INDEX: `is_active`

**Business Rules / قوانین:**
- Each room has 6 beds (default) / هر اتاق 6 تخت دارد

---

### 1.7 Table: `beds` (Beds / تخت‌ها)

| Column / ستون | Type / نوع | Nullable | Default / پیش‌فرض | Description / توضیحات |
|---------------|-----------|----------|-------------------|----------------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Unique ID / شناسه یکتا |
| `room_id` | BIGINT UNSIGNED | NO | - | Room ID (FK) / شناسه اتاق |
| `number` | TINYINT | NO | - | Bed number (1-6) / شماره تخت |
| `status` | ENUM | NO | 'available' | Status: `available`, `occupied`, `needs_cleaning`, `under_maintenance` |
| `is_active` | BOOLEAN | NO | 1 | Active/Inactive / فعال/غیرفعال |
| `created_at` | TIMESTAMP | YES | NULL | Creation time / زمان ایجاد |
| `updated_at` | TIMESTAMP | YES | NULL | Update time / زمان بروزرسانی |

**Indexes / ایندکس‌ها:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `room_id` REFERENCES `rooms(id)` ON DELETE CASCADE
- UNIQUE: `room_id`, `number`
- INDEX: `status`, `is_active`

**Business Rules / قوانین:**
- Each room has 6 beds (number: 1-6) / هر اتاق 6 تخت دارد
- Room status calculated from bed statuses: / وضعیت اتاق از وضعیت تخت‌ها محاسبه می‌شود:
  - 0 occupied → Empty / خالی
  - 1-5 occupied → Half-full / نیمه‌پر
  - 6 occupied → Full / اشغال

---

### 1.8 Table: `admission_types` (Admission Types / انواع پذیرش)

| Column / ستون | Type / نوع | Nullable | Default / پیش‌فرض | Description / توضیحات |
|---------------|-----------|----------|-------------------|----------------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Unique ID / شناسه یکتا |
| `name` | VARCHAR(100) | NO | - | Admission type name / نام نوع پذیرش |
| `code` | VARCHAR(50) | NO | - | Code (unique) / کد (یکتا) |
| `has_reservation` | BOOLEAN | NO | 0 | Has reservation? / آیا رزرو دارد؟ |
| `reservation_days_before` | TINYINT | YES | NULL | Days before for reservation / تعداد روز قبل برای رزرو |
| `description` | TEXT | YES | NULL | Description / توضیحات |
| `is_active` | BOOLEAN | NO | 1 | Active/Inactive / فعال/غیرفعال |
| `created_at` | TIMESTAMP | YES | NULL | Creation time / زمان ایجاد |
| `updated_at` | TIMESTAMP | YES | NULL | Update time / زمان بروزرسانی |

**Indexes / ایندکس‌ها:**
- PRIMARY KEY: `id`
- UNIQUE: `code`

**Defaults / پیش‌فرض:**
1. Class Course / دوره کلاسی → `has_reservation = 0`
2. Conference / همایش → `has_reservation = 0`
3. Official Mission / ماموریت اداری → `has_reservation = 1`, `reservation_days_before = 2-3`

---

### 1.9 Table: `reservations` (Reservations / رزروها)

| Column / ستون | Type / نوع | Nullable | Default / پیش‌فرض | Description / توضیحات |
|---------------|-----------|----------|-------------------|----------------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Unique ID / شناسه یکتا |
| `admission_type_id` | BIGINT UNSIGNED | NO | - | Admission type (FK) / نوع پذیرش |
| `personnel_id` | BIGINT UNSIGNED | YES | NULL | Personnel ID (FK) / شناسه پرسنل |
| `guest_id` | BIGINT UNSIGNED | YES | NULL | Guest ID (FK) / شناسه مهمان |
| `room_id` | BIGINT UNSIGNED | NO | - | Room ID (FK) / شناسه اتاق |
| `check_in_date` | DATE | NO | - | Check-in date / تاریخ ورود |
| `check_out_date` | DATE | NO | - | Check-out date / تاریخ خروج |
| `actual_check_in` | TIMESTAMP | YES | NULL | Actual check-in time / زمان واقعی ورود |
| `actual_check_out` | TIMESTAMP | YES | NULL | Actual check-out time / زمان واقعی خروج |
| `status` | ENUM | NO | 'pending' | Status: `pending`, `confirmed`, `checked_in`, `checked_out`, `cancelled` |
| `notes` | TEXT | YES | NULL | Notes / یادداشت |
| `created_by` | BIGINT UNSIGNED | NO | - | Created by (FK users) / ایجاد توسط |
| `created_at` | TIMESTAMP | YES | NULL | Creation time / زمان ایجاد |
| `updated_at` | TIMESTAMP | YES | NULL | Update time / زمان بروزرسانی |

**Indexes / ایندکس‌ها:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `admission_type_id` REFERENCES `admission_types(id)`
- FOREIGN KEY: `personnel_id` REFERENCES `personnel(id)` ON DELETE SET NULL
- FOREIGN KEY: `guest_id` REFERENCES `guests(id)` ON DELETE SET NULL
- FOREIGN KEY: `room_id` REFERENCES `rooms(id)`
- FOREIGN KEY: `created_by` REFERENCES `users(id)`
- INDEX: `status`, `check_in_date`, `check_out_date`

**Business Rules / قوانین:**
- One of `personnel_id` or `guest_id` must have value / یکی از پرسنل یا مهمان باید مقدار داشته باشد
- `check_out_date` must be greater than `check_in_date` / تاریخ خروج باید بزرگتر از تاریخ ورود باشد
- Only active personnel can make reservations / فقط پرسنل فعال می‌توانند رزرو کنند

---

### 1.10 Table: `reservation_beds` (Reserved Beds / تخت‌های رزرو شده)

| Column / ستون | Type / نوع | Nullable | Default / پیش‌فرض | Description / توضیحات |
|---------------|-----------|----------|-------------------|----------------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Unique ID / شناسه یکتا |
| `reservation_id` | BIGINT UNSIGNED | NO | - | Reservation ID (FK) / شناسه رزرو |
| `bed_id` | BIGINT UNSIGNED | NO | - | Bed ID (FK) / شناسه تخت |
| `created_at` | TIMESTAMP | YES | NULL | Creation time / زمان ایجاد |
| `updated_at` | TIMESTAMP | YES | NULL | Update time / زمان بروزرسانی |

**Indexes / ایندکس‌ها:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `reservation_id` REFERENCES `reservations(id)` ON DELETE CASCADE
- FOREIGN KEY: `bed_id` REFERENCES `beds(id)` ON DELETE CASCADE
- UNIQUE: `reservation_id`, `bed_id`

**Business Rules / قوانین:**
- Each reservation can have 1-6 beds / هر رزرو می‌تواند 1 تا 6 تخت داشته باشد
- All beds in a reservation must be from same room / تخت‌های یک رزرو باید از یک اتاق باشند

---

### 1.11 Table: `meals` (Meals / وعده‌های غذایی)

| Column / ستون | Type / نوع | Nullable | Default / پیش‌فرض | Description / توضیحات |
|---------------|-----------|----------|-------------------|----------------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Unique ID / شناسه یکتا |
| `reservation_id` | BIGINT UNSIGNED | NO | - | Reservation ID (FK) / شناسه رزرو |
| `date` | DATE | NO | - | Meal date / تاریخ وعده |
| `breakfast` | BOOLEAN | NO | 0 | Breakfast / صبحانه |
| `lunch` | BOOLEAN | NO | 0 | Lunch / ناهار |
| `dinner` | BOOLEAN | NO | 0 | Dinner / شام |
| `notes` | TEXT | YES | NULL | Notes / یادداشت |
| `created_at` | TIMESTAMP | YES | NULL | Creation time / زمان ایجاد |
| `updated_at` | TIMESTAMP | YES | NULL | Update time / زمان بروزرسانی |

**Indexes / ایندکس‌ها:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `reservation_id` REFERENCES `reservations(id)` ON DELETE CASCADE
- UNIQUE: `reservation_id`, `date`
- INDEX: `date`

**Business Rules / قوانین:**
- Meals only for confirmed or checked-in reservations / وعده فقط برای رزروهای تایید شده یا چک‌این شده
- Date must be between `check_in_date` and `check_out_date` / تاریخ باید بین ورود و خروج باشد

---

### 1.12 Table: `cleaning_logs` (Cleaning Logs / لاگ نظافت)

| Column / ستون | Type / نوع | Nullable | Default / پیش‌فرض | Description / توضیحات |
|---------------|-----------|----------|-------------------|----------------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Unique ID / شناسه یکتا |
| `room_id` | BIGINT UNSIGNED | YES | NULL | Room ID (FK) / شناسه اتاق |
| `bed_id` | BIGINT UNSIGNED | YES | NULL | Bed ID (FK) / شناسه تخت |
| `cleaned_at` | TIMESTAMP | NO | CURRENT_TIMESTAMP | Cleaning time / زمان نظافت |
| `type` | ENUM | NO | 'daily' | Type: `daily`, `weekly`, `deep` / نوع: روزانه، هفتگی، عمیق |
| `cleaned_by` | BIGINT UNSIGNED | NO | - | Cleaner (FK users) / انجام‌دهنده |
| `notes` | TEXT | YES | NULL | Notes / یادداشت |
| `created_at` | TIMESTAMP | YES | NULL | Creation time / زمان ایجاد |
| `updated_at` | TIMESTAMP | YES | NULL | Update time / زمان بروزرسانی |

**Indexes / ایندکس‌ها:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `room_id` REFERENCES `rooms(id)` ON DELETE CASCADE
- FOREIGN KEY: `bed_id` REFERENCES `beds(id)` ON DELETE CASCADE
- FOREIGN KEY: `cleaned_by` REFERENCES `users(id)`
- INDEX: `cleaned_at`, `type`

**Business Rules / قوانین:**
- One of `room_id` or `bed_id` must have value / یکی از اتاق یا تخت باید مقدار داشته باشد
- After cleaning, bed status changes from `needs_cleaning` to `available` / پس از نظافت، وضعیت تخت به خالی تغییر می‌کند

---

### 1.13 Table: `maintenance_requests` (Maintenance Requests / درخواست‌های تعمیر)

| Column / ستون | Type / نوع | Nullable | Default / پیش‌فرض | Description / توضیحات |
|---------------|-----------|----------|-------------------|----------------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Unique ID / شناسه یکتا |
| `room_id` | BIGINT UNSIGNED | YES | NULL | Room ID (FK) / شناسه اتاق |
| `bed_id` | BIGINT UNSIGNED | YES | NULL | Bed ID (FK) / شناسه تخت |
| `description` | TEXT | NO | - | Problem description / شرح مشکل |
| `priority` | ENUM | NO | 'normal' | Priority: `low`, `normal`, `high`, `urgent` / اولویت |
| `status` | ENUM | NO | 'pending' | Status: `pending`, `in_progress`, `completed`, `cancelled` |
| `reported_by` | BIGINT UNSIGNED | NO | - | Reporter (FK users) / گزارش‌دهنده |
| `assigned_to` | BIGINT UNSIGNED | YES | NULL | Assigned staff (FK users) / مسئول تعمیر |
| `started_at` | TIMESTAMP | YES | NULL | Start time / زمان شروع |
| `completed_at` | TIMESTAMP | YES | NULL | Completion time / زمان اتمام |
| `notes` | TEXT | YES | NULL | Notes / یادداشت |
| `created_at` | TIMESTAMP | YES | NULL | Creation time / زمان ایجاد |
| `updated_at` | TIMESTAMP | YES | NULL | Update time / زمان بروزرسانی |

**Indexes / ایندکس‌ها:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `room_id` REFERENCES `rooms(id)` ON DELETE CASCADE
- FOREIGN KEY: `bed_id` REFERENCES `beds(id)` ON DELETE CASCADE
- FOREIGN KEY: `reported_by` REFERENCES `users(id)`
- FOREIGN KEY: `assigned_to` REFERENCES `users(id)` ON DELETE SET NULL
- INDEX: `status`, `priority`, `created_at`

**Business Rules / قوانین:**
- One of `room_id` or `bed_id` must have value / یکی از اتاق یا تخت باید مقدار داشته باشد
- When `status = 'in_progress'` → bed `status = 'under_maintenance'` / در حال انجام → تخت در حال تعمیر
- When `status = 'completed'` → bed `status = 'needs_cleaning'` / اتمام → تخت نیاز به نظافت دارد

---

### 1.14 Table: `activity_logs` (Activity Logs / لاگ فعالیت)

| Column / ستون | Type / نوع | Nullable | Default / پیش‌فرض | Description / توضیحات |
|---------------|-----------|----------|-------------------|----------------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Unique ID / شناسه یکتا |
| `user_id` | BIGINT UNSIGNED | YES | NULL | User ID (FK) / شناسه کاربر |
| `action` | VARCHAR(255) | NO | - | Action type / نوع عملیات |
| `model` | VARCHAR(100) | YES | NULL | Related model / مدل مرتبط |
| `model_id` | BIGINT UNSIGNED | YES | NULL | Record ID / شناسه رکورد |
| `description` | TEXT | YES | NULL | Description / توضیحات |
| `ip_address` | VARCHAR(45) | YES | NULL | User IP / IP کاربر |
| `user_agent` | TEXT | YES | NULL | User Agent |
| `created_at` | TIMESTAMP | YES | NULL | Creation time / زمان ایجاد |

**Indexes / ایندکس‌ها:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `user_id` REFERENCES `users(id)` ON DELETE SET NULL
- INDEX: `action`, `model`, `created_at`

---

## 🔗 2. Relationships / روابط

```
buildings (1) ──→ (N) units
units (1) ──→ (N) rooms
rooms (1) ──→ (N) beds

reservations (N) ──→ (1) admission_types
reservations (N) ──→ (1) personnel (nullable)
reservations (N) ──→ (1) guest (nullable)
reservations (N) ──→ (1) rooms
reservations (1) ──→ (N) reservation_beds
reservations (1) ──→ (N) meals

reservation_beds (N) ──→ (1) beds

cleaning_logs (N) ──→ (1) rooms (nullable)
cleaning_logs (N) ──→ (1) beds (nullable)
cleaning_logs (N) ──→ (1) users (cleaned_by)

maintenance_requests (N) ──→ (1) rooms (nullable)
maintenance_requests (N) ──→ (1) beds (nullable)
maintenance_requests (N) ──→ (1) users (reported_by)
maintenance_requests (N) ──→ (1) users (assigned_to, nullable)

activity_logs (N) ──→ (1) users (nullable)
```

---

## 📋 3. Endpoints & Controllers / نقاط پایانی و کنترلرها

### 3.1 Authentication (`AuthController`)

| Method | URL | Action | Description / توضیحات |
|--------|-----|--------|----------------------|
| GET | `/login` | `showLoginForm()` | Show login form / نمایش فرم ورود |
| POST | `/login` | `login()` | Login user / ورود کاربر |
| POST | `/logout` | `logout()` | Logout user / خروج کاربر |

**Middleware:** `guest` for login, `auth` for logout

---

### 3.2 Dashboard (`DashboardController`)

| Method | URL | Action | Description / توضیحات |
|--------|-----|--------|----------------------|
| GET | `/` | `index()` | Main dashboard with schematic view / داشبورد اصلی با نمایش شماتیک |

**Middleware:** `auth`

**Data / داده‌ها:**
- Total beds / تعداد کل تخت‌ها
- Available beds / تعداد تخت‌های خالی
- Occupied beds / تعداد تخت‌های اشغال
- Needs cleaning / تعداد تخت‌های نیازمند نظافت
- Under maintenance / تعداد تخت‌های در حال تعمیر
- Units list with rooms and beds status / لیست واحدها با وضعیت اتاق‌ها و تخت‌ها

---

### 3.3 Personnel Management (`PersonnelController`)

| Method | URL | Action | Description / توضیحات | Role |
|--------|-----|--------|----------------------|------|
| GET | `/personnel` | `index()` | Personnel list / لیست پرسنل | admin, manager |
| GET | `/personnel/create` | `create()` | Add personnel form / فرم افزودن پرسنل | admin |
| POST | `/personnel` | `store()` | Save new personnel / ذخیره پرسنل جدید | admin |
| GET | `/personnel/{id}` | `show()` | Personnel details / جزئیات پرسنل | admin, manager |
| GET | `/personnel/{id}/edit` | `edit()` | Edit personnel form / فرم ویرایش پرسنل | admin |
| PUT | `/personnel/{id}` | `update()` | Update personnel / بروزرسانی پرسنل | admin |
| DELETE | `/personnel/{id}` | `destroy()` | Soft delete (set inactive) / حذف منطقی | admin |
| GET | `/personnel/import` | `showImportForm()` | Excel import form / فرم Import اکسل | admin |
| POST | `/personnel/import` | `import()` | Import from Excel / Import از اکسل | admin |
| GET | `/personnel/export` | `export()` | Export to Excel / Export به اکسل | admin |

**Middleware:** `auth`, `role:admin,manager`

---

### 3.4 Guest Management (`GuestController`)

| Method | URL | Action | Description / توضیحات | Role |
|--------|-----|--------|----------------------|------|
| GET | `/guests` | `index()` | Guests list / لیست مهمان‌ها | all |
| GET | `/guests/create` | `create()` | Add guest form / فرم افزودن مهمان | operator, admin |
| POST | `/guests` | `store()` | Save new guest / ذخیره مهمان جدید | operator, admin |
| GET | `/guests/{id}` | `show()` | Guest details / جزئیات مهمان | all |
| GET | `/guests/{id}/edit` | `edit()` | Edit guest form / فرم ویرایش مهمان | operator, admin |
| PUT | `/guests/{id}` | `update()` | Update guest / بروزرسانی مهمان | operator, admin |
| DELETE | `/guests/{id}` | `destroy()` | Delete guest / حذف مهمان | admin |

**Middleware:** `auth`

---

### 3.5 Room Management (`RoomController`)

| Method | URL | Action | Description / توضیحات | Role |
|--------|-----|--------|----------------------|------|
| GET | `/rooms` | `index()` | Rooms list / لیست اتاق‌ها | all |
| GET | `/rooms/{id}` | `show()` | Room & beds details / جزئیات اتاق و تخت‌ها | all |
| GET | `/rooms/{id}/edit` | `edit()` | Edit room form / فرم ویرایش اتاق | admin |
| PUT | `/rooms/{id}` | `update()` | Update room / بروزرسانی اتاق | admin |

**Middleware:** `auth`

---

### 3.6 Reservation Management (`ReservationController`)

| Method | URL | Action | Description / توضیحات | Role |
|--------|-----|--------|----------------------|------|
| GET | `/reservations` | `index()` | Reservations list / لیست رزروها | all |
| GET | `/reservations/create` | `create()` | New reservation form / فرم رزرو جدید | operator, admin |
| POST | `/reservations` | `store()` | Save reservation / ذخیره رزرو | operator, admin |
| GET | `/reservations/{id}` | `show()` | Reservation details / جزئیات رزرو | all |
| GET | `/reservations/{id}/edit` | `edit()` | Edit reservation form / فرم ویرایش رزرو | operator, admin |
| PUT | `/reservations/{id}` | `update()` | Update reservation / بروزرسانی رزرو | operator, admin |
| DELETE | `/reservations/{id}` | `destroy()` | Cancel reservation / لغو رزرو | operator, admin |
| POST | `/reservations/{id}/check-in` | `checkIn()` | Check-in / چک‌این | operator, admin |
| POST | `/reservations/{id}/check-out` | `checkOut()` | Check-out / چک‌اوت | operator, admin |

**Middleware:** `auth`

---

### 3.7 Meal Management (`MealController`)

| Method | URL | Action | Description / توضیحات | Role |
|--------|-----|--------|----------------------|------|
| GET | `/meals` | `index()` | Meals list / لیست وعده‌ها | all |
| POST | `/meals` | `store()` | Record meal / ثبت وعده | operator, admin |
| PUT | `/meals/{id}` | `update()` | Update meal / بروزرسانی وعده | operator, admin |
| DELETE | `/meals/{id}` | `destroy()` | Delete meal / حذف وعده | operator, admin |

**Middleware:** `auth`

---

### 3.8 Cleaning Management (`CleaningController`)

| Method | URL | Action | Description / توضیحات | Role |
|--------|-----|--------|----------------------|------|
| GET | `/cleaning` | `index()` | Cleaning logs / لیست نظافت‌ها | all |
| POST | `/cleaning` | `store()` | Record cleaning / ثبت نظافت | cleaning_staff, admin |
| GET | `/cleaning/pending` | `pending()` | Needs cleaning list / لیست نیازمند نظافت | cleaning_staff, admin |

**Middleware:** `auth`

---

### 3.9 Maintenance Management (`MaintenanceController`)

| Method | URL | Action | Description / توضیحات | Role |
|--------|-----|--------|----------------------|------|
| GET | `/maintenance` | `index()` | Maintenance list / لیست تعمیرات | all |
| GET | `/maintenance/create` | `create()` | Record request form / فرم ثبت تعمیر | all |
| POST | `/maintenance` | `store()` | Record request / ثبت تعمیر | all |
| GET | `/maintenance/{id}` | `show()` | Request details / جزئیات تعمیر | all |
| PUT | `/maintenance/{id}` | `update()` | Update status / بروزرسانی وضعیت | maintenance_staff, admin |
| PUT | `/maintenance/{id}/assign` | `assign()` | Assign to staff / تخصیص به تعمیرکار | admin |

**Middleware:** `auth`

---

### 3.10 Reports (`ReportController`)

| Method | URL | Action | Description / توضیحات | Role |
|--------|-----|--------|----------------------|------|
| GET | `/reports` | `index()` | Reports page / صفحه گزارش‌ها | manager, admin |
| GET | `/reports/reservations` | `reservations()` | Reservations report / گزارش رزروها | manager, admin |
| GET | `/reports/occupancy` | `occupancy()` | Occupancy report / گزارش اشغال | manager, admin |
| GET | `/reports/meals` | `meals()` | Meals report / گزارش وعده‌ها | manager, admin |
| GET | `/reports/cleaning` | `cleaning()` | Cleaning report / گزارش نظافت | manager, admin |
| GET | `/reports/maintenance` | `maintenance()` | Maintenance report / گزارش تعمیرات | manager, admin |

**Middleware:** `auth`, `role:manager,admin`

**Output:** Excel/PDF with advanced filters / اکسل/PDF با فیلتر پیشرفته

---

## ✅ 4. Validation Rules / قوانین اعتبارسنجی

### 4.1 Personnel Import Validation / اعتبارسنجی Import پرسنل

```php
[
    'file' => 'required|mimes:xlsx,xls|max:2048',
    // In file / در فایل:
    'استخدامی' => 'required|unique:personnel,employment_code',
    'نام' => 'required|string|max:100',
    'نام خانوادگی' => 'required|string|max:100',
    'سال تولد' => 'required|integer|min:1300|max:1400',
    'ماه تولد' => 'required|integer|min:1|max:12',
    'روز تولد' => 'required|integer|min:1|max:31',
    'کد ملی' => 'required|digits:10|unique:personnel,national_code',
    'وضعیت خدمت' => 'required|in:فعال,بازنشسته,فوتی,اخراج,انتقال',
    'جنسیت' => 'required|in:مرد,زن',
]
```

### 4.2 Reservation Validation / اعتبارسنجی رزرو

```php
[
    'admission_type_id' => 'required|exists:admission_types,id',
    'personnel_id' => 'required_without:guest_id|exists:personnel,id',
    'guest_id' => 'required_without:personnel_id|exists:guests,id',
    'room_id' => 'required|exists:rooms,id',
    'bed_ids' => 'required|array|min:1|max:6',
    'bed_ids.*' => 'exists:beds,id',
    'check_in_date' => 'required|date',
    'check_out_date' => 'required|date|after:check_in_date',
]
```

**Custom Validation:**
- Beds must be from selected room / تخت‌ها باید از اتاق انتخاب شده باشند
- Beds must not be reserved for those dates / تخت‌ها نباید در آن تاریخ‌ها رزرو شده باشند
- Personnel must be active (`is_active = 1`) / پرسنل باید فعال باشد

### 4.3 Check-in Validation / اعتبارسنجی چک‌این

```php
[
    'actual_check_in' => 'required|date',
]
```

**Business Logic:**
- Status changes from `pending`/`confirmed` to `checked_in` / وضعیت به چک‌این شده تغییر می‌کند
- Related beds status becomes `occupied` / تخت‌ها اشغال می‌شوند

### 4.4 Check-out Validation / اعتبارسنجی چک‌اوت

```php
[
    'actual_check_out' => 'required|date|after:actual_check_in',
]
```

**Business Logic:**
- Status changes to `checked_out` / وضعیت به خارج شده تغییر می‌کند
- Related beds status becomes `needs_cleaning` / تخت‌ها نیاز به نظافت پیدا می‌کنند

---

## 🔄 5. User Flows / جریان کاربر

### 5.1 Import Personnel from Excel / ورود پرسنل از اکسل

**English:**
1. System admin enters `/personnel/import`
2. Uploads Excel file
3. System validates:
   - Column format
   - Required data
   - Unique employment code & national ID
4. On success:
   - New personnel added
   - Existing personnel updated
   - `is_active` set based on employment status
5. Import result report displayed

**فارسی:**
1. مدیر سیستم وارد `/personnel/import` می‌شود
2. فایل Excel را آپلود می‌کند
3. سیستم اعتبارسنجی می‌کند:
   - فرمت ستون‌ها
   - داده‌های اجباری
   - یکتایی کد استخدامی و کد ملی
4. در صورت موفقیت:
   - پرسنل‌های جدید اضافه می‌شوند
   - پرسنل‌های موجود بروزرسانی می‌شوند
   - `is_active` بر اساس وضعیت خدمت تنظیم می‌شود
5. گزارش نتیجه Import نمایش داده می‌شود

---

### 5.2 Bed/Room Reservation / رزرو تخت/اتاق

**English:**
1. Operator enters `/reservations/create`
2. Selects admission type
3. Selects personnel or guest
4. Selects unit and room
5. Selects desired beds (1-6 beds)
6. Enters check-in and check-out dates
7. System validates:
   - Personnel is active
   - Beds are available for those dates
   - Beds are from selected room
8. Reservation saved with `status = 'pending'`
9. Beds don't become 'reserved' until check-in

**فارسی:**
1. اپراتور وارد `/reservations/create` می‌شود
2. نوع پذیرش را انتخاب می‌کند
3. پرسنل یا مهمان را انتخاب می‌کند
4. واحد و اتاق را انتخاب می‌کند
5. تخت‌های موردنظر (1-6 تخت) را انتخاب می‌کند
6. تاریخ ورود و خروج را وارد می‌کند
7. سیستم موارد زیر را بررسی می‌کند:
   - پرسنل فعال باشد
   - تخت‌ها در آن تاریخ خالی باشند
   - تخت‌ها از اتاق انتخاب شده باشند
8. رزرو با `status = 'pending'` ذخیره می‌شود
9. تخت‌ها تا زمان چک‌این 'رزرو شده' نمی‌شوند

---

### 5.3 Check-in / چک‌این

**English:**
1. Operator enters reservation details
2. Clicks "Check-in" button
3. Actual check-in time recorded
4. Reservation `status` changes to `checked_in`
5. Related beds `status` becomes `occupied`
6. Default meals created

**فارسی:**
1. اپراتور وارد جزئیات رزرو می‌شود
2. دکمه "چک‌این" را می‌زند
3. زمان واقعی ورود ثبت می‌شود
4. `status` رزرو به `checked_in` تغییر می‌کند
5. تخت‌های مربوطه `status = 'occupied'` می‌شوند
6. وعده‌های غذایی پیش‌فرض ایجاد می‌شوند

---

### 5.4 Check-out / چک‌اوت

**English:**
1. Operator enters reservation details
2. Clicks "Check-out" button
3. Actual check-out time recorded
4. Reservation `status` changes to `checked_out`
5. Related beds `status` becomes `needs_cleaning`

**فارسی:**
1. اپراتور وارد جزئیات رزرو می‌شود
2. دکمه "چک‌اوت" را می‌زند
3. زمان واقعی خروج ثبت می‌شود
4. `status` رزرو به `checked_out` تغییر می‌کند
5. تخت‌های مربوطه `status = 'needs_cleaning'` می‌شوند

---

### 5.5 Record Cleaning / ثبت نظافت

**English:**
1. Cleaning staff enters `/cleaning/pending`
2. Sees list of beds needing cleaning
3. After cleaning, marks the bed
4. Selects cleaning type (daily/weekly/deep)
5. Writes note (optional)
6. Records
7. Bed `status` changes to `available`
8. `cleaned_at` updated

**فارسی:**
1. مسئول نظافت وارد `/cleaning/pending` می‌شود
2. لیست تخت‌های نیازمند نظافت را می‌بیند
3. پس از نظافت، تخت را علامت می‌زند
4. نوع نظافت (روزانه/هفتگی/عمیق) را انتخاب می‌کند
5. یادداشت می‌نویسد (اختیاری)
6. ثبت می‌کند
7. `status` تخت به `available` تغییر می‌کند
8. `cleaned_at` بروزرسانی می‌شود

---

### 5.6 Record Maintenance / ثبت تعمیر

**English:**
1. User (any role) sees problem in bed/room
2. Enters `/maintenance/create`
3. Selects bed or room
4. Enters problem description and priority
5. Request saved with `status = 'pending'`
6. Bed `status` changes to `under_maintenance`
7. Admin assigns maintenance staff
8. Staff changes `status` to `in_progress`
9. After completion, `status` changes to `completed`
10. Bed `status` changes to `needs_cleaning`

**فارسی:**
1. کاربر (هر نقش) مشکلی در تخت/اتاق می‌بیند
2. وارد `/maintenance/create` می‌شود
3. تخت یا اتاق را انتخاب می‌کند
4. شرح مشکل و اولویت را وارد می‌کند
5. درخواست با `status = 'pending'` ذخیره می‌شود
6. `status` تخت به `under_maintenance` تغییر می‌کند
7. مدیر سیستم تعمیرکار تخصیص می‌دهد
8. تعمیرکار `status` را به `in_progress` تغییر می‌دهد
9. پس از اتمام، `status` به `completed` تغییر می‌کند
10. `status` تخت به `needs_cleaning` تغییر می‌کند

---

### 5.7 Reporting / گزارش‌گیری

**English:**
1. Dormitory manager enters `/reports`
2. Selects report type
3. Applies filters:
   - Date range
   - Unit
   - Room
   - Admission type
   - Status
4. Selects output format (Excel/PDF/Display)
5. Report generated and displayed/downloaded

**فارسی:**
1. مدیر خوابگاه وارد `/reports` می‌شود
2. نوع گزارش را انتخاب می‌کند
3. فیلترها را اعمال می‌کند:
   - بازه تاریخ
   - واحد
   - اتاق
   - نوع پذیرش
   - وضعیت
4. فرمت خروجی (Excel/PDF/نمایش) را انتخاب می‌کند
5. گزارش تولید و نمایش/دانلود می‌شود

---

## 🎨 6. UI Components / اجزای رابط کاربری

### 6.1 Schematic Dashboard / داشبورد شماتیک

**Layout:**
```
┌─────────────────────────────────────────┐
│  📊 Overall Stats / آمار کلی            │
│  Total: 132 | Available: 85 | Occupied: 30 │
│  خالی: 85 | اشغال: 30 | نظافت: 12 | تعمیر: 5 │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│  🏢 East Section (Units 1-12) / بخش شرقی │
│  ┌────┬────┬────┬────┬────┬────┐        │
│  │ W1 │ W2 │ W3 │ W4 │ W5 │ W6 │        │
│  │ 🟢🟢│🔴🟢│🟡🟢│🟢🟢│🟢🟢│🔵🟢│        │
│  │ 🟢🟢│🟢🟢│🟢🟢│🟢🟢│🟢🟢│🟢🟢│        │
│  │ 🟢🟢│🟢🟢│🟢🟢│🟢🟢│🟢🟢│🟢🟢│        │
│  └────┴────┴────┴────┴────┴────┘        │
│  ... (Units 7-12 / واحدهای 7-12)        │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│  🏢 West Section (Units 13-22) / بخش غربی│
│  ... (Similar / مشابه بخش شرقی)         │
└─────────────────────────────────────────┘

Colors / رنگ‌ها:
🟢 Available / خالی (available)
🔴 Occupied / اشغال (occupied)
🟡 Needs Cleaning / نیاز به نظافت (needs_cleaning)
🔵 Under Maintenance / در حال تعمیر (under_maintenance)
```

**Filters / فیلترها:**
- Admission type / نوع پذیرش
- Unit / واحد
- Bed status / وضعیت تخت

---

### 6.2 Reservation Form / فرم رزرو

**Fields / فیلدها:**
1. Admission type (dropdown) / نوع پذیرش
2. Select personnel/guest (search/select) / انتخاب پرسنل/مهمان
3. Select unit (dropdown) / انتخاب واحد
4. Select room (dropdown) / انتخاب اتاق
5. Select beds (checkbox - multiple) / انتخاب تخت‌ها
   - Visual display of available beds / نمایش تصویری تخت‌های خالی
6. Check-in date (Persian datepicker) / تاریخ ورود
7. Check-out date (Persian datepicker) / تاریخ خروج
8. Notes (textarea) / یادداشت

**Real-time Validation:**
- Show available beds / نمایش تخت‌های در دسترس
- Warning for reserved beds / هشدار برای تخت‌های رزرو شده
- Warning for inactive personnel / هشدار برای پرسنل غیرفعال

---

### 6.3 Personnel Import Page / صفحه Import پرسنل

**UI:**
1. Download sample Excel file button / دکمه دانلود نمونه فایل Excel
2. Upload file / آپلود فایل
3. Data preview / نمایش پیش‌نمایش داده‌ها
4. Confirm Import button / دکمه تایید Import
5. Display results / نمایش نتایج:
   - Added count / تعداد افزوده شده
   - Updated count / تعداد بروزرسانی شده
   - Error count / تعداد خطا
   - Error list with details / لیست خطاها با جزئیات

---

## 🔐 7. Authorization & Permissions / مجوزها و دسترسی‌ها

### 7.1 Role-Based Access Control (RBAC)

| Role / نقش | Permissions / دسترسی‌ها |
|-----------|------------------------|
| **admin** | Full access to all sections / دسترسی کامل به همه بخش‌ها |
| **operator** | Reservations, check-in/out, guests, meals / رزرو، چک‌این/اوت، مهمان‌ها، وعده‌ها |
| **manager** | View reports, dashboard, reservation list / مشاهده گزارش‌ها، داشبورد، لیست رزروها |
| **cleaning_staff** | Record cleaning, view needs cleaning list / ثبت نظافت، مشاهده لیست نیازمند نظافت |
| **maintenance_staff** | Manage maintenance, update status / مدیریت تعمیرات، بروزرسانی وضعیت |

---

## 📱 8. Frontend Technology / تکنولوژی Frontend

- **Blade Templates** (Persian, RTL / فارسی، راست‌چین)
- **Bootstrap 5** or **Tailwind CSS** (RTL)
- **Persian Datepicker** (Jalali calendar / تقویم شمسی)
- **Select2** or **Choices.js** (Personnel/guest search / جستجوی پرسنل/مهمان)
- **DataTables** (Tables with filter & pagination / جداول با فیلتر و صفحه‌بندی)
- **Chart.js** or **ApexCharts** (Charts in reports / نمودارها در گزارش‌ها)
- **Alpine.js** or **Vue.js** (Simple interactions / تعاملات ساده)

---

## 🗄️ 9. Database Seeding / مقداردهی اولیه دیتابیس

### 9.1 Users (Default / پیش‌فرض)

```php
[
    ['name' => 'مدیر سیستم', 'email' => 'admin@bank.ir', 'role' => 'admin'],
    ['name' => 'اپراتور', 'email' => 'operator@bank.ir', 'role' => 'operator'],
    ['name' => 'مدیر خوابگاه', 'email' => 'manager@bank.ir', 'role' => 'manager'],
]
```

### 9.2 Admission Types / انواع پذیرش

```php
[
    ['name' => 'دوره کلاسی', 'code' => 'class', 'has_reservation' => 0],
    ['name' => 'همایش', 'code' => 'conference', 'has_reservation' => 0],
    ['name' => 'ماموریت اداری', 'code' => 'mission', 'has_reservation' => 1, 'reservation_days_before' => 3],
]
```

### 9.3 Buildings, Units, Rooms, Beds

```php
Building::create(['name' => 'خوابگاه اصلی', 'code' => 'MAIN']);

// 22 units / 22 واحد
for ($i = 1; $i <= 22; $i++) {
    $unit = Unit::create([
        'building_id' => 1,
        'number' => $i,
        'section' => $i <= 12 ? 'east' : 'west',
    ]);

    // Each unit 1 room (6 beds) / هر واحد 1 اتاق (6 نفره)
    $room = Room::create([
        'unit_id' => $unit->id,
        'number' => 1,
        'capacity' => 6,
    ]);

    // 6 beds per room / 6 تخت برای هر اتاق
    for ($b = 1; $b <= 6; $b++) {
        Bed::create([
            'room_id' => $room->id,
            'number' => $b,
            'status' => 'available',
        ]);
    }
}
```

---

## 📊 10. Performance & Optimization / عملکرد و بهینه‌سازی

- **Eager Loading:** Prevent N+1 queries / جلوگیری از N+1 query
- **Caching:** Cache units and rooms list / کش کردن لیست واحدها و اتاق‌ها
- **Indexing:** Proper indexes on tables / ایندکس‌های مناسب روی جداول
- **Pagination:** Pagination in lists / صفحه‌بندی در لیست‌ها
- **Queue:** For large personnel import / برای Import پرسنل با حجم بالا

---

## 🔒 11. Security / امنیت

- **CSRF Protection:** In all forms / در تمام فرم‌ها
- **SQL Injection Prevention:** Using Eloquent ORM / استفاده از Eloquent ORM
- **XSS Prevention:** Escape outputs / Escape خروجی‌ها
- **Password Hashing:** bcrypt
- **Activity Logging:** Record all sensitive operations / ثبت تمام عملیات حساس
- **Backup:** Daily automatic backup / بک‌آپ خودکار روزانه

---

## 📦 12. Dependencies / وابستگی‌ها

```json
{
    "php": "^8.2",
    "laravel/framework": "^11.0",
    "maatwebsite/excel": "^3.1",
    "barryvdh/laravel-dompdf": "^2.0",
    "spatie/laravel-permission": "^6.0",
    "morilog/jalali": "^3.4"
}
```

---

## ✅ OpenSpec Section 1 Complete / تکمیل بخش 1 از OpenSpec

This section includes / این بخش شامل:
✅ Complete database models / مدل‌های دیتابیس کامل
✅ Relationships / روابط
✅ Endpoints & Controllers
✅ Validation Rules / قوانین اعتبارسنجی
✅ User Flows / جریان کاربر
✅ UI Components / اجزای UI
✅ Authorization / مجوزها
✅ Frontend Technology / تکنولوژی Frontend
✅ Database Seeding / مقداردهی اولیه
✅ Performance / عملکرد
✅ Security / امنیت

---

**Do you approve this OpenSpec? / آیا این OpenSpec را تایید می‌کنید؟**

If approved, I will move to next step: **Laravel Project Structure** / اگر تایید کردید، به مرحله بعد می‌روم: **ساختار پروژه Laravel**

Waiting for your command. / منتظر دستور شما هستم. 🎯
