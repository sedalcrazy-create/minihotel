@extends('layouts.app')

@section('title', 'مدیریت پرسنل')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>لیست پرسنل سازمان</h2>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('personnel.template') }}" class="btn btn-secondary" title="تمپلیت خالی با نمونه داده">📄 تمپلیت</a>
        <a href="{{ route('personnel.update-template') }}" class="btn btn-secondary" title="دانلود داده‌های فعلی برای ویرایش" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">📝 تمپلیت اپدیت</a>
        <button onclick="document.getElementById('importFile').click()" class="btn btn-primary">📤 ورود اکسل</button>
        <form id="importForm" action="{{ route('personnel.import') }}" method="POST" enctype="multipart/form-data" style="display: none;">
            @csrf
            <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" onchange="document.getElementById('importForm').submit()">
        </form>
        <button onclick="document.getElementById('bimehFile').click()" class="btn btn-primary" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);" title="همگام‌سازی با فایل بیمه ماهانه">🏦 همگام‌سازی بیمه</button>
        <form id="bimehForm" action="{{ route('personnel.sync-bimeh') }}" method="POST" enctype="multipart/form-data" style="display: none;">
            @csrf
            <input type="file" id="bimehFile" name="file" accept=".xlsx,.xls" onchange="confirmBimehSync()">
        </form>
        <a href="{{ route('personnel.create') }}" class="btn btn-primary">+ افزودن پرسنل</a>
    </div>
</div>

<div class="card" style="background: linear-gradient(135deg, rgba(249, 108, 8, 0.05) 0%, rgba(255,255,255,0.95) 100%); border-right: 4px solid #f96c08;">
    <div style="display: flex; align-items: center; gap: 15px;">
        <div style="font-size: 36px;">📋</div>
        <div>
            <h3 style="margin-bottom: 8px; color: #f96c08;">راهنمای کار با فایل اکسل</h3>
            <p style="margin: 5px 0; color: #6b7280;">📄 <strong>تمپلیت:</strong> فایل خالی با نمونه داده - برای افزودن پرسنل جدید</p>
            <p style="margin: 5px 0; color: #6b7280;">📝 <strong>تمپلیت اپدیت:</strong> دانلود تمام داده‌های فعلی - برای ویرایش دسته‌جمعی</p>
            <p style="margin: 5px 0; color: #6b7280;">📤 <strong>ورود اکسل:</strong> آپلود فایل - هم برای افزودن و هم برای بروزرسانی</p>
            <p style="margin: 5px 0; color: #6b7280;">🏦 <strong>همگام‌سازی بیمه:</strong> همگام‌سازی ماهانه با فایل بیمه (Bimeh_*.xlsx)</p>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 20px;">
    <form method="GET" action="{{ route('personnel.index') }}" style="display: flex; gap: 10px; align-items: center;">
        <div style="flex: 1; position: relative;">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="🔍 جستجو با کد پرسنلی، کد ملی یا نام و نام خانوادگی..."
                style="width: 100%; padding: 12px 45px 12px 15px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px; transition: all 0.3s;"
                onfocus="this.style.borderColor='#f96c08'; this.style.boxShadow='0 0 0 3px rgba(249, 108, 8, 0.1)'"
                onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
            >
            @if(request('search'))
                <a href="{{ route('personnel.index') }}" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; text-decoration: none; font-size: 20px;" title="پاک کردن جستجو">×</a>
            @endif
        </div>
        <button type="submit" class="btn btn-primary" style="white-space: nowrap;">جستجو</button>
    </form>
    @if(request('search'))
        <div style="margin-top: 15px; padding: 10px 15px; background: #fef3c7; border-right: 3px solid #f59e0b; border-radius: 8px; font-size: 14px;">
            نتایج جستجو برای: <strong>{{ request('search') }}</strong>
            ({{ $personnel->total() }} مورد یافت شد)
        </div>
    @endif
</div>

<div class="card">
    @if($personnel->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>کد پرسنلی</th>
                    <th>نام و نام خانوادگی</th>
                    <th>کد ملی</th>
                    <th>دپارتمان</th>
                    <th>محل خدمت</th>
                    <th>وضعیت استخدام</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($personnel as $person)
                    <tr>
                        <td><strong>{{ $person->employment_code }}</strong></td>
                        <td>{{ $person->full_name }}</td>
                        <td>{{ $person->national_code }}</td>
                        <td>{{ $person->department ?? '-' }}</td>
                        <td>{{ $person->service_location ?? '-' }}</td>
                        <td>
                            <span class="badge badge-confirmed">{{ $person->employment_status }}</span>
                        </td>
                        <td>
                            <a href="{{ route('personnel.show', $person) }}" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">مشاهده</a>
                            <a href="{{ route('personnel.edit', $person) }}" class="btn btn-secondary" style="padding: 5px 10px; font-size: 12px;">ویرایش</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            {{ $personnel->appends(request()->query())->links() }}
        </div>
    @else
        <p style="text-align: center; color: #6b7280; padding: 40px;">هیچ پرسنلی ثبت نشده است.</p>
        <div class="text-center">
            <a href="{{ route('personnel.create') }}" class="btn btn-primary">ثبت اولین پرسنل</a>
        </div>
    @endif
</div>

<script>
function confirmBimehSync() {
    const confirmed = confirm(
        '⚠️ هشدار: همگام‌سازی بیمه\n\n' +
        'این عملیات:\n' +
        '• پرسنل جدید را اضافه می‌کند\n' +
        '• اطلاعات پرسنل موجود را آپدیت می‌کند\n' +
        '• پرسنلی که در فایل نیست را غیرفعال می‌کند\n\n' +
        'آیا مطمئن هستید؟'
    );

    if (confirmed) {
        // نمایش پیام در حال پردازش
        const overlay = document.createElement('div');
        overlay.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; flex-direction: column;';
        overlay.innerHTML = '<div style="text-align: center;"><div style="font-size: 48px; margin-bottom: 20px;">⏳</div><div>در حال همگام‌سازی با فایل بیمه...</div><div style="font-size: 14px; margin-top: 10px; opacity: 0.8;">لطفاً صبر کنید، این کار ممکن است چند دقیقه طول بکشد.</div></div>';
        document.body.appendChild(overlay);

        document.getElementById('bimehForm').submit();
    }
}
</script>
@endsection
