<?php $__env->startSection('content'); ?>
<div class="card section-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h3 class="fw-bold mb-1">Access Control</h3>
            <div class="muted-label">Assign user roles, duties, wards, and account status from one place.</div>
        </div>
        <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">New user</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle datatable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Duty</th>
                    <th>Ward</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php ($formId = 'access-form-'.$user->id); ?>
                    <?php ($wardClass = strtolower(str_replace(' ', '-', $user->ward?->name ?? 'unassigned'))); ?>
                    <tr class="patient-ward-row patient-ward-row-<?php echo e($wardClass); ?>">
                        <td class="fw-semibold"><?php echo e($user->name); ?></td>
                        <td><?php echo e($user->email); ?></td>
                        <td style="min-width: 220px;">
                            <select name="role_id" form="<?php echo e($formId); ?>" class="form-select form-select-sm" required>
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role->id); ?>" <?php if($user->role_id == $role->id): echo 'selected'; endif; ?>><?php echo e($role->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td style="min-width: 220px;">
                            <select name="team_id" form="<?php echo e($formId); ?>" class="form-select form-select-sm">
                                <option value="">No duty</option>
                                <?php $__currentLoopData = $teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($team->id); ?>" <?php if($user->team_id == $team->id): echo 'selected'; endif; ?>><?php echo e($team->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td style="min-width: 220px;">
                            <select name="ward_id" form="<?php echo e($formId); ?>" class="form-select form-select-sm">
                                <option value="">No ward</option>
                                <?php $__currentLoopData = $wards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ward): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ward->id); ?>" <?php if($user->ward_id == $ward->id): echo 'selected'; endif; ?>><?php echo e($ward->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td style="min-width: 160px;">
                            <select name="status" form="<?php echo e($formId); ?>" class="form-select form-select-sm" required>
                                <option value="active" <?php if($user->status === 'active'): echo 'selected'; endif; ?>>Active</option>
                                <option value="inactive" <?php if($user->status === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
                            </select>
                        </td>
                        <td class="text-nowrap">
                            <form id="<?php echo e($formId); ?>" action="<?php echo e(route('users.update', $user)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <input type="hidden" name="name" value="<?php echo e($user->name); ?>">
                                <input type="hidden" name="email" value="<?php echo e($user->email); ?>">
                                <input type="hidden" name="phone" value="<?php echo e($user->phone); ?>">
                                <input type="hidden" name="password" value="">
                                <input type="hidden" name="password_confirmation" value="">
                                <button type="submit" class="btn btn-sm btn-primary">Save access</button>
                            </form>
                            <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-outline-secondary">Full edit</a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\White Board\resources\views/users/access-control.blade.php ENDPATH**/ ?>