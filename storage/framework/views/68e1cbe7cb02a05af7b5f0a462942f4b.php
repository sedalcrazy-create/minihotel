<?php $__env->startSection('title', 'داشبورد'); ?>

<?php $__env->startSection('content'); ?>
<h2 class="mb-20">داشبورد مدیریت خوابگاه</h2>

<!-- آمار کلی تخت‌ها -->
<div class="stats-grid">
    <div class="stat-card available">
        <div class="stat-label">تخت‌های آزاد</div>
        <div class="stat-value"><?php echo e($availableBeds); ?></div>
        <div style="font-size: 12px; color: #6b7280;">از <?php echo e($totalBeds); ?> تخت</div>
    </div>

    <div class="stat-card occupied">
        <div class="stat-label">تخت‌های اشغال</div>
        <div class="stat-value"><?php echo e($occupiedBeds); ?></div>
        <div style="font-size: 12px; color: #6b7280;">در حال استفاده</div>
    </div>

    <div class="stat-card cleaning">
        <div class="stat-label">نیاز به نظافت</div>
        <div class="stat-value"><?php echo e($cleaningBeds); ?></div>
        <div style="font-size: 12px; color: #6b7280;">آماده نظافت</div>
    </div>

    <div class="stat-card maintenance">
        <div class="stat-label">در تعمیر</div>
        <div class="stat-value"><?php echo e($maintenanceBeds); ?></div>
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
        <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 15px; background: #f9fafb;">
                <div style="font-weight: bold; margin-bottom: 10px; color: #1e3a8a;">
                    واحد <?php echo e($unit->number); ?>

                    <span style="font-size: 11px; font-weight: normal; color: #6b7280;">
                        (<?php echo e($unit->section == 'east' ? 'شرقی' : 'غربی'); ?>)
                    </span>
                </div>

                <?php $__currentLoopData = $unit->rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="margin-bottom: 10px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">
                            اتاق <?php echo e($room->number); ?>

                        </div>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px;">
                            <?php $__currentLoopData = $room->beds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $color = match($bed->status) {
                                        'available' => '#10b981',
                                        'occupied' => '#ef4444',
                                        'needs_cleaning' => '#f59e0b',
                                        'under_maintenance' => '#6b7280',
                                        default => '#e5e7eb'
                                    };
                                ?>
                                <div
                                    style="background: <?php echo e($color); ?>; color: white; padding: 8px; border-radius: 5px; text-align: center; font-size: 11px; cursor: pointer;"
                                    title="<?php echo e($bed->identifier); ?> - <?php echo e($bed->status); ?>"
                                >
                                    تخت <?php echo e($bed->number); ?>

                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<!-- رزروهای فعال -->
<div class="card">
    <div class="card-header">
        رزروهای فعال (چک‌این شده)
        <a href="<?php echo e(route('reservations.index')); ?>" style="float: left; font-size: 14px; color: #3b82f6; text-decoration: none;">مشاهده همه →</a>
    </div>

    <?php if($activeReservations->count() > 0): ?>
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
                <?php $__currentLoopData = $activeReservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($reservation->guest_name); ?></td>
                        <td><?php echo e($reservation->admissionType->name); ?></td>
                        <td>واحد <?php echo e($reservation->room->unit->number); ?> - اتاق <?php echo e($reservation->room->number); ?></td>
                        <td><?php echo e($reservation->beds->pluck('number')->implode('، ')); ?></td>
                        <td><?php echo e($reservation->check_in_date); ?></td>
                        <td><?php echo e($reservation->check_out_date); ?></td>
                        <td>
                            <a href="<?php echo e(route('reservations.show', $reservation)); ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">مشاهده</a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center; color: #6b7280; padding: 20px;">رزرو فعالی وجود ندارد.</p>
    <?php endif; ?>
</div>

<!-- تعمیرات در انتظار -->
<?php if($pendingMaintenance->count() > 0): ?>
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
            <?php $__currentLoopData = $pendingMaintenance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $maintenance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($maintenance->bed->identifier); ?></td>
                    <td><?php echo e($maintenance->description); ?></td>
                    <td><?php echo e($maintenance->reporter->name); ?></td>
                    <td><?php echo e($maintenance->created_at->format('Y-m-d H:i')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/dashboard.blade.php ENDPATH**/ ?>