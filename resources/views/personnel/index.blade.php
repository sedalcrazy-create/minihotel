@extends('layouts.app')

@section('title', 'مدیریت پرسنل')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>لیست پرسنل سازمان</h2>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('personnel.template') }}" class="btn btn-secondary" title="دانلود فایل نمونه با راهنما">📄 تمپلیت اکسل</a>
        <a href="{{ route('personnel.export') }}" class="btn btn-success">📥 خروجی اکسل</a>
        <button onclick="document.getElementById('importFile').click()" class="btn btn-primary">📤 ورود اکسل</button>
        <form id="importForm" action="{{ route('personnel.import') }}" method="POST" enctype="multipart/form-data" style="display: none;">
            @csrf
            <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" onchange="document.getElementById('importForm').submit()">
        </form>
        <a href="{{ route('personnel.create') }}" class="btn btn-primary">+ افزودن پرسنل</a>
    </div>
</div>

<div class="card" style="background: linear-gradient(135deg, rgba(249, 108, 8, 0.05) 0%, rgba(255,255,255,0.95) 100%); border-right: 4px solid #f96c08;">
    <div style="display: flex; align-items: center; gap: 15px;">
        <div style="font-size: 36px;">📋</div>
        <div>
            <h3 style="margin-bottom: 8px; color: #f96c08;">راهنمای استفاده از فایل اکسل</h3>
            <p style="margin: 5px 0; color: #6b7280;">📄 ابتدا <strong>تمپلیت اکسل</strong> را دانلود کنید - این فایل شامل راهنمای کامل و نمونه داده است</p>
            <p style="margin: 5px 0; color: #6b7280;">✏️ فایل را با اطلاعات پرسنل پر کنید (ستون‌های الزامی با علامت * مشخص شده‌اند)</p>
            <p style="margin: 5px 0; color: #6b7280;">📤 فایل پر شده را از طریق دکمه <strong>ورود اکسل</strong> آپلود کنید</p>
            <p style="margin: 5px 0; color: #6b7280;">📥 برای دانلود لیست فعلی پرسنل از دکمه <strong>خروجی اکسل</strong> استفاده کنید</p>
        </div>
    </div>
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
            {{ $personnel->links() }}
        </div>
    @else
        <p style="text-align: center; color: #6b7280; padding: 40px;">هیچ پرسنلی ثبت نشده است.</p>
        <div class="text-center">
            <a href="{{ route('personnel.create') }}" class="btn btn-primary">ثبت اولین پرسنل</a>
        </div>
    @endif
</div>
@endsection
