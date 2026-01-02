@extends('layouts.app')

@section('title', 'ویرایش رزرو')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>ویرایش رزرو #{{ $reservation->id }}</h2>
    <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary">بازگشت</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('reservations.update', $reservation) }}" id="reservationForm">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="admission_type_id">نوع پذیرش *</label>
            <select name="admission_type_id" id="admission_type_id" class="form-control" required>
                <option value="">انتخاب کنید...</option>
                @foreach($admissionTypes as $type)
                    <option value="{{ $type->id }}" {{ old('admission_type_id', $reservation->admission_type_id) == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="border: 2px dashed #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px; background: #f9fafb;">
            <div style="font-weight: bold; margin-bottom: 15px; color: #1e3a8a;">اطلاعات مهمان</div>

            <div class="form-group">
                <label>
                    <input type="radio" name="guest_type" value="personnel" id="guestTypePersonnel" {{ $reservation->personnel_id ? 'checked' : '' }} style="margin-left: 5px;">
                    پرسنل بانک
                </label>
                <label style="margin-right: 20px;">
                    <input type="radio" name="guest_type" value="external" id="guestTypeExternal" {{ $reservation->guest_id ? 'checked' : '' }} style="margin-left: 5px;">
                    مهمان خارجی
                </label>
            </div>

            <div id="personnelSection" style="{{ $reservation->guest_id ? 'display: none;' : '' }}">
                <div class="form-group">
                    <label for="personnel_id">انتخاب پرسنل</label>
                    <select name="personnel_id" id="personnel_id" class="form-control">
                        <option value="">انتخاب کنید...</option>
                        @foreach($personnel as $person)
                            <option value="{{ $person->id }}" {{ old('personnel_id', $reservation->personnel_id) == $person->id ? 'selected' : '' }}>
                                {{ $person->full_name }} ({{ $person->employment_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="externalSection" style="{{ $reservation->personnel_id ? 'display: none;' : '' }}">
                <div class="form-group">
                    <label for="guest_name">نام و نام خانوادگی مهمان</label>
                    <input type="text" name="guest_name" id="guest_name" class="form-control" value="{{ old('guest_name', $reservation->guest->full_name ?? '') }}" placeholder="علی احمدی">
                </div>

                <div class="form-group">
                    <label for="guest_phone">شماره تماس</label>
                    <input type="text" name="guest_phone" id="guest_phone" class="form-control" value="{{ old('guest_phone', $reservation->guest->phone ?? '') }}" placeholder="09121234567">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="room_id">انتخاب اتاق *</label>
            <select name="room_id" id="room_id" class="form-control" required>
                <option value="">ابتدا اتاق را انتخاب کنید...</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}" data-beds="{{ json_encode($room->beds) }}" {{ old('room_id', $reservation->room_id) == $room->id ? 'selected' : '' }}>
                        واحد {{ $room->unit->number }} - اتاق {{ $room->number }} ({{ $room->unit->section == 'east' ? 'شرقی' : 'غربی' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" id="bedsSection">
            <label>انتخاب تخت‌ها * (حداقل 1 تخت)</label>
            <div id="bedsList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin-top: 10px;">
                <!-- تخت‌ها به صورت داینامیک اضافه می‌شوند -->
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="check_in_date">تاریخ ورود (شمسی) *</label>
                <input type="text" name="check_in_date" class="jalali-datepicker form-control" id="check_in_date" value="{{ old('check_in_date', \Morilog\Jalali\Jalalian::fromCarbon($reservation->check_in_date)->format('Y/m/d')) }}" required placeholder="۱۴۰۴/۱۰/۱۴">
            </div>

            <div class="form-group">
                <label for="check_out_date">تاریخ خروج (شمسی) *</label>
                <input type="text" name="check_out_date" class="jalali-datepicker form-control" id="check_out_date" value="{{ old('check_out_date', \Morilog\Jalali\Jalalian::fromCarbon($reservation->check_out_date)->format('Y/m/d')) }}" required placeholder="۱۴۰۴/۱۰/۱۵">
            </div>
        </div>

        <div class="form-group">
            <label for="notes">یادداشت</label>
            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="توضیحات اضافی...">{{ old('notes', $reservation->notes) }}</textarea>
        </div>

        <div style="text-align: left;">
            <button type="submit" class="btn btn-success">ذخیره تغییرات</button>
            <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary">انصراف</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const personnelRadio = document.getElementById('guestTypePersonnel');
        const externalRadio = document.getElementById('guestTypeExternal');
        const personnelSection = document.getElementById('personnelSection');
        const externalSection = document.getElementById('externalSection');
        const personnelSelect = document.getElementById('personnel_id');
        const guestNameInput = document.getElementById('guest_name');
        const roomSelect = document.getElementById('room_id');
        const bedsSection = document.getElementById('bedsSection');
        const bedsList = document.getElementById('bedsList');

        // Currently selected beds from reservation
        const selectedBedIds = @json($reservation->beds->pluck('id'));

        // Toggle guest type sections
        function updateGuestSections() {
            if (personnelRadio.checked) {
                personnelSection.style.display = 'block';
                externalSection.style.display = 'none';
                personnelSelect.required = true;
                guestNameInput.required = false;
            } else {
                personnelSection.style.display = 'none';
                externalSection.style.display = 'block';
                personnelSelect.required = false;
                guestNameInput.required = true;
            }
        }

        personnelRadio.addEventListener('change', updateGuestSections);
        externalRadio.addEventListener('change', updateGuestSections);

        // Load beds function
        function loadBeds() {
            const selectedOption = roomSelect.options[roomSelect.selectedIndex];

            if (!selectedOption.value) {
                bedsSection.style.display = 'none';
                bedsList.innerHTML = '';
                return;
            }

            const beds = JSON.parse(selectedOption.dataset.beds || '[]');
            bedsList.innerHTML = '';

            beds.forEach(bed => {
                // Consider beds from current reservation as available
                const isCurrentReservationBed = selectedBedIds.includes(bed.id);
                const effectiveStatus = isCurrentReservationBed ? 'available' : bed.status;

                const bedColor = {
                    'available': '#10b981',
                    'occupied': '#ef4444',
                    'needs_cleaning': '#f59e0b',
                    'under_maintenance': '#6b7280'
                }[effectiveStatus] || '#e5e7eb';

                const isAvailable = effectiveStatus === 'available';
                const isChecked = selectedBedIds.includes(bed.id);

                const bedDiv = document.createElement('div');
                bedDiv.style.cssText = `
                    border: 2px solid ${isChecked ? '#3b82f6' : bedColor};
                    border-radius: 8px;
                    padding: 15px;
                    text-align: center;
                    cursor: ${isAvailable ? 'pointer' : 'not-allowed'};
                    opacity: ${isAvailable ? '1' : '0.5'};
                    transition: all 0.3s;
                    background: ${isChecked ? '#e0f2fe' : 'white'};
                `;

                bedDiv.innerHTML = `
                    <input type="checkbox" name="bed_ids[]" value="${bed.id}" id="bed_${bed.id}"
                        ${!isAvailable ? 'disabled' : ''}
                        ${isChecked ? 'checked' : ''}
                        style="margin-bottom: 5px;">
                    <label for="bed_${bed.id}" style="display: block; cursor: ${isAvailable ? 'pointer' : 'not-allowed'};">
                        <div style="font-weight: bold;">تخت ${bed.number}</div>
                        <div style="font-size: 11px; color: #6b7280; margin-top: 5px;">
                            ${effectiveStatus === 'available' ? 'آزاد' :
                              effectiveStatus === 'occupied' ? 'اشغال' :
                              effectiveStatus === 'needs_cleaning' ? 'نظافت' : 'تعمیر'}
                        </div>
                    </label>
                `;

                if (isAvailable) {
                    bedDiv.addEventListener('click', function(e) {
                        if (e.target.tagName !== 'INPUT') {
                            const checkbox = bedDiv.querySelector('input');
                            checkbox.checked = !checkbox.checked;
                            updateBedSelection(bedDiv, checkbox.checked);
                        } else {
                            updateBedSelection(bedDiv, e.target.checked);
                        }
                    });
                }

                bedsList.appendChild(bedDiv);
            });

            bedsSection.style.display = 'block';
        }

        function updateBedSelection(bedDiv, isChecked) {
            if (isChecked) {
                bedDiv.style.background = '#e0f2fe';
                bedDiv.style.borderColor = '#3b82f6';
            } else {
                bedDiv.style.background = 'white';
                bedDiv.style.borderColor = '#10b981';
            }
        }

        // Load beds when room changes
        roomSelect.addEventListener('change', loadBeds);

        // Initial load
        loadBeds();
    });
</script>
@endsection
