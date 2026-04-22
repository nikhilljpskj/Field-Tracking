<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">User Management</h2>
                        <p class="text-muted mb-0">Manage system users, roles, and assignments.</p>
                    </div>
                    <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#userModal">
                        <i class="fe fe-user-plus mr-1"></i> Add New User
                    </button>
                </div>

                <?php if(isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger border-0 shadow-sm"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
                <?php endif; ?>

                <!-- Search + Filter -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <input type="text" id="userSearch" class="form-control form-control-sm" style="max-width:240px;" placeholder="Search name or email...">
                            <select id="statusFilter" class="form-control form-control-sm" style="max-width:160px;">
                                <option value="all">All Users</option>
                                <option value="active">Active Only</option>
                                <option value="inactive">Disabled Only</option>
                            </select>
                            <span class="text-muted small ml-auto" id="userCount"></span>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="usersTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="pl-4">User Details</th>
                                        <th>Role</th>
                                        <th>Phone</th>
                                        <th>Reports To</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th class="text-right pr-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <?php foreach($users as $u): ?>
                                    <tr class="user-row"
                                        data-name="<?php echo strtolower(htmlspecialchars($u['name'])); ?>"
                                        data-email="<?php echo strtolower(htmlspecialchars($u['email'])); ?>"
                                        data-status="<?php echo ($u['is_active'] ?? 1) ? 'active' : 'inactive'; ?>">
                                        <td class="pl-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm mr-3">
                                                    <span class="avatar-title rounded-circle font-weight-bold <?php echo ($u['is_active'] ?? 1) ? '' : 'bg-secondary'; ?>">
                                                        <?php echo strtoupper(substr($u['name'], 0, 1)); ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="font-weight-600 mb-0 <?php echo ($u['is_active'] ?? 1) ? '' : 'text-muted'; ?>">
                                                        <?php echo htmlspecialchars($u['name']); ?>
                                                        <?php if(!($u['is_active'] ?? 1)): ?>
                                                            <span class="badge badge-secondary ml-1" style="font-size:0.65rem;">Disabled</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($u['email']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                                $badgeClass = 'badge-soft-primary text-primary';
                                                if($u['role_name'] == 'Admin') $badgeClass = 'badge-soft-danger text-danger';
                                                if($u['role_name'] == 'Manager') $badgeClass = 'badge-soft-warning text-warning';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?> px-2 py-1"><?php echo $u['role_name']; ?></span>
                                        </td>
                                        <td><?php echo $u['phone'] ?: '-'; ?></td>
                                        <td>
                                            <?php if($u['manager_name']): ?>
                                                <span class="text-dark font-weight-500"><?php echo htmlspecialchars($u['manager_name']); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">Independent</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($u['is_active'] ?? 1): ?>
                                                <span class="badge badge-soft-success text-success px-2 py-1"><i class="fe fe-check-circle fe-10 mr-1"></i>Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-soft-danger text-danger px-2 py-1"><i class="fe fe-slash fe-10 mr-1"></i>Disabled</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><small class="text-muted"><?php echo date('M d, Y', strtotime($u['created_at'])); ?></small></td>
                                        <td class="text-right pr-4">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-toggle="dropdown">
                                                    <i class="fe fe-more-horizontal"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                                                    <a class="dropdown-item" href="users?action=edit&id=<?php echo $u['id']; ?>">
                                                        <i class="fe fe-edit-3 fe-12 mr-2"></i> Edit
                                                    </a>
                                                    <?php if(($u['is_active'] ?? 1)): ?>
                                                        <a class="dropdown-item text-warning" href="users?action=toggleActive&id=<?php echo $u['id']; ?>"
                                                           onclick="return confirm('Disable this employee? They will not be able to log in.')">
                                                            <i class="fe fe-user-x fe-12 mr-2"></i> Disable
                                                        </a>
                                                    <?php else: ?>
                                                        <a class="dropdown-item text-success" href="users?action=toggleActive&id=<?php echo $u['id']; ?>"
                                                           onclick="return confirm('Re-enable this employee? They will regain login access.')">
                                                            <i class="fe fe-user-check fe-12 mr-2"></i> Enable
                                                        </a>
                                                    <?php endif; ?>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="users?action=delete&id=<?php echo $u['id']; ?>"
                                                       onclick="return confirm('Permanently delete this user? This cannot be undone.')">
                                                        <i class="fe fe-trash-2 fe-12 mr-2"></i> Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Controls -->
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top" id="userPaginationBar">
                            <span class="text-muted small" id="userPageInfo"></span>
                            <nav>
                                <ul class="pagination pagination-sm mb-0" id="userPagination"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title font-weight-bold" id="userModalLabel">Register New User</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="users?action=create" method="POST">
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-lg bg-light border-0" placeholder="e.g. Rahul Sharma" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-lg bg-light border-0" placeholder="rahul@example.com" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg bg-light border-0" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">Phone Number</label>
                                <input type="text" name="phone" class="form-control form-control-lg bg-light border-0" placeholder="+91 0000000000">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">User Role</label>
                                <select name="role_id" class="form-control form-control-lg bg-light border-0">
                                    <?php foreach($roles as $role): ?>
                                        <option value="<?php echo $role['id']; ?>" <?php echo $role['name'] == 'Executive' ? 'selected' : ''; ?>>
                                            <?php echo $role['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">Reporting Manager</label>
                                <select name="manager_id" class="form-control form-control-lg bg-light border-0">
                                    <option value="">No Manager (Independent)</option>
                                    <?php foreach($managers as $manager): ?>
                                        <option value="<?php echo $manager['id']; ?>"><?php echo htmlspecialchars($manager['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-light px-4" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm">Create User Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<style>
.badge-soft-primary { background-color: rgba(67, 97, 238, 0.1); }
.badge-soft-danger  { background-color: rgba(247, 37, 133, 0.1); }
.badge-soft-warning { background-color: rgba(255, 190, 11, 0.1); }
.badge-soft-success { background-color: rgba(0, 184, 148, 0.1); }
.font-weight-600 { font-weight: 600; }
.font-weight-500 { font-weight: 500; }
.gap-2 { gap: 8px; }
</style>

<script>
(function() {
    const PER_PAGE = 10;
    let currentPage = 1;
    let filteredRows = [];

    const searchInput  = document.getElementById('userSearch');
    const statusFilter = document.getElementById('statusFilter');
    const tbody        = document.getElementById('usersTableBody');
    const allRows      = Array.from(tbody.querySelectorAll('tr.user-row'));

    function applyFilters() {
        const q      = searchInput.value.toLowerCase().trim();
        const status = statusFilter.value;

        filteredRows = allRows.filter(row => {
            const nameMatch  = row.dataset.name.includes(q) || row.dataset.email.includes(q);
            const rowStatus  = row.dataset.status;
            const statusMatch = (status === 'all') || (status === rowStatus);
            return nameMatch && statusMatch;
        });
        currentPage = 1;
        render();
    }

    function render() {
        const total     = filteredRows.length;
        const pages     = Math.max(1, Math.ceil(total / PER_PAGE));
        const start     = (currentPage - 1) * PER_PAGE;
        const end       = Math.min(start + PER_PAGE, total);
        const showing   = filteredRows.slice(start, end);

        // Hide all, show only current page
        allRows.forEach(r => r.style.display = 'none');
        showing.forEach(r => r.style.display = '');

        // Page info
        document.getElementById('userCount').textContent = `${total} user(s) found`;
        document.getElementById('userPageInfo').textContent =
            total === 0 ? 'No results' : `Showing ${start + 1}–${end} of ${total}`;

        // Build pagination
        const ul = document.getElementById('userPagination');
        ul.innerHTML = '';

        const prevLi = document.createElement('li');
        prevLi.className = 'page-item' + (currentPage === 1 ? ' disabled' : '');
        prevLi.innerHTML = `<a class="page-link" href="#">&laquo;</a>`;
        prevLi.addEventListener('click', e => { e.preventDefault(); if(currentPage > 1){ currentPage--; render(); }});
        ul.appendChild(prevLi);

        for (let i = 1; i <= pages; i++) {
            const li = document.createElement('li');
            li.className = 'page-item' + (i === currentPage ? ' active' : '');
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.addEventListener('click', e => { e.preventDefault(); currentPage = i; render(); });
            ul.appendChild(li);
        }

        const nextLi = document.createElement('li');
        nextLi.className = 'page-item' + (currentPage === pages ? ' disabled' : '');
        nextLi.innerHTML = `<a class="page-link" href="#">&raquo;</a>`;
        nextLi.addEventListener('click', e => { e.preventDefault(); if(currentPage < pages){ currentPage++; render(); }});
        ul.appendChild(nextLi);
    }

    searchInput.addEventListener('input', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    applyFilters(); // initial render
})();
</script>

<?php include 'layout/footer.php'; ?>
