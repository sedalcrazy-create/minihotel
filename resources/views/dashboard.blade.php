@extends('layouts.app')

@section('title', 'داشبورد')

@section('content')
<h2 class="mb-20">داشبورد مدیریت خوابگاه</h2>

<!-- آمار کلی تخت‌ها -->
<div class="stats-grid">
    <div class="stat-card available">
        <div class="stat-label">تخت‌های آزاد</div>
        <div class="stat-value">{{ $availableBeds }}</div>
        <div style="font-size: 12px; color: #6b7280;">از {{ $totalBeds }} تخت</div>
    </div>

    <div class="stat-card occupied">
        <div class="stat-label">تخت‌های اشغال</div>
        <div class="stat-value">{{ $occupiedBeds }}</div>
        <div style="font-size: 12px; color: #6b7280;">در حال استفاده</div>
    </div>

    <div class="stat-card cleaning">
        <div class="stat-label">نیاز به نظافت</div>
        <div class="stat-value">{{ $cleaningBeds }}</div>
        <div style="font-size: 12px; color: #6b7280;">آماده نظافت</div>
    </div>

    <div class="stat-card maintenance">
        <div class="stat-label">در تعمیر</div>
        <div class="stat-value">{{ $maintenanceBeds }}</div>
        <div style="font-size: 12px; color: #6b7280;">تحت تعمیر</div>
    </div>
</div>

<!-- نمایش شماتیک واحدها و تخت‌ها -->
<div class="card">
    <div class="card-header">
        وضعیت واحدها و تخت‌ها
        <div style="float: left; font-size: 12px; font-weight: normal;">
            <span style="display: inline-block; width: 15px; height: 15px; background: #10b981; margin-left: 5px; border-radius: 3px;"></span> آزاد
            <span style="display: inline-block; width: 15px; height: 15px; background: #ef4444; margin-right: 10px; margin-left: 5px; border-radius: 3px;"></span> اشغال
            <span style="display: inline-block; width: 15px; height: 15px; background: #f59e0b; margin-right: 10px; margin-left: 5px; border-radius: 3px;"></span> نظافت
            <span style="display: inline-block; width: 15px; height: 15px; background: #6b7280; margin-right: 10px; margin-left: 5px; border-radius: 3px;"></span> تعمیر
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
        @foreach($units as $unit)
            <div style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 15px; background: #f9fafb;">
                <div style="font-weight: bold; margin-bottom: 10px; color: #1e3a8a;">
                    واحد {{ $unit->number }}
                    <span style="font-size: 11px; font-weight: normal; color: #6b7280;">
                        ({{ $unit->section == 'east' ? 'شرقی' : 'غربی' }})
                    </span>
                </div>

                @foreach($unit->rooms as $room)
                    <div style="margin-bottom: 10px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">
                            اتاق {{ $room->number }}
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px;">
                            @foreach($room->beds as $bed)
                                @php
                                    $color = match($bed->status) {
                                        'available' => '#10b981',
                                        'occupied' => '#ef4444',
                                        'needs_cleaning' => '#f59e0b',
                                        'under_maintenance' => '#6b7280',
                                        default => '#e5e7eb'
                                    };
                                @endphp
                                <div
                                    style="background: {{ $color }}; color: white; padding: 8px; border-radius: 5px; text-align: center; font-size: 11px; cursor: pointer;"
                                    title="{{ $bed->identifier }} - {{ $bed->status }}"
                                >
                                    تخت {{ $bed->number }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

<!-- رزروهای فعال -->
<div class="card">
    <div class="card-header">
        رزروهای فعال (چک‌این شده)
        <a href="{{ route('reservations.index') }}" style="float: left; font-size: 14px; color: #3b82f6; text-decoration: none;">مشاهده همه →</a>
    </div>

    @if($activeReservations->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>نام مهمان</th>
                    <th>نوع پذیرش</th>
                    <th>اتاق</th>
                    <th>تخت‌ها</th>
                    <th>تاریخ ورود</th>
                    <th>تاریخ خروج</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activeReservations as $reservation)
                    <tr>
                        <td>{{ $reservation->guest_name }}</td>
                        <td>{{ $reservation->admissionType->name }}</td>
                        <td>واحد {{ $reservation->room->unit->number }} - اتاق {{ $reservation->room->number }}</td>
                        <td>{{ $reservation->beds->pluck('number')->implode('، ') }}</td>
                        <td>{{ $reservation->check_in_date }}</td>
                        <td>{{ $reservation->check_out_date }}</td>
                        <td>
                            <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">مشاهده</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: #6b7280; padding: 20px;">رزرو فعالی وجود ندارد.</p>
    @endif
</div>

<!-- تعمیرات در انتظار -->
@if($pendingMaintenance->count() > 0)
<div class="card">
    <div class="card-header">تعمیرات در انتظار</div>

    <table>
        <thead>
            <tr>
                <th>تخت</th>
                <th>شرح مشکل</th>
                <th>گزارش دهنده</th>
                <th>تاریخ گزارش</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendingMaintenance as $maintenance)
                <tr>
                    <td>{{ $maintenance->bed->identifier }}</td>
                    <td>{{ $maintenance->description }}</td>
                    <td>{{ $maintenance->reporter->name }}</td>
                    <td>{{ $maintenance->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
