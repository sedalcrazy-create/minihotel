# مشخصات فنی نسخه 2.0 - سیستم مدیریت خوابگاه

## 1. ماژول دوره‌ها (Courses)

### 1.1 جدول `courses`
```sql
- id
- name (نام دوره)
- code (کد دوره - یکتا)
- description (توضیحات)
- start_date (تاریخ شروع)
- end_date (تاریخ پایان)
- capacity (ظرفیت - اختیاری)
- location (محل برگزاری - اختیاری)
- is_active
- created_by (کاربر ایجادکننده)
- timestamps
```

### 1.2 قوانین تجاری دوره‌ها
- پذیرش دوره کلاسی فقط برای دوره‌های **45 روز آینده** قابل انتخاب
- دوره‌های گذشته نمایش داده نمی‌شوند
- رزروهای دوره کلاسی تا **20 روز پس از پایان دوره** غیرقابل ویرایش
- نمایش تقویمی دوره‌ها با رنگ‌بندی وضعیت

---

## 2. ماژول همایش‌ها (Conferences)

### 2.1 جدول `conferences`
```sql
- id
- name (نام همایش)
- code (کد همایش - یکتا)
- description (توضیحات)
- start_date (تاریخ شروع)
- end_date (تاریخ پایان)
- organizer (برگزارکننده)
- capacity (ظرفیت - اختیاری)
- is_active
- created_by
- timestamps
```

### 2.2 قوانین تجاری همایش‌ها
- مشابه دوره‌ها: فقط همایش‌های **45 روز آینده** قابل انتخاب
- همایش‌های گذشته نمایش داده نمی‌شوند
- نمایش تقویمی همایش‌ها

---

## 3. تغییرات جدول رزروها (Reservations)

### 3.1 فیلدهای جدید
```sql
ALTER TABLE reservations ADD:
- course_id (FK به courses - nullable)
- conference_id (FK به conferences - nullable)
- edit_locked_until (تاریخ قفل ویرایش - محاسبه‌شده)
```

### 3.2 قوانین
- اگر `admission_type = دوره کلاسی` → `course_id` اجباری
- اگر `admission_type = همایش` → `conference_id` اجباری
- اگر `admission_type = ماموریت اداری` → هر دو null

### 3.3 قفل ویرایش
```php
// رزرو قابل ویرایش است اگر:
$canEdit = $reservation->course
    ? Carbon::now()->lt($reservation->course->end_date->addDays(20))
    : true;
```

---

## 4. محدودیت جنسیتی واحدها

### 4.1 تغییرات جدول `units`
```sql
ALTER TABLE units ADD:
- gender_restriction ENUM('male', 'female', 'mixed') DEFAULT 'mixed'
```

### 4.2 تغییرات جدول `guests`
```sql
ALTER TABLE guests ADD:
- gender ENUM('male', 'female') DEFAULT 'male'
```

### 4.3 قوانین تجاری
1. هنگام پذیرش:
   - بررسی جنسیت فرد (پرسنل یا مهمان)
   - بررسی `gender_restriction` واحد
   - اگر واحد `mixed` است و اولین پذیرش خانم باشد → واحد `female` می‌شود
   - اگر واحد `mixed` است و اولین پذیرش آقا باشد → واحد `male` می‌شود

2. اعتبارسنجی:
   - آقا در واحد `female` ❌
   - خانم در واحد `male` ❌
   - وقتی همه تخت‌های واحد خالی شد → `gender_restriction = mixed`

### 4.4 افکت‌های UI واحدهای بانوان (Kawaii)
```css
.unit-female {
    background: linear-gradient(135deg, #fce4ec, #f8bbd9);
    border: 2px solid #ec407a;
    box-shadow: 0 0 20px rgba(236, 64, 122, 0.3);
}

.unit-female::before {
    content: '🌸';
    animation: float 2s ease-in-out infinite;
}

.unit-female .bed-available {
    background: #f48fb1;
    border-color: #ec407a;
}

/* انیمیشن‌های kawaii */
@keyframes sparkle { ... }
@keyframes float { ... }
```

---

## 5. بهبود سیستم تعمیرات

### 5.1 تغییرات جدول `maintenance_requests`
```sql
ALTER TABLE maintenance_requests ADD:
- unit_id (FK به units - nullable)
- start_date DATE
- end_date DATE
```

### 5.2 قوانین
- تعمیرات می‌تواند برای: واحد، اتاق، یا تخت باشد
- تاریخ شروع و پایان اجباری
- در بازه تعمیرات، پذیرش در آن واحد/اتاق/تخت ممنوع

---

## 6. بهبود چک‌این/چک‌اوت

### 6.1 وضعیت فعلی ✅
```sql
-- موجود در reservations:
actual_check_in TIMESTAMP
actual_check_out TIMESTAMP
status ENUM('pending','confirmed','checked_in','checked_out','cancelled')
```

