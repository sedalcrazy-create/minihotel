@extends('layouts.app')

@section('title', 'جزئیات رزرو #' . $reservation->id)

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>جزئیات رزرو #{{ $reservation->id }}</h2>
    <a href="{{ route('reservations.index') }}" class="btn btn-secondary">بازگشت به لیست</a>
</div>

<!-- وضعیت رزرو -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="margin-bottom: 10px;">وضعیت رزرو</h3>
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
            <span class="badge {{ $status['class'] }}" style="font-size: 16px; padding: 8px 16px;">{{ $status['label'] }}</span>
        </div>

        <div>
            @if($reservation->status === 'pending' || $reservation->status === 'confirmed')
                <form action="{{ route('reservations.check-in', $reservation) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('آیا از چک‌این این رزرو مطمئن هستید؟')">
                        ✓ چک‌این
                    </button>
                </form>
            @endif

            @if($reservation->status === 'checked_in')
                <form action="{{ route('reservations.check-out', $reservation) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('آیا از چک‌اوت این رزرو مطمئن هستید؟')">
                        ✗ چک‌اوت
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<!-- اطلاعات مهمان -->
<div class="card">
    <div class="card-header">اطلاعات مهمان</div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <div>
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">نام مهمان</div>
            <div style="font-weight: bold; font-size: 16px;">{{ $reservation->guest_name }}</div>
        </div>

        @if($reservation->personnel)
            <div>
                <div style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">کد پرسنلی</div>
                <div style="font-weight: bold; font-size: 16px;">{{ $reservation->personnel->employment_code }}</div>
            </div>

            <div>
                <div style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">شماره تماس</div>
                <div style="font-weight: bold; font-size: 16px;">{{ $reservation->personnel->phone ?? '-' }}</div>
            </div>
        @endif

        @if($reservation->guest)
            <div>
                <div style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">شماره تماس</div>
                <div style="font-weight: bold; font-size: 16px;">{{ $reservation->guest->phone ?? '-' }}</div>
            </div>
        @endif

        <div>
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">نوع پذیرش</div>
            <div style="font-weight: bold; font-size: 16px;">{{ $reservation->admissionType->name }}</div>
        </div>
    </div>
</div>

<!-- اطلاعات اقامت -->
<div class="card">
    <div class="card-header">اطلاعات اقامت</div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <div>
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">واحد و اتاق</div>
            <div style="font-weight: bold; font-size: 16px;">
                واحد {{ $reservation->room->unit->number }} - اتاق {{ $reservation->room->number }}
            </div>
            <div style="color: #6b7280; font-size: 12px; margin-top: 5px;">
                بخش {{ $reservation->room->unit->section == 'east' ? 'شرقی' : 'غربی' }}
            </div>
        </div>

        <div>
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">تخت‌های اختصاص یافته</div>
            <div style="font-weight: bold; font-size: 16px;">
                @if($reservation->beds->count() > 0)
                    {{ $reservation->beds->pluck('number')->implode('، ') }}
                @else
                    -
                @endif
            </div>
        </div>

        <div>
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">تاریخ ورود</div>
            <div style="font-weight: bold; font-size: 16px;">{{ $reservation->check_in_date }}</div>
            @if($reservation->actual_check_in)
                <div style="color: #10b981; font-size: 12px; margin-top: 5px;">
                    چک‌این واقعی: {{ $reservation->actual_check_in->format('Y-m-d H:i') }}
                </div>
            @endif
        </div>

        <div>
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">تاریخ خروج</div>
            <div style="font-weight: bold; font-size: 16px;">{{ $reservation->check_out_date }}</div>
            @if($reservation->actual_check_out)
                <div style="color: #ef4444; font-size: 12px; margin-top: 5px;">
                    چک‌اوت واقعی: {{ $reservation->actual_check_out->format('Y-m-d H:i') }}
                </div>
            @endif
        </div>
    </div>

    @if($reservation->notes)
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">یادداشت</div>
            <div style="background: #f9fafb; padding: 15px; border-radius: 5px;">{{ $reservation->notes }}</div>
        </div>
    @endif
</div>

<!-- وعده‌های غذایی -->
@if($reservation->meals->count() > 0)
<div class="card">
    <div class="card-header">وعده‌های غذایی</div>

    <table>
        <thead>
            <tr>
                <th>تاریخ</th>
                <th>وعده</th>
                <th>وضعیت</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservation->meals as $meal)
                <tr>
                    <td>{{ $meal->meal_date }}</td>
                    <td>
                        @php
                            $mealTypeMap = [
                                'breakfast' => 'صبحانه',
                                'lunch' => 'ناهار',
                                'dinner' => 'شام',
                            ];
                        @endphp
                        {{ $mealTypeMap[$meal->meal_type] ?? $meal->meal_type }}
                    </td>
                    <td>
                        @if($meal->is_served)
                            <span class="badge badge-checked-in">سرو شده</span>
                        @else
                            <span class="badge badge-pending">در انتظار</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- اطلاعات ثبت -->
<div class="card">
    <div class="card-header">اطلاعات ثبت</div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <div>
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">ثبت کننده</div>
            <div style="font-weight: bold; font-size: 16px;">{{ $reservation->creator->name ?? '-' }}</div>
        </div>

        <div>
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">تاریخ ثبت</div>
            <div style="font-weight: bold; font-size: 16px;">{{ $reservation->created_at->format('Y-m-d H:i') }}</div>
        </div>
    </div>
</div>
@endsection
