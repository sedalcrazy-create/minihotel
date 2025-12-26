@extends('layouts.app')

@section('title', 'ویرایش همایش')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>ویرایش همایش: {{ $conference->name }}</h2>
    <a href="{{ route('conferences.index') }}" class="btn btn-secondary">بازگشت</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('conferences.update', $conference) }}">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="code">کد همایش *</label>
                <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $conference->code) }}" required>
            </div>

            <div class="form-group">
                <label for="name">نام همایش *</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $conference->name) }}" required>
            </div>

            <div class="form-group">
                <label for="organizer">برگزارکننده</label>
                <input type="text" name="organizer" id="organizer" class="form-control" value="{{ old('organizer', $conference->organizer) }}">
            </div>

            <div class="form-group">
                <label for="capacity">ظرفیت</label>
                <input type="number" name="capacity" id="capacity" class="form-control" value="{{ old('capacity', $conference->capacity) }}" min="1">
            </div>

            <div class="form-group">
                <label for="start_date">تاریخ شروع *</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $conference->start_date->format('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label for="end_date">تاریخ پایان *</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $conference->end_date->format('Y-m-d')) }}" required>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label for="description">توضیحات</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $conference->description) }}</textarea>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $conference->is_active) ? 'checked' : '' }}>
                    فعال
                </label>
            </div>
        </div>

        <div style="text-align: left; padding-top: 20px; border-top: 2px solid #e5e7eb; margin-top: 20px;">
            <button type="submit" class="btn btn-success">بروزرسانی</button>
            <a href="{{ route('conferences.index') }}" class="btn btn-secondary">انصراف</a>
        </div>
    </form>
</div>
@endsection
