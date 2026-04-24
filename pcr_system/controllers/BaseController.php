<?php
/**
 * Base Controller Class
 * Provides common functionality for all controllers
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

class BaseController {
    protected $viewPath;
    
    public function __construct() {
        $this->viewPath = __DIR__ . '/../views/';
    }
    
    /**
     * Render a view
     */
    protected function render($view, $data = []) {
        // Extract data array to variables
        extract($data);
        
        // Set pageTitle if not set
        if (!isset($pageTitle)) {
            $pageTitle = 'Page';
        }
        
        // Include header
        include __DIR__ . '/../includes/header.php';
        
        // Include the view file
        $viewFile = $this->viewPath . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "<div class='alert alert-danger'>View not found: $view</div>";
        }
        
        // Include footer
        include __DIR__ . '/../includes/footer.php';
    }
    
    /**
     * Redirect to a URL
     */
    protected function redirect($url, $message = null) {
        if ($message) {
            $_SESSION['message'] = $message;
        }
        header("Location: " . $url);
        exit();
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    /**
     * Require login
     */
    protected function requireLogin() {
        if (!isSessionValid()) {
            $this->redirect('../auth/login.php', 'Please login to access this page');
        }
    }
    
    /**
     * Require admin access
     */
    protected function requireAdmin() {
        $this->requireLogin();
        if (!isAdmin()) {
            $this->redirect('student/dashboard.php', 'Access denied. Admin privileges required.');
        }
    }
    
    /**
     * Get POST data
     */
    protected function getPost($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Get GET data
     */
    protected function getGet($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
}

