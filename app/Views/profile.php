<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <h2 class="h3 mb-4 page-title">Settings</h2>
                <div class="my-4">
                    <ul class="nav nav-tabs mb-4" id="contactTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Security</a>
                        </li>
                    </ul>
                    <form action="profile?action=update" method="POST" enctype="multipart/form-data">
                        <div class="row mt-5 align-items-center">
                            <div class="col-md-3 text-center mb-5">
                                <div class="avatar avatar-xl">
                                    <?php 
                                        $pic = (isset($user['profile_pic']) && !empty($user['profile_pic'])) ? $user['profile_pic'] : 'assets/avatars/default.jpg';
                                    ?>
                                    <img src="<?php echo $pic; ?>" alt="..." class="avatar-img rounded-circle border border-primary shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                                </div>
                                <div class="mt-3">
                                    <label for="profile_pic" class="btn btn-sm btn-outline-primary mb-0">Change Photo</label>
                                    <input type="file" id="profile_pic" name="profile_pic" class="d-none" onchange="previewImage(this)">
                                </div>
                            </div>
                            <div class="col">
                                <div class="row align-items-center">
                                    <div class="col-md-7">
                                        <h4 class="mb-1"><?php echo htmlspecialchars($user['name']); ?></h4>
                                        <p class="small mb-3"><span class="badge badge-dark"><?php echo htmlspecialchars($user['role_name']); ?></span></p>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-7">
                                        <p class="text-muted"> Update your personal information and profile picture. Ensure your contact details are accurate for reporting. </p>
                                    </div>
                                    <div class="col">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="firstname">Full Name</label>
                                <input type="text" id="firstname" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Email</label>
                                <input type="email" class="form-control" id="inputEmail4" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
                            </div>
                        </div>
                        <hr class="my-4">
                        <h5 class="mb-3">Bank Details</h5>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="bank_name">Bank Name</label>
                                <input type="text" id="bank_name" name="bank_name" class="form-control" value="<?php echo htmlspecialchars($user['bank_name'] ?? ''); ?>" placeholder="e.g. State Bank of India">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="account_number">Account Number</label>
                                <input type="text" id="account_number" name="account_number" class="form-control" value="<?php echo htmlspecialchars($user['account_number'] ?? ''); ?>" placeholder="Enter account number">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="ifsc_code">IFSC Code</label>
                                <input type="text" id="ifsc_code" name="ifsc_code" class="form-control" value="<?php echo htmlspecialchars($user['ifsc_code'] ?? ''); ?>" placeholder="e.g. SBIN0001234">
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="inputPassword5">New Password</label>
                                    <input type="password" class="form-control" id="inputPassword5" name="password" placeholder="Leave blank to keep current password">
                                </div>
                                <p class="small text-muted mb-2"> Password requirements: </p>
                                <ul class="small text-muted pl-4 mb-0">
                                    <li> Minimum 8 characters </li>
                                    <li>At least one special character</li>
                                    <li>At least one number</li>
                                    <li>Can’t be the same as a previous password </li>
                                </ul>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary shadow">Save Changes</button>
                    </form>
                </div> <!-- /.card-body -->
            </div> <!-- /.col-12 -->
        </div> <!-- .row -->
    </div> <!-- .container-fluid -->
</main>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.avatar-img').setAttribute('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'layout/footer.php'; ?>
