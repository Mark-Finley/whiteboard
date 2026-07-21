<?php $__env->startSection('content'); ?>
<div class="card section-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h3 class="fw-bold mb-1">Patients</h3>
            <div class="muted-label">Search, update, transfer, discharge, and admit patients.</div>
        </div>
        <div class="d-flex gap-2">
            <form method="GET" class="d-flex gap-2">
                <input type="search" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Search patients">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </form>
            <a href="<?php echo e(route('patients.create')); ?>" class="btn btn-primary">New patient</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle datatable">
            <thead>
                <tr>
                    <th>GHIMS</th>
                    <th>Patient</th>
                    <th>Ward</th>
                    <th>Specialty</th>
                    <th>Status</th>
                    <th>Time In</th>
                    <th>LOS</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($patient->ghims_number); ?></td>
                        <td>
                            <div class="patient-name"><?php echo e($patient->patient_name); ?></div>
                            <div class="muted-label">Age <?php echo e($patient->age); ?></div>
                            <?php if($patient->nurse_notes): ?>
                                <div class="muted-label" data-bs-toggle="tooltip" title="<?php echo e($patient->nurse_notes); ?>">Notes: <?php echo e(\Illuminate\Support\Str::limit($patient->nurse_notes, 60)); ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($patient->ward?->name ?? 'Unassigned'); ?></td>
                        <td><?php echo e($patient->team?->name ?? 'Unassigned'); ?></td>
                        <td><span class="badge <?php echo e(match($patient->status) { 'active' => 'badge-soft-green', 'transferred' => 'badge-soft-orange', 'admitted' => 'badge-soft-yellow', 'deceased' => 'bg-dark', 'cancelled' => 'badge-soft-secondary', default => 'badge-soft-gray' }); ?>"><?php echo e(ucfirst($patient->status)); ?></span></td>
                        <td><?php echo e(optional($patient->time_in)->format('d M Y, H:i')); ?></td>
                        <td><?php echo e($patient->ward_time_spent ?? '—'); ?></td>
                        <td class="text-nowrap">
                            <a href="<?php echo e(route('patients.show', $patient)); ?>" class="btn btn-sm btn-outline-info">View</a>
                            <?php ($user = auth()->user()); ?>
                            <?php if($user?->isAdmin() || $user?->isTriage() || $user?->isTriageDoctor()): ?>
                                <?php if($patient->status === 'cancelled'): ?>
                                    <button type="button" class="btn btn-sm btn-outline-success redo-btn" data-patient-id="<?php echo e($patient->id); ?>">Redo</button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger cancel-btn" data-patient-id="<?php echo e($patient->id); ?>" data-patient-name="<?php echo e($patient->patient_name); ?>">Cancel</button>
                                <?php endif; ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary transfer-btn" data-patient-id="<?php echo e($patient->id); ?>" data-patient-name="<?php echo e($patient->patient_name); ?>">Transfer</button>
                                <button type="button" class="btn btn-sm btn-outline-warning discharge-btn" data-patient-id="<?php echo e($patient->id); ?>" data-patient-name="<?php echo e($patient->patient_name); ?>">Discharge</button>
                                <button type="button" class="btn btn-sm btn-outline-dark deceased-btn" data-patient-id="<?php echo e($patient->id); ?>" data-patient-name="<?php echo e($patient->patient_name); ?>">Deceased</button>
                            <?php elseif($user?->isWard()): ?>
                                <?php if($patient->status !== 'cancelled'): ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary transfer-btn" data-patient-id="<?php echo e($patient->id); ?>" data-patient-name="<?php echo e($patient->patient_name); ?>">Transfer</button>
                                    <button type="button" class="btn btn-sm btn-outline-warning discharge-btn" data-patient-id="<?php echo e($patient->id); ?>" data-patient-name="<?php echo e($patient->patient_name); ?>">Discharge</button>
                                    <button type="button" class="btn btn-sm btn-outline-dark deceased-btn" data-patient-id="<?php echo e($patient->id); ?>" data-patient-name="<?php echo e($patient->patient_name); ?>">Deceased</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3"><?php echo e($patients->links()); ?></div>
