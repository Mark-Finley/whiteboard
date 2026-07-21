<?php $__env->startSection('content'); ?>
<div class="row g-3">
    <!-- Board Header & Filters -->
    <div class="col-12">
        <div class="card section-card glass-card p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h3 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-notes-medical me-2 text-teal"></i>Procedures & Investigations Board</h3>
                    <div class="muted-label small">Emergency Department real-time command board. Assign, track, and progress patient investigations.</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="loading-pill" id="boardStatusPill"><i class="fa-solid fa-sync fa-spin me-1"></i> Live</span>
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text border-end-0 bg-white text-muted"><i class="fa-solid fa-search"></i></span>
                        <input type="text" id="boardSearchInput" class="form-control border-start-0 ps-0" placeholder="Search patient name or GHIMS...">
                    </div>
                </div>
                <div class="col-md-9 d-flex flex-wrap align-items-center gap-3 justify-content-md-end">
                    <!-- Category Filters -->
                    <div class="d-flex align-items-center gap-1 border-end pe-3 border-light-subtle">
                        <span class="text-muted small fw-semibold me-2">Category:</span>
                        <input type="checkbox" class="btn-check filter-btn" id="filterCatLab" data-filter="category" data-value="Laboratory" checked autocomplete="off">
                        <label class="btn btn-sm btn-outline-teal rounded-pill" for="filterCatLab">🧪 Lab</label>
                        
                        <input type="checkbox" class="btn-check filter-btn" id="filterCatImg" data-filter="category" data-value="Imaging" checked autocomplete="off">
                        <label class="btn btn-sm btn-outline-teal rounded-pill" for="filterCatImg">🩻 Imaging</label>

                        <input type="checkbox" class="btn-check filter-btn" id="filterCatProc" data-filter="category" data-value="Procedures" checked autocomplete="off">
                        <label class="btn btn-sm btn-outline-teal rounded-pill" for="filterCatProc">❤️ Procedures</label>
                    </div>

                    <!-- Status Filters -->
                    <div class="d-flex align-items-center gap-1 border-end pe-3 border-light-subtle">
                        <span class="text-muted small fw-semibold me-2">Status:</span>
                        <input type="checkbox" class="btn-check filter-btn" id="filterStatPending" data-filter="status" data-value="Pending" checked autocomplete="off">
                        <label class="btn btn-sm btn-outline-warning rounded-pill" for="filterStatPending">🟡 Pending</label>

                        <input type="checkbox" class="btn-check filter-btn" id="filterStatProgress" data-filter="status" data-value="In Progress,Sample Taken,Sent" checked autocomplete="off">
                        <label class="btn btn-sm btn-outline-primary rounded-pill" for="filterStatProgress">🔵 In Progress</label>

                        <input type="checkbox" class="btn-check filter-btn" id="filterStatCompleted" data-filter="status" data-value="Completed" autocomplete="off">
                        <label class="btn btn-sm btn-outline-success rounded-pill" for="filterStatCompleted">🟢 Completed</label>

                        <input type="checkbox" class="btn-check filter-btn" id="filterStatCancelled" data-filter="status" data-value="Cancelled" autocomplete="off">
                        <label class="btn btn-sm btn-outline-danger rounded-pill" for="filterStatCancelled">🔴 Cancelled</label>
                    </div>

                    <!-- Priority Filters -->
                    <div class="d-flex align-items-center gap-1">
                        <input type="checkbox" class="btn-check filter-btn" id="filterPriorityUrgent" data-filter="priority" data-value="Urgent,Stat" autocomplete="off">
                        <label class="btn btn-sm btn-outline-danger rounded-pill fw-bold" for="filterPriorityUrgent"><i class="fa-solid fa-fire me-1"></i>Urgent / Stat Only</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanban Board Wards Grid -->
    <div class="col-12">
        <div class="procedures-board-container" id="proceduresBoardContainer" data-live-url="<?php echo e(route('api.procedures.board')); ?>">
            <div class="row g-3 flex-nowrap overflow-x-auto pb-3" style="min-height: 650px;">
                
                <!-- TRIAGE HOLDING Ward Column -->
                <div class="col-12 col-md-6 col-lg-3 procedures-column-wrapper">
                    <div class="procedures-column" style="background: rgba(108, 117, 125, 0.05); border-top: 4px solid #6c757d;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-truck-medical me-2 text-muted"></i>Triage Holding</h5>
                            <span class="badge bg-secondary rounded-pill" id="count-triage-holding">0</span>
                        </div>
                        <div class="patient-cards-list" id="column-triage-holding">
                            <!-- Injected dynamically -->
                        </div>
                    </div>
                </div>

                <!-- RED Ward Column -->
                <div class="col-12 col-md-6 col-lg-3 procedures-column-wrapper">
                    <div class="procedures-column" style="background: rgba(220, 38, 38, 0.05); border-top: 4px solid #dc2626;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-circle-exclamation me-2 text-danger"></i>RED Ward</h5>
                            <span class="badge bg-danger rounded-pill" id="count-red">0</span>
                        </div>
                        <div class="patient-cards-list" id="column-red">
                            <!-- Injected dynamically -->
                        </div>
                    </div>
                </div>

                <!-- ORANGE Ward Column -->
                <div class="col-12 col-md-6 col-lg-3 procedures-column-wrapper">
                    <div class="procedures-column" style="background: rgba(249, 115, 22, 0.05); border-top: 4px solid #f97316;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>ORANGE Ward</h5>
                            <span class="badge bg-warning text-dark rounded-pill" id="count-orange">0</span>
                        </div>
                        <div class="patient-cards-list" id="column-orange">
                            <!-- Injected dynamically -->
                        </div>
                    </div>
                </div>

                <!-- YELLOW Ward Column -->
                <div class="col-12 col-md-6 col-lg-3 procedures-column-wrapper">
                    <div class="procedures-column" style="background: rgba(234, 179, 8, 0.05); border-top: 4px solid #eab308;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-circle-info me-2 text-info"></i>YELLOW Ward</h5>
                            <span class="badge bg-info text-dark rounded-pill" id="count-yellow">0</span>
                        </div>
                        <div class="patient-cards-list" id="column-yellow">
                            <!-- Injected dynamically -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('partials.investigation-modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .procedures-column-wrapper {
        min-width: 300px;
    }
    .procedures-column {
        border-radius: 1.25rem;
        border: 1px solid var(--kepts-border);
        padding: 1.25rem;
        min-height: 600px;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.02);
        backdrop-filter: blur(8px);
    }
    .patient-cards-list {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        max-height: 520px;
        overflow-y: auto;
        padding-right: 4px;
    }
    .patient-cards-list::-webkit-scrollbar {
        width: 4px;
    }
    .patient-cards-list::-webkit-scrollbar-thumb {
        background: rgba(15, 23, 42, 0.1);
        border-radius: 4px;
    }
    .patient-procedures-card {
        background: #fff;
        border-radius: 1rem;
        border: 1px solid var(--kepts-border);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
        padding: 1rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .patient-procedures-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }
    .text-teal {
        color: var(--kepts-primary) !important;
    }
    .btn-outline-teal {
        color: var(--kepts-primary);
        border-color: var(--kepts-primary);
        background-color: transparent;
    }
    .btn-outline-teal:hover, .btn-check:checked + .btn-outline-teal {
        color: #fff;
        background-color: var(--kepts-primary);
        border-color: var(--kepts-primary);
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\White Board\resources\views/dashboard/procedures-board.blade.php ENDPATH**/ ?>