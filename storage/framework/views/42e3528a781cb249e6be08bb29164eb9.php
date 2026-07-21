<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title ?? 'KEPTS | KATH Emergency Live Patient Tracking System'); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@2.1.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/app.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="kepts-body">
<div class="kepts-shell">
    <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="kepts-main">
        <?php echo $__env->make('partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <main class="container-fluid py-4">
            <?php echo $__env->make('partials.flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
</div>

<div class="offcanvas offcanvas-start mobile-sidebar" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header">
        <div>
            <div class="brand-title" id="mobileSidebarLabel">KATH Emergency</div>
            <div class="brand-subtitle text-muted">Live Patient Tracking</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body pt-0">
        <nav class="sidebar-nav">
            <a href="<?php echo e(route('dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <i class="fa-solid fa-chart-line"></i><span>Dashboard</span>
            </a>
            <a href="<?php echo e(route('white.board')); ?>" class="nav-item <?php echo e(request()->routeIs('white.board') ? 'active' : ''); ?>">
                <i class="fa-solid fa-chalkboard"></i><span>White Board</span>
            </a>
            <a href="<?php echo e(route('procedures.board')); ?>" class="nav-item <?php echo e(request()->routeIs('procedures.board') ? 'active' : ''); ?>">
                <i class="fa-solid fa-notes-medical"></i><span>Procedures Board</span>
            </a>

            <?php if(auth()->user()?->isAdmin()): ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                    <i class="fa-solid fa-building-shield"></i><span>Admin View</span>
                </a>
                <a href="<?php echo e(route('users.access-control')); ?>" class="nav-item <?php echo e(request()->routeIs('users.access-control') ? 'active' : ''); ?>">
                    <i class="fa-solid fa-user-lock"></i><span>Access Control</span>
                </a>
                <a href="<?php echo e(route('patients.index')); ?>" class="nav-item <?php echo e(request()->routeIs('patients.*') ? 'active' : ''); ?>">
                    <i class="fa-solid fa-user-injured"></i><span>Patients</span>
                </a>
                <a href="<?php echo e(route('users.index')); ?>" class="nav-item <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>">
                    <i class="fa-solid fa-users-gear"></i><span>Users</span>
                </a>
                <a href="<?php echo e(route('wards.index')); ?>" class="nav-item <?php echo e(request()->routeIs('wards.*') ? 'active' : ''); ?>">
                    <i class="fa-solid fa-bed-pulse"></i><span>Wards</span>
                </a>
                <a href="<?php echo e(route('teams.index')); ?>" class="nav-item <?php echo e(request()->routeIs('teams.*') ? 'active' : ''); ?>">
                    <i class="fa-solid fa-user-doctor"></i><span>Specialties</span>
                </a>
                <a href="<?php echo e(route('audits.index')); ?>" class="nav-item <?php echo e(request()->routeIs('audits.*') ? 'active' : ''); ?>">
                    <i class="fa-solid fa-shield-halved"></i><span>Audit Logs</span>
                </a>
            <?php elseif(auth()->user()?->isTriage()): ?>
                <a href="<?php echo e(route('triage.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('triage.dashboard') ? 'active' : ''); ?>">
                    <i class="fa-solid fa-truck-medical"></i><span>Triage Board</span>
                </a>
                <a href="<?php echo e(route('patients.index')); ?>" class="nav-item <?php echo e(request()->routeIs('patients.*') ? 'active' : ''); ?>">
                    <i class="fa-solid fa-user-injured"></i><span>Patients</span>
                </a>
            <?php elseif(auth()->user()?->isWard()): ?>
                <a href="<?php echo e(route('ward.dashboard', ['ward' => 'RED'])); ?>" class="nav-item <?php echo e(request()->routeIs('ward.dashboard') ? 'active' : ''); ?>">
                    <i class="fa-solid fa-circle-half-stroke"></i><span>Ward Board</span>
                </a>
            <?php elseif(auth()->user()?->isSpecialtyDoctor()): ?>
                <a href="<?php echo e(route('specialty.dashboard', ['team' => auth()->user()?->team?->name ?? 'Emergency Medicine'])); ?>" class="nav-item <?php echo e(request()->routeIs('specialty.dashboard') ? 'active' : ''); ?>">
                    <i class="fa-solid fa-user-doctor"></i><span>Specialty Board</span>
                </a>
            <?php endif; ?>

            <a href="<?php echo e(route('search')); ?>" class="nav-item <?php echo e(request()->routeIs('search') ? 'active' : ''); ?>">
                <i class="fa-solid fa-magnifying-glass"></i><span>Search</span>
            </a>
        </nav>
    </div>
</div>

<button type="button" class="btn btn-ghost nav-exit-toggle" data-nav-toggle aria-pressed="false" aria-label="Exit full screen mode">
    <i class="fa-solid fa-compress me-2"></i>
    <span>Exit full screen</span>
</button>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@2.1.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    window.KEPTS_CAN_ASSIGN = <?php echo json_encode(auth()->user()?->canAssignProcedures() ?? false, 15, 512) ?>;
</script>
<script src="<?php echo e(asset('assets/js/app.js')); ?>"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\White Board\resources\views/layouts/app.blade.php ENDPATH**/ ?>