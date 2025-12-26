@extends('layouts.app')

@section('title', 'جزئیات دوره')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>{{ $course->name }}</h2>
    <div>
        <a href="{{ route('courses.edit', $course) }}" class="btn btn-warning">ویرایش</a>
        <a href="{{ route('courses.index') }}" class="btn btn-secondary">بازگشت</a>
    </div>
</div>

<div class="card">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <strong>کد دوره:</strong>
            <p>{{ $course->code }}</p>
        </div>

        <div>
            <strong>نام دوره:</strong>
            <p>{{ $course->name }}</p>
        </div>

        <div>
            <strong>تاریخ شروع:</strong>
            <p>{{ verta($course->start_date)->format('Y/m/d') }}</p>
        </div>

        <div>
            <strong>تاریخ پایان:</strong>
            <p>{{ verta($course->end_date)->format('Y/m/d') }}</p>
        </div>

        <div>
            <strong>مدت دوره:</strong>
            <p>{{ $course->duration }} روز</p>
        </div>

        <div>
            <strong>ظرفیت:</strong>
            <p>{{ $course->capacity ?? '-' }}</p>
        </div>

        <div>
            <strong>محل برگزاری:</strong>
            <p>{{ $course->location ?? '-' }}</p>
        </div>

        <div>
            <strong>وضعیت:</strong>
            <p>
                <span class="badge badge-{{ $course->status == 'ongoing' ? 'success' : ($course->status == 'upcoming' ? 'warning' : 'secondary') }}">
                    {{ $course->status_label }}
                </span>
                @if($course->is_active)
                    <span class="badge badge-success">فعال</span>
                @else
                    <span class="badge badge-secondary">غیرفعال</span>
                @endif
            </p>
        </div>

        @if($course->description)
        <div style="grid-column: span 2;">
            <strong>توضیحات:</strong>
            <p>{{ $course->description }}</p>
        </div>
        @endif
    </div>

    @if($course->reservations->count() > 0)
    <div style="border-top: 2px solid #e5e7eb; margin-top: 25px; padding-top: 25px;">
        <h3 style="margin-bottom: 15px;">رزروهای این دوره ({{ $course->reservations->count() }})</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>مهمان</th>
                    <th>اتاق</th>
                    <th>تاریخ ورود</th>
                    <th>تاریخ خروج</th>
                    <th>وضعیت</th>
                </tr>
            </thead>
            <tbody>
                @foreach($course->reservations as $reservation)
                <tr>
                    <td>{{ $reservation->guest->full_name ?? '-' }}</td>
                    <td>{{ $reservation->unit->unit_number ?? '-' }}</td>
                    <td>{{ verta($reservation->check_in)->format('Y/m/d') }}</td>
                    <td>{{ verta($reservation->check_out)->format('Y/m/d') }}</td>
                    <td>{{ $reservation->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
