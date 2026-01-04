@extends('layouts.app')

@section('title', 'ویرایش دوره')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>ویرایش دوره: {{ $course->name }}</h2>
    <a href="{{ route('courses.index') }}" class="btn btn-secondary">بازگشت</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('courses.update', $course) }}">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="code">کد دوره *</label>
                <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $course->code) }}" required>
            </div>

            <div class="form-group">
                <label for="name">نام دوره *</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $course->name) }}" required>
            </div>

            <div class="form-group">
                <label for="start_date">تاریخ شروع *</label>
                <input type="text" name="start_date" id="start_date" class="form-control jalali-datepicker" autocomplete="off" value="{{ old('start_date', verta($course->start_date ?? $conference->start_date)->format('Y\/m\/d')) }}" required>
            </div>

            <div class="form-group">
                <label for="end_date">تاریخ پایان *</label>
                <input type="text" name="end_date" id="end_date" class="form-control jalali-datepicker" autocomplete="off" value="{{ old('end_date', verta($course->end_date ?? $conference->end_date)->format('Y\/m\/d')) }}" required>
            </div>

            <div class="form-group">
                <label for="capacity">ظرفیت</label>
                <input type="number" name="capacity" id="capacity" class="form-control" value="{{ old('capacity', $course->capacity) }}" min="1">
            </div>

            <div class="form-group">
                <label for="location">محل برگزاری</label>
                <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $course->location) }}">
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label for="description">توضیحات</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $course->description) }}</textarea>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                    فعال
                </label>
            </div>
        </div>

        <div style="text-align: left; padding-top: 20px; border-top: 2px solid #e5e7eb; margin-top: 20px;">
            <button type="submit" class="btn btn-success">بروزرسانی</button>
            <a href="{{ route('courses.index') }}" class="btn btn-secondary">انصراف</a>
        </div>
    </form>
</div>
@endsection
