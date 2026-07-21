<aside class="kepts-sidebar">
    <div class="brand-block">
        <div class="brand-mark">KEPTS</div>
        <div>
            <div class="brand-title">KATH Emergency</div>
            <div class="brand-subtitle">Live Patient Tracking</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="<?php echo e(route('dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
            <i class="fa-solid fa-chart-line"></i><span>Dashboard</span>
        </a>
        <a href="<?php echo e(route('overview.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('overview.dashboard') ? 'active' : ''); ?>">
            <i class="fa-solid fa-globe"></i><span>Overview</span>
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
            <a href="<?php echo e(route('reports.index')); ?>" class="nav-item <?php echo e(request()->routeIs('reports.index') ? 'active' : ''); ?>">
                <i class="fa-solid fa-file-lines"></i><span>Reports</span>
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
</aside>
<?php /**PATH C:\laragon\www\White Board\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>