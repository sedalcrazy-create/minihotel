@extends('layouts.app')

@section('title', 'ویرایش مهمان')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>ویرایش مهمان: {{ $guest->full_name }}</h2>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('guests.update', $guest) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="full_name">نام کامل *</label>
                <input type="text" name="full_name" id="full_name" class="form-control" value="{{ old('full_name', $guest->full_name) }}" required>
            </div>

            <div class="form-group">
                <label for="national_code">کد ملی</label>
                <input type="text" name="national_code" id="national_code" class="form-control" value="{{ old('national_code', $guest->national_code) }}" maxlength="10">
            </div>

            <div class="form-group">
                <label for="phone">تلفن</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $guest->phone) }}">
            </div>

            <div class="form-group">
                <label for="email">ایمیل</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $guest->email) }}">
            </div>

            <div class="form-group">
                <label for="organization">سازمان</label>
                <input type="text" name="organization" id="organization" class="form-control" value="{{ old('organization', $guest->organization) }}">
            </div>

            <div class="form-group">
                <label for="reason">دلیل اقامت</label>
                <textarea name="reason" id="reason" class="form-control" rows="3">{{ old('reason', $guest->reason) }}</textarea>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                <a href="{{ route('guests.index') }}" class="btn btn-secondary">انصراف</a>
            </div>
        </form>
    </div>
</div>
@endsection
