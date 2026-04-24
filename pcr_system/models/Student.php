<?php
/**
 * Student Model
 * Handles all database operations for students
 */

require_once __DIR__ . '/BaseModel.php';

class Student extends BaseModel {
    protected $table = 'students';
    
    /**
     * Register a new student
     */
    public function register($name, $email, $password, $interests = '') {
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
        $existing = $this->findOne(['email' => $email]);
        if ($existing) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        // Hash password and create student
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $data = [
            'name' => sanitizeInput($name),
            'email' => sanitizeInput($email),
            'password' => $hashedPassword,
            'interests' => sanitizeInput($interests),
            'is_admin' => 0
        ];
        
        return $this->create($data);
    }
    
    /**
     * Login student
     */
    public function login($email, $password) {
        $student = $this->findOne(['email' => $email]);
        
        if (!$student) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
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
    }
    
    /**
     * Update student profile
     */
    public function updateProfile($studentId, $name, $interests) {
        $data = [
            'name' => sanitizeInput($name),
            'interests' => sanitizeInput($interests)
        ];
        
        $result = $this->update($studentId, $data);
        
        if ($result['success']) {
            // Update session data
            $_SESSION['user_name'] = $name;
            $_SESSION['user_interests'] = $interests;
        }
        
        return $result;
    }
    
    /**
     * Change password
     */
    public function changePassword($studentId, $currentPassword, $newPassword) {
        $student = $this->findById($studentId);
        
        if (!password_verify($currentPassword, $student['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
            return ['success' => false, 'message' => 'New password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long'];
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($studentId, ['password' => $hashedPassword]);
    }
    
    /**
     * Get student statistics
     */
    public function getStats() {
        try {
            $stats = [];
            
            $stats['total_students'] = $this->count(['is_admin' => 0]);
            $stats['total_admins'] = $this->count(['is_admin' => 1]);
            
            return $stats;
        } catch (PDOException $e) {
            return [];
        }
    }
}

