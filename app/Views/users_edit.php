<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="mb-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent p-0 mb-2">
                            <li class="breadcrumb-item"><a href="users">Users</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit User</li>
                        </ol>
                    </nav>
                    <h2 class="h3 mb-0 page-title">Edit User Account</h2>
                </div>
                
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white p-4">
                        <h5 class="card-title"><i class="fe fe-user mr-2 text-primary"></i> Profile Information</h5>
                    </div>
                    <form action="users?action=update" method="POST">
                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-600">Full Name</label>
                                    <input type="text" name="name" class="form-control form-control-lg bg-light border-0" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-600">Email Address</label>
                                    <input type="email" name="email" class="form-control form-control-lg bg-light border-0" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-600">Phone Number</label>
                                    <input type="text" name="phone" class="form-control form-control-lg bg-light border-0" value="<?php echo htmlspecialchars($user['phone']); ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-600">New Password <small class="text-muted">(Leave blank to keep current)</small></label>
                                    <input type="password" name="password" class="form-control form-control-lg bg-light border-0">
                                </div>
                            </div>

                            <hr class="my-4 op-1">

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-600">User Role</label>
                                    <select name="role_id" class="form-control form-control-lg bg-light border-0">
                                        <?php foreach($roles as $role): ?>
                                            <option value="<?php echo $role['id']; ?>" <?php echo $user['role_id'] == $role['id'] ? 'selected' : ''; ?>>
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
                                            <?php if($manager['id'] != $user['id']): // Prevent circular assignment ?>
                                                <option value="<?php echo $manager['id']; ?>" <?php echo $user['manager_id'] == $manager['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($manager['name']); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 p-4 d-flex justify-content-between">
                            <a href="users" class="btn btn-outline-secondary px-4">Back to List</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.font-weight-600 { font-weight: 600; }
.op-1 { opacity: 0.1; }
</style>

<?php include 'layout/footer.php'; ?>
