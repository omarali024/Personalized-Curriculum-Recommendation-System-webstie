<?php
/**
 * Common Footer
 * Personalized Curriculum Recommendation System
 */
?>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-graduation-cap me-2"></i>
                    <?php echo APP_NAME; ?>
                </h5>
                <p class="text-light">
                    Empowering students with personalized course recommendations based on their interests, 
                    academic history, and career aspirations.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="text-light">
                        <i class="fab fa-facebook-f fa-lg"></i>
                    </a>
                    <a href="#" class="text-light">
                        <i class="fab fa-twitter fa-lg"></i>
                    </a>
                    <a href="#" class="text-light">
                        <i class="fab fa-linkedin-in fa-lg"></i>
                    </a>
                    <a href="#" class="text-light">
                        <i class="fab fa-instagram fa-lg"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-2 mb-4">
                <h6 class="mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="<?php echo getPath('index.php'); ?>" class="text-light text-decoration-none">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <li class="mb-2">
                                <a href="<?php echo getPath('admin/dashboard.php'); ?>" class="text-light text-decoration-none">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="<?php echo getPath('admin/courses.php'); ?>" class="text-light text-decoration-none">
                                    <i class="fas fa-book me-1"></i>Courses
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="mb-2">
                                <a href="<?php echo getPath('student/dashboard.php'); ?>" class="text-light text-decoration-none">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="<?php echo getPath('student/profile.php'); ?>" class="text-light text-decoration-none">
                                    <i class="fas fa-user me-1"></i>Profile
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="mb-2">
                            <a href="<?php echo getPath('auth/login.php'); ?>" class="text-light text-decoration-none">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo getPath('auth/register.php'); ?>" class="text-light text-decoration-none">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="col-lg-3 mb-4">
                <h6 class="mb-3">Features</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-star me-1 text-warning"></i>
                        <span class="text-light">Smart Recommendations</span>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-chart-line me-1 text-info"></i>
                        <span class="text-light">Progress Tracking</span>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-heart me-1 text-danger"></i>
                        <span class="text-light">Interest-Based Matching</span>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-graduation-cap me-1 text-success"></i>
                        <span class="text-light">Academic Planning</span>
                    </li>
                </ul>
            </div>
            
            <div class="col-lg-3 mb-4">
                <h6 class="mb-3">Contact Info</h6>
                <ul class="list-unstyled text-light">
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        support@pcr-system.edu
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2"></i>
                        +1 (555) 123-4567
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        123 University Ave<br>
                        Education City, EC 12345
                    </li>
                </ul>
            </div>
        </div>
        
        <hr class="my-4 border-light">
        
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 text-light">
                    &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="#" class="text-light text-decoration-none me-3">Privacy Policy</a>
                <a href="#" class="text-light text-decoration-none me-3">Terms of Service</a>
                <a href="#" class="text-light text-decoration-none">Support</a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="../assets/js/main.js"></script>

</body>
</html>
