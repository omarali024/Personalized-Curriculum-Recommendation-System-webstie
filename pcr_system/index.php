<?php
/**
 * Home Page
 * Personalized Curriculum Recommendation System
 */

require_once 'config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/dashboard.php' : 'student/dashboard.php');
}

$pageTitle = 'Welcome';
include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="hero-section bg-gradient text-white py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    Personalized Curriculum Recommendation System
                </h1>
                <p class="lead mb-4">
                    Discover your ideal university courses based on your interests and academic journey. 
                    Get personalized recommendations that match your learning goals and career aspirations.
                </p>
                <div class="d-flex gap-3">
                    <a href="auth/register.php" class="btn btn-light btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Get Started
                    </a>
                    <a href="auth/login.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <i class="fas fa-graduation-cap fa-10x opacity-75"></i>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="container py-5">
    <div class="row text-center mb-5">
        <div class="col-12">
            <h2 class="display-5 fw-bold mb-3">Why Choose Our System?</h2>
            <p class="lead text-muted">Advanced recommendation algorithms powered by your interests and academic history</p>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="feature-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-brain fa-2x"></i>
                    </div>
                    <h4 class="card-title">Smart Recommendations</h4>
                    <p class="card-text text-muted">
                        Our intelligent algorithm analyzes your interests and completed courses to suggest the most relevant courses for your academic journey.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-user-graduate fa-2x"></i>
                    </div>
                    <h4 class="card-title">Personalized Learning</h4>
                    <p class="card-text text-muted">
                        Every recommendation is tailored to your unique academic profile, ensuring courses that align with your interests and career goals.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="feature-icon bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                    <h4 class="card-title">Track Progress</h4>
                    <p class="card-text text-muted">
                        Monitor your academic progress, manage completed courses, and see how your learning path evolves over time.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div class="bg-light py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="display-5 fw-bold mb-3">How It Works</h2>
                <p class="lead text-muted">Simple steps to get personalized course recommendations</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3">
                <div class="text-center">
                    <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <span class="fw-bold fs-4">1</span>
                    </div>
                    <h5>Create Account</h5>
                    <p class="text-muted">Sign up with your email and specify your academic interests</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="text-center">
                    <div class="step-number bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <span class="fw-bold fs-4">2</span>
                    </div>
                    <h5>Add Completed Courses</h5>
                    <p class="text-muted">Mark courses you've already completed to build your academic profile</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="text-center">
                    <div class="step-number bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <span class="fw-bold fs-4">3</span>
                    </div>
                    <h5>Get Recommendations</h5>
                    <p class="text-muted">Our system analyzes your profile and suggests relevant courses</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="text-center">
                    <div class="step-number bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <span class="fw-bold fs-4">4</span>
                    </div>
                    <h5>Plan Your Path</h5>
                    <p class="text-muted">Use recommendations to plan your academic journey and career goals</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="container py-5">
    <div class="row text-center">
        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <i class="fas fa-book fa-3x text-primary mb-3"></i>
                <h3 class="fw-bold">20+</h3>
                <p class="text-muted">Available Courses</p>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <i class="fas fa-users fa-3x text-success mb-3"></i>
                <h3 class="fw-bold">500+</h3>
                <p class="text-muted">Happy Students</p>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <i class="fas fa-star fa-3x text-warning mb-3"></i>
                <h3 class="fw-bold">1000+</h3>
                <p class="text-muted">Recommendations Generated</p>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <i class="fas fa-graduation-cap fa-3x text-info mb-3"></i>
                <h3 class="fw-bold">95%</h3>
                <p class="text-muted">Success Rate</p>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="bg-primary text-white py-5">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-3">Ready to Get Started?</h2>
        <p class="lead mb-4">Join thousands of students who have found their perfect academic path</p>
        <a href="auth/register.php" class="btn btn-light btn-lg">
            <i class="fas fa-rocket me-2"></i>Start Your Journey
        </a>
    </div>
</div>

<style>
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 60vh;
}

.feature-icon {
    width: 80px;
    height: 80px;
}

.step-number {
    width: 60px;
    height: 60px;
}

.stat-card {
    padding: 2rem;
    border-radius: 15px;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.card {
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}
</style>

<?php include 'includes/footer.php'; ?>
