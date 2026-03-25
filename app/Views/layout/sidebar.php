<aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar>
<a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
  <i class="fe fe-x"><span class="sr-only"></span></i>
</a>
<nav class="vertnav navbar navbar-light">
  <!-- nav bar -->
  <div class="w-100 mb-4 d-flex">
      <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="./">
        <img src="assets/images/redeemer-technologies-logo.png" alt="Redeemer Technologies" class="img-fluid px-4 mb-2" style="max-height: 50px;">
        <h6 class="brand-name font-weight-bold mb-0 text-primary">Redeemer Technologies</h6>
      </a>
  </div>
  <ul class="navbar-nav flex-fill w-100 mb-2">
    <li class="nav-item w-100">
      <a class="nav-link" href="dashboard">
        <i class="fe fe-home fe-16"></i>
        <span class="ml-3 item-text">Dashboard</span>
      </a>
    </li>
  </ul>
  
  <p class="text-muted nav-heading mt-4 mb-1">
    <span>Modules</span>
  </p>
  <ul class="navbar-nav flex-fill w-100 mb-2">
    <!-- Executive / Field Staff -->
    <li class="nav-item w-100">
      <a class="nav-link" href="attendance">
        <i class="fe fe-user-check fe-16"></i>
        <span class="ml-3 item-text">Attendance</span>
      </a>
    </li>
    <li class="nav-item w-100">
      <a class="nav-link" href="tasks">
        <i class="fe fe-list fe-16"></i>
        <span class="ml-3 item-text">Daily Tasks</span>
      </a>
    </li>
    <li class="nav-item w-100">
      <a class="nav-link" href="meetings">
        <i class="fe fe-users fe-16"></i>
        <span class="ml-3 item-text">Client Meetings</span>
      </a>
    </li>
    <li class="nav-item w-100">
      <a class="nav-link" href="tracking">
        <i class="fe fe-map-pin fe-16"></i>
        <span class="ml-3 item-text">Live Tracking</span>
      </a>
    </li>

    <p class="text-muted nav-heading mt-4 mb-1">
      <span>Leaves</span>
    </p>
    <li class="nav-item w-100">
      <a class="nav-link" href="leaves">
        <i class="fe fe-calendar fe-16"></i>
        <span class="ml-3 item-text">My Leaves</span>
      </a>
    </li>
    <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin', 'Manager', 'HR'])): ?>
    <li class="nav-item w-100">
      <a class="nav-link" href="leave-manage">
        <i class="fe fe-check-square fe-16"></i>
        <span class="ml-3 item-text">Manage Requests</span>
      </a>
    </li>
    <li class="nav-item w-100">
      <a class="nav-link" href="leave-allocate">
        <i class="fe fe-layers fe-16"></i>
        <span class="ml-3 item-text">Quota Management</span>
      </a>
    </li>
    <?php endif; ?>

    <p class="text-muted nav-heading mt-4 mb-1">
      <span>Payroll</span>
    </p>
    <li class="nav-item w-100">
      <a class="nav-link" href="payroll">
        <i class="fe fe-credit-card fe-16"></i>
        <span class="ml-3 item-text">My Payslips</span>
      </a>
    </li>
    <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin', 'HR'])): ?>
    <li class="nav-item w-100">
      <a class="nav-link" href="payroll-manage">
        <i class="fe fe-settings fe-16"></i>
        <span class="ml-3 item-text">Payroll Admin</span>
      </a>
    </li>
    <?php endif; ?>
    
    <!-- Manager/Admin -->
    <li class="nav-item w-100">
      <a class="nav-link" href="targets">
        <i class="fe fe-target fe-16"></i>
        <span class="ml-3 item-text">Sales Targets</span>
      </a>
    </li>
    <li class="nav-item w-100">
      <a class="nav-link" href="allowance">
        <i class="fe fe-dollar-sign fe-16"></i>
        <span class="ml-3 item-text">Travel Allowance</span>
      </a>
    </li>
  </ul>

  <p class="text-muted nav-heading mt-4 mb-1">
    <span>Reporting</span>
  </p>
  <ul class="navbar-nav flex-fill w-100 mb-2">
    <li class="nav-item w-100">
      <a class="nav-link" href="reports-daily">
        <i class="fe fe-file-text fe-16"></i>
        <span class="ml-3 item-text">Daily Reports</span>
      </a>
    </li>
    <li class="nav-item w-100">
      <a class="nav-link" href="reports-monthly">
        <i class="fe fe-calendar fe-16"></i>
        <span class="ml-3 item-text">Monthly Reports</span>
      </a>
    </li>
  </ul>

  <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'Admin'): ?>
  <p class="text-muted nav-heading mt-4 mb-1">
    <span>Settings</span>
  </p>
  <ul class="navbar-nav flex-fill w-100 mb-2">
    <li class="nav-item w-100">
      <a class="nav-link" href="users">
        <i class="fe fe-users fe-16"></i>
        <span class="ml-3 item-text">User Management</span>
      </a>
    </li>
    <li class="nav-item w-100">
      <a class="nav-link" href="settings">
        <i class="fe fe-settings fe-16"></i>
        <span class="ml-3 item-text">System Settings</span>
      </a>
    </li>
  </ul>
  <?php endif; ?>

  <p class="text-muted nav-heading mt-4 mb-1">
    <span>Exit</span>
  </p>
  <ul class="navbar-nav flex-fill w-100 mb-2">
    <li class="nav-item w-100">
      <a class="nav-link text-danger" href="logout">
        <i class="fe fe-log-out fe-16"></i>
        <span class="ml-3 item-text">Logout</span>
      </a>
    </li>
  </ul>
</nav>
</aside>
