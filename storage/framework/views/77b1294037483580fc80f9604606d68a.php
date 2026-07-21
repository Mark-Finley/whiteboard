<?php $__env->startSection('content'); ?>
<div class="auth-card row g-0">
    <div class="col-lg-5 auth-hero d-flex flex-column justify-content-between">
        <div>
            <div class="brand-mark mb-4">KEPTS</div>
            <h1 class="display-6 fw-bold mb-3">KATH Emergency Live Patient Tracking System</h1>
            <p class="text-white-50 mb-0">
                Real-time emergency patient visibility for triage, ward teams, specialty doctors, and hospital command staff.
            </p>
        </div>

        <div class="mt-5 pt-4 border-top border-white border-opacity-25">
            <div class="d-flex gap-3 align-items-center mb-2">
                <i class="fa-solid fa-shield-heart fa-lg"></i>
                <strong>Secure internal access</strong>
            </div>
            <div class="small text-white-50">Role-based dashboards · live polling every 3 seconds · audit trails</div>
        </div>
    </div>

    <div class="col-lg-7 auth-form">
        <h2 class="fw-bold mb-2">Sign in</h2>
        <p class="text-muted mb-4">Use your hospital account to enter KEPTS.</p>

        <form method="POST" action="<?php echo e(route('login.submit')); ?>" class="vstack gap-3">
            <?php echo csrf_field(); ?>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="form-control form-control-lg <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="name@kath.gov.gh">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control form-control-lg <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Password">
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" name="remember" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button class="btn btn-primary btn-lg w-100" type="submit">Login to KEPTS</button>
            <div class="text-center pt-2">
                <span class="text-muted">Need an account?</span>
                <a href="<?php echo e(route('register')); ?>" class="fw-semibold text-decoration-none ms-1">Sign up</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/White Board/resources/views/auth/login.blade.php ENDPATH**/ ?>