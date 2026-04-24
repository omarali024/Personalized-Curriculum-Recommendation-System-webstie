<?php
/**
 * Database Configuration
 * Personalized Curriculum Recommendation System
 * 
 * Update these settings according to your XAMPP/MySQL configuration
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'curriculum_recommendation');
define('DB_USER', 'root');
define('DB_PASS', ''); // Leave empty for default XAMPP setup
define('DB_CHARSET', 'utf8mb4');

// Application configuration
define('APP_NAME', 'Personalized Curriculum Recommendation System');
define('APP_URL', 'http://localhost/curriculum-system/');
define('ADMIN_EMAIL', 'admin@example.com');

// Security settings
define('PASSWORD_MIN_LENGTH', 6);
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// Database connection class
class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    public $conn;

    /**
     * Get database connection
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
            die();
        }
        
        return $this->conn;
    }
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Helper function to redirect with message
function redirect($url, $message = null) {
    if ($message) {
        $_SESSION['message'] = $message;
    }
    header("Location: " . $url);
    exit();
}

// Helper function to display messages
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        return $message;
    }
    return null;
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to check if user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

// Helper function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Helper function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Helper function to generate random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Helper function to get correct path based on current directory
function getPath($path) {
    $currentDir = dirname($_SERVER['PHP_SELF']);
    $currentDir = rtrim($currentDir, '/');
    
    // If we're in root directory
    if ($currentDir === '' || $currentDir === '/New folder') {
        return $path;
    }
    
    // If we're in a subdirectory, go up one level
    if (strpos($currentDir, '/auth') !== false || 
        strpos($currentDir, '/admin') !== false || 
        strpos($currentDir, '/student') !== false) {
        return '../' . $path;
    }
    
    return $path;
}
?>
