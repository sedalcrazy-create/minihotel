@extends('layouts.app')

@section('title', 'افزودن پرسنل جدید')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>افزودن پرسنل جدید</h2>
    <a href="{{ route('personnel.index') }}" class="btn btn-secondary">بازگشت</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('personnel.store') }}">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="employment_code">کد پرسنلی *</label>
                <input type="text" name="employment_code" id="employment_code" class="form-control" value="{{ old('employment_code') }}" required>
            </div>

            <div class="form-group">
                <label for="national_code">کد ملی *</label>
                <input type="text" name="national_code" id="national_code" class="form-control" value="{{ old('national_code') }}" maxlength="10" required>
            </div>

            <div class="form-group">
                <label for="first_name">نام *</label>
                <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name') }}" required>
            </div>

            <div class="form-group">
                <label for="last_name">نام خانوادگی *</label>
                <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name') }}" required>
            </div>

            <div class="form-group">
                <label for="father_name">نام پدر</label>
                <input type="text" name="father_name" id="father_name" class="form-control" value="{{ old('father_name') }}">
            </div>

            <div class="form-group">
                <label for="gender">جنسیت *</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>مرد</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>زن</option>
                </select>
            </div>
        </div>

        <div style="border-top: 2px solid #e5e7eb; margin: 25px 0; padding-top: 25px;">
            <h3 style="margin-bottom: 20px; color: #f96c08;">تاریخ تولد (شمسی)</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="birth_year">سال *</label>
                    <input type="number" name="birth_year" id="birth_year" class="form-control" value="{{ old('birth_year') }}" min="1300" max="1400" required>
                </div>

                <div class="form-group">
                    <label for="birth_month">ماه *</label>
                    <input type="number" name="birth_month" id="birth_month" class="form-control" value="{{ old('birth_month') }}" min="1" max="12" required>
                </div>

                <div class="form-group">
                    <label for="birth_day">روز *</label>
                    <input type="number" name="birth_day" id="birth_day" class="form-control" value="{{ old('birth_day') }}" min="1" max="31" required>
                </div>
            </div>
        </div>

        <div style="border-top: 2px solid #e5e7eb; margin: 25px 0; padding-top: 25px;">
            <h3 style="margin-bottom: 20px; color: #f96c08;">اطلاعات استخدامی</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="employment_status">وضعیت استخدام *</label>
                    <select name="employment_status" id="employment_status" class="form-control" required>
                        <option value="">انتخاب کنید...</option>
                        <option value="رسمی" {{ old('employment_status') == 'رسمی' ? 'selected' : '' }}>رسمی</option>
                        <option value="پیمانی" {{ old('employment_status') == 'پیمانی' ? 'selected' : '' }}>پیمانی</option>
                        <option value="قراردادی" {{ old('employment_status') == 'قراردادی' ? 'selected' : '' }}>قراردادی</option>
                        <option value="موقت" {{ old('employment_status') == 'موقت' ? 'selected' : '' }}>موقت</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="main_or_branch">ستاد / شعبه</label>
                    <select name="main_or_branch" id="main_or_branch" class="form-control">
                        <option value="">انتخاب کنید...</option>
                        <option value="ستاد" {{ old('main_or_branch') == 'ستاد' ? 'selected' : '' }}>ستاد</option>
                        <option value="شعبه" {{ old('main_or_branch') == 'شعبه' ? 'selected' : '' }}>شعبه</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="department_code">دپارتمان</label>
                    <select name="department_code" id="department_code" class="form-control">
                        <option value="">انتخاب کنید...</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->code }}" {{ old('department_code') == $dept->code ? 'selected' : '' }}>
                                {{ $dept->name }} (کد: {{ $dept->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="service_location_code">محل خدمت</label>
                    <select name="service_location_code" id="service_location_code" class="form-control">
                        <option value="">انتخاب کنید...</option>
                        @foreach($serviceLocations as $location)
                            <option value="{{ $location->code }}" {{ old('service_location_code') == $location->code ? 'selected' : '' }}>
                                {{ $location->name }} (کد: {{ $location->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div style="border-top: 2px solid #e5e7eb; margin: 25px 0; padding-top: 25px;">
            <h3 style="margin-bottom: 20px; color: #f96c08;">سایر اطلاعات</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="relation">نسبت</label>
                    <select name="relation" id="relation" class="form-control">
                        <option value="">انتخاب کنید...</option>
                        <option value="کارمند" {{ old('relation') == 'کارمند' ? 'selected' : '' }}>کارمند</option>
                        <option value="همسر" {{ old('relation') == 'همسر' ? 'selected' : '' }}>همسر</option>
                        <option value="فرزند" {{ old('relation') == 'فرزند' ? 'selected' : '' }}>فرزند</option>
                        <option value="پدر" {{ old('relation') == 'پدر' ? 'selected' : '' }}>پدر</option>
                        <option value="مادر" {{ old('relation') == 'مادر' ? 'selected' : '' }}>مادر</option>
                        <option value="برادر" {{ old('relation') == 'برادر' ? 'selected' : '' }}>برادر</option>
                        <option value="خواهر" {{ old('relation') == 'خواهر' ? 'selected' : '' }}>خواهر</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="account_number">شماره حساب</label>
                    <input type="text" name="account_number" id="account_number" class="form-control" value="{{ old('account_number') }}">
                </div>

                <div class="form-group">
                    <label for="funkefalat">فوق العاده</label>
                    <input type="text" name="funkefalat" id="funkefalat" class="form-control" value="{{ old('funkefalat') }}">
                </div>

            </div>
        </div>

        <div style="text-align: left; padding-top: 20px; border-top: 2px solid #e5e7eb;">
            <button type="submit" class="btn btn-success">ثبت پرسنل</button>
            <a href="{{ route('personnel.index') }}" class="btn btn-secondary">انصراف</a>
        </div>
    </form>
</div>
@endsection
