<header class="kepts-navbar sticky-top">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-ghost d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div>
            <div class="text-uppercase small text-muted fw-semibold">KATH Emergency</div>
            <h1 class="page-title mb-0">{{ $title ?? 'Dashboard' }}</h1>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-ghost" type="button" data-nav-toggle aria-pressed="false" aria-label="Hide navigation for full screen access">
            <i class="fa-solid fa-up-right-and-down-left-from-center me-2"></i>
            <span>Full screen</span>
        </button>
        <button class="btn btn-light btn-search" type="button" data-bs-toggle="modal" data-bs-target="#searchModal">
            <i class="fa-solid fa-magnifying-glass me-2"></i>Search patient
        </button>
        <!-- Notifications Dropdown -->
        <div class="dropdown me-2" id="notificationDropdownContainer">
            <button class="btn btn-ghost position-relative p-2" type="button" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notificationBadge" style="font-size: 0.65rem;">
                    0
                </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow-sm p-0" aria-labelledby="notificationBell" style="width: 340px; max-height: 400px; overflow-y: auto;">
                <div class="p-2 px-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Notifications</h6>
                    <button class="btn btn-sm btn-link text-decoration-none p-0" id="markAllReadBtn" style="font-size: 0.8rem; color: #0f766e;">Mark all read</button>
                </div>
                <div id="notificationList" class="list-group list-group-flush">
                    <div class="p-3 text-center text-muted small">No new alerts</div>
                </div>
            </div>
        </div>

        <div class="dropdown">
            <button class="btn btn-ghost dropdown-toggle" data-bs-toggle="dropdown" type="button">
                <i class="fa-solid fa-user-circle me-2"></i>{{ auth()->user()->name }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->role?->name }}</span></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card">
            <div class="modal-header border-0">
                <h5 class="modal-title">Search by GHIMS number or patient name</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('search') }}" method="GET" class="modal-body pt-0">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="query" class="form-control" placeholder="Enter GHIMS number or patient name">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>
</div>
