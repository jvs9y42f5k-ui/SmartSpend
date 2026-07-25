<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SmartSpend</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Home -->
    <li class="nav-item {{ $active === 'home' ? 'active' : '' }}" id="nav-home">
        <a class="nav-link" href="/">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Home</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Nav Item - Analytics -->
    <li class="nav-item {{ $active === 'analytics' ? 'active' : '' }}" id="nav-analytics">
        <a class="nav-link" href="/analytics">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Analytics</span></a>
    </li>

    <!-- Nav Item - Budget Limit -->
    <li class="nav-item {{ $active === 'budget' ? 'active' : '' }}" id="nav-budget">
        <a class="nav-link" href="/budget">
            <i class="fas fa-fw fa-landmark"></i>
            <span>Budget Limit</span></a>
    </li>

    <!-- Nav Item - Savings Goals -->
    <li class="nav-item {{ $active === 'goals' ? 'active' : '' }}" id="nav-goals">
        <a class="nav-link" href="/goals">
            <i class="fas fa-fw fa-bullseye"></i>
            <span>Savings Goals</span></a>
    </li>

    <!-- Nav Item - Transactions -->
    <li class="nav-item {{ $active === 'transactions' ? 'active' : '' }}" id="nav-transactions">
        <a class="nav-link" href="/tables">
            <i class="fas fa-fw fa-table"></i>
            <span>Transactions</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
