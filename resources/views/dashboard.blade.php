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
            <span style="margin-right: 15px;">|</span>
            <span style="display: inline-block; width: 15px; height: 15px; background: #ff69b4; margin-left: 5px; border-radius: 3px;"></span> خانم‌ها
            <span style="display: inline-block; width: 15px; height: 15px; background: #4a90d9; margin-right: 10px; margin-left: 5px; border-radius: 3px;"></span> آقایان
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
        @foreach($units as $unit)
            <div style="border: 2px solid {{ $unit->gender_restriction == 'female' ? '#ff69b4' : ($unit->gender_restriction == 'male' ? '#4a90d9' : '#e5e7eb') }}; border-radius: 8px; padding: 15px; background: {{ $unit->gender_restriction == 'female' ? 'linear-gradient(135deg, #fff0f5, #ffe4ec)' : ($unit->gender_restriction == 'male' ? 'linear-gradient(135deg, #f0f8ff, #e6f2ff)' : '#f9fafb') }}; position: relative; overflow: hidden;">
                @if($unit->gender_restriction == 'female')
                <div style="position: absolute; top: 8px; right: 8px; font-size: 16px; opacity: 0.6; animation: float 3s ease-in-out infinite;">🌸</div>
                <div style="position: absolute; bottom: 55px; right: 8px; font-size: 14px; opacity: 0.5; animation: float 2.5s ease-in-out infinite 1s;">✨</div>
                @endif
                <div style="font-weight: bold; margin-bottom: 10px; color: {{ $unit->gender_restriction == 'female' ? '#d63384' : ($unit->gender_restriction == 'male' ? '#1e3a8a' : '#1e3a8a') }};">
                    واحد {{ $unit->number }}
                    <span style="font-size: 11px; font-weight: normal; color: #6b7280;">
                        ({{ $unit->section == 'east' ? 'شرقی' : 'غربی' }})
                    </span>
                    @if($unit->gender_restriction != 'mixed')
                    <span style="font-size: 10px; padding: 2px 8px; border-radius: 10px; margin-right: 5px; background: {{ $unit->gender_restriction == 'female' ? '#ff69b4' : '#4a90d9' }}; color: white;">
                        {{ $unit->gender_restriction == 'female' ? 'خانم‌ها' : 'آقایان' }}
                    </span>
                    @endif
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
                                    $statusLabel = match($bed->status) {
                                        'available' => 'آزاد',
                                        'occupied' => 'اشغال',
                                        'needs_cleaning' => 'نیاز به نظافت',
                                        'under_maintenance' => 'در تعمیر',
                                        default => 'نامشخص'
                                    };
                                    // پیدا کردن رزرو فعال برای این تخت
                                    $activeReservation = $bed->reservations->first();
                                @endphp
                                <div
                                    class="bed-card"
                                    style="background: {{ $color }}; color: white; padding: 8px; border-radius: 5px; text-align: center; font-size: 11px; cursor: pointer; transition: all 0.2s;"
                                    title="{{ $bed->identifier }} - {{ $statusLabel }}"
                                    onclick="openBedModal({{ $bed->id }}, '{{ $bed->identifier }}', '{{ $bed->status }}', '{{ $statusLabel }}', {{ $unit->id }}, {{ $room->id }}, {{ $activeReservation ? $activeReservation->id : 'null' }}, '{{ $activeReservation ? $activeReservation->status : '' }}', '{{ $activeReservation ? addslashes($activeReservation->guest_name) : '' }}')"
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
                    <td>{{ $maintenance->bed->identifier ?? "-" }}</td>
                    <td>{{ $maintenance->description }}</td>
                    <td>{{ $maintenance->reporter->name }}</td>
                    <td>{{ $maintenance->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- مودال تخت -->
<div id="bedModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; width: 90%; max-width: 400px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <!-- هدر مودال -->
        <div style="background: linear-gradient(135deg, #f96c08, #e37415); color: white; padding: 20px; border-radius: 16px 16px 0 0;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 id="modalTitle" style="margin: 0; font-size: 18px;">اطلاعات تخت</h3>
                <button onclick="closeBedModal()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 18px;">×</button>
            </div>
            <div id="modalStatus" style="margin-top: 10px; padding: 6px 12px; background: rgba(255,255,255,0.2); border-radius: 20px; display: inline-block; font-size: 13px;"></div>
        </div>

        <!-- محتوای مودال -->
        <div style="padding: 20px;">
            <!-- تغییر وضعیت -->
            <div style="margin-bottom: 20px;">
                <label style="font-weight: bold; display: block; margin-bottom: 10px; color: #374151;">تغییر وضعیت:</label>
                <form id="statusForm" method="POST" style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @csrf
                    @method('PUT')
                    <button type="submit" name="status" value="available" class="status-btn" style="flex: 1; min-width: 80px; padding: 10px; border: 2px solid #10b981; background: #d1fae5; color: #065f46; border-radius: 8px; cursor: pointer; font-size: 12px;">🟢 آزاد</button>
                    <button type="submit" name="status" value="occupied" class="status-btn" style="flex: 1; min-width: 80px; padding: 10px; border: 2px solid #ef4444; background: #fee2e2; color: #991b1b; border-radius: 8px; cursor: pointer; font-size: 12px;">🔴 اشغال</button>
                    <button type="submit" name="status" value="needs_cleaning" class="status-btn" style="flex: 1; min-width: 80px; padding: 10px; border: 2px solid #f59e0b; background: #fef3c7; color: #92400e; border-radius: 8px; cursor: pointer; font-size: 12px;">🟡 نظافت</button>
                    <button type="submit" name="status" value="under_maintenance" class="status-btn" style="flex: 1; min-width: 80px; padding: 10px; border: 2px solid #6b7280; background: #e5e7eb; color: #374151; border-radius: 8px; cursor: pointer; font-size: 12px;">⚫ تعمیر</button>
                </form>
            </div>

            <!-- دکمه‌های عملیات -->
            <div style="border-top: 1px solid #e5e7eb; padding-top: 20px;">
                <label style="font-weight: bold; display: block; margin-bottom: 10px; color: #374151;">عملیات:</label>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a id="reserveBtn" href="#" class="btn btn-primary" style="text-align: center; padding: 12px; display: none;">
                        📅 ثبت رزرو جدید
                    </a>
                    <a id="maintenanceBtn" href="#" class="btn btn-secondary" style="text-align: center; padding: 12px;">
                        🔧 ثبت درخواست تعمیر
                    </a>
                </div>
            </div>

            <!-- نمایش رزرو فعال و دکمه‌های چک‌این/چک‌اوت -->
            <div id="activeReservationSection" style="display: none; border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 20px;">
                <label style="font-weight: bold; display: block; margin-bottom: 10px; color: #374151;">رزرو فعال:</label>
                <div id="activeReservationInfo" style="background: #fef3c7; padding: 15px; border-radius: 8px; font-size: 13px; margin-bottom: 15px;"></div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <form id="checkInForm" method="POST" style="display: none;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; text-align: center;">
                            ✅ چک‌این (ورود)
                        </button>
                    </form>
                    <form id="checkOutForm" method="POST" style="display: none;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-secondary" style="width: 100%; padding: 12px; text-align: center; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                            🚪 چک‌اوت (خروج)
                        </button>
                    </form>
                    <a id="viewReservationBtn" href="#" class="btn btn-secondary" style="text-align: center; padding: 12px; display: none;">
                        👁️ مشاهده جزئیات رزرو
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bed-card:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    z-index: 10;
    position: relative;
}
.status-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes shimmer {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes sparkle {
    0%, 100% { opacity: 0.3; transform: scale(1) rotate(0deg); }
    50% { opacity: 1; transform: scale(1.2) rotate(180deg); }
}
</style>

<script>
let currentBedId = null;
let currentRoomId = null;

function openBedModal(bedId, identifier, status, statusLabel, unitId, roomId, reservationId, reservationStatus, guestName) {
    currentBedId = bedId;
    currentRoomId = roomId;

    const modalTitle = document.getElementById('modalTitle');
    const modalStatus = document.getElementById('modalStatus');
    const statusForm = document.getElementById('statusForm');
    const reserveBtn = document.getElementById('reserveBtn');
    const maintenanceBtn = document.getElementById('maintenanceBtn');
    const bedModal = document.getElementById('bedModal');
    const activeReservationSection = document.getElementById('activeReservationSection');
    const activeReservationInfo = document.getElementById('activeReservationInfo');
    const checkInForm = document.getElementById('checkInForm');
    const checkOutForm = document.getElementById('checkOutForm');
    const viewReservationBtn = document.getElementById('viewReservationBtn');

    if (!modalTitle || !modalStatus || !statusForm || !reserveBtn || !maintenanceBtn || !bedModal) {
        console.error('Modal elements not found');
        return;
    }

    modalTitle.textContent = identifier;
    modalStatus.textContent = 'وضعیت: ' + statusLabel;

    // تنظیم فرم تغییر وضعیت
    statusForm.action = '/beds/' + bedId + '/status';

    // تنظیم لینک‌ها
    reserveBtn.href = '/reservations/create?bed_id=' + bedId + '&room_id=' + roomId;
    maintenanceBtn.href = '/maintenance/create?bed_id=' + bedId;

    // مدیریت نمایش دکمه‌ها بر اساس وضعیت رزرو
    if (reservationId && reservationId !== null) {
        // اگر رزرو فعال داریم
        activeReservationSection.style.display = 'block';
        reserveBtn.style.display = 'none';

        // نمایش اطلاعات رزرو
        activeReservationInfo.innerHTML = '<strong>مهمان:</strong> ' + guestName + '<br><strong>وضعیت:</strong> ' + (reservationStatus === 'reserved' ? 'رزرو شده' : 'چک‌این شده');

        // نمایش دکمه مشاهده رزرو
        viewReservationBtn.style.display = 'block';
        viewReservationBtn.href = '/reservations/' + reservationId;

        // نمایش دکمه‌های مناسب بر اساس وضعیت رزرو
        if (reservationStatus === 'reserved') {
            // اگر فقط رزرو شده، دکمه چک‌این نمایش بده
            checkInForm.style.display = 'block';
            checkInForm.action = '/reservations/' + reservationId + '/check-in';
            checkOutForm.style.display = 'none';
        } else if (reservationStatus === 'checked_in') {
            // اگر چک‌این شده، دکمه چک‌اوت نمایش بده
            checkInForm.style.display = 'none';
            checkOutForm.style.display = 'block';
            checkOutForm.action = '/reservations/' + reservationId + '/check-out';
        }
    } else {
        // اگر رزرو فعال نداریم، دکمه رزرو جدید نمایش بده
        activeReservationSection.style.display = 'none';
        reserveBtn.style.display = 'block';
        checkInForm.style.display = 'none';
        checkOutForm.style.display = 'none';
    }

    // نمایش مودال
    bedModal.style.display = 'flex';

    // غیرفعال کردن دکمه وضعیت فعلی
    document.querySelectorAll('.status-btn').forEach(btn => {
        btn.disabled = false;
        btn.style.opacity = '1';
    });
    const currentBtn = document.querySelector('.status-btn[value="' + status + '"]');
    if (currentBtn) {
        currentBtn.disabled = true;
        currentBtn.style.opacity = '0.5';
    }
}

function closeBedModal() {
    document.getElementById('bedModal').style.display = 'none';
}

// بستن مودال با کلیک خارج از آن
document.getElementById('bedModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBedModal();
    }
});

// بستن با کلید Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeBedModal();
    }
});
</script>
@endsection
