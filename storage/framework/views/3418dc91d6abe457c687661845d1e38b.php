<?php $__env->startSection('content'); ?>
<div class="card section-card">
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Create user</h3>
        <div class="muted-label">Provision a secure KEPTS account.</div>
    </div>

    <?php echo $__env->make('users._form', [
        'action' => route('users.store'),
        'method' => 'POST',
        'user' => null,
        'submitLabel' => 'Create user',
        'roles' => $roles,
        'teams' => $teams,
        'wards' => $wards,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\White Board\resources\views/users/create.blade.php ENDPATH**/ ?>