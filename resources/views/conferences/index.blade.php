@extends('layouts.app')

@section('title', 'همایش‌ها')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>همایش‌ها</h2>
    <a href="{{ route('conferences.create') }}" class="btn btn-success">+ افزودن همایش</a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>کد</th>
                <th>نام همایش</th>
                <th>برگزارکننده</th>
                <th>تاریخ شروع</th>
                <th>تاریخ پایان</th>
                <th>مدت</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($conferences as $conference)
            <tr>
                <td>{{ $conference->code }}</td>
                <td>{{ $conference->name }}</td>
                <td>{{ $conference->organizer ?? '-' }}</td>
                <td>{{ verta($conference->start_date)->format('Y/m/d') }}</td>
                <td>{{ verta($conference->end_date)->format('Y/m/d') }}</td>
                <td>{{ $conference->duration }} روز</td>
                <td>
                    <span class="badge badge-{{ $conference->status == 'ongoing' ? 'success' : ($conference->status == 'upcoming' ? 'warning' : 'secondary') }}">
                        {{ $conference->status_label }}
                    </span>
                    @if(!$conference->is_active)
                        <span class="badge badge-secondary">غیرفعال</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('conferences.show', $conference) }}" class="btn btn-sm btn-info">مشاهده</a>
                    <a href="{{ route('conferences.edit', $conference) }}" class="btn btn-sm btn-warning">ویرایش</a>
                    <form action="{{ route('conferences.destroy', $conference) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('آیا مطمئن هستید؟')">حذف</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">همایشی یافت نشد</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($conferences->hasPages())
<div style="margin-top: 20px;">
    {{ $conferences->links() }}
</div>
@endif
@endsection
