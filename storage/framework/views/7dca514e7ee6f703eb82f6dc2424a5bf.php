<?php $__env->startSection('title', 'رزرو جدید'); ?>

<?php $__env->startSection('content'); ?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>رزرو جدید</h2>
    <a href="<?php echo e(route('reservations.index')); ?>" class="btn btn-secondary">بازگشت</a>
</div>

<div class="card">
    <form method="POST" action="<?php echo e(route('reservations.store')); ?>" id="reservationForm">
        <?php echo csrf_field(); ?>

        <div class="form-group">
            <label for="admission_type_id">نوع پذیرش *</label>
            <select name="admission_type_id" id="admission_type_id" class="form-control" required>
                <option value="">انتخاب کنید...</option>
                <?php $__currentLoopData = $admissionTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type->id); ?>" <?php echo e(old('admission_type_id') == $type->id ? 'selected' : ''); ?>>
                        <?php echo e($type->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
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
                        <?php $__currentLoopData = $personnel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($person->id); ?>" <?php echo e(old('personnel_id') == $person->id ? 'selected' : ''); ?>>
                                <?php echo e($person->full_name); ?> (<?php echo e($person->employment_code); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div id="externalSection" style="display: none;">
                <div class="form-group">
                    <label for="guest_name">نام و نام خانوادگی مهمان</label>
                    <input type="text" name="guest_name" id="guest_name" class="form-control" value="<?php echo e(old('guest_name')); ?>" placeholder="علی احمدی">
                </div>

                <div class="form-group">
                    <label for="guest_phone">شماره تماس</label>
                    <input type="text" name="guest_phone" id="guest_phone" class="form-control" value="<?php echo e(old('guest_phone')); ?>" placeholder="09121234567">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="room_id">انتخاب اتاق *</label>
            <select name="room_id" id="room_id" class="form-control" required>
                <option value="">ابتدا اتاق را انتخاب کنید...</option>
                <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($room->id); ?>" data-beds="<?php echo e(json_encode($room->beds)); ?>" <?php echo e(old('room_id') == $room->id ? 'selected' : ''); ?>>
                        واحد <?php echo e($room->unit->number); ?> - اتاق <?php echo e($room->number); ?> (<?php echo e($room->unit->section == 'east' ? 'شرقی' : 'غربی'); ?>)
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="form-group" id="bedsSection" style="display: none;">
            <label>انتخاب تخت‌ها * (حداقل 1 تخت)</label>
            <div id="bedsList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin-top: 10px;">
                <!-- تخت‌ها به صورت داینامیک اضافه می‌شوند -->
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="check_in_date">تاریخ ورود *</label>
                <input type="date" name="check_in_date" id="check_in_date" class="form-control" value="<?php echo e(old('check_in_date')); ?>" required>
            </div>

            <div class="form-group">
                <label for="check_out_date">تاریخ خروج *</label>
                <input type="date" name="check_out_date" id="check_out_date" class="form-control" value="<?php echo e(old('check_out_date')); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="notes">یادداشت</label>
            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="توضیحات اضافی..."><?php echo e(old('notes')); ?></textarea>
        </div>

        <div style="text-align: left;">
            <button type="submit" class="btn btn-success">ثبت رزرو</button>
            <a href="<?php echo e(route('reservations.index')); ?>" class="btn btn-secondary">انصراف</a>
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

        // Load beds when room is selected
        roomSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];

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

                const bedDiv = document.createElement('div');
                bedDiv.style.cssText = `
                    border: 2px solid ${bedColor};
                    border-radius: 8px;
                    padding: 15px;
                    text-align: center;
                    cursor: ${isAvailable ? 'pointer' : 'not-allowed'};
                    opacity: ${isAvailable ? '1' : '0.5'};
                    transition: all 0.3s;
                    background: white;
                `;

                bedDiv.innerHTML = `
                    <input type="checkbox" name="bed_ids[]" value="${bed.id}" id="bed_${bed.id}"
                        ${!isAvailable ? 'disabled' : ''}
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
        });

        function updateBedSelection(bedDiv, isChecked) {
            if (isChecked) {
                bedDiv.style.background = '#e0f2fe';
                bedDiv.style.borderColor = '#3b82f6';
            } else {
                bedDiv.style.background = 'white';
                const checkbox = bedDiv.querySelector('input');
                const bedColor = {
                    'available': '#10b981',
                    'occupied': '#ef4444',
                    'needs_cleaning': '#f59e0b',
                    'under_maintenance': '#6b7280'
                }[checkbox.dataset.status] || '#10b981';
                bedDiv.style.borderColor = bedColor;
            }
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/reservations/create.blade.php ENDPATH**/ ?>