### 6.2 سناریوهای استفاده
| سناریو | check_in_date | check_out_date | actual_check_in | actual_check_out |
|--------|---------------|----------------|-----------------|------------------|
| رزرو ۷ روزه | 1403/10/01 | 1403/10/07 | - | - |
| ورود روز اول | 1403/10/01 | 1403/10/07 | 1403/10/01 10:30 | - |
| خروج زودهنگام روز ۳ | 1403/10/01 | 1403/10/07 | 1403/10/01 10:30 | 1403/10/03 14:00 |

### 6.3 منطق آزادسازی تخت
```php
// تخت آزاد می‌شود وقتی:
// 1. actual_check_out پر شود
// 2. یا check_out_date گذشته باشد و actual_check_in خالی باشد (no-show)
```

---

## 7. لاگ سیستم (Activity Logs)

### 7.1 وضعیت فعلی ✅
جدول `activity_logs` موجود است.

### 7.2 رویدادهای جدید برای لاگ
- ایجاد/ویرایش/حذف دوره
- ایجاد/ویرایش/حذف همایش
- تغییر جنسیت واحد
- قفل شدن ویرایش رزرو
- چک‌این/چک‌اوت زودهنگام

---

## 8. نمای تقویم (Calendar View)

### 8.1 ویژگی‌ها
- نمایش دوره‌ها با رنگ آبی
- نمایش همایش‌ها با رنگ سبز
- نمایش رزروها با رنگ نارنجی
- فیلتر بر اساس نوع
- کلیک روی رویداد → نمایش جزئیات

### 8.2 کتابخانه پیشنهادی
- FullCalendar.js (با پشتیبانی Jalali)

---

## 9. خلاصه مایگریشن‌های جدید

```
2024_01_01_000016_create_courses_table.php
2024_01_01_000017_create_conferences_table.php
2024_01_01_000018_add_course_conference_to_reservations.php
2024_01_01_000019_add_gender_to_units.php
2024_01_01_000020_add_gender_to_guests.php
2024_01_01_000021_add_unit_and_dates_to_maintenance.php
```

---

## 10. خلاصه کنترلرهای جدید

```
CourseController.php          - CRUD دوره‌ها
ConferenceController.php      - CRUD همایش‌ها
CalendarController.php        - API تقویم
```

---

## 11. روت‌های جدید

```php
// دوره‌ها
Route::resource('courses', CourseController::class);
Route::get('courses/calendar', [CourseController::class, 'calendar']);

// همایش‌ها
Route::resource('conferences', ConferenceController::class);
Route::get('conferences/calendar', [ConferenceController::class, 'calendar']);

// API تقویم
Route::get('api/calendar/events', [CalendarController::class, 'events']);
```

---

## 12. اولویت‌بندی پیاده‌سازی

| اولویت | ماژول | پیچیدگی |
|--------|-------|---------|
| 1 | جدول و CRUD دوره‌ها | متوسط |
| 2 | جدول و CRUD همایش‌ها | متوسط |
| 3 | لینک رزرو به دوره/همایش | کم |
| 4 | محدودیت جنسیتی واحدها | متوسط |
| 5 | افکت kawaii واحدهای بانوان | کم |
| 6 | بهبود تعمیرات (واحد + تاریخ) | کم |
| 7 | نمای تقویم | زیاد |
| 8 | قفل ویرایش ۲۰ روزه | کم |

---

## 13. نکات فنی

### Validation Rules
```php
// پذیرش دوره کلاسی
'course_id' => 'required_if:admission_type_id,1|exists:courses,id'

// پذیرش همایش
'conference_id' => 'required_if:admission_type_id,2|exists:conferences,id'

// بررسی جنسیت
// در ReservationController@store
$unit = Unit::find($request->unit_id);
$person = $request->personnel_id ? Personnel::find($request->personnel_id) : Guest::find($request->guest_id);

if ($unit->gender_restriction !== 'mixed' && $unit->gender_restriction !== $person->gender) {
    return back()->withErrors(['unit' => 'این واحد مخصوص ' . ($unit->gender_restriction === 'female' ? 'بانوان' : 'آقایان') . ' است.']);
}
```

---

## 14. آپدیت ماهانه پرسنل از اکسل (Bimeh)

### 14.1 ساختار فایل اکسل بیمه
فایل ماهانه بیمه (`Bimeh_YYYYMMDD.xlsx`) شامل اطلاعات کامل پرسنل است.

#### ستون‌های کلیدی:
| ستون | نام فارسی | کاربرد |
|------|-----------|--------|
| A | کد استخدام | `employment_code` - کلید یکتا |
| B | نام | `first_name` |
| C | نام خانوادگی | `last_name` |
| D | کد ملی | `national_code` |
| E | نام پدر | `father_name` |
| F-H | تاریخ تولد | `birth_year/month/day` |
| S | **افراد اصلی/غیراصلی** | فیلتر پذیرش ⚠️ |
| AA | **وضعیت خدمت همکار** | فیلتر پذیرش ⚠️ |
| ? | جنسیت | `gender` |
| ? | محل خدمت | `service_location` |
| ? | اداره/شعبه | `department` |

### 14.2 قوانین پذیرش بر اساس فایل بیمه

