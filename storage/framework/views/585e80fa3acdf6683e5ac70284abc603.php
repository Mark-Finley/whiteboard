<?php $__env->startSection('content'); ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card section-card h-100">
            <h3 class="fw-bold mb-3">Create specialty team</h3>
            <form action="<?php echo e(route('teams.store')); ?>" method="POST" class="vstack gap-3">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="form-label">Team name</label>
                    <input type="text" name="name" class="form-control" placeholder="Emergency Medicine" required>
                </div>
                <button class="btn btn-primary" type="submit">Save team</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card section-card">
            <h3 class="fw-bold mb-3">Specialty teams</h3>
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="fw-semibold"><?php echo e($team->name); ?></td>
                                <td class="text-nowrap">
                                    <form action="<?php echo e(route('teams.update', $team)); ?>" method="POST" class="d-inline-flex gap-2 align-items-center">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <input type="text" name="name" value="<?php echo e($team->name); ?>" class="form-control form-control-sm" style="width: 220px;">
                                        <button class="btn btn-sm btn-outline-primary" type="submit">Update</button>
                                    </form>
                                    <form action="<?php echo e(route('teams.destroy', $team)); ?>" method="POST" class="d-inline" data-confirm="Delete this team?">
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
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/White Board/resources/views/teams/index.blade.php ENDPATH**/ ?>