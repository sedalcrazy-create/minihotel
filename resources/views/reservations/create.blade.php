@extends('layouts.app')

@section('title', 'رزرو جدید')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>رزرو جدید</h2>
    <a href="{{ route('reservations.index') }}" class="btn btn-secondary">بازگشت</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('reservations.store') }}" id="reservationForm">
        @csrf

        <div class="form-group">
            <label for="admission_type_id">نوع پذیرش *</label>
            <select name="admission_type_id" id="admission_type_id" class="form-control" required>
                <option value="">انتخاب کنید...</option>
                @foreach($admissionTypes as $type)
                    <option value="{{ $type->id }}" data-name="{{ $type->name }}" {{ old('admission_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- انتخاب دوره (فقط برای دوره کلاسی) -->
        <div class="form-group" id="courseSection" style="display: none;">
            <label for="course_id">انتخاب دوره *</label>
            <select name="course_id" id="course_id" class="form-control">
                <option value="">انتخاب کنید...</option>
                @foreach($courses as $course)
                    @php
                        $startJalali = \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($course->start_date))->format('Y/m/d');
                        $endJalali = \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($course->end_date))->format('Y/m/d');
                    @endphp
                    <option value="{{ $course->id }}"
                            data-start="{{ $startJalali }}"
                            data-end="{{ $endJalali }}"
                            {{ old('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->name }} ({{ $course->code }}) - {{ $startJalali }} تا {{ $endJalali }}
                    </option>
                @endforeach
            </select>
            <small class="form-text" style="color: #6b7280;">فقط دوره‌های 45 روز آینده نمایش داده می‌شود</small>
        </div>

        <!-- انتخاب همایش (فقط برای همایش) -->
        <div class="form-group" id="conferenceSection" style="display: none;">
            <label for="conference_id">انتخاب همایش *</label>
            <select name="conference_id" id="conference_id" class="form-control">
                <option value="">انتخاب کنید...</option>
                @foreach($conferences as $conference)
                    @php
                        $confStartJalali = \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($conference->start_date))->format('Y/m/d');
                        $confEndJalali = \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($conference->end_date))->format('Y/m/d');
                    @endphp
                    <option value="{{ $conference->id }}"
                            data-start="{{ $confStartJalali }}"
                            data-end="{{ $confEndJalali }}"
                            {{ old('conference_id') == $conference->id ? 'selected' : '' }}>
                        {{ $conference->name }} ({{ $conference->code }}) - {{ $confStartJalali }} تا {{ $confEndJalali }}
                    </option>
                @endforeach
            </select>
            <small class="form-text" style="color: #6b7280;">فقط همایش‌های 45 روز آینده نمایش داده می‌شود</small>
        </div>

        <div style="border: 2px dashed #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px; background: #f9fafb;">
            <div style="font-weight: bold; margin-bottom: 15px; color: #1e3a8a;">اطلاعات مهمان</div>

            <div class="form-group">
                <label>
                    <input type="radio" name="guest_type" value="personnel" id="guestTypePersonnel" checked style="margin-left: 5px;">
                    پرسنل بانک
                </label>
                <label style="margin-right: 20px;">
                    <input type="radio" name="guest_type" value="external" id="guestTypeExternal" style="margin-left: 5px;">
                    مهمان خارجی
                </label>
            </div>

            <div id="personnelSection">
                <div class="form-group">
                    <label for="personnel_id">انتخاب پرسنل</label>
                    <select name="personnel_id" id="personnel_id" class="form-control">
                        <option value="">انتخاب کنید...</option>
                        @foreach($personnel as $person)
                            <option value="{{ $person->id }}" {{ old('personnel_id') == $person->id ? 'selected' : '' }}>
                                {{ $person->full_name }} ({{ $person->employment_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="externalSection" style="display: none;">
                <div class="form-group">
                    <label for="guest_name">نام و نام خانوادگی مهمان</label>
                    <input type="text" name="guest_name" id="guest_name" class="form-control" value="{{ old('guest_name') }}" placeholder="علی احمدی">
                </div>

                <div class="form-group">
                    <label for="guest_phone">شماره تماس</label>
                    <input type="text" name="guest_phone" id="guest_phone" class="form-control" value="{{ old('guest_phone') }}" placeholder="09121234567">
                </div>

                <div class="form-group">
                    <label for="guest_gender">جنسیت *</label>
                    <select name="guest_gender" id="guest_gender" class="form-control">
                        <option value="">انتخاب کنید...</option>
                        <option value="male" {{ old('guest_gender') == 'male' ? 'selected' : '' }}>آقا</option>
                        <option value="female" {{ old('guest_gender') == 'female' ? 'selected' : '' }}>خانم</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="room_id">انتخاب اتاق *</label>
            <select name="room_id" id="room_id" class="form-control" required>
                <option value="">ابتدا اتاق را انتخاب کنید...</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}" data-beds="{{ json_encode($room->beds) }}" {{ (old('room_id') == $room->id || (isset($selectedRoomId) && $selectedRoomId == $room->id)) ? 'selected' : '' }}>
                        واحد {{ $room->unit->number }} - اتاق {{ $room->number }} ({{ $room->unit->section == 'east' ? 'شرقی' : 'غربی' }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- تخت از پیش انتخاب شده -->
        <input type="hidden" id="preSelectedBedId" value="{{ $selectedBedId ?? '' }}">

        <div class="form-group" id="bedsSection" style="display: none;">
            <label>انتخاب تخت‌ها * (حداقل 1 تخت)</label>
            <div id="bedsList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin-top: 10px;">
                <!-- تخت‌ها به صورت داینامیک اضافه می‌شوند -->
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="check_in_date">تاریخ ورود (شمسی) *</label>
                <input type="text" name="check_in_date" class="jalali-datepicker form-control" id="check_in_date" value="{{ old('check_in_date') }}" required placeholder="۱۴۰۴/۱۰/۱۴">
            </div>

            <div class="form-group">
                <label for="check_out_date">تاریخ خروج (شمسی) *</label>
                <input type="text" name="check_out_date" class="jalali-datepicker form-control" id="check_out_date" value="{{ old('check_out_date') }}" required placeholder="۱۴۰۴/۱۰/۱۵">
            </div>
        </div>

        <div class="form-group">
            <label for="notes">یادداشت</label>
            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="توضیحات اضافی...">{{ old('notes') }}</textarea>
        </div>

        <div style="text-align: left;">
            <button type="submit" class="btn btn-success">ثبت رزرو</button>
            <a href="{{ route('reservations.index') }}" class="btn btn-secondary">انصراف</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const admissionTypeSelect = document.getElementById('admission_type_id');
        const courseSection = document.getElementById('courseSection');
        const conferenceSection = document.getElementById('conferenceSection');
        const courseSelect = document.getElementById('course_id');
        const conferenceSelect = document.getElementById('conference_id');
        const checkInDate = document.getElementById('check_in_date');
        const checkOutDate = document.getElementById('check_out_date');

        const personnelRadio = document.getElementById('guestTypePersonnel');
        const externalRadio = document.getElementById('guestTypeExternal');
        const personnelSection = document.getElementById('personnelSection');
        const externalSection = document.getElementById('externalSection');
        const personnelSelect = document.getElementById('personnel_id');
        const guestNameInput = document.getElementById('guest_name');
        const roomSelect = document.getElementById('room_id');
        const bedsSection = document.getElementById('bedsSection');
        const bedsList = document.getElementById('bedsList');
        const preSelectedBedId = document.getElementById('preSelectedBedId').value;

        // نمایش/مخفی کردن دوره/همایش بر اساس نوع پذیرش
        function updateAdmissionTypeSections() {
            const selectedOption = admissionTypeSelect.options[admissionTypeSelect.selectedIndex];
            const admissionTypeName = selectedOption.dataset.name || '';

            courseSection.style.display = 'none';
            conferenceSection.style.display = 'none';
            courseSelect.required = false;
            conferenceSelect.required = false;

            if (admissionTypeName.includes('دوره')) {
                courseSection.style.display = 'block';
                courseSelect.required = true;
            } else if (admissionTypeName.includes('همایش')) {
                conferenceSection.style.display = 'block';
                conferenceSelect.required = true;
            }
        }

        admissionTypeSelect.addEventListener('change', updateAdmissionTypeSections);

        // تنظیم خودکار تاریخ بر اساس دوره انتخاب شده
        courseSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const startDate = selectedOption.dataset.start;
                const endDate = selectedOption.dataset.end;
                checkInDate.value = startDate;
                checkOutDate.value = endDate;
                checkInDate.min = startDate;
                checkInDate.max = endDate;
                checkOutDate.min = startDate;
                checkOutDate.max = endDate;
            }
        });

        // تنظیم خودکار تاریخ بر اساس همایش انتخاب شده
        conferenceSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const startDate = selectedOption.dataset.start;
                const endDate = selectedOption.dataset.end;
                checkInDate.value = startDate;
                checkOutDate.value = endDate;
                checkInDate.min = startDate;
                checkInDate.max = endDate;
                checkOutDate.min = startDate;
                checkOutDate.max = endDate;
            }
        });

        // اجرای اولیه برای نمایش صحیح بخش‌ها
        updateAdmissionTypeSections();

        // Toggle guest type sections
        function updateGuestSections() {
            if (personnelRadio.checked) {
                personnelSection.style.display = 'block';
                externalSection.style.display = 'none';
                personnelSelect.required = true;
                guestNameInput.required = false;
                // Disable external guest fields
                guestNameInput.disabled = true;
                document.getElementById('guest_phone').disabled = true;
                document.getElementById('guest_gender').disabled = true;
            } else {
                personnelSection.style.display = 'none';
                externalSection.style.display = 'block';
                personnelSelect.required = false;
                guestNameInput.required = true;
                // Enable external guest fields
                guestNameInput.disabled = false;
                document.getElementById('guest_phone').disabled = false;
                document.getElementById('guest_gender').disabled = false;
                // Disable personnel field
                personnelSelect.disabled = true;
            }
        }

        personnelRadio.addEventListener('change', updateGuestSections);
        externalRadio.addEventListener('change', updateGuestSections);

        // اجرای اولیه
        updateGuestSections();

        // Load beds when room is selected
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
                const bedColor = {
                    'available': '#10b981',
                    'occupied': '#ef4444',
                    'needs_cleaning': '#f59e0b',
                    'under_maintenance': '#6b7280'
                }[bed.status] || '#e5e7eb';

                const isAvailable = bed.status === 'available';
                const isPreSelected = preSelectedBedId && bed.id == preSelectedBedId;

                const bedDiv = document.createElement('div');
                bedDiv.style.cssText = `
                    border: 2px solid ${isPreSelected ? '#3b82f6' : bedColor};
                    border-radius: 8px;
                    padding: 15px;
                    text-align: center;
                    cursor: ${isAvailable ? 'pointer' : 'not-allowed'};
                    opacity: ${isAvailable ? '1' : '0.5'};
                    transition: all 0.3s;
                    background: ${isPreSelected ? '#e0f2fe' : 'white'};
                `;

                bedDiv.innerHTML = `
                    <input type="checkbox" name="bed_ids[]" value="${bed.id}" id="bed_${bed.id}"
                        ${!isAvailable ? 'disabled' : ''}
                        ${isPreSelected && isAvailable ? 'checked' : ''}
                        style="margin-bottom: 5px;">
                    <label for="bed_${bed.id}" style="display: block; cursor: ${isAvailable ? 'pointer' : 'not-allowed'};">
                        <div style="font-weight: bold;">تخت ${bed.number}</div>
                        <div style="font-size: 11px; color: #6b7280; margin-top: 5px;">
                            ${bed.status === 'available' ? 'آزاد' :
                              bed.status === 'occupied' ? 'اشغال' :
                              bed.status === 'needs_cleaning' ? 'نظافت' : 'تعمیر'}
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

        roomSelect.addEventListener('change', loadBeds);

        function updateBedSelection(bedDiv, isChecked) {
            if (isChecked) {
                bedDiv.style.background = '#e0f2fe';
                bedDiv.style.borderColor = '#3b82f6';
            } else {
                bedDiv.style.background = 'white';
                bedDiv.style.borderColor = '#10b981';
            }
        }

        // اگر اتاق از قبل انتخاب شده، تخت‌ها را بارگذاری کن
        if (roomSelect.value) {
            loadBeds();
        }
    });
</script>
@endsection
