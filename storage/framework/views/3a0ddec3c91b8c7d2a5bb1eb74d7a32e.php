<?php $__env->startSection('content'); ?>
<?php
    $liveUrl = match ($ward->name) {
        'RED' => route('api.red-patients'),
        'ORANGE' => route('api.orange-patients'),
        'YELLOW' => route('api.yellow-patients'),
        default => route('api.triege-patients'),
    };
    $userWardColor = optional(auth()->user()->ward)->color_code ?? $ward->color_code;
?>

<div class="card section-card mb-4" style="border: 2px solid <?php echo e($userWardColor); ?>;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h3 class="fw-bold mb-1"><?php echo e($ward->name); ?> Ward</h3>
            <div class="muted-label"><?php echo e($ward->name); ?> patients live view.</div>
        </div>
        <span class="badge" style="background: <?php echo e($userWardColor); ?>; color: #fff;"><?php echo e($ward->name); ?></span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle datatable" style="border-top: 3px solid <?php echo e($userWardColor); ?>;" data-live-url="<?php echo e($liveUrl); ?>" data-table-kind="ward-board">
            <thead style="background: <?php echo e($userWardColor); ?>; color: #fff;">
                <tr>
                    <th>GHIMS</th>
                    <th>Patient Name</th>
                    <th>Assigned Team</th>
                    <th>Time In</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($patient->ghims_number); ?></td>
                        <td><div class="patient-name"><?php echo e($patient->patient_name); ?></div></td>
                        <td><?php echo e($patient->team?->name ?? 'Unassigned'); ?></td>
                        <td><?php echo e(optional($patient->time_in)->format('d M Y, H:i')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\White Board\resources\views/dashboard/ward.blade.php ENDPATH**/ ?>