<?php $__env->startSection('content'); ?>
<div class="card section-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h3 class="fw-bold mb-1">Triage Holding</h3>
            <div class="muted-label">Patients awaiting assignment to RED, ORANGE, or YELLOW.</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle datatable" data-live-url="<?php echo e(route('api.triage-patients')); ?>" data-table-kind="ward-board">
            <thead>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/White Board/resources/views/dashboard/triage.blade.php ENDPATH**/ ?>