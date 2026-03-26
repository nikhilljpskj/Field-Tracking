<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sales & Marketing Field Tracking System">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
    <title><?php echo $title ?? 'Redeemer HRMS'; ?></title>
    <base href="<?php echo str_replace('index.php', '', $_SERVER['SCRIPT_NAME']); ?>">
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="css/simplebar.css">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="css/feather.css">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="css/daterangepicker.css">
    <!-- App CSS -->
    <link rel="stylesheet" href="css/app-light.css" id="lightTheme">
    <!-- Modern Design Overrides -->
    <link rel="stylesheet" href="assets/css/modern.css">
    <!-- HERE Maps JS API CSS -->
    <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />
    <link rel="stylesheet" href="css/app-dark.css" id="darkTheme" disabled>
    <script src="js/jquery.min.js"></script>
  </head>
  <body class="vertical  light  ">
    <div class="wrapper">
      <div class="sidebar-overlay"></div>
      <nav class="topnav navbar navbar-light">
        <button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
          <i class="fe fe-menu navbar-toggler-icon"></i>
        </button>
        <form class="form-inline mr-auto searchform text-muted">
          <input class="form-control mr-sm-2 bg-transparent border-0 pl-4 text-muted" type="search" placeholder="Type something..." aria-label="Search">
        </form>
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link text-muted my-2" href="#" id="modeSwitcher" data-mode="light">
              <i class="fe fe-sun fe-16"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-muted my-2" href="./#" data-toggle="modal" data-target=".modal-shortcut">
              <span class="fe fe-grid fe-16"></span>
            </a>
          </li>
          <?php 
            $notifModel = new \App\Models\Notification();
            $unreadCount = $notifModel->getCount($_SESSION['user_id']);
            $unreadList = $notifModel->getUnread($_SESSION['user_id']);
          ?>
          <li class="nav-item nav-notif">
            <a class="nav-link text-muted my-2" href="./#" data-toggle="modal" data-target=".modal-notif">
              <span class="fe fe-bell fe-16"></span>
              <?php if($unreadCount > 0): ?>
                <span class="dot dot-md bg-danger"></span>
              <?php endif; ?>
            </a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-muted pr-0" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="avatar avatar-sm mt-2">
                <img src="<?php echo $_SESSION['profile_pic'] ?? 'assets/avatars/default.jpg'; ?>" alt="..." class="avatar-img rounded-circle border">
              </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="profile">Profile</a>
              <a class="dropdown-item" href="settings">Settings</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item text-danger" href="logout">
                <i class="fe fe-log-out fe-14 mr-2"></i> Logout
              </a>
            </div>
          </li>
        </ul>
      </nav>

<!-- Notification Modal -->
<div class="modal fade modal-notif modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="defaultModalLabel">Intelligence Alerts (<?php echo $unreadCount; ?>)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="list-group list-group-flush my-n3">
          <?php if(empty($unreadList)): ?>
            <div class="list-group-item bg-transparent text-center py-4">
              <p class="text-muted mb-0">No new notifications</p>
            </div>
          <?php else: ?>
            <?php foreach($unreadList as $n): ?>
              <div class="list-group-item bg-transparent">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="fe <?php 
                        echo ($n['type'] == 'Target') ? 'fe-target text-primary' : 
                             (($n['type'] == 'Performance') ? 'fe-alert-circle text-danger' : 'fe-bell text-secondary'); 
                    ?> fe-24"></span>
                  </div>
                  <div class="col">
                    <small><strong><?php echo htmlspecialchars($n['type']); ?></strong></small>
                    <div class="my-0 text-muted small"><?php echo htmlspecialchars($n['message']); ?></div>
                    <small class="badge badge-pill badge-light text-muted"><?php echo date('h:i A', strtotime($n['created_at'])); ?></small>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div> <!-- / .list-group -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
