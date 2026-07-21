<?php $__env->startSection('content'); ?>
<div class="card section-card">
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Register patient</h3>
        <div class="muted-label">Create a new emergency patient record.</div>
    </div>

    <?php echo $__env->make('patients._form', [
        'action' => route('patients.store'),
        'method' => 'POST',
        'patient' => null,
        'submitLabel' => 'Register patient',
        'wards' => $wards,
        'teams' => $teams,
         'showCondition' => false,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\White Board\resources\views/patients/create.blade.php ENDPATH**/ ?>