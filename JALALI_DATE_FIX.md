# مشکل تاریخ شمسی

## مشکل:
- فرم‌های ایجاد/ویرایش دوره و همایش از `<input type="date">` استفاده می‌کنند که میلادی است
- نیاز به تبدیل به date picker شمسی

## راه‌حل:

### 1. استفاده از کتابخانه Persian Datepicker
یکی از این دو کتابخانه:
- `persian-datepicker` (jQuery)
- `@majidh1/jalalidatepicker` (pure JS)

### 2. تغییرات لازم در views:

#### فایل‌های نیازمند تغییر:
- `resources/views/courses/create.blade.php`
- `resources/views/courses/edit.blade.php`
- `resources/views/conferences/create.blade.php`
- `resources/views/conferences/edit.blade.php`

#### تغییر input از:
```html
<input type="date" name="start_date" class="form-control" required>
```

#### به:
```html
<input type="text" name="start_date" class="form-control jalali-datepicker" required>
```

### 3. اضافه کردن assets به layout:

در `resources/views/layouts/app.blade.php`:

```html
<head>
    ...
    <link rel="stylesheet" href="https://unpkg.com/persian-datepicker@latest/dist/css/persian-datepicker.min.css">
</head>

<body>
    ...
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/persian-datepicker@latest/dist/js/persian-datepicker.min.js"></script>
    <script src="https://unpkg.com/persian-date@latest/dist/persian-date.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.jalali-datepicker').persianDatepicker({
                format: 'YYYY/MM/DD',
                autoClose: true,
                initialValue: false,
                observer: true,
                altField: '.jalali-datepicker-alt',
                altFormat: 'X', // timestamp
            });
        });
    </script>
</body>
```

### 4. تبدیل در Controller:

```php
use Morilog\Jalali\Jalalian;

// هنگام ذخیره:
$gregorianDate = Jalalian::fromFormat('Y/m/d', $request->start_date)->toCarbon();

// یا با helper verta:
$gregorianDate = verta()->parse($request->start_date)->datetime();
```

## وضعیت: 🔴 نیاز به اصلاح دارد
