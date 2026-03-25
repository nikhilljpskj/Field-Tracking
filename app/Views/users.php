<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-0 page-title">User Management</h2>
                        <p class="text-muted">Manage system users, roles, and managerial assignments.</p>
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

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="pl-4">User Details</th>
                                        <th>Role</th>
                                        <th>Phone</th>
                                        <th>Reports To</th>
                                        <th>Created</th>
                                        <th class="text-right pr-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($users as $u): ?>
                                    <tr>
                                        <td class="pl-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm mr-3">
                                                    <span class="avatar-title rounded-circle font-weight-bold">
                                                        <?php echo strtoupper(substr($u['name'], 0, 1)); ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="font-weight-600 mb-0"><?php echo htmlspecialchars($u['name']); ?></div>
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
                                            <span class="badge <?php echo $badgeClass; ?> px-2 py-1">
                                                <?php echo $u['role_name']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $u['phone'] ?: '-'; ?></td>
                                        <td>
                                            <?php if($u['manager_name']): ?>
                                                <span class="text-dark font-weight-500"><?php echo htmlspecialchars($u['manager_name']); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small italic">Independent</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><small class="text-muted"><?php echo date('M d, Y', strtotime($u['created_at'])); ?></small></td>
                                        <td class="text-right pr-4">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fe fe-more-horizontal"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                                                    <a class="dropdown-item" href="users?action=edit&id=<?php echo $u['id']; ?>"><i class="fe fe-edit-3 fe-12 mr-2"></i> Edit</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="users?action=delete&id=<?php echo $u['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')"><i class="fe fe-trash-2 fe-12 mr-2"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Modal -->
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
                                        <option value="<?php echo $manager['id']; ?>">
                                            <?php echo htmlspecialchars($manager['name']); ?>
                                        </option>
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
.badge-soft-danger { background-color: rgba(247, 37, 133, 0.1); }
.badge-soft-warning { background-color: rgba(255, 190, 11, 0.1); }
.font-weight-600 { font-weight: 600; }
.font-weight-500 { font-weight: 500; }
</style>

<?php include 'layout/footer.php'; ?>
