<?php $__env->startSection('content'); ?>
<div class="card section-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h3 class="fw-bold mb-1">Patient search</h3>
            <div class="muted-label">Search by GHIMS number or patient name.</div>
        </div>
        <form method="GET" class="d-flex gap-2">
            <input type="search" name="query" value="<?php echo e($query ?? request('query')); ?>" class="form-control" placeholder="GHIMS number or patient name">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle datatable">
            <thead>
                <tr>
                    <th>GHIMS</th>
                    <th>Patient</th>
                    <th>Ward</th>
                    <th>Specialty</th>
                    <th>Complaint</th>
                    <th>Time In</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($patient->ghims_number); ?></td>
                        <td class="fw-semibold"><?php echo e($patient->patient_name); ?></td>
                        <td><?php echo e($patient->ward?->name ?? 'Unassigned'); ?></td>
                        <td><?php echo e($patient->team?->name ?? 'Unassigned'); ?></td>
                        <td><?php echo e($patient->chief_complaint); ?></td>
                        <td><?php echo e(optional($patient->time_in)->format('d M Y, H:i')); ?></td>
                        <td><?php echo e(ucfirst($patient->status)); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/White Board/resources/views/search/index.blade.php ENDPATH**/ ?>