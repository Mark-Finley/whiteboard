<?php $__env->startSection('content'); ?>
<div class="card section-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h3 class="fw-bold mb-1">Users</h3>
            <div class="muted-label">Manage hospital staff access, duties, and ward assignments.</div>
        </div>
        <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">New user</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle datatable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Duty</th>
                    <th>Ward</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php ($wardClass = strtolower(str_replace(' ', '-', $user->ward?->name ?? 'unassigned'))); ?>
                    <tr class="patient-ward-row patient-ward-row-<?php echo e($wardClass); ?>">
                        <td class="fw-semibold"><?php echo e($user->name); ?></td>
                        <td><?php echo e($user->email); ?></td>
                        <td><?php echo e($user->phone ?? '-'); ?></td>
                        <td><?php echo e($user->role?->name ?? '-'); ?></td>
                        <td><?php echo e($user->team?->name ?? '-'); ?></td>
                        <td>
                            <?php if($user->ward): ?>
                                <span class="badge" style="background: <?php echo e($user->ward->color_code); ?>; color: #fff;"><?php echo e($user->ward->name); ?></span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><span class="badge <?php echo e($user->status === 'active' ? 'badge-soft-green' : 'badge-soft-gray'); ?>"><?php echo e(ucfirst($user->status)); ?></span></td>
                        <td class="text-nowrap">
                            <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" class="d-inline" data-confirm="Delete this user account?">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3"><?php echo e($users->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\White Board\resources\views/users/index.blade.php ENDPATH**/ ?>