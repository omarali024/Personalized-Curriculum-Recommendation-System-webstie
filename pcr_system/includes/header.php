<?php
/**
 * Common Header
 * Personalized Curriculum Recommendation System
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Menu.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
    
    <style>
        .navbar-brand {
            font-weight: bold;
            color: #2c3e50 !important;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky !important;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .navbar.scrolled {
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s ease;
        }
        .navbar-nav .nav-link:hover {
            color: #f8f9fa !important;
            transform: translateY(-1px);
        }
        .navbar-nav .nav-link.active {
            background-color: rgba(255,255,255,0.25) !important;
            font-weight: 600;
            border-bottom: 3px solid rgba(255,255,255,0.8);
            border-radius: 5px 5px 0 0;
            padding-bottom: 0.3rem !important;
        }
        .navbar-nav .nav-link.active i {
            color: #fff !important;
            text-shadow: 0 0 5px rgba(255,255,255,0.5);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border: none;
            border-radius: 10px;
            font-weight: 500;
        }
        .badge {
            border-radius: 20px;
            padding: 8px 12px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
        }
        .footer {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 40px 0;
            margin-top: 50px;
        }
    </style>
    <script>
        // Make navbar stick and add scroll effect
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.getElementById('mainNavbar');
            
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        });
    </script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="<?php echo Menu::getHomeUrl(); ?>">
                <i class="fas fa-graduation-cap me-2"></i>
                <?php echo APP_NAME; ?>
                <?php if (isLoggedIn()): ?>
                    <small class="badge bg-light text-primary ms-2" style="font-size: 0.65rem; vertical-align: middle;">
                        <?php echo isAdmin() ? 'Admin' : 'Student'; ?>
                    </small>
                <?php endif; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php 
                    // Dynamic menu - items change based on user role
                    // Guest: Home, Login, Register
                    // Student: Home, Dashboard, Profile
                    // Admin: Home, Dashboard, Courses
                    echo Menu::renderMainMenu(); 
                    echo Menu::renderUserMenu(); 
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container-fluid py-4">
        <?php 
        // Display messages
        $message = displayMessage();
        if ($message): 
        ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i><?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
