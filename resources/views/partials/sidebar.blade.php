<aside class="kepts-sidebar">
    <div class="brand-block">
        <div class="brand-mark">KEPTS</div>
        <div>
            <div class="brand-title">KATH Emergency</div>
            <div class="brand-subtitle">Live Patient Tracking</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i><span>Dashboard</span>
        </a>
        <a href="{{ route('overview.dashboard') }}" class="nav-item {{ request()->routeIs('overview.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-globe"></i><span>Overview</span>
        </a>
        <a href="{{ route('white.board') }}" class="nav-item {{ request()->routeIs('white.board') ? 'active' : '' }}">
            <i class="fa-solid fa-chalkboard"></i><span>White Board</span>
        </a>
        <a href="{{ route('procedures.board') }}" class="nav-item {{ request()->routeIs('procedures.board') ? 'active' : '' }}">
            <i class="fa-solid fa-notes-medical"></i><span>Procedures Board</span>
        </a>

        @if(auth()->user()?->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-building-shield"></i><span>Admin View</span>
            </a>
            <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines"></i><span>Reports</span>
            </a>
            <a href="{{ route('users.access-control') }}" class="nav-item {{ request()->routeIs('users.access-control') ? 'active' : '' }}">
                <i class="fa-solid fa-user-lock"></i><span>Access Control</span>
            </a>
            <a href="{{ route('patients.index') }}" class="nav-item {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-injured"></i><span>Patients</span>
            </a>
            <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users-gear"></i><span>Users</span>
            </a>
            <a href="{{ route('wards.index') }}" class="nav-item {{ request()->routeIs('wards.*') ? 'active' : '' }}">
                <i class="fa-solid fa-bed-pulse"></i><span>Wards</span>
            </a>
            <a href="{{ route('teams.index') }}" class="nav-item {{ request()->routeIs('teams.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-doctor"></i><span>Specialties</span>
            </a>
            <a href="{{ route('audits.index') }}" class="nav-item {{ request()->routeIs('audits.*') ? 'active' : '' }}">
                <i class="fa-solid fa-shield-halved"></i><span>Audit Logs</span>
            </a>
        @elseif(auth()->user()?->isTriage())
            <a href="{{ route('triage.dashboard') }}" class="nav-item {{ request()->routeIs('triage.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-truck-medical"></i><span>Triage Board</span>
            </a>
            <a href="{{ route('patients.index') }}" class="nav-item {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-injured"></i><span>Patients</span>
            </a>
        @elseif(auth()->user()?->isWard())
            <a href="{{ route('ward.dashboard', ['ward' => 'RED']) }}" class="nav-item {{ request()->routeIs('ward.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-circle-half-stroke"></i><span>Ward Board</span>
            </a>
        @elseif(auth()->user()?->isSpecialtyDoctor())
            <a href="{{ route('specialty.dashboard', ['team' => auth()->user()?->team?->name ?? 'Emergency Medicine']) }}" class="nav-item {{ request()->routeIs('specialty.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-user-doctor"></i><span>Specialty Board</span>
            </a>
        @endif

        <a href="{{ route('search') }}" class="nav-item {{ request()->routeIs('search') ? 'active' : '' }}">
            <i class="fa-solid fa-magnifying-glass"></i><span>Search</span>
        </a>
    </nav>
</aside>