</div>

<!-- Transfer modal -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transfer patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="transferForm" method="POST" action="#">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-2">Transfer <strong id="transferPatientName"></strong> to:</div>
                    <div class="mb-3">
                        <select name="to_ward_id" class="form-select" required>
                            <option value="">Choose ward</option>
                            <?php $__currentLoopData = $wards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ward): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ward->id); ?>"><?php echo e($ward->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <textarea name="notes" class="form-control" placeholder="Notes (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cancelForm" method="POST" action="#">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-2">Cancel <strong id="cancelPatientName"></strong> and record the reason.</div>
                    <div class="mb-3">
                        <textarea name="cancel_reason" class="form-control" rows="4" placeholder="Cancellation reason (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                    <button type="submit" class="btn btn-danger">Cancel patient</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="dischargeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Discharge patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="dischargeForm" method="POST" action="#">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-2">Discharge <strong id="dischargePatientName"></strong>.</div>
                    <div class="mb-3">
                        <textarea name="notes" class="form-control" rows="4" placeholder="Discharge note (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                    <button type="submit" class="btn btn-warning">Discharge</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="deceasedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark deceased</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deceasedForm" method="POST" action="#">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-2">Mark <strong id="deceasedPatientName"></strong> as deceased.</div>
                    <div class="mb-3">
                        <textarea name="notes" class="form-control" rows="4" placeholder="Notes for deceased status (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                    <button type="submit" class="btn btn-dark">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="redoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Redo patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="redoForm" method="POST" action="#">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <p>Reopen cancelled patient <strong id="redoPatientName"></strong> and restore them to active care.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                    <button type="submit" class="btn btn-success">Redo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const transferModal = new bootstrap.Modal(document.getElementById('transferModal'));
        const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
        const dischargeModal = new bootstrap.Modal(document.getElementById('dischargeModal'));
        const deceasedModal = new bootstrap.Modal(document.getElementById('deceasedModal'));
        const redoModal = new bootstrap.Modal(document.getElementById('redoModal'));

        document.querySelectorAll('.transfer-btn').forEach(button => {
            button.addEventListener('click', () => {
                const patientId = button.dataset.patientId;
                const patientName = button.dataset.patientName;
                document.getElementById('transferPatientName').textContent = patientName;
                document.getElementById('transferForm').action = `/patients/${patientId}/movements`;
                transferModal.show();
            });
        });

        document.querySelectorAll('.cancel-btn').forEach(button => {
            button.addEventListener('click', () => {
                const patientId = button.dataset.patientId;
                const patientName = button.dataset.patientName;
                document.getElementById('cancelPatientName').textContent = patientName;
                document.getElementById('cancelForm').action = `/patients/${patientId}/cancel`;
                cancelModal.show();
            });
        });

        document.querySelectorAll('.discharge-btn').forEach(button => {
            button.addEventListener('click', () => {
                const patientId = button.dataset.patientId;
                const patientName = button.dataset.patientName;
                document.getElementById('dischargePatientName').textContent = patientName;
                document.getElementById('dischargeForm').action = `/patients/${patientId}/discharge`;
                dischargeModal.show();
            });
        });

        document.querySelectorAll('.deceased-btn').forEach(button => {
            button.addEventListener('click', () => {
                const patientId = button.dataset.patientId;
                const patientName = button.dataset.patientName;
                document.getElementById('deceasedPatientName').textContent = patientName;
                document.getElementById('deceasedForm').action = `/patients/${patientId}/deceased`;
                deceasedModal.show();
            });
        });

        document.querySelectorAll('.redo-btn').forEach(button => {
            button.addEventListener('click', () => {
                const patientId = button.dataset.patientId;
                const patientName = button.dataset.patientName;
                document.getElementById('redoPatientName').textContent = patientName;
                document.getElementById('redoForm').action = `/patients/${patientId}/redo`;
                redoModal.show();
            });
        });
    });
    // initialize tooltips
    var ttList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    ttList.forEach(function (el) { new bootstrap.Tooltip(el); });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/White Board/resources/views/patients/index.blade.php ENDPATH**/ ?>