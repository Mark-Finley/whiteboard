<?php $__env->startSection('content'); ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card section-card h-100">
            <h3 class="fw-bold mb-3">Create ward</h3>
            <form action="<?php echo e(route('wards.store')); ?>" method="POST" class="vstack gap-3">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="form-label">Ward name</label>
                    <input type="text" name="name" class="form-control" placeholder="RED" required>
                </div>
                <div>
                    <label class="form-label">Color code</label>
                    <select name="color_code" class="form-select" required>
                        <option value="">Choose a ward color</option>
                        <?php $__currentLoopData = $colorOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $colorCode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($colorCode); ?>"><?php echo e($label); ?> (<?php echo e($colorCode); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <button class="btn btn-primary" type="submit">Save ward</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card section-card">
            <h3 class="fw-bold mb-3">Wards</h3>
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Color</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $wards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ward): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="fw-semibold"><?php echo e($ward->name); ?></td>
                                <td><span class="badge" style="background: <?php echo e($ward->color_code); ?>;"><?php echo e($ward->color_code); ?></span></td>
                                <td class="text-nowrap">
                                    <form action="<?php echo e(route('wards.update', $ward)); ?>" method="POST" class="d-inline-flex gap-2 align-items-center">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <input type="text" name="name" value="<?php echo e($ward->name); ?>" class="form-control form-control-sm" style="width: 130px;">
                                        <select name="color_code" class="form-select form-select-sm" style="width: 180px;">
                                            <?php $__currentLoopData = $colorOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $colorCode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($colorCode); ?>" <?php if($ward->color_code === $colorCode): echo 'selected'; endif; ?>><?php echo e($label); ?> (<?php echo e($colorCode); ?>)</option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <button class="btn btn-sm btn-outline-primary" type="submit">Update</button>
                                    </form>
                                    <form action="<?php echo e(route('wards.destroy', $ward)); ?>" method="POST" class="d-inline" data-confirm="Delete this ward?">
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\White Board\resources\views/wards/index.blade.php ENDPATH**/ ?>