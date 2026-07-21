<?php $__env->startSection('content'); ?>
<div class="row g-4">
    <div class="col-12">
        <div class="card section-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <div>
                    <h3 class="fw-bold mb-1">White Board</h3>
                    <div class="muted-label">Patients enter the triage queue first, then move automatically into their assigned ward board.</div>
                </div>
                <span class="loading-pill"><i class="fa-solid fa-layer-group"></i> Live patient board</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle datatable" data-live-url="<?php echo e(route('api.triage-patients')); ?>" data-table-kind="white-board">
                    <thead>
                        <tr>
                            <th>GHIMS</th>
                            <th>Patient</th>
                            <th>Ward</th>
                            <th>Assigned Team</th>
                            <th>Nurse Notes</th>
                            <th>Condition</th>
                            <th>LOS</th>
                            <th>Status</th>
                            <th>Time In</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $triagePatients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $statusClass = match($patient['status']) {
                                    'active' => 'badge-soft-green',
                                    'transferred' => 'badge-soft-orange',
                                    'admitted' => 'badge-soft-yellow',
                                    'deceased' => 'bg-dark',
                                    default => 'badge-soft-gray',
                                };
                            ?>
                            <tr class="patient-ward-row patient-ward-row-triage-holding">
                                <td><?php echo e($patient['ghims_number']); ?></td>
                                <td>
                                    <div class="patient-name"><?php echo e($patient['patient_name']); ?></div>
                                    <?php if(isset($patient['investigations']) && count($patient['investigations'])): ?>
                                        <div class="d-flex flex-wrap gap-1 mt-1 align-items-center">
                                            <?php $__currentLoopData = collect($patient['investigations'])->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $color = match($inv['status']) {
                                                        'Pending' => '#eab308',
                                                        'In Progress', 'Sample Taken', 'Sent' => '#2563eb',
                                                        'Completed' => '#16a34a',
                                                        'Cancelled' => '#dc2626',
                                                        default => '#6b7280',
                                                    };
                                                ?>
                                                <span class="badge cursor-pointer status-badge" 
                                                      data-id="<?php echo e($inv['id']); ?>" 
                                                      data-name="<?php echo e($inv['investigation_type']); ?>" 
                                                      data-status="<?php echo e($inv['status']); ?>" 
                                                      data-notes="<?php echo e($inv['notes'] ?? ''); ?>"
                                                      data-timeline='<?php echo json_encode($inv['updates'] ?? [], 15, 512) ?>'
                                                      style="background: <?php echo e($color); ?>; color: #fff; font-size: 0.7rem; padding: 0.35em 0.7em;" 
                                                      title="<?php echo e($inv['investigation_type']); ?> (<?php echo e($inv['status']); ?>)">
                                                    <?php echo e($inv['investigation_type']); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(count($patient['investigations']) > 5): ?>
                                                <span class="badge bg-secondary" style="font-size: 0.7rem;">+<?php echo e(count($patient['investigations']) - 5); ?> more</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: <?php echo e($patient['ward_color']); ?>; color: #fff;">
                                        <?php echo e($patient['current_ward'] ?? 'Unassigned'); ?>

                                    </span>
                                </td>
                                <td><?php echo e($patient['assigned_team'] ?? 'Unassigned'); ?></td>
                                    <td <?php if($patient['nurse_notes']): ?> data-bs-toggle="tooltip" title="<?php echo e($patient['nurse_notes']); ?>" <?php endif; ?>><?php echo e($patient['nurse_notes'] ? \Illuminate\Support\Str::limit($patient['nurse_notes'], 80) : '—'); ?></td>
                                <td><?php echo e(ucfirst($patient['condition'] ?? 'unknown')); ?></td>
                                <td <?php if(!empty($patient['ward_time_breakdown'])): ?> data-bs-toggle="tooltip" title="<?php echo e($patient['ward_time_breakdown']); ?>" <?php endif; ?>><?php echo e($patient['ward_time'] ?? '—'); ?></td>
                                <td><span class="badge <?php echo e($statusClass); ?>"><?php echo e(ucfirst($patient['status'])); ?></span></td>
                                <td><?php echo e($patient['time_in']); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card section-card h-100">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <h5 class="fw-bold mb-0">RED Ward</h5>
                <span class="badge" style="background: <?php echo e(\App\Models\Ward::RED_COLOR); ?>; color: #fff;">RED</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" data-live-url="<?php echo e(route('api.red-patients')); ?>" data-table-kind="white-board-ward">
                    <thead>
                        <tr>
                            <th>GHIMS</th>
                            <th>Patient</th>
                            <th>Assigned Team</th>
                                <th>Nurse Notes</th>
                                <th>LOS</th>
                                <th>Time In</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $redPatients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="patient-ward-row patient-ward-row-red">
                                <td><?php echo e($patient['ghims_number']); ?></td>
                                <td>
                                    <div class="patient-name"><?php echo e($patient['patient_name']); ?></div>
                                    <?php if(isset($patient['investigations']) && count($patient['investigations'])): ?>
                                        <div class="d-flex flex-wrap gap-1 mt-1 align-items-center">
                                            <?php $__currentLoopData = collect($patient['investigations'])->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $color = match($inv['status']) {
                                                        'Pending' => '#eab308',
                                                        'In Progress', 'Sample Taken', 'Sent' => '#2563eb',
                                                        'Completed' => '#16a34a',
                                                        'Cancelled' => '#dc2626',
                                                        default => '#6b7280',
                                                    };
                                                ?>
                                                <span class="badge cursor-pointer status-badge" 
                                                      data-id="<?php echo e($inv['id']); ?>" 
                                                      data-name="<?php echo e($inv['investigation_type']); ?>" 
                                                      data-status="<?php echo e($inv['status']); ?>" 
                                                      data-notes="<?php echo e($inv['notes'] ?? ''); ?>"
                                                      data-timeline='<?php echo json_encode($inv['updates'] ?? [], 15, 512) ?>'
                                                      style="background: <?php echo e($color); ?>; color: #fff; font-size: 0.7rem; padding: 0.35em 0.7em;" 
                                                      title="<?php echo e($inv['investigation_type']); ?> (<?php echo e($inv['status']); ?>)">
                                                    <?php echo e($inv['investigation_type']); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(count($patient['investigations']) > 5): ?>
                                                <span class="badge bg-secondary" style="font-size: 0.7rem;">+<?php echo e(count($patient['investigations']) - 5); ?> more</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($patient['assigned_team'] ?? 'Unassigned'); ?></td>
                                    <td <?php if($patient['nurse_notes']): ?> data-bs-toggle="tooltip" title="<?php echo e($patient['nurse_notes']); ?>" <?php endif; ?>><?php echo e($patient['nurse_notes'] ? \Illuminate\Support\Str::limit($patient['nurse_notes'], 80) : '—'); ?></td>
                                    <td><?php echo e($patient['ward_time'] ?? '—'); ?></td>
                                    <td><?php echo e($patient['time_in']); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card section-card h-100">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <h5 class="fw-bold mb-0">ORANGE Ward</h5>
                <span class="badge" style="background: <?php echo e(\App\Models\Ward::ORANGE_COLOR); ?>; color: #fff;">ORANGE</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" data-live-url="<?php echo e(route('api.orange-patients')); ?>" data-table-kind="white-board-ward">
                    <thead>
                        <tr>
                            <th>GHIMS</th>
                            <th>Patient</th>
                            <th>Assigned Team</th>
                                <th>Nurse Notes</th>
                                <th>LOS</th>
                                <th>Time In</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $orangePatients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="patient-ward-row patient-ward-row-orange">
                                <td><?php echo e($patient['ghims_number']); ?></td>
                                <td>
                                    <div class="patient-name"><?php echo e($patient['patient_name']); ?></div>
                                    <?php if(isset($patient['investigations']) && count($patient['investigations'])): ?>
                                        <div class="d-flex flex-wrap gap-1 mt-1 align-items-center">
                                            <?php $__currentLoopData = collect($patient['investigations'])->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $color = match($inv['status']) {
                                                        'Pending' => '#eab308',
                                                        'In Progress', 'Sample Taken', 'Sent' => '#2563eb',
                                                        'Completed' => '#16a34a',
                                                        'Cancelled' => '#dc2626',
                                                        default => '#6b7280',
                                                    };
                                                ?>
                                                <span class="badge cursor-pointer status-badge" 
                                                      data-id="<?php echo e($inv['id']); ?>" 
                                                      data-name="<?php echo e($inv['investigation_type']); ?>" 
                                                      data-status="<?php echo e($inv['status']); ?>" 
                                                      data-notes="<?php echo e($inv['notes'] ?? ''); ?>"
                                                      data-timeline='<?php echo json_encode($inv['updates'] ?? [], 15, 512) ?>'
                                                      style="background: <?php echo e($color); ?>; color: #fff; font-size: 0.7rem; padding: 0.35em 0.7em;" 
                                                      title="<?php echo e($inv['investigation_type']); ?> (<?php echo e($inv['status']); ?>)">
                                                    <?php echo e($inv['investigation_type']); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(count($patient['investigations']) > 5): ?>
                                                <span class="badge bg-secondary" style="font-size: 0.7rem;">+<?php echo e(count($patient['investigations']) - 5); ?> more</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($patient['assigned_team'] ?? 'Unassigned'); ?></td>
                                    <td <?php if($patient['nurse_notes']): ?> data-bs-toggle="tooltip" title="<?php echo e($patient['nurse_notes']); ?>" <?php endif; ?>><?php echo e($patient['nurse_notes'] ? \Illuminate\Support\Str::limit($patient['nurse_notes'], 80) : '—'); ?></td>
                                    <td><?php echo e($patient['ward_time'] ?? '—'); ?></td>
                                    <td><?php echo e($patient['time_in']); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card section-card h-100">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <h5 class="fw-bold mb-0">YELLOW Ward</h5>
                <span class="badge" style="background: <?php echo e(\App\Models\Ward::YELLOW_COLOR); ?>; color: #fff;">YELLOW</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" data-live-url="<?php echo e(route('api.yellow-patients')); ?>" data-table-kind="white-board-ward">
                    <thead>
                        <tr>
                            <th>GHIMS</th>
                            <th>Patient</th>
                            <th>Assigned Team</th>
                                <th>Nurse Notes</th>
                                <th>LOS</th>
                                <th>Time In</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $yellowPatients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="patient-ward-row patient-ward-row-yellow">
                                <td><?php echo e($patient['ghims_number']); ?></td>
                                <td>
                                    <div class="patient-name"><?php echo e($patient['patient_name']); ?></div>
                                    <?php if(isset($patient['investigations']) && count($patient['investigations'])): ?>
                                        <div class="d-flex flex-wrap gap-1 mt-1 align-items-center">
                                            <?php $__currentLoopData = collect($patient['investigations'])->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $color = match($inv['status']) {
                                                        'Pending' => '#eab308',
                                                        'In Progress', 'Sample Taken', 'Sent' => '#2563eb',
                                                        'Completed' => '#16a34a',
                                                        'Cancelled' => '#dc2626',
                                                        default => '#6b7280',
                                                    };
                                                ?>
                                                <span class="badge cursor-pointer status-badge" 
                                                      data-id="<?php echo e($inv['id']); ?>" 
                                                      data-name="<?php echo e($inv['investigation_type']); ?>" 
                                                      data-status="<?php echo e($inv['status']); ?>" 
                                                      data-notes="<?php echo e($inv['notes'] ?? ''); ?>"
                                                      data-timeline='<?php echo json_encode($inv['updates'] ?? [], 15, 512) ?>'
                                                      style="background: <?php echo e($color); ?>; color: #fff; font-size: 0.7rem; padding: 0.35em 0.7em;" 
                                                      title="<?php echo e($inv['investigation_type']); ?> (<?php echo e($inv['status']); ?>)">
                                                    <?php echo e($inv['investigation_type']); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(count($patient['investigations']) > 5): ?>
                                                <span class="badge bg-secondary" style="font-size: 0.7rem;">+<?php echo e(count($patient['investigations']) - 5); ?> more</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($patient['assigned_team'] ?? 'Unassigned'); ?></td>
                                    <td <?php if($patient['nurse_notes']): ?> data-bs-toggle="tooltip" title="<?php echo e($patient['nurse_notes']); ?>" <?php endif; ?>><?php echo e($patient['nurse_notes'] ? \Illuminate\Support\Str::limit($patient['nurse_notes'], 80) : '—'); ?></td>
                                    <td><?php echo e($patient['ward_time'] ?? '—'); ?></td>
                                    <td><?php echo e($patient['time_in']); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('partials.investigation-modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/White Board/resources/views/dashboard/white-board.blade.php ENDPATH**/ ?>