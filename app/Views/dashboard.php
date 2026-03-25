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
                    <div class="btn-group shadow-sm">
                        <?php if($_SESSION['role'] == 'Executive'): ?>
                            <a href="attendance" class="btn btn-primary px-4">
                                <i class="fe fe-clock mr-1"></i> My Attendance
                            </a>
                        <?php endif; ?>
                        <a href="logout" class="btn btn-outline-danger px-4 ml-2">
                            <i class="fe fe-log-out mr-1"></i> Logout
                        </a>
                    </div>
                </div>

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
                    <div class="col-md-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Pending Tasks</h5>
                            </div>
                            <div class="card-body text-center py-5">
                                <p class="text-muted mb-0">Check your <a href="tasks">Tasks Page</a> for hospital visits assigned to you today.</p>
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
