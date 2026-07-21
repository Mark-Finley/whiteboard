<?php
    $categories = \App\Models\InvestigationCatalog::where('is_active', true)->orderBy('name')->get()->groupBy('category');
?>

<!-- Assign Investigation Modal -->
<div class="modal fade" id="assignInvestigationModal" tabindex="-1" aria-labelledby="assignInvestigationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content glass-card">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="assignInvestigationModalLabel"><i class="fa-solid fa-file-medical me-2 text-teal"></i>Assign Investigation / Procedure</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignInvestigationForm" method="POST" action="">
                <?php echo csrf_field(); ?>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Patient Name</label>
                        <input type="text" id="assign_patient_name" class="form-control bg-light" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="investigation_type_select">Investigation / Procedure Type</label>
                        <select name="investigation_type_select" id="investigation_type_select" class="form-select" required>
                            <option value="">Select type...</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <optgroup label="<?php echo e($category); ?>">
                                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->name); ?>" data-category="<?php echo e($category); ?>"><?php echo e($item->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </optgroup>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Hidden input to hold category -->
                    <input type="hidden" name="category" id="investigation_category" required>

                    <!-- Custom input field, hidden by default -->
                    <div class="mb-3 d-none" id="custom_type_container">
                        <label class="form-label small fw-semibold" for="custom_investigation_type">Specify Custom Investigation Name</label>
                        <input type="text" name="custom_investigation_type" id="custom_investigation_type" class="form-control" placeholder="Enter custom procedure/investigation name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="priority">Priority</label>
                        <select name="priority" id="priority" class="form-select" required>
                            <option value="Routine" selected>Routine</option>
                            <option value="Urgent">Urgent</option>
                            <option value="Stat">Stat</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="notes">Notes / Clinical History</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Optional clinical details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="background-color: var(--kepts-primary); border-color: var(--kepts-primary);">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateInvestigationStatusModal" tabindex="-1" aria-labelledby="updateInvestigationStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content glass-card">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="updateInvestigationStatusModalLabel"><i class="fa-solid fa-clock-rotate-left me-2 text-teal"></i>Update Investigation Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateInvestigationStatusForm" method="POST" action="">
                <?php echo csrf_field(); ?>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Investigation</label>
                        <input type="text" id="update_investigation_name" class="form-control bg-light" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="update_status">Status</label>
                        <select name="status" id="update_status" class="form-select" required>
                            <option value="Pending">Pending</option>
                            <option value="Sample Taken">Sample Taken</option>
                            <option value="Sent">Sent</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="update_comments">Comments / Findings</label>
                        <textarea name="comments" id="update_comments" class="form-control" rows="3" placeholder="Enter comments about this status change..."></textarea>
                    </div>

                    <!-- Updates Timeline -->
                    <div class="mt-4">
                        <h6 class="fw-bold small mb-2 text-dark"><i class="fa-solid fa-history me-1 text-teal"></i> Update History & Timeline</h6>
                        <div id="investigationTimeline" class="list-group list-group-flush border rounded-3 p-1" style="max-height: 150px; overflow-y: auto; background: rgba(248, 250, 252, 0.6);">
                            <!-- Mapped dynamically in JS -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="background-color: var(--kepts-primary); border-color: var(--kepts-primary);">Save Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/White Board/resources/views/partials/investigation-modals.blade.php ENDPATH**/ ?>