```php
// فقط این افراد امکان پذیرش دارند:
$canAdmit =
    $personnel->person_type === 'اصلی'           // ستون S
    && $personnel->colleague_status === 'شاغل'; // ستون AA
```

### 14.3 تغییرات جدول `personnel`
```sql
ALTER TABLE personnel ADD:
- person_type ENUM('اصلی', 'غیراصلی') DEFAULT 'اصلی'
- colleague_status VARCHAR(50) -- وضعیت خدمت همکار
- last_sync_date DATE -- تاریخ آخرین همگام‌سازی
```

### 14.4 فرآیند آپدیت ماهانه

#### گردش کار:
```
1. آپلود فایل اکسل جدید
2. پارس فایل و استخراج داده‌ها
3. مقایسه با دیتابیس فعلی:
   - پرسنل جدید → INSERT
   - پرسنل موجود → UPDATE (وضعیت خدمت، جنسیت، ...)
   - پرسنل حذف‌شده → is_active = false
4. لاگ تغییرات
5. گزارش خلاصه
```

#### API/Route:
```php
Route::post('personnel/sync-bimeh', [PersonnelController::class, 'syncFromBimeh'])
    ->name('personnel.sync-bimeh');
```

#### متد کنترلر:
```php
public function syncFromBimeh(Request $request)
{
    $request->validate(['file' => 'required|mimes:xlsx,xls']);

    $import = new BimehImport();
    Excel::import($import, $request->file('file'));

    return back()->with('success', sprintf(
        'همگام‌سازی انجام شد. جدید: %d، آپدیت: %d، غیرفعال: %d',
        $import->inserted,
        $import->updated,
        $import->deactivated
    ));
}
```

### 14.5 کلاس Import جدید

```php
// app/Imports/BimehImport.php
class BimehImport implements ToCollection, WithHeadingRow
{
    public int $inserted = 0;
    public int $updated = 0;
    public int $deactivated = 0;

    public function collection(Collection $rows)
    {
        $existingCodes = Personnel::pluck('id', 'employment_code')->toArray();
        $processedCodes = [];

        foreach ($rows as $row) {
            $code = $row['کد_استخدام']; // یا نام ستون انگلیسی
            $processedCodes[] = $code;

            $data = [
                'employment_code' => $code,
                'first_name' => $row['نام'],
                'last_name' => $row['نام_خانوادگی'],
                'national_code' => $row['کد_ملی'],
                'gender' => $row['جنسیت'] === 'زن' ? 'female' : 'male',
                'person_type' => $row['افراد_اصلی'], // ستون S
                'colleague_status' => $row['وضعیت_خدمت_همکار'], // ستون AA
                'last_sync_date' => now(),
                'is_active' => true,
            ];

            if (isset($existingCodes[$code])) {
                Personnel::where('employment_code', $code)->update($data);
                $this->updated++;
            } else {
                Personnel::create($data);
                $this->inserted++;
            }
        }

        // غیرفعال کردن پرسنل حذف‌شده از لیست
        $this->deactivated = Personnel::whereNotIn('employment_code', $processedCodes)
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }
}
```

### 14.6 اعتبارسنجی هنگام پذیرش

```php
// در ReservationController@store
$personnel = Personnel::findOrFail($request->personnel_id);

if ($personnel->person_type !== 'اصلی') {
    return back()->withErrors(['personnel' => 'این فرد جزو افراد اصلی نیست و امکان پذیرش ندارد.']);
}

if ($personnel->colleague_status !== 'شاغل') {
    return back()->withErrors(['personnel' => 'وضعیت خدمت این فرد شاغل نیست.']);
}
```

### 14.7 نمایش در UI

#### لیست پرسنل:
- نشانگر رنگی برای وضعیت پذیرش:
  - 🟢 قابل پذیرش (اصلی + شاغل)
  - 🔴 غیرقابل پذیرش
- فیلتر بر اساس وضعیت

#### فرم پذیرش:
- فقط پرسنل قابل پذیرش در dropdown نمایش داده شوند
- یا نمایش همه با غیرفعال کردن افراد غیرقابل پذیرش

### 14.8 گزارش همگام‌سازی

```php
// جدول personnel_sync_logs
- id
- file_name
- total_rows
- inserted_count
- updated_count
- deactivated_count
- synced_by (user_id)
- synced_at
- errors (JSON)
```

---

## 15. خلاصه کل تغییرات دیتابیس

### جداول جدید:
```
- courses
- conferences
- personnel_sync_logs
```

### تغییرات جداول موجود:
```
personnel:
  + person_type ENUM('اصلی', 'غیراصلی')
  + colleague_status VARCHAR(50)
  + last_sync_date DATE

reservations:
  + course_id FK
  + conference_id FK

units:
  + gender_restriction ENUM('male', 'female', 'mixed')

guests:
  + gender ENUM('male', 'female')

maintenance_requests:
  + unit_id FK
  + start_date DATE
  + end_date DATE
```

---

**تاریخ تهیه:** 1403/10/06
**نسخه:** 2.0-draft
