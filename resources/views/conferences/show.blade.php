@extends('layouts.app')

@section('title', 'جزئیات همایش')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>{{ $conference->name }}</h2>
    <div>
        <a href="{{ route('conferences.edit', $conference) }}" class="btn btn-warning">ویرایش</a>
        <a href="{{ route('conferences.index') }}" class="btn btn-secondary">بازگشت</a>
    </div>
</div>

<div class="card">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <strong>کد همایش:</strong>
            <p>{{ $conference->code }}</p>
        </div>

        <div>
            <strong>نام همایش:</strong>
            <p>{{ $conference->name }}</p>
        </div>

        <div>
            <strong>برگزارکننده:</strong>
            <p>{{ $conference->organizer ?? '-' }}</p>
        </div>

        <div>
            <strong>ظرفیت:</strong>
            <p>{{ $conference->capacity ?? '-' }}</p>
        </div>

        <div>
            <strong>تاریخ شروع:</strong>
            <p>{{ verta($conference->start_date)->format('Y/m/d') }}</p>
        </div>

        <div>
            <strong>تاریخ پایان:</strong>
            <p>{{ verta($conference->end_date)->format('Y/m/d') }}</p>
        </div>

        <div>
            <strong>مدت همایش:</strong>
            <p>{{ $conference->duration }} روز</p>
        </div>

        <div>
            <strong>وضعیت:</strong>
            <p>
                <span class="badge badge-{{ $conference->status == 'ongoing' ? 'success' : ($conference->status == 'upcoming' ? 'warning' : 'secondary') }}">
                    {{ $conference->status_label }}
                </span>
                @if($conference->is_active)
                    <span class="badge badge-success">فعال</span>
                @else
                    <span class="badge badge-secondary">غیرفعال</span>
                @endif
            </p>
        </div>

        @if($conference->description)
        <div style="grid-column: span 2;">
            <strong>توضیحات:</strong>
            <p>{{ $conference->description }}</p>
        </div>
        @endif
    </div>

    @if($conference->reservations->count() > 0)
    <div style="border-top: 2px solid #e5e7eb; margin-top: 25px; padding-top: 25px;">
        <h3 style="margin-bottom: 15px;">رزروهای این همایش ({{ $conference->reservations->count() }})</h3>
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
                @foreach($conference->reservations as $reservation)
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
