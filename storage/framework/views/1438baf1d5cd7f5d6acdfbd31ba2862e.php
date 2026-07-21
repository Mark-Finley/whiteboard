<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Overview</h3>
        <div class="muted-label">All time activity snapshot across wards and specialties.</div>
    </div>
</div>

<div class="kpi-grid mb-4">
    <div class="card metric-card p-4">
        <div class="metric-label">Total Active Patients</div>
        <div class="metric-value"><?php echo e($totalActivePatients); ?></div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">RED</div>
        <div class="metric-value" style="color:<?php echo e($redColor); ?>;"><?php echo e($redCount); ?></div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">ORANGE</div>
        <div class="metric-value" style="color:<?php echo e($orangeColor); ?>;"><?php echo e($orangeCount); ?></div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">YELLOW</div>
        <div class="metric-value" style="color:<?php echo e($yellowColor); ?>;"><?php echo e($yellowCount); ?></div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">TRIAGE HOLDING</div>
        <div class="metric-value" style="color:<?php echo e($triageColor); ?>;"><?php echo e($triageCount); ?></div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">Deceased</div>
        <div class="metric-value text-danger"><?php echo e($deceasedCount); ?></div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">Total Discharged</div>
        <div class="metric-value"><?php echo e($totalDischarged); ?></div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">Currently Admitted</div>
        <div class="metric-value"><?php echo e($currentAdmitted); ?></div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card chart-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold">Ward Distribution</h5>
                <span class="loading-pill"><i class="fa-solid fa-chart-pie"></i> Live overview</span>
            </div>
            <canvas id="wardChart" height="260"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card chart-card h-100">
            <h5 class="mb-3 fw-bold">Specialty Distribution</h5>
            <canvas id="specialtyChart" height="260"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card chart-card h-100">
            <h5 class="mb-3 fw-bold">Patient Growth</h5>
            <canvas id="activityChart" height="260"></canvas>
        </div>
    </div>
</div>

<div class="card section-card">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h5 class="fw-bold mb-1">Recent patients</h5>
            <div class="muted-label">Filter by ward, specialty, or patient name.</div>
        </div>
        <form class="row g-2 align-items-center" method="GET">
            <div class="col-auto">
                <select name="ward" class="form-select">
                    <option value="">All wards</option>
                    <?php $__currentLoopData = $wardOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wardOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($wardOption->name); ?>" <?php if(request('ward') === $wardOption->name): echo 'selected'; endif; ?>><?php echo e($wardOption->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-auto">
                <select name="specialty" class="form-select">
                    <option value="">All specialties</option>
                    <?php $__currentLoopData = $specialtyOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $specialtyOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($specialtyOption->name); ?>" <?php if(request('specialty') === $specialtyOption->name): echo 'selected'; endif; ?>><?php echo e($specialtyOption->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-auto"><input type="search" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Search"></div>
            <div class="col-auto"><button class="btn btn-primary" type="submit">Apply</button></div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle datatable">
            <thead>
                <tr>
                    <th>GHIMS</th>
                    <th>Patient</th>
                    <th>Specialty</th>
                    <th>Status</th>
                    <th>Time In</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="patient-ward-row patient-ward-row-<?php echo e(strtolower(str_replace(' ', '-', $patient->ward?->name ?? 'unassigned'))); ?>">
                        <td><?php echo e($patient->ghims_number); ?></td>
                        <td><div class="patient-name"><?php echo e($patient->patient_name); ?></div></td>
                        <td><?php echo e($patient->team?->name ?? 'Unassigned'); ?></td>
                        <td><span class="badge <?php echo e(match($patient->status) { 'active' => 'badge-soft-green', 'transferred' => 'badge-soft-orange', 'admitted' => 'badge-soft-yellow', default => 'badge-soft-gray' }); ?>"><?php echo e(ucfirst($patient->status)); ?></span></td>
                        <td><?php echo e(optional($patient->time_in)->format('d M Y, g:i a')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3"><?php echo e($patients->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const wardCtx = document.getElementById('wardChart');
if (wardCtx) {
    new Chart(wardCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($wardDistribution->pluck('name'), 15, 512) ?>,
            datasets: [{
                data: <?php echo json_encode($wardDistribution->pluck('count'), 15, 512) ?>,
                backgroundColor: <?php echo json_encode($wardDistribution->pluck('color'), 15, 512) ?>,
                borderWidth: 0,
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });
}

const specialtyCtx = document.getElementById('specialtyChart');
if (specialtyCtx) {
    new Chart(specialtyCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($specialtyDistribution->pluck('name'), 15, 512) ?>,
            datasets: [{
                label: 'Patients',
                data: <?php echo json_encode($specialtyDistribution->pluck('count'), 15, 512) ?>,
                backgroundColor: '<?php echo e($triageColor); ?>',
                borderRadius: 12,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
}

const activityCtx = document.getElementById('activityChart');
if (activityCtx) {
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_keys($wardDistribution->pluck('count')->toArray()), 15, 512) ?>,
            datasets: [
                {
                    label: 'Ward Patients',
                    data: <?php echo json_encode($wardDistribution->pluck('count'), 15, 512) ?>,
                    borderColor: '#0f766e',
                    backgroundColor: 'rgba(15,118,110,0.14)',
                    tension: 0.35,
                    fill: true,
                }
            ]
        },
        options: { plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\White Board\resources\views/dashboard/overview.blade.php ENDPATH**/ ?>