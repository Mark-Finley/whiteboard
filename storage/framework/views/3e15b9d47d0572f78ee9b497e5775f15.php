<?php $__env->startSection('content'); ?>
<div class="card section-card">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h3 class="fw-bold">Patient: <?php echo e($patient->patient_name); ?></h3>
            <div class="muted-label">GHIMS: <?php echo e($patient->ghims_number); ?> • Age <?php echo e($patient->age); ?> • Status: <strong><?php echo e(ucfirst($patient->status)); ?></strong></div>
        </div>
        <div>
            <a href="<?php echo e(route('patients.index')); ?>" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-3 mb-3">
                <h5 class="mb-2">Details</h5>
                <p><strong>Ward:</strong> <?php echo e($patient->ward?->name ?? 'Unassigned'); ?></p>
                <p><strong>Specialty Teams:</strong> <?php echo e($patient->teams->pluck('name')->join(', ') ?: ($patient->team?->name ?? 'Unassigned')); ?></p>
                <p><strong>Time In:</strong> <?php echo e(optional($patient->time_in)->format('d M Y, H:i')); ?> • <strong>LOS:</strong> <?php echo e($patient->ward_time_spent ?? '—'); ?></p>
                <p><strong>Chief Complaint:</strong><br><?php echo e($patient->chief_complaint); ?></p>
            </div>

            <div class="card p-3 mb-3">
                <h5 class="mb-2">Nurse Notes</h5>
                <div class="mb-2"><?php echo e($patient->nurse_notes ? nl2br(e($patient->nurse_notes)) : '<span class="text-muted">No notes recorded.</span>'); ?></div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $patient)): ?>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#notesModal">Edit Notes</button>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Investigations Panel -->
            <div class="card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-flask me-2 text-teal"></i>Investigations & Procedures</h5>
                    <?php if(auth()->user()?->canAssignProcedures()): ?>
                        <button class="btn btn-sm btn-outline-teal rounded-pill" id="assignInvestigationBtn" data-patient-id="<?php echo e($patient->id); ?>" data-patient-name="<?php echo e($patient->patient_name); ?>">
                            <i class="fa-solid fa-plus me-1"></i>Assign
                        </button>
                    <?php endif; ?>
                </div>

                <?php if($patient->investigations && $patient->investigations->count()): ?>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $patient->investigations->sortByDesc('assigned_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $statusClass = match($inv->status) {
                                    'Pending' => 'badge-soft-yellow',
                                    'Sample Taken' => 'badge-soft-orange',
                                    'Sent' => 'badge-soft-orange',
                                    'In Progress' => 'badge-soft-primary',
                                    'Completed' => 'badge-soft-green',
                                    'Cancelled' => 'badge-soft-red',
                                    default => 'badge-soft-gray',
                                };
                                $priorityClass = match($inv->priority) {
                                    'Stat' => 'bg-danger text-white',
                                    'Urgent' => 'bg-warning text-dark',
                                    default => 'bg-secondary text-white',
                                };
                            ?>
                            <div class="list-group-item px-0 py-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                            <?php echo e($inv->investigation_type); ?>

                                        </div>
                                        <div class="text-muted small mt-1" style="font-size: 0.8rem;">
                                            Category: <span class="fw-semibold text-secondary"><?php echo e($inv->category); ?></span> | 
                                            Priority: <span class="badge <?php echo e($priorityClass); ?>"><?php echo e($inv->priority); ?></span>
                                        </div>
                                        <?php if($inv->notes): ?>
                                            <div class="text-secondary small mt-1 bg-light p-2 rounded" style="border-left: 3px solid #0f766e; font-size: 0.8rem;">
                                                <?php echo e($inv->notes); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge cursor-pointer status-badge <?php echo e($statusClass); ?>" 
                                              data-id="<?php echo e($inv->id); ?>" 
                                              data-name="<?php echo e($inv->investigation_type); ?>" 
                                              data-status="<?php echo e($inv->status); ?>" 
                                              data-notes="<?php echo e($inv->notes); ?>"
                                              data-timeline='<?php echo json_encode($inv->getTimelineData(), 15, 512) ?>'
                                              style="font-size: 0.8rem; padding: 0.4em 0.8em; cursor: pointer;">
                                            <?php echo e($inv->status); ?>

                                        </span>
                                        <div class="text-muted small mt-1" style="font-size: 0.7rem; line-height: 1.2;">
                                            Assigned by <?php echo e($inv->assignedBy?->name ?? 'System'); ?><br>
                                            <?php echo e(optional($inv->assigned_at)->format('d M Y, H:i')); ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-muted small py-3 text-center">No investigations or procedures assigned yet.</div>
                <?php endif; ?>
            </div>

            <!-- Movements Panel -->
            <div class="card p-3">
                <h5 class="mb-2 fw-bold">Movements</h5>
                <?php if($patient->movements && $patient->movements->count()): ?>
                    <ul class="list-group">
                        <?php $__currentLoopData = $patient->movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item">
                                <div><strong><?php echo e($m->action); ?></strong> by <?php echo e($m->movedBy?->name ?? 'System'); ?> on <?php echo e(optional($m->created_at)->format('d M Y, H:i')); ?></div>
                                <?php if($m->notes): ?>
                                    <div class="muted-label"><?php echo e($m->notes); ?></div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <div class="text-muted">No movements recorded.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('partials.investigation-modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<div class="mt-3">
    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#timeBreakdownModal">View LOS Breakdown</button>
</div>

<!-- Time breakdown modal -->
<div class="modal fade" id="timeBreakdownModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                        <h5 class="modal-title">LOS Breakdown</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php ($timeline = $patient->ward_timeline); ?>
                <?php if($timeline && count($timeline)): ?>
                    <ul class="list-group">
                        <?php $__currentLoopData = $timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item">
                                <div>
                                    <div><strong><?php echo e($segment['ward']); ?></strong></div>
                                    <div class="muted-label"><?php echo e($segment['start']); ?> → <?php echo e($segment['end']); ?></div>
                                    <div class="muted-label">Duration: <?php echo e($segment['duration']); ?></div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <div class="text-muted">No time data available.</div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Notes modal -->
<div class="modal fade" id="notesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Nurse Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?php echo e(route('patients.notes', $patient)); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <textarea name="nurse_notes" rows="6" class="form-control"><?php echo e(old('nurse_notes', $patient->nurse_notes)); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save notes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/White Board/resources/views/patients/show.blade.php ENDPATH**/ ?>