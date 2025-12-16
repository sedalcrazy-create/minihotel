@extends('layouts.app')

@section('title', 'لیست رزروها')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>لیست رزروها</h2>
    <a href="{{ route('reservations.create') }}" class="btn btn-primary">+ رزرو جدید</a>
</div>

<div class="card">
    @if($reservations->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>شماره</th>
                    <th>نام مهمان</th>
                    <th>نوع پذیرش</th>
                    <th>اتاق</th>
                    <th>تخت‌ها</th>
                    <th>تاریخ ورود</th>
                    <th>تاریخ خروج</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservations as $reservation)
                    <tr>
                        <td>{{ $reservation->id }}</td>
                        <td>{{ $reservation->guest_name }}</td>
                        <td>{{ $reservation->admissionType->name }}</td>
                        <td>واحد {{ $reservation->room->unit->number }} - اتاق {{ $reservation->room->number }}</td>
                        <td>
                            @if($reservation->beds->count() > 0)
                                {{ $reservation->beds->pluck('number')->implode('، ') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $reservation->check_in_date }}</td>
                        <td>{{ $reservation->check_out_date }}</td>
                        <td>
                            @php
                                $statusMap = [
                                    'pending' => ['label' => 'در انتظار', 'class' => 'badge-pending'],
                                    'confirmed' => ['label' => 'تایید شده', 'class' => 'badge-confirmed'],
                                    'checked_in' => ['label' => 'چک‌این شده', 'class' => 'badge-checked-in'],
                                    'checked_out' => ['label' => 'چک‌اوت شده', 'class' => 'badge-checked-out'],
                                    'cancelled' => ['label' => 'لغو شده', 'class' => 'badge-cancelled'],
                                ];
                                $status = $statusMap[$reservation->status] ?? ['label' => $reservation->status, 'class' => 'badge-pending'];
                            @endphp
                            <span class="badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                        </td>
                        <td>
                            <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">مشاهده</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            {{ $reservations->links() }}
        </div>
    @else
        <p style="text-align: center; color: #6b7280; padding: 40px;">رزروی ثبت نشده است.</p>
        <div class="text-center">
            <a href="{{ route('reservations.create') }}" class="btn btn-primary">ایجاد اولین رزرو</a>
        </div>
    @endif
</div>
@endsection
