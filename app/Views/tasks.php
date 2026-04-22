<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
<div class="container-fluid px-2 px-md-3 pb-5">

    <!-- ══════════════════════════════════════════════ -->
    <!-- PAGE HEADER                                     -->
    <!-- ══════════════════════════════════════════════ -->
    <div class="tasks-hero mb-4">
        <div class="tasks-hero-content">
            <div>
                <h2 class="tasks-hero-title"><i class="fe fe-layers mr-2"></i>Work &amp; Visit Assignments</h2>
                <p class="tasks-hero-sub d-none d-sm-block">Your field visits and in-house task assignments in one place.</p>
            </div>
            <button class="btn-hero-action" data-toggle="modal" data-target="#assignInhouseModal">
                <i class="fe fe-plus"></i>
                <span class="d-none d-sm-inline ml-1">Assign Task</span>
            </button>
        </div>
    </div>

    <!-- Flash messages -->
    <?php if(isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-lg mb-3">
            <i class="fe fe-check-circle mr-2"></i><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <!-- Overdue alert -->
    <?php if(!empty($overdueTasks)): ?>
        <div class="overdue-banner mb-4">
            <div class="overdue-icon"><i class="fe fe-alert-triangle"></i></div>
            <div class="overdue-body">
                <strong><?php echo count($overdueTasks); ?> Overdue Task<?php echo count($overdueTasks) > 1 ? 's' : ''; ?></strong>
                <span class="d-none d-sm-inline"> — Immediate action required on past-deadline assignments.</span>
            </div>
        </div>
    <?php endif; ?>

    <!-- ══════════════════════════════════════════════ -->
    <!-- SECTION 1 : VISIT SCHEDULE                     -->
    <!-- ══════════════════════════════════════════════ -->
    <div class="section-label mb-3">
        <span class="section-label-pill"><i class="fe fe-map-pin mr-1"></i>Visit Schedule</span>
        <span class="section-label-count"><?php echo count($tasks ?? []); ?> assignment<?php echo count($tasks ?? []) != 1 ? 's' : ''; ?></span>
    </div>

    <?php if(empty($tasks)): ?>
        <div class="empty-state mb-5">
            <div class="empty-icon"><i class="fe fe-clipboard"></i></div>
            <h6>No Visits Scheduled</h6>
            <p>You have no hospital or office visits assigned today.</p>
        </div>
    <?php else: ?>
        <div class="row g-3 mb-5">
            <?php foreach($tasks as $t):
                $priorityColor = '#17a2b8';
                $priorityBg    = 'rgba(23,162,184,0.1)';
                if($t['priority'] == 'High')   { $priorityColor = '#dc3545'; $priorityBg = 'rgba(220,53,69,0.1)'; }
                if($t['priority'] == 'Medium') { $priorityColor = '#f59e0b'; $priorityBg = 'rgba(245,158,11,0.1)'; }

                $statusColor = '#f59e0b'; $statusBg = 'rgba(245,158,11,0.1)';
                if($t['status'] == 'Completed')  { $statusColor = '#10b981'; $statusBg = 'rgba(16,185,129,0.1)'; }
                if($t['status'] == 'In Progress'){ $statusColor = '#4361ee'; $statusBg = 'rgba(67,97,238,0.1)'; }
                if($t['status'] == 'Cancelled')  { $statusColor = '#dc3545'; $statusBg = 'rgba(220,53,69,0.1)'; }

                $isCompleted = in_array($t['status'], ['Completed', 'Cancelled']);
            ?>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="visit-card <?php echo $isCompleted ? 'visit-card-dim' : ''; ?>">
                    <!-- Top accent bar -->
                    <div class="visit-card-accent" style="background:<?php echo $priorityColor; ?>;"></div>

                    <div class="visit-card-inner">
                        <!-- Header row -->
                        <div class="visit-card-head">
                            <span class="priority-chip" style="color:<?php echo $priorityColor; ?>; background:<?php echo $priorityBg; ?>;">
                                <?php echo $t['priority']; ?>
                            </span>
                            <span class="visit-date">
                                <i class="fe fe-calendar fe-10 mr-1"></i><?php echo date('D, d M', strtotime($t['visit_date'])); ?>
                            </span>
                        </div>

                        <!-- Title -->
                        <h5 class="visit-title"><?php echo htmlspecialchars($t['hospital_office_name']); ?></h5>

                        <!-- Location -->
                        <div class="visit-location">
                            <i class="fe fe-map-pin fe-11"></i>
                            <span><?php echo htmlspecialchars($t['location_desc']); ?></span>
                        </div>

                        <!-- Objective -->
                        <div class="visit-objective">
                            <div class="visit-obj-label">Target Objective</div>
                            <p><?php echo htmlspecialchars($t['target_desc']); ?></p>
                        </div>

                        <?php if($t['notes']): ?>
                        <div class="visit-notes">
                            <i class="fe fe-message-circle fe-10 mr-1"></i>
                            <em><?php echo htmlspecialchars($t['notes']); ?></em>
                        </div>
                        <?php endif; ?>

                        <!-- Footer -->
                        <div class="visit-card-foot">
                            <span class="status-pill" style="color:<?php echo $statusColor; ?>; background:<?php echo $statusBg; ?>;">
                                <span class="status-dot" style="background:<?php echo $statusColor; ?>;"></span>
                                <?php echo $t['status']; ?>
                            </span>
                            <?php if(!$isCompleted): ?>
                            <div class="dropdown">
                                <button class="btn-update-status dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fe fe-edit-2 fe-11 mr-1"></i> Update
                                </button>
                                <div class="dropdown-menu dropdown-menu-right shadow border-0 rounded-lg">
                                    <form action="tasks?action=updateStatus" method="POST">
                                        <input type="hidden" name="task_id" value="<?php echo $t['id']; ?>">
                                        <button type="submit" name="status" value="In Progress" class="dropdown-item py-2">
                                            <i class="fe fe-play-circle mr-2 text-primary"></i> Set In Progress
                                        </button>
                                        <button type="submit" name="status" value="Completed" class="dropdown-item py-2 text-success font-weight-bold">
                                            <i class="fe fe-check-circle mr-2"></i> Mark Completed
                                        </button>
                                        <div class="dropdown-divider"></div>
                                        <button type="submit" name="status" value="Cancelled" class="dropdown-item py-2 text-danger">
                                            <i class="fe fe-x-circle mr-2"></i> Cancel Visit
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- ══════════════════════════════════════════════ -->
    <!-- SECTION 2 : IN-HOUSE TASKS                     -->
    <!-- ══════════════════════════════════════════════ -->
    <div class="section-label mb-3">
        <span class="section-label-pill" style="background:rgba(139,92,246,0.12);color:#7c3aed;">
            <i class="fe fe-briefcase mr-1"></i>In-House Tasks
        </span>
        <span class="section-label-count"><?php echo count($inhouseTasks ?? []); ?> task<?php echo count($inhouseTasks ?? []) != 1 ? 's' : ''; ?></span>
    </div>

    <?php if(empty($inhouseTasks)): ?>
        <div class="empty-state mb-5">
            <div class="empty-icon" style="color:#7c3aed;background:rgba(139,92,246,0.1);"><i class="fe fe-inbox"></i></div>
            <h6>No Active Assignments</h6>
            <p>No in-house tasks assigned at the moment.</p>
        </div>
    <?php else: ?>
        <div class="inhouse-list mb-5">
            <?php foreach($inhouseTasks as $ih):
                $isOverdue   = strtotime($ih['deadline']) < time() && !in_array($ih['status'], ['Completed', 'Pending Approval']);
                $isPending   = $ih['status'] === 'Pending';
                $isActive    = in_array($ih['status'], ['Accepted', 'Overdue', 'Revision Requested', 'Partial Submitted']);
                $isInReview  = $ih['status'] === 'Pending Approval';
                $isDone      = $ih['status'] === 'Completed';

                $statusMeta = [
                    'Pending'           => ['color' => '#6c757d', 'bg' => 'rgba(108,117,125,0.1)', 'icon' => 'fe-clock'],
                    'Accepted'          => ['color' => '#4361ee', 'bg' => 'rgba(67,97,238,0.1)',  'icon' => 'fe-check'],
                    'Overdue'           => ['color' => '#dc3545', 'bg' => 'rgba(220,53,69,0.1)',  'icon' => 'fe-alert-circle'],
                    'Pending Approval'  => ['color' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.1)', 'icon' => 'fe-eye'],
                    'Partial Submitted' => ['color' => '#17a2b8', 'bg' => 'rgba(23,162,184,0.1)', 'icon' => 'fe-upload-cloud'],
                    'Revision Requested'=> ['color' => '#dc3545', 'bg' => 'rgba(220,53,69,0.1)', 'icon' => 'fe-corner-up-left'],
                    'Completed'         => ['color' => '#10b981', 'bg' => 'rgba(16,185,129,0.1)', 'icon' => 'fe-check-circle'],
                ];
                $sm = $statusMeta[$ih['status']] ?? ['color'=>'#6c757d','bg'=>'rgba(108,117,125,0.1)','icon'=>'fe-circle'];
            ?>
            <div class="inhouse-card <?php echo $isOverdue ? 'inhouse-card-overdue' : ''; ?> <?php echo $isDone ? 'inhouse-card-done' : ''; ?>">

                <!-- Left accent stripe -->
                <div class="inhouse-stripe" style="background:<?php echo $sm['color']; ?>;"></div>

                <div class="inhouse-body">
                    <!-- Row 1: Title + Status + Actions -->
                    <div class="inhouse-top">
                        <div class="inhouse-title-group">
                            <h6 class="inhouse-title"><?php echo htmlspecialchars($ih['task_name']); ?></h6>
                            <div class="inhouse-meta">
                                <span class="inhouse-assigner">
                                    <i class="fe fe-user fe-10 mr-1"></i><?php echo htmlspecialchars($ih['assigner_name']); ?>
                                </span>
                                <span class="inhouse-deadline <?php echo $isOverdue ? 'deadline-overdue' : ''; ?>">
                                    <i class="fe fe-clock fe-10 mr-1"></i><?php echo date('d M Y', strtotime($ih['deadline'])); ?>
                                    <?php if($isOverdue): ?><span class="overdue-chip">OVERDUE</span><?php endif; ?>
                                </span>
                            </div>
                        </div>

                        <div class="inhouse-actions">
                            <!-- Status badge (always visible) -->
                            <span class="inhouse-status-chip" style="color:<?php echo $sm['color']; ?>;background:<?php echo $sm['bg']; ?>;">
                                <i class="fe <?php echo $sm['icon']; ?> fe-10 mr-1"></i>
                                <span class="d-none d-sm-inline"><?php echo $ih['status']; ?></span>
                            </span>

                            <?php if($isPending): ?>
                                <button class="btn-inhouse btn-inhouse-primary" onclick="openAcceptModal(<?php echo $ih['id']; ?>)">
                                    <i class="fe fe-check mr-1"></i><span>Accept</span>
                                </button>
                            <?php elseif($isActive): ?>
                                <button class="btn-inhouse btn-inhouse-success" onclick="openCompleteModal(<?php echo $ih['id']; ?>)">
                                    <i class="fe fe-upload-cloud mr-1"></i><span>Submit</span>
                                </button>
                            <?php elseif($isInReview): ?>
                                <span class="btn-inhouse btn-inhouse-muted" style="cursor:default;">
                                    <i class="fe fe-loader mr-1"></i><span>In Review</span>
                                </span>
                            <?php elseif($isDone): ?>
                                <span class="btn-inhouse btn-inhouse-done" style="cursor:default;">
                                    <i class="fe fe-check-circle mr-1"></i><span class="d-none d-sm-inline">Done</span>
                                </span>
                            <?php endif; ?>

                            <button class="btn-inhouse btn-inhouse-ghost"
                                data-task="<?php echo htmlspecialchars(json_encode($ih), ENT_QUOTES, 'UTF-8'); ?>"
                                onclick="openViewEditInhouseModal(this)"
                                title="View Details">
                                <i class="fe fe-eye mr-1"></i><span class="d-none d-sm-inline">Details</span>
                            </button>

                            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                                <a href="tasks?action=deleteInhouse&id=<?php echo $ih['id']; ?>"
                                   class="btn-inhouse btn-inhouse-danger"
                                   onclick="return confirm('Permanently delete this task?');"
                                   title="Delete">
                                    <i class="fe fe-trash-2"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Row 2: Requirements (collapsible on mobile via CSS) -->
                    <div class="inhouse-requirements">
                        <?php echo nl2br(htmlspecialchars($ih['requirements'])); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
</main>

<!-- ═══════════════════════════════════════ STYLES ═══ -->
<style>
/* ---- Google Font ---- */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

/* ---- Root tokens ---- */
:root {
    --t-radius: 14px;
    --t-shadow: 0 2px 12px rgba(0,0,0,0.07);
    --t-shadow-hover: 0 8px 28px rgba(67,97,238,0.13);
    --t-primary: #4361ee;
    --t-success: #10b981;
    --t-danger: #dc3545;
    --t-warning: #f59e0b;
    --t-purple: #7c3aed;
    --t-text: #1e293b;
    --t-muted: #64748b;
    --t-border: #e8edf5;
    --t-bg: #f8fafd;
}

/* ── Hero Banner ── */
.tasks-hero {
    background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
    border-radius: var(--t-radius);
    padding: 20px 24px;
    box-shadow: 0 4px 24px rgba(67,97,238,0.25);
}
.tasks-hero-content { display:flex; align-items:center; justify-content:space-between; gap:12px; }
.tasks-hero-title {
    font-family: 'Inter', sans-serif;
    font-size: 1.2rem; font-weight: 800;
    color: #fff; margin: 0;
    letter-spacing: -0.3px;
}
.tasks-hero-sub { color: rgba(255,255,255,0.7); font-size: 0.82rem; margin: 4px 0 0; }
.btn-hero-action {
    display: inline-flex; align-items: center;
    background: rgba(255,255,255,0.18);
    border: 1.5px solid rgba(255,255,255,0.4);
    color: #fff; border-radius: 10px;
    padding: 8px 18px; font-size: 0.82rem;
    font-weight: 700; cursor: pointer;
    transition: background 0.2s, transform 0.15s;
    white-space: nowrap;
}
.btn-hero-action:hover { background: rgba(255,255,255,0.3); transform: scale(1.03); }

/* ── Overdue Banner ── */
.overdue-banner {
    display: flex; align-items: center; gap: 12px;
    background: linear-gradient(90deg, #dc3545, #c82333);
    color: #fff; border-radius: 10px;
    padding: 10px 18px; font-size: 0.85rem; font-weight: 600;
    box-shadow: 0 2px 10px rgba(220,53,69,0.3);
}
.overdue-icon { font-size: 1.2rem; }

/* ── Section Labels ── */
.section-label { display: flex; align-items: center; gap: 10px; }
.section-label-pill {
    display: inline-flex; align-items: center;
    background: rgba(67,97,238,0.1); color: var(--t-primary);
    border-radius: 20px; padding: 4px 14px;
    font-size: 0.76rem; font-weight: 700;
    letter-spacing: 0.03em; text-transform: uppercase;
}
.section-label-count { font-size: 0.76rem; color: var(--t-muted); }

/* ── Empty State ── */
.empty-state {
    background: #fff; border-radius: var(--t-radius);
    border: 2px dashed var(--t-border);
    text-align: center; padding: 40px 20px;
}
.empty-icon {
    width: 56px; height: 56px; border-radius: 50%;
    background: rgba(67,97,238,0.1); color: var(--t-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; margin: 0 auto 14px;
}
.empty-state h6 { font-weight: 700; color: var(--t-text); margin-bottom: 4px; }
.empty-state p  { color: var(--t-muted); font-size: 0.83rem; margin: 0; }

/* ══════════════════════════════════════════════
   VISIT CARDS
══════════════════════════════════════════════ */
.visit-card {
    background: #fff;
    border-radius: var(--t-radius);
    box-shadow: var(--t-shadow);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    height: 100%;
    display: flex; flex-direction: column;
    position: relative;
}
.visit-card:hover { transform: translateY(-4px); box-shadow: var(--t-shadow-hover); }
.visit-card-dim { opacity: 0.7; }
.visit-card-accent { height: 4px; flex-shrink: 0; }
.visit-card-inner { padding: 16px; display: flex; flex-direction: column; flex: 1; }

.visit-card-head {
    display: flex; align-items: center;
    justify-content: space-between; margin-bottom: 10px;
}
.priority-chip {
    font-size: 0.7rem; font-weight: 700;
    border-radius: 20px; padding: 3px 10px;
    letter-spacing: 0.04em; text-transform: uppercase;
}
.visit-date { font-size: 0.72rem; color: var(--t-muted); font-weight: 600; }

.visit-title {
    font-size: 1rem; font-weight: 700;
    color: var(--t-text); margin-bottom: 6px;
    line-height: 1.3;
}
.visit-location {
    display: flex; align-items: flex-start; gap: 5px;
    color: var(--t-muted); font-size: 0.76rem; margin-bottom: 12px;
}
.visit-objective {
    background: var(--t-bg); border-radius: 8px;
    padding: 10px 12px; margin-bottom: 10px;
    flex: 1;
}
.visit-obj-label {
    font-size: 0.65rem; font-weight: 700;
    color: var(--t-muted); text-transform: uppercase;
    letter-spacing: 0.06em; margin-bottom: 4px;
}
.visit-objective p { margin: 0; font-size: 0.82rem; color: var(--t-text); line-height: 1.5; }
.visit-notes {
    font-size: 0.75rem; color: var(--t-muted);
    margin-bottom: 10px; line-height: 1.4;
}

.visit-card-foot {
    display: flex; align-items: center;
    justify-content: space-between;
    border-top: 1px solid var(--t-border);
    padding-top: 12px; margin-top: auto;
}
.status-pill {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 0.72rem; font-weight: 700;
    border-radius: 20px; padding: 4px 10px;
}
.status-dot {
    width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
}
.btn-update-status {
    background: var(--t-bg); border: 1.5px solid var(--t-border);
    color: var(--t-text); border-radius: 8px;
    padding: 5px 12px; font-size: 0.75rem; font-weight: 600;
    cursor: pointer; transition: background 0.15s;
}
.btn-update-status:hover { background: #eef0ff; border-color: var(--t-primary); color: var(--t-primary); }

/* ══════════════════════════════════════════════
   IN-HOUSE TASK LIST
══════════════════════════════════════════════ */
.inhouse-list { display: flex; flex-direction: column; gap: 10px; }
.inhouse-card {
    background: #fff; border-radius: var(--t-radius);
    box-shadow: var(--t-shadow);
    display: flex; overflow: hidden;
    transition: box-shadow 0.2s;
}
.inhouse-card:hover { box-shadow: var(--t-shadow-hover); }
.inhouse-card-overdue { border: 1.5px solid rgba(220,53,69,0.3); }
.inhouse-card-done { opacity: 0.65; }
.inhouse-stripe { width: 4px; flex-shrink: 0; }
.inhouse-body { flex: 1; padding: 14px 16px; min-width: 0; }

.inhouse-top {
    display: flex; align-items: flex-start;
    gap: 10px; flex-wrap: wrap;
}
.inhouse-title-group { flex: 1; min-width: 0; }
.inhouse-title {
    font-size: 0.92rem; font-weight: 700;
    color: var(--t-text); margin: 0 0 4px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.inhouse-meta { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
.inhouse-assigner {
    font-size: 0.72rem; color: var(--t-muted); font-weight: 500;
    display: flex; align-items: center;
}
.inhouse-deadline {
    font-size: 0.72rem; color: var(--t-muted); font-weight: 500;
    display: flex; align-items: center; gap: 3px;
}
.deadline-overdue { color: var(--t-danger) !important; font-weight: 700; }
.overdue-chip {
    background: rgba(220,53,69,0.1); color: var(--t-danger);
    font-size: 0.6rem; font-weight: 800; border-radius: 4px;
    padding: 1px 5px; letter-spacing: 0.06em; margin-left: 4px;
}

.inhouse-actions {
    display: flex; align-items: center; gap: 6px;
    flex-shrink: 0; flex-wrap: wrap;
}
.inhouse-status-chip {
    font-size: 0.68rem; font-weight: 700;
    border-radius: 20px; padding: 3px 8px;
    display: inline-flex; align-items: center;
    white-space: nowrap;
}

/* Button variants */
.btn-inhouse {
    display: inline-flex; align-items: center;
    border-radius: 8px; padding: 5px 11px;
    font-size: 0.74rem; font-weight: 700;
    cursor: pointer; border: none; text-decoration: none;
    transition: opacity 0.15s, transform 0.1s;
    white-space: nowrap;
}
.btn-inhouse:hover { opacity: 0.85; transform: scale(1.03); text-decoration: none; }
.btn-inhouse-primary { background: var(--t-primary); color: #fff; }
.btn-inhouse-success { background: var(--t-success); color: #fff; }
.btn-inhouse-ghost { background: var(--t-bg); color: var(--t-text); border: 1.5px solid var(--t-border); }
.btn-inhouse-ghost:hover { background: #eef0ff; color: var(--t-primary); border-color: var(--t-primary); }
.btn-inhouse-danger { background: rgba(220,53,69,0.1); color: var(--t-danger); }
.btn-inhouse-muted { background: rgba(108,117,125,0.1); color: var(--t-muted); }
.btn-inhouse-done { background: rgba(16,185,129,0.1); color: var(--t-success); }

.inhouse-requirements {
    margin-top: 10px; padding: 8px 12px;
    background: var(--t-bg); border-radius: 8px;
    font-size: 0.78rem; color: var(--t-muted);
    line-height: 1.6; max-height: 72px;
    overflow: hidden; position: relative;
    border-left: 3px solid var(--t-border);
}

/* ── Mobile tweaks ── */
@media (max-width: 576px) {
    .tasks-hero { padding: 14px 16px; }
    .tasks-hero-title { font-size: 1rem; }
    .btn-hero-action { padding: 7px 12px; }
    .inhouse-body { padding: 12px 12px 12px 14px; }
    .btn-inhouse { padding: 5px 9px; font-size: 0.7rem; }
    .inhouse-requirements { max-height: 56px; }
    .visit-card-inner { padding: 12px; }
}

/* ── Utility ── */
.font-weight-600 { font-weight: 600; }
.font-weight-700 { font-weight: 700; }
</style>


<!-- ═══════════════════════════════ MODALS ═══════════════════════════ -->

<!-- Assign In-House Task -->
<div class="modal fade" id="assignInhouseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="tasks?action=createInhouse" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-xl">
            <div class="modal-header border-0" style="background:linear-gradient(135deg,#4361ee,#3a0ca3);">
                <h5 class="modal-title text-white font-weight-bold"><i class="fe fe-briefcase mr-2"></i>Assign In-House Task</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group">
                    <label class="task-modal-label">Assign To</label>
                    <select name="assigned_to" class="form-control" required>
                        <?php foreach($team as $u): ?>
                            <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?> <?php echo ($u['id'] == $_SESSION['user_id']) ? '(Self)' : ''; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="task-modal-label">Task Name / Title</label>
                    <input type="text" name="task_name" class="form-control" required placeholder="e.g. Develop UI Dashboard">
                </div>
                <div class="form-group">
                    <label class="task-modal-label">Requirements &amp; Details</label>
                    <textarea name="requirements" class="form-control" rows="3" required placeholder="Specify exactly what needs to be done..."></textarea>
                </div>
                <div class="form-group">
                    <label class="task-modal-label">Deadline</label>
                    <input type="datetime-local" name="deadline" class="form-control" required>
                </div>
                <div class="form-group mb-0">
                    <label class="task-modal-label">Brief / Spec File (PDF/DOC/Image)</label>
                    <input type="file" name="attachment" class="form-control-file" accept=".pdf,.doc,.docx,.png,.jpg">
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-light px-4" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary shadow-sm px-4"><i class="fe fe-send mr-1"></i> Dispatch Task</button>
            </div>
        </form>
    </div>
</div>

<!-- Accept Task -->
<div class="modal fade" id="acceptModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <form action="tasks?action=updateInhouse" method="POST" class="modal-content border-0 shadow-lg rounded-xl">
            <div class="modal-header border-0" style="background:#4361ee;">
                <h6 class="modal-title text-white font-weight-bold"><i class="fe fe-check mr-2"></i>Accept Assignment</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="action" value="accept">
                <input type="hidden" name="task_id" id="accept_task_id">
                <div class="form-group mb-0">
                    <label class="task-modal-label">Acceptance Comment <span class="text-muted">(optional)</span></label>
                    <textarea name="acceptance_comment" class="form-control" rows="3" placeholder="I have reviewed and will begin working on this..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary shadow-sm flex-fill">Confirm Acceptance</button>
            </div>
        </form>
    </div>
</div>

<!-- Complete / Submit Task -->
<div class="modal fade" id="completeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="tasks?action=updateInhouse" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-xl">
            <div class="modal-header border-0" style="background:linear-gradient(135deg,#10b981,#059669);">
                <h6 class="modal-title text-white font-weight-bold"><i class="fe fe-upload-cloud mr-2"></i>Submit Task Completion</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="action" value="complete">
                <input type="hidden" name="task_id" id="complete_task_id">
                <div class="form-group">
                    <label class="task-modal-label">Submission Type</label>
                    <select name="submission_type" class="form-control font-weight-bold" required>
                        <option value="Partial">Partial Submission (Work in Progress)</option>
                        <option value="Final" selected>Final Submission (Ready for Approval)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="task-modal-label">Completion Details / URL</label>
                    <textarea name="completion_details" class="form-control" rows="3" required placeholder="What was done? Code links, resolutions, etc."></textarea>
                </div>
                <div class="form-group">
                    <label class="task-modal-label">Deliverable File <span class="text-muted">(optional)</span></label>
                    <input type="file" name="completion_file" class="form-control-file">
                </div>
                <div class="form-group mb-0">
                    <label class="task-modal-label">Closing Comment</label>
                    <input type="text" name="completion_comment" class="form-control" placeholder="Any final thoughts for the assigner?">
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success shadow-sm text-white font-weight-bold px-4">Submit Completion</button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden data store for PDF generation -->
<span id="ve_pdf_task_name" style="display:none;"></span>
<span id="ve_pdf_assigner"  style="display:none;"></span>
<span id="ve_pdf_deadline"  style="display:none;"></span>
<span id="ve_pdf_status"    style="display:none;"></span>
<span id="ve_pdf_requirements" style="display:none;"></span>

<!-- View / Edit Detail Modal -->
<div class="modal fade" id="viewEditInhouseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg rounded-xl">
            <div class="modal-header border-0" style="background:linear-gradient(135deg,#4361ee,#3a0ca3);">
                <h5 class="modal-title text-white font-weight-bold"><i class="fe fe-cpu mr-2"></i>Task Intelligence &amp; Operations</h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" onclick="downloadTaskPdf()" class="btn btn-sm btn-light text-primary font-weight-bold shadow-sm mr-2" title="Download PDF">
                        <i class="fe fe-download mr-1"></i> PDF
                    </button>
                    <button type="button" class="close text-white" data-dismiss="modal" style="position:static;margin:0;opacity:1;"><span>&times;</span></button>
                </div>
            </div>
            <div class="modal-body p-3 p-md-4" style="background:#f8fafd;">
                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-uppercase text-muted font-weight-bold d-block">Assigned By</small>
                        <div id="ve_assigned_by" class="font-weight-700 text-dark mt-1"></div>
                    </div>
                    <div class="col-6 text-right">
                        <span id="ve_status_badge" class="badge px-3 py-2 font-weight-bold" style="font-size:0.8rem;"></span>
                    </div>
                </div>

                <!-- History Timeline -->
                <div class="bg-white rounded-lg shadow-sm mb-4 overflow-hidden border border-light">
                    <div class="px-3 pt-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                        <small class="text-uppercase text-muted font-weight-bold"><i class="fe fe-clock mr-1"></i>Full Task History &amp; Audit Trail</small>
                        <span id="ve_history_count" class="badge badge-secondary">0 events</span>
                    </div>
                    <div id="ve_history_timeline" style="max-height:280px;overflow-y:auto;" class="px-3 pt-3 pb-2">
                        <div class="text-center text-muted py-4 small"><i class="fe fe-loader"></i> Loading history...</div>
                    </div>
                    <div class="px-3 pb-3" id="ve_attachments"></div>
                </div>

                <hr class="my-3">

                <?php if (in_array($_SESSION['role'], ['Admin', 'Manager'])): ?>
                <div id="ve_admin_controls" style="display:none;" class="bg-white p-3 border border-warning rounded-lg mb-4 shadow-sm">
                    <h6 class="font-weight-bold text-warning mb-2"><i class="fe fe-shield mr-1"></i> Leadership Review Required</h6>
                    <p class="small text-muted mb-3">This task has been submitted. Please review the output and either approve or request revisions.</p>
                    <form action="tasks?action=updateInhouse" method="POST" id="ve_review_form">
                        <input type="hidden" name="task_id" id="ve_review_task_id">
                        <div class="form-group">
                            <label class="task-modal-label">Manager Feedback / Revision Notes</label>
                            <textarea name="manager_feedback" class="form-control" rows="3" placeholder="If requesting revisions, detail exactly what needs fixing..."></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="action" value="revision" class="btn btn-warning shadow-sm flex-fill font-weight-bold"
                                onclick="return confirm('Send task back to user for further revisions?');">
                                <i class="fe fe-corner-up-left mr-1"></i> Request Revision
                            </button>
                            <button type="submit" name="action" value="approve" class="btn btn-success text-white shadow-sm flex-fill font-weight-bold"
                                onclick="return confirm('You are formally approving this task. Continue?');">
                                <i class="fe fe-check-circle mr-1"></i> Approve &amp; Close
                            </button>
                        </div>
                    </form>
                </div>

                <h6 class="font-weight-bold text-dark mb-3"><i class="fe fe-edit-3 mr-2"></i>Administrative Task Control</h6>
                <form action="tasks?action=editInhouse" method="POST">
                    <input type="hidden" name="task_id" id="ve_task_id">
                    <div class="form-group">
                        <label class="task-modal-label">Task Name</label>
                        <input type="text" name="task_name" id="ve_task_name" class="form-control font-weight-bold" required>
                    </div>
                    <div class="form-group">
                        <label class="task-modal-label">Core Requirements</label>
                        <textarea name="requirements" id="ve_requirements" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="task-modal-label">Deadline</label>
                            <input type="datetime-local" name="deadline" id="ve_deadline" class="form-control" required>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="button" class="btn btn-light shadow-sm mr-2" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary shadow-sm"><i class="fe fe-save mr-1"></i> Update Properties</button>
                    </div>
                </form>
                <?php else: ?>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-light px-5 shadow-sm" data-dismiss="modal">Close Details</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal extras */
.rounded-xl { border-radius: 16px !important; }
.task-modal-label {
    font-size: 0.72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.06em;
    color: var(--t-muted); margin-bottom: 4px;
}
.gap-2 { gap: 8px; }
</style>

<script>
function htmlspecialchars_decode(str) {
    var map = { '&amp;': '&', '&#039;': "'", '&quot;': '"', '&lt;': '<', '&gt;': '>' };
    return str.replace(/&amp;|&#039;|&quot;|&lt;|&gt;/g, function(m) { return map[m]; });
}

function openAcceptModal(id) {
    document.getElementById('accept_task_id').value = id;
    $('#acceptModal').modal('show');
}
function openCompleteModal(id) {
    document.getElementById('complete_task_id').value = id;
    $('#completeModal').modal('show');
}
function openViewEditInhouseModal(btn) {
    try {
        let jsonStr = btn.getAttribute('data-task');
        let task = JSON.parse(jsonStr);
        let veId = document.getElementById('ve_task_id');
        if(veId) veId.value = task.id;
        let veName = document.getElementById('ve_task_name');
        if(veName) veName.value = task.task_name;
        let veReq = document.getElementById('ve_requirements');
        if(veReq) veReq.value = task.requirements;
        let d = new Date(task.deadline);
        d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
        let veDeadline = document.getElementById('ve_deadline');
        if(veDeadline) veDeadline.value = d.toISOString().slice(0, 16);
        document.getElementById('ve_assigned_by').innerText = task.assigner_name || 'System';
        let statusBadge = document.getElementById('ve_status_badge');
        statusBadge.innerText = task.status;
        statusBadge.className = 'badge px-3 py-2 font-weight-bold ';
        if(task.status == 'Pending')            statusBadge.classList.add('badge-secondary');
        if(task.status == 'Accepted')           statusBadge.classList.add('badge-primary');
        if(task.status == 'Completed')          statusBadge.classList.add('badge-success');
        if(task.status == 'Overdue')            statusBadge.classList.add('badge-danger','text-white');
        if(task.status == 'Partial Submitted')  statusBadge.classList.add('badge-info','text-white');
        if(task.status == 'Pending Approval')   statusBadge.classList.add('badge-warning','text-dark');
        if(task.status == 'Revision Requested') statusBadge.classList.add('badge-danger','text-white');

        document.getElementById('ve_pdf_task_name').textContent    = task.task_name || '';
        document.getElementById('ve_pdf_assigner').textContent     = task.assigner_name || 'System';
        document.getElementById('ve_pdf_requirements').textContent = task.requirements || '';
        document.getElementById('ve_pdf_status').textContent       = task.status || '';
        const dl = task.deadline ? new Date(task.deadline).toLocaleString('en-IN', {day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '';
        document.getElementById('ve_pdf_deadline').textContent = dl;

        const historyEl    = document.getElementById('ve_history_timeline');
        const historyCount = document.getElementById('ve_history_count');
        const history      = task.history || [];
        const actionConfig = {
            'Task Created':       {icon:'fe-plus-circle',    color:'#6f42c1', label:'Task Created',       bg:'#f3eeff'},
            'Accepted':           {icon:'fe-check',          color:'#007bff', label:'Accepted',           bg:'#e8f4ff'},
            'Submitted':          {icon:'fe-upload-cloud',   color:'#17a2b8', label:'Submitted',          bg:'#e8f9fc'},
            'Approved':           {icon:'fe-check-circle',   color:'#28a745', label:'Approved & Closed',  bg:'#eaffef'},
            'Revision Requested': {icon:'fe-corner-up-left', color:'#dc3545', label:'Revision Requested', bg:'#fff1f1'},
        };

        if (history.length === 0) {
            historyEl.innerHTML = `<div class="text-center text-muted small py-4"><i class="fe fe-inbox" style="font-size:1.5rem"></i><br>No recorded history yet.</div>`;
            historyCount.textContent = '0 events';
        } else {
            historyCount.textContent = history.length + ' event' + (history.length > 1 ? 's' : '');
            let html = '<div style="position:relative;padding-left:20px;border-left:2px solid #e0e0e0;">';
            history.forEach(function(ev, idx) {
                const cfg = actionConfig[ev.action] || {icon:'fe-activity',color:'#6c757d',label:ev.action,bg:'#f8f9fa'};
                const isLast = idx === history.length - 1;
                const dateStr = ev.created_at ? new Date(ev.created_at).toLocaleString('en-IN',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '';
                html += `<div style="margin-bottom:${isLast?'4px':'18px'};position:relative;">
                    <div style="position:absolute;left:-28px;top:2px;width:18px;height:18px;border-radius:50%;background:${cfg.bg};border:2px solid ${cfg.color};display:flex;align-items:center;justify-content:center;">
                        <i class="fe ${cfg.icon}" style="font-size:9px;color:${cfg.color};"></i>
                    </div>
                    <div style="background:${cfg.bg};border-left:3px solid ${cfg.color};border-radius:4px;padding:8px 12px;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong style="color:${cfg.color};font-size:0.78rem;text-transform:uppercase;letter-spacing:0.5px;">${cfg.label}</strong>
                            <span class="text-muted" style="font-size:0.72rem;">${dateStr}</span>
                        </div>
                        <div class="font-weight-bold text-dark" style="font-size:0.82rem;">${ev.user_name || 'System'}</div>
                        ${ev.message ? `<div class="text-muted mt-1" style="font-size:0.8rem;white-space:pre-wrap;">${ev.message}</div>` : ''}
                    </div>
                </div>`;
            });
            html += '</div>';
            historyEl.innerHTML = html;
            historyEl.scrollTop = historyEl.scrollHeight;
        }

        let attachments = '';
        if(task.attachment_path)      attachments += `<a href="${task.attachment_path}" target="_blank" class="btn btn-sm btn-outline-secondary mr-2 mt-2"><i class="fe fe-paperclip mr-1"></i> Original Brief</a>`;
        if(task.completion_file_path) attachments += `<a href="${task.completion_file_path}" target="_blank" class="btn btn-sm btn-success text-white mt-2"><i class="fe fe-download mr-1"></i> Download Deliverable</a>`;
        document.getElementById('ve_attachments').innerHTML = attachments;

        let adminControls = document.getElementById('ve_admin_controls');
        if (adminControls) {
            if (task.status === 'Pending Approval' || task.status === 'Partial Submitted') {
                adminControls.style.display = 'block';
                document.getElementById('ve_review_task_id').value = task.id;
            } else {
                adminControls.style.display = 'none';
            }
        }

        $('#viewEditInhouseModal').modal('show');
    } catch(e) { console.error('Parse error:', e); }
}

function downloadTaskPdf() {
    const name     = document.getElementById('ve_pdf_task_name').textContent;
    const assigner = document.getElementById('ve_pdf_assigner').textContent;
    const deadline = document.getElementById('ve_pdf_deadline').textContent;
    const status   = document.getElementById('ve_pdf_status').textContent;
    const reqs     = document.getElementById('ve_pdf_requirements').textContent;
    if (!name) { alert('No task loaded.'); return; }
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
    const pageW = doc.internal.pageSize.getWidth();
    const margin = 18;
    const contentW = pageW - margin * 2;
    doc.setFillColor(37, 99, 235);
    doc.rect(0, 0, pageW, 36, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(15); doc.setFont('helvetica', 'bold');
    doc.text('Task Requirements Briefing', margin, 15);
    doc.setFontSize(9); doc.setFont('helvetica', 'normal');
    doc.text('Redeemer Technologies Field Tracking System', margin, 23);
    doc.text('Generated: ' + new Date().toLocaleString('en-IN'), margin, 30);
    let y = 48;
    doc.setFillColor(243, 244, 246);
    doc.roundedRect(margin, y, contentW, 18, 2, 2, 'F');
    doc.setTextColor(17, 24, 39);
    doc.setFontSize(13); doc.setFont('helvetica', 'bold');
    doc.text(name, margin + 4, y + 12);
    y += 26;
    const metaItems = [{label:'Assigned By',value:assigner},{label:'Current Status',value:status},{label:'Deadline',value:deadline}];
    doc.setFontSize(9);
    metaItems.forEach(function(m, i) {
        const col = i % 2, row = Math.floor(i / 2);
        const x = margin + col * (contentW / 2 + 2);
        const itemY = y + row * 20;
        doc.setFont('helvetica', 'bold'); doc.setTextColor(107, 114, 128);
        doc.text(m.label.toUpperCase(), x, itemY);
        doc.setFont('helvetica', 'normal'); doc.setTextColor(17, 24, 39);
        doc.text(m.value || '—', x, itemY + 5);
    });
    y += Math.ceil(metaItems.length / 2) * 20 + 6;
    doc.setDrawColor(229, 231, 235); doc.setLineWidth(0.4);
    doc.line(margin, y, pageW - margin, y); y += 8;
    doc.setFont('helvetica', 'bold'); doc.setFontSize(10); doc.setTextColor(37, 99, 235);
    doc.text('REQUIREMENTS & DETAILS', margin, y); y += 6;
    doc.setFont('helvetica', 'normal'); doc.setFontSize(10); doc.setTextColor(31, 41, 55);
    const lines = doc.splitTextToSize(reqs || 'No requirements specified.', contentW);
    lines.forEach(function(line) {
        if (y > 270) { doc.addPage(); y = 20; }
        doc.text(line, margin, y); y += 5.5;
    });
    const totalPages = doc.internal.getNumberOfPages();
    for (let p = 1; p <= totalPages; p++) {
        doc.setPage(p); doc.setFontSize(8); doc.setTextColor(156, 163, 175);
        doc.text('Page ' + p + ' of ' + totalPages + '  |  Redeemer Technologies – Confidential', margin, 290);
    }
    const safeFilename = name.replace(/[^a-z0-9]/gi, '_').substring(0, 50);
    doc.save('Task_' + safeFilename + '.pdf');
}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<?php include 'layout/footer.php'; ?>
