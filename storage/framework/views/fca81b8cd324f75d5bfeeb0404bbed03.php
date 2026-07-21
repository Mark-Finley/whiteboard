<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Reports</h3>
        <div class="muted-label">Generate system-wide reports for patients, wards, and specialties with downloadable output.</div>
    </div>
</div>

<form class="card section-card p-4 mb-4" method="GET">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">From</label>
            <input type="date" name="from_date" value="<?php echo e($fromDate); ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">To</label>
            <input type="date" name="to_date" value="<?php echo e($toDate); ?>" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Ward</label>
            <select name="ward" class="form-select">
                <option value="">All wards</option>
                <?php $__currentLoopData = $wardOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wardOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($wardOption->name); ?>" <?php if($selectedWard === $wardOption->name): echo 'selected'; endif; ?>><?php echo e($wardOption->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Specialty</label>
            <select name="specialty" class="form-select">
                <option value="">All specialties</option>
                <?php $__currentLoopData = $specialtyOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $specialtyOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($specialtyOption->name); ?>" <?php if($selectedSpecialty === $specialtyOption->name): echo 'selected'; endif; ?>><?php echo e($specialtyOption->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($statusOption); ?>" <?php if($selectedStatus === $statusOption): echo 'selected'; endif; ?>><?php echo e(ucfirst($statusOption)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Generate</button>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12 d-flex flex-wrap gap-2">
            <button type="submit" name="export" value="csv" class="btn btn-outline-secondary">Download CSV</button>
            <button type="submit" name="export" value="xlsx" class="btn btn-outline-secondary">Download XLSX</button>
            <button type="submit" name="export" value="pdf" class="btn btn-outline-secondary">Download PDF</button>
        </div>
    </div>
</form>

<div class="kpi-grid mb-4">
    <div class="card metric-card p-4">
        <div class="metric-label">Total Patients</div>
        <div class="metric-value"><?php echo e($totalPatients); ?></div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">Active</div>
        <div class="metric-value"><?php echo e($activeCount); ?></div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">Admitted</div>
        <div class="metric-value"><?php echo e($admittedCount); ?></div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">Discharged</div>
        <div class="metric-value"><?php echo e($dischargedCount); ?></div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">Deceased</div>
        <div class="metric-value text-danger"><?php echo e($deceasedCount); ?></div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card section-card h-100">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Ward Summary</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Ward</th>
                            <th>Active</th>
                            <th>Admitted</th>
                            <th>Transferred</th>
                            <th>Discharged</th>
                            <th>Deceased</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $wardSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ward): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($ward['name']); ?></td>
                                <td><?php echo e($ward['active']); ?></td>
                                <td><?php echo e($ward['admitted']); ?></td>
                                <td><?php echo e($ward['transferred']); ?></td>
                                <td><?php echo e($ward['discharged']); ?></td>
                                <td><?php echo e($ward['deceased']); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card section-card h-100">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Specialty Summary</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Specialty</th>
                            <th>Patients</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $specialtySummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($team['name']); ?></td>
                                <td><?php echo e($team['patients']); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">

<div class="card section-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Patient Report</h5>
            <div class="muted-label">List of patients matching the selected report filters.</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>GHIMS</th>
                    <th>Patient</th>
                    <th>Ward</th>
                    <th>Specialty</th>
                    <th>Status</th>
                    <th>Time In</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($patient->ghims_number); ?></td>
                        <td><?php echo e($patient->patient_name); ?></td>
                        <td><?php echo e($patient->ward?->name ?? 'Unassigned'); ?></td>
                        <td><?php echo e($patient->team?->name ?? 'Unassigned'); ?></td>
                        <td><?php echo e(ucfirst($patient->status)); ?></td>
                        <td><?php echo e(optional($patient->time_in)->format('d M Y, g:i a')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="text-center text-muted">No patients found for this report.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3"><?php echo e($patients->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\White Board\resources\views/reports/index.blade.php ENDPATH**/ ?>