<?php
/**
 * Authentication Functions
 * Personalized Curriculum Recommendation System
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Register a new student
 */
function registerStudent($name, $email, $password, $interests = '') {
    global $db;
    
    try {
        // Validate input
        if (empty($name) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        if (!validateEmail($email)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            return ['success' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long'];
        }
        
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM students WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        // Hash password and insert new student
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("INSERT INTO students (name, email, password, interests) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$name, $email, $hashedPassword, $interests]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Registration successful! You can now login.'];
        } else {
            return ['success' => false, 'message' => 'Registration failed. Please try again.'];
        }
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Login student
 */
function loginStudent($email, $password) {
    global $db;
    
    try {
        // Find student by email
        $stmt = $db->prepare("SELECT id, name, email, password, interests, is_admin FROM students WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        $student = $stmt->fetch();
        
        // Verify password
        if (!password_verify($password, $student['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Set session variables
        $_SESSION['user_id'] = $student['id'];
        $_SESSION['user_name'] = $student['name'];
        $_SESSION['user_email'] = $student['email'];
        $_SESSION['user_interests'] = $student['interests'];
        $_SESSION['is_admin'] = (bool)$student['is_admin'];
        $_SESSION['login_time'] = time();
        
        return ['success' => true, 'message' => 'Login successful!'];
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Logout student
 */
function logoutStudent() {
    // Destroy session
    session_destroy();
    return ['success' => true, 'message' => 'Logged out successfully'];
}

/**
 * Check if session is valid (not expired)
 */
function isSessionValid() {
    if (!isLoggedIn()) {
        return false;
    }
    
    // Check session timeout
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > SESSION_TIMEOUT) {
        logoutStudent();
        return false;
    }
    
    return true;
}

/**
 * Require login - redirect if not logged in
 */
function requireLogin() {
    if (!isSessionValid()) {
        redirect('auth/login.php', 'Please login to access this page');
    }
}

/**
 * Require admin access - redirect if not admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        redirect('student/dashboard.php', 'Access denied. Admin privileges required.');
    }
}

/**
 * Update student profile
 */
function updateStudentProfile($studentId, $name, $interests) {
    global $db;
    
    try {
        $stmt = $db->prepare("UPDATE students SET name = ?, interests = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $result = $stmt->execute([$name, $interests, $studentId]);
        
        if ($result) {
            // Update session data
            $_SESSION['user_name'] = $name;
            $_SESSION['user_interests'] = $interests;
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update profile'];
        }
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Change student password
 */
function changePassword($studentId, $currentPassword, $newPassword) {
    global $db;
    
    try {
        // Verify current password
        $stmt = $db->prepare("SELECT password FROM students WHERE id = ?");
        $stmt->execute([$studentId]);
        $student = $stmt->fetch();
        
        if (!password_verify($currentPassword, $student['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        // Validate new password
        if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
            return ['success' => false, 'message' => 'New password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long'];
        }
        
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE students SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $result = $stmt->execute([$hashedPassword, $studentId]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Password changed successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to change password'];
        }
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}
?>
