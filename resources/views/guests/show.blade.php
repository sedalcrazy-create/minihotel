@extends('layouts.app')

@section('title', 'جزئیات مهمان')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>جزئیات مهمان: {{ $guest->full_name }}</h2>
        <div>
            <a href="{{ route('guests.edit', $guest) }}" class="btn btn-warning">ویرایش</a>
            <a href="{{ route('guests.index') }}" class="btn btn-secondary">بازگشت</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th style="width: 150px;">نام کامل:</th>
                        <td>{{ $guest->full_name }}</td>
                    </tr>
                    <tr>
                        <th>کد ملی:</th>
                        <td>{{ $guest->national_code ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>تلفن:</th>
                        <td>{{ $guest->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>ایمیل:</th>
                        <td>{{ $guest->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>سازمان:</th>
                        <td>{{ $guest->organization ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>دلیل اقامت:</th>
                        <td>{{ $guest->reason ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>تاریخ ثبت:</th>
                        <td>{{ jdate($guest->created_at)->format('Y/m/d H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($guest->reservations->count() > 0)
        <hr>
        <h4>رزروهای این مهمان</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>شماره رزرو</th>
                    <th>نوع پذیرش</th>
                    <th>تاریخ ورود</th>
                    <th>تاریخ خروج</th>
                    <th>وضعیت</th>
                </tr>
            </thead>
            <tbody>
                @foreach($guest->reservations as $reservation)
                <tr>
                    <td>{{ $reservation->id }}</td>
                    <td>{{ $reservation->admissionType->name ?? '-' }}</td>
                    <td>{{ jdate($reservation->check_in_date)->format('Y/m/d') }}</td>
                    <td>{{ jdate($reservation->check_out_date)->format('Y/m/d') }}</td>
                    <td>
                        @php
                            $statusLabels = [
                                'reserved' => 'رزرو شده',
                                'checked_in' => 'ورود',
                                'checked_out' => 'خروج',
                                'cancelled' => 'لغو شده',
                            ];
                        @endphp
                        {{ $statusLabels[$reservation->status] ?? $reservation->status }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
