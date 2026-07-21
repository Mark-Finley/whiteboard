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
                    <div class="input-group">
                        <input type="color" name="color_code" class="form-control form-control-color" style="width: 80px; cursor: pointer;" value="#6b7280" required title="Click to choose a color">
                        <input type="text" class="form-control" placeholder="#000000" title="Enter hex color code (e.g., #dc2626)" readonly>
                    </div>
                    <small class="text-muted d-block mt-2">Quick presets:</small>
                    <div class="d-flex gap-2 mt-2 flex-wrap">
                        <?php $__currentLoopData = $colorOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $colorCode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" class="btn btn-sm" style="background: <?php echo e($colorCode); ?>; color: #fff; border: 2px solid <?php echo e($colorCode); ?>;" onclick="document.querySelector('input[name=color_code]').value='<?php echo e($colorCode); ?>'; document.querySelector('input[type=text]').value='<?php echo e($colorCode); ?>';" title="<?php echo e($label); ?>">
                                <?php echo e($label); ?>

                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
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
                                        <input type="color" name="color_code" value="<?php echo e($ward->color_code); ?>" class="form-control form-control-color form-control-sm" style="width: 80px; cursor: pointer;" title="Click to choose a color">
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

<?php $__env->startPush('scripts'); ?>
<script>
    // Update hex display when color picker changes
    document.querySelectorAll('input[type="color"][name="color_code"]').forEach(colorInput => {
        const updateHexDisplay = () => {
            const textInput = colorInput.parentElement.querySelector('input[type="text"]');
            if (textInput) {
                textInput.value = colorInput.value.toUpperCase();
            }
        };
        colorInput.addEventListener('change', updateHexDisplay);
        colorInput.addEventListener('input', updateHexDisplay);
        // Set initial value
        updateHexDisplay();
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\White Board\resources\views/wards/index.blade.php ENDPATH**/ ?>