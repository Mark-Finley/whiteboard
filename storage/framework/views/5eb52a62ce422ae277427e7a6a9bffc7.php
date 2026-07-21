<?php
    $currentPatient = $patient ?? null;
?>

<form method="POST" action="<?php echo e($action); ?>" class="row g-3">
    <?php echo csrf_field(); ?>
<?php if(isset($method) && $method !== 'POST'): ?>
    <input type="hidden" name="_method" value="<?php echo e($method); ?>">
<?php endif; ?>

    <div class="col-md-6">
        <label class="form-label">GHIMS Number</label>
        <input type="text" name="ghims_number" value="<?php echo e(old('ghims_number', $currentPatient?->ghims_number)); ?>" class="form-control <?php $__errorArgs = ['ghims_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
        <?php $__errorArgs = ['ghims_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-6">
        <label class="form-label">Patient Full Name</label>
        <input type="text" name="patient_name" value="<?php echo e(old('patient_name', $currentPatient?->patient_name)); ?>" class="form-control <?php $__errorArgs = ['patient_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
        <?php $__errorArgs = ['patient_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Age</label>
        <input id="patient-age" type="number" min="0" max="150" name="age" value="<?php echo e(old('age', $currentPatient?->age)); ?>" class="form-control <?php $__errorArgs = ['age'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
        <?php $__errorArgs = ['age'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Date of Birth</label>
        <input id="patient-date-of-birth" type="date" name="date_of_birth" value="<?php echo e(old('date_of_birth', optional($currentPatient?->date_of_birth)->format('Y-m-d'))); ?>" class="form-control <?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
        <?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Triage Outcome</label>
        <select name="triage_outcome" class="form-select <?php $__errorArgs = ['triage_outcome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
            <option value="">Select outcome</option>
            <option value="alive" <?php if(old('triage_outcome') === 'alive'): echo 'selected'; endif; ?>>Alive — admit and treat</option>
            <option value="dead" <?php if(old('triage_outcome') === 'dead'): echo 'selected'; endif; ?>>Brought in dead</option>
        </select>
        <?php $__errorArgs = ['triage_outcome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
    <label class="form-label">Specialty Teams</label>
<div class="row g-2">
    <?php
        $selectedTeamIds = old('team_ids', $currentPatient ? $currentPatient->teams->pluck('id')->toArray() : []);
    ?>
    <?php $__currentLoopData = $teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="col-6">
            <div class="form-check w-100">>
                <input class="form-check-input" type="checkbox" name="team_ids[]" value="<?php echo e($team->id); ?>" id="team_<?php echo e($team->id); ?>" <?php if(in_array($team->id, $selectedTeamIds)): ?> checked <?php endif; ?>>
                <label class="form-check-label" for="team_<?php echo e($team->id); ?>">
                    <?php echo e($team->name); ?>

                </label>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__errorArgs = ['team_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-12">
        <label class="form-label">Chief Complaint</label>
        <textarea name="chief_complaint" rows="4" class="form-control <?php $__errorArgs = ['chief_complaint'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('chief_complaint', $currentPatient?->chief_complaint)); ?></textarea>
        <?php $__errorArgs = ['chief_complaint'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-12">
        <label class="form-label">Nurse Notes</label>
        <textarea name="nurse_notes" rows="4" class="form-control <?php $__errorArgs = ['nurse_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Enter nurse notes or observations"><?php echo e(old('nurse_notes', $currentPatient?->nurse_notes)); ?></textarea>
        <?php $__errorArgs = ['nurse_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <?php if($showCondition ?? true): ?>
        <div class="col-md-4">
            <label class="form-label">Condition</label>
            <select name="condition" class="form-select <?php $__errorArgs = ['condition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <option value="stable" <?php if(old('condition', $currentPatient?->condition ?? 'stable') === 'stable'): echo 'selected'; endif; ?>>Stable</option>
                <option value="moderate" <?php if(old('condition', $currentPatient?->condition ?? '') === 'moderate'): echo 'selected'; endif; ?>>Moderate</option>
                <option value="serious" <?php if(old('condition', $currentPatient?->condition ?? '') === 'serious'): echo 'selected'; endif; ?>>Serious</option>
                <option value="critical" <?php if(old('condition', $currentPatient?->condition ?? '') === 'critical'): echo 'selected'; endif; ?>>Critical</option>
            </select>
            <?php $__errorArgs = ['condition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    <?php endif; ?>
    <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit"><?php echo e($submitLabel ?? 'Save Patient'); ?></button>
        <a href="<?php echo e(route('patients.index')); ?>" class="btn btn-light">Cancel</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ageInput = document.getElementById('patient-age');
            const dobInput = document.getElementById('patient-date-of-birth');

            if (!ageInput || !dobInput) {
                return;
            }

            const formatDate = (date) => date.toISOString().slice(0, 10);

            const updateDobFromAge = () => {
                const age = parseInt(ageInput.value, 10);
                if (Number.isNaN(age) || age < 0) {
                    return;
                }

                const today = new Date();
                const dob = new Date(today.getFullYear() - age, today.getMonth(), today.getDate());
                dobInput.value = formatDate(dob);
            };

            const updateAgeFromDob = () => {
                const dob = new Date(dobInput.value);
                if (Number.isNaN(dob.getTime())) {
                    return;
                }

                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                const monthDiff = today.getMonth() - dob.getMonth();
                const dayDiff = today.getDate() - dob.getDate();

                if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
                    age -= 1;
                }

                ageInput.value = age >= 0 ? age : 0;
            };

            ageInput.addEventListener('input', updateDobFromAge);
            dobInput.addEventListener('change', updateAgeFromDob);
        });
    </script>
</form>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/White Board/resources/views/patients/_form.blade.php ENDPATH**/ ?>