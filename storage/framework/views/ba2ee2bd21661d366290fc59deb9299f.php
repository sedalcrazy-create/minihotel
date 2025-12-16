<?php $__env->startSection('title', 'مدیریت پرسنل'); ?>

<?php $__env->startSection('content'); ?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>لیست پرسنل سازمان</h2>
    <div style="display: flex; gap: 10px;">
        <a href="<?php echo e(route('personnel.template')); ?>" class="btn btn-secondary" title="دانلود فایل نمونه با راهنما">📄 تمپلیت اکسل</a>
        <a href="<?php echo e(route('personnel.export')); ?>" class="btn btn-success">📥 خروجی اکسل</a>
        <button onclick="document.getElementById('importFile').click()" class="btn btn-primary">📤 ورود اکسل</button>
        <form id="importForm" action="<?php echo e(route('personnel.import')); ?>" method="POST" enctype="multipart/form-data" style="display: none;">
            <?php echo csrf_field(); ?>
            <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" onchange="document.getElementById('importForm').submit()">
        </form>
        <a href="<?php echo e(route('personnel.create')); ?>" class="btn btn-primary">+ افزودن پرسنل</a>
    </div>
</div>

<div class="card" style="background: linear-gradient(135deg, rgba(249, 108, 8, 0.05) 0%, rgba(255,255,255,0.95) 100%); border-right: 4px solid #f96c08;">
    <div style="display: flex; align-items: center; gap: 15px;">
        <div style="font-size: 36px;">📋</div>
        <div>
            <h3 style="margin-bottom: 8px; color: #f96c08;">راهنمای استفاده از فایل اکسل</h3>
            <p style="margin: 5px 0; color: #6b7280;">📄 ابتدا <strong>تمپلیت اکسل</strong> را دانلود کنید - این فایل شامل راهنمای کامل و نمونه داده است</p>
            <p style="margin: 5px 0; color: #6b7280;">✏️ فایل را با اطلاعات پرسنل پر کنید (ستون‌های الزامی با علامت * مشخص شده‌اند)</p>
            <p style="margin: 5px 0; color: #6b7280;">📤 فایل پر شده را از طریق دکمه <strong>ورود اکسل</strong> آپلود کنید</p>
            <p style="margin: 5px 0; color: #6b7280;">📥 برای دانلود لیست فعلی پرسنل از دکمه <strong>خروجی اکسل</strong> استفاده کنید</p>
        </div>
    </div>
</div>

<div class="card">
    <?php if($personnel->count() > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>کد پرسنلی</th>
                    <th>نام و نام خانوادگی</th>
                    <th>کد ملی</th>
                    <th>دپارتمان</th>
                    <th>سمت</th>
                    <th>وضعیت استخدام</th>
                    <th>تلفن</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $personnel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><strong><?php echo e($person->employment_code); ?></strong></td>
                        <td><?php echo e($person->full_name); ?></td>
                        <td><?php echo e($person->national_id); ?></td>
                        <td><?php echo e($person->department ?? '-'); ?></td>
                        <td><?php echo e($person->position ?? '-'); ?></td>
                        <td>
                            <?php
                                $statusMap = [
                                    'permanent' => 'رسمی',
                                    'contract' => 'قراردادی',
                                    'temporary' => 'موقت',
                                ];
                            ?>
                            <span class="badge badge-confirmed"><?php echo e($statusMap[$person->employment_status] ?? $person->employment_status); ?></span>
                        </td>
                        <td><?php echo e($person->phone ?? '-'); ?></td>
                        <td>
                            <a href="<?php echo e(route('personnel.show', $person)); ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">مشاهده</a>
                            <a href="<?php echo e(route('personnel.edit', $person)); ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 12px;">ویرایش</a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php echo e($personnel->links()); ?>

        </div>
    <?php else: ?>
        <p style="text-align: center; color: #6b7280; padding: 40px;">هیچ پرسنلی ثبت نشده است.</p>
        <div class="text-center">
            <a href="<?php echo e(route('personnel.create')); ?>" class="btn btn-primary">ثبت اولین پرسنل</a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/personnel/index.blade.php ENDPATH**/ ?>