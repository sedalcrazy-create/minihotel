<?php $__env->startSection('title', 'لیست رزروها'); ?>

<?php $__env->startSection('content'); ?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>لیست رزروها</h2>
    <a href="<?php echo e(route('reservations.create')); ?>" class="btn btn-primary">+ رزرو جدید</a>
</div>

<div class="card">
    <?php if($reservations->count() > 0): ?>
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
                <?php $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($reservation->id); ?></td>
                        <td><?php echo e($reservation->guest_name); ?></td>
                        <td><?php echo e($reservation->admissionType->name); ?></td>
                        <td>واحد <?php echo e($reservation->room->unit->number); ?> - اتاق <?php echo e($reservation->room->number); ?></td>
                        <td>
                            <?php if($reservation->beds->count() > 0): ?>
                                <?php echo e($reservation->beds->pluck('number')->implode('، ')); ?>

                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($reservation->check_in_date); ?></td>
                        <td><?php echo e($reservation->check_out_date); ?></td>
                        <td>
                            <?php
                                $statusMap = [
                                    'pending' => ['label' => 'در انتظار', 'class' => 'badge-pending'],
                                    'confirmed' => ['label' => 'تایید شده', 'class' => 'badge-confirmed'],
                                    'checked_in' => ['label' => 'چک‌این شده', 'class' => 'badge-checked-in'],
                                    'checked_out' => ['label' => 'چک‌اوت شده', 'class' => 'badge-checked-out'],
                                    'cancelled' => ['label' => 'لغو شده', 'class' => 'badge-cancelled'],
                                ];
                                $status = $statusMap[$reservation->status] ?? ['label' => $reservation->status, 'class' => 'badge-pending'];
                            ?>
                            <span class="badge <?php echo e($status['class']); ?>"><?php echo e($status['label']); ?></span>
                        </td>
                        <td>
                            <a href="<?php echo e(route('reservations.show', $reservation)); ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">مشاهده</a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php echo e($reservations->links()); ?>

        </div>
    <?php else: ?>
        <p style="text-align: center; color: #6b7280; padding: 40px;">رزروی ثبت نشده است.</p>
        <div class="text-center">
            <a href="<?php echo e(route('reservations.create')); ?>" class="btn btn-primary">ایجاد اولین رزرو</a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/reservations/index.blade.php ENDPATH**/ ?>