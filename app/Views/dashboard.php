<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">Welcome Back, <?php echo $_SESSION['user_name']; ?></h2>
                        <p class="text-muted">Here's what's happening today in the field.</p>
                    </div>
                    <?php if($_SESSION['role'] == 'Executive'): ?>
                    <div class="btn-group shadow-sm d-none d-md-flex">
                        <a href="attendance" class="btn btn-outline-primary px-4">
                            <i class="fe fe-clock mr-1"></i> My Attendance
                        </a>
                        <a href="meetings" class="btn btn-primary px-4 ml-2">
                            <i class="fe fe-users mr-1"></i> Intelligence Hub
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Mobile Quick Actions (Full Width) -->
                <?php if($_SESSION['role'] == 'Executive'): ?>
                <div class="row d-md-none mb-4">
                    <div class="col-12 mb-2">
                        <a href="attendance" class="btn btn-primary btn-block py-3 shadow-sm font-weight-bold">
                            <i class="fe fe-clock mr-2"></i> My Attendance
                        </a>
                    </div>
                    <div class="col-12">
                        <a href="meetings" class="btn btn-dark btn-block py-3 shadow-sm font-weight-bold">
                            <i class="fe fe-users mr-2"></i> Intelligence Hub
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($_SESSION['role'] == 'Executive'): ?>
                <!-- Performance Intelligence Alerts (Daily) -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 <?php 
                            echo ($perf_level == 'Critical') ? 'bg-danger text-white' : 
                                (($perf_level == 'Excellent') ? 'bg-success text-white' : 'bg-white'); 
                        ?>" style="border-radius: 12px;">
                            <div class="card-body d-flex align-items-center justify-content-between p-4">
                                <div>
                                    <h4 class="mb-1 font-weight-bold">Daily Intelligence Audit: <?php echo $perf_level; ?></h4>
                                    <p class="mb-0 <?php echo in_array($perf_level, ['Critical', 'Excellent']) ? 'text-white-50' : 'text-muted'; ?>">
                                        <?php if($perf_level == 'Critical'): ?>
                                            <i class="fe fe-alert-triangle mr-2"></i> Performance critical. You have logged <?php echo $today_meetings; ?> approved visits today. Target for average is <?php echo $thresholds['value_low']; ?>+.
                                        <?php elseif($perf_level == 'Excellent'): ?>
                                            <i class="fe fe-award mr-2"></i> Outstanding work! You have exceeded the daily excellence target of <?php echo $thresholds['value_exc']; ?>.
                                        <?php else: ?>
                                            You have <?php echo $today_meetings; ?> approved interactions today. Next milestone: <?php echo ($perf_level == 'Average') ? $thresholds['value_avg'] : $thresholds['value_exc']; ?> visits.
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="h1 mb-0 opacity-25">
                                    <i class="fe <?php echo ($perf_level == 'Critical') ? 'fe-trending-down' : 'fe-trending-up'; ?>"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Stats Cards -->
                <div class="row">
                    <?php if($_SESSION['role'] != 'Executive'): ?>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-3 text-center">
                                        <span class="circle circle-sm bg-soft-primary">
                                            <i class="fe fe-users fe-16 text-primary"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <p class="small text-muted mb-0">Team Size</p>
                                        <span class="h3 mb-0 font-weight-bold"><?php echo $total_employees; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-3 text-center">
                                        <span class="circle circle-sm bg-soft-success">
                                            <i class="fe fe-check-circle fe-16 text-success"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <p class="small text-muted mb-0">Active Today</p>
                                        <span class="h3 mb-0 font-weight-bold"><?php echo $_SESSION['role'] == 'Executive' ? ($attendance ? 'Signed In' : 'Absent') : $today_attendance; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-3 text-center">
                                        <span class="circle circle-sm bg-soft-warning">
                                            <i class="fe fe-map-pin fe-16 text-warning"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <p class="small text-muted mb-0">Distance (KM)</p>
                                        <span class="h3 mb-0 font-weight-bold"><?php echo number_format($total_distance, 1); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-3 text-center">
                                        <span class="circle circle-sm bg-soft-danger">
                                            <i class="fe fe-briefcase fe-16 text-danger"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <p class="small text-muted mb-0">Meetings</p>
                                        <span class="h3 mb-0 font-weight-bold"><?php echo $_SESSION['role'] == 'Executive' ? $today_meetings_count : $today_meetings; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if($_SESSION['role'] != 'Executive'): ?>
                <div class="row">
                    <div class="col-md-8 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Activity Trend (Last 7 Days)</h5>
                            </div>
                            <div class="card-body">
                                <div id="performanceChart" style="height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item px-0 border-0">
                                        <a href="tasks" class="btn btn-outline-primary btn-block text-left py-2">
                                            <i class="fe fe-plus-circle mr-2"></i> Assign Daily Visit
                                        </a>
                                    </li>
                                    <li class="list-group-item px-0 border-0">
                                        <a href="targets" class="btn btn-outline-success btn-block text-left py-2">
                                            <i class="fe fe-target mr-2"></i> Set New Target
                                        </a>
                                    </li>
                                    <li class="list-group-item px-0 border-0">
                                        <a href="reports" class="btn btn-outline-warning btn-block text-left py-2">
                                            <i class="fe fe-file-text mr-2"></i> Review Approvals
                                        </a>
                                    </li>
                                    <li class="list-group-item px-0 border-0">
                                        <a href="tracking" class="btn btn-outline-info btn-block text-left py-2">
                                            <i class="fe fe-map mr-2"></i> Live Team View
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($_SESSION['role'] == 'Executive'): ?>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="card-title mb-0 font-weight-bold">Monthly Target Progress</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="h1 mb-0 font-weight-bold text-primary"><?php echo $monthly_percent; ?>%</div>
                                    <div class="small text-muted text-uppercase tracking-wider">Target Realization</div>
                                </div>
                                <div class="progress mb-3" style="height: 12px; border-radius: 10px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $monthly_percent; ?>%" aria-valuenow="<?php echo $monthly_percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between small font-weight-bold">
                                    <span><?php echo $monthly_count; ?> Approved</span>
                                    <span class="text-muted">Goal: <?php echo $monthly_target; ?></span>
                                </div>
                                <div class="mt-4 p-3 bg-light rounded small">
                                    <i class="fe fe-info mr-2 text-primary"></i> 
                                    <?php if($monthly_percent < 40): ?>
                                        Performance is currently <strong class="text-danger">Critical</strong> for this month's target.
                                    <?php elseif($monthly_percent < 75): ?>
                                        You are on track for <strong class="text-warning">Average</strong> monthly performance.
                                    <?php elseif($monthly_percent < 90): ?>
                                        Excellent progress! You are hitting <strong class="text-success">Good</strong> standing.
                                    <?php else: ?>
                                        Outstanding! You have reached <strong class="text-success">Excellent</strong> monthly status.
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="card-title mb-0 font-weight-bold">Intelligence Feed Quickview</h5>
                            </div>
                            <div class="card-body text-center py-5">
                                <i class="fe fe-activity fe-32 text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-4 small">Real-time verification of your field interactions is available in the hub.</p>
                                <a href="meetings" class="btn btn-dark btn-block shadow">Enter Intelligence Hub</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php if($_SESSION['role'] != 'Executive'): ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var options = {
        series: [{
            name: 'Meetings',
            data: <?php echo json_encode(array_column($trends, 'meetings')); ?>
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: { show: false }
        },
        colors: ['#4361ee'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: <?php echo json_encode(array_column($trends, 'date')); ?>,
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100, 100, 100]
            }
        },
        tooltip: {
            x: { format: 'dd MMM' },
        },
    };

    var chart = new ApexCharts(document.querySelector("#performanceChart"), options);
    chart.render();
</script>
<?php endif; ?>

<style>
.bg-soft-primary { background-color: rgba(67, 97, 238, 0.1); }
.bg-soft-success { background-color: rgba(40, 167, 69, 0.1); }
.bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
.circle-sm { width: 40px; height: 40px; line-height: 40px; border-radius: 50%; display: inline-block; }
.font-weight-bold { font-weight: 700; }
</style>

<?php include 'layout/footer.php'; ?>
