<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-cog me-2"></i>Profile Settings
                    </h4>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success) && $success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Profile Information -->
                    <div class="mb-5">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-user me-2"></i>Personal Information
                        </h5>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_profile">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" disabled>
                                    <div class="form-text">Email cannot be changed</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="interests" class="form-label">
                                    Academic Interests
                                    <span class="text-muted">(separate with commas)</span>
                                </label>
                                <textarea class="form-control" id="interests" name="interests" rows="4" 
                                          placeholder="e.g., Programming, Mathematics, Psychology, Business, Data Science..."><?php echo htmlspecialchars($_SESSION['user_interests']); ?></textarea>
                                <div class="form-text">Your interests help us recommend relevant courses</div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="dashboard.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Update Profile
                                </button>
                            </div>
                        </form>
                    </div>

                    <hr>

                    <!-- Change Password -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-lock me-2"></i>Change Password
                        </h5>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="change_password">
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" 
                                           minlength="<?php echo PASSWORD_MIN_LENGTH; ?>" required>
                                    <div class="form-text">Minimum <?php echo PASSWORD_MIN_LENGTH; ?> characters</div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-1"></i>Change Password
                            </button>
                        </form>
                    </div>

                    <hr>

                    <!-- Account Information -->
                    <div>
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Account Information
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Account Type</h6>
                                        <p class="card-text">
                                            <?php echo isAdmin() ? 
                                                '<span class="badge bg-danger"><i class="fas fa-crown me-1"></i>Administrator</span>' : 
                                                '<span class="badge bg-primary"><i class="fas fa-user me-1"></i>Student</span>'; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Member Since</h6>
                                        <p class="card-text text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo date('F Y'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (newPassword !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

// Show/hide password fields
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
    field.setAttribute('type', type);
}
</script>

