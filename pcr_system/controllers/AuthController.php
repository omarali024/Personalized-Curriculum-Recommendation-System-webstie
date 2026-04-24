<?php
/**
 * Authentication Controller
 * Handles login, registration, and logout
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Student.php';

class AuthController extends BaseController {
    private $studentModel;
    
    public function __construct() {
        parent::__construct();
        $this->studentModel = new Student();
    }
    
    /**
     * Show login page
     */
    public function login() {
        // Redirect if already logged in
        if (isLoggedIn()) {
            $this->redirect(isAdmin() ? '../admin/dashboard.php' : '../student/dashboard.php');
        }
        
        $error = '';
        $success = '';
        
        // Handle login form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitizeInput($this->getPost('email'));
            $password = $this->getPost('password');
            
            if (empty($email) || empty($password)) {
                $error = 'Please fill in all fields';
            } else {
                $result = $this->studentModel->login($email, $password);
                
                if ($result['success']) {
                    $this->redirect(isAdmin() ? '../admin/dashboard.php' : '../student/dashboard.php', $result['message']);
                } else {
                    $error = $result['message'];
                }
            }
        }
        
        $this->render('auth/login', [
            'pageTitle' => 'Student Login',
            'error' => $error,
            'success' => $success,
            'email' => $this->getPost('email', '')
        ]);
    }
    
    /**
     * Show registration page
     */
    public function register() {
        // Redirect if already logged in
        if (isLoggedIn()) {
            $this->redirect(isAdmin() ? '../admin/dashboard.php' : '../student/dashboard.php');
        }
        
        $error = '';
        $success = '';
        
        // Handle registration form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = sanitizeInput($this->getPost('name'));
            $email = sanitizeInput($this->getPost('email'));
            $password = $this->getPost('password');
            $confirmPassword = $this->getPost('confirm_password');
            $interests = sanitizeInput($this->getPost('interests', ''));
            
            if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
                $error = 'Please fill in all required fields';
            } elseif ($password !== $confirmPassword) {
                $error = 'Passwords do not match';
            } else {
                $result = $this->studentModel->register($name, $email, $password, $interests);
                
                if ($result['success']) {
                    $success = $result['message'];
                    // Clear form data
                    $name = $email = $interests = '';
                } else {
                    $error = $result['message'];
                }
            }
        }
        
        $this->render('auth/register', [
            'pageTitle' => 'Student Registration',
            'error' => $error,
            'success' => $success,
            'name' => $this->getPost('name', ''),
            'email' => $this->getPost('email', ''),
            'interests' => $this->getPost('interests', '')
        ]);
    }
    
    /**
     * Handle logout
     */
    public function logout() {
        session_destroy();
        $this->redirect('../auth/login.php', 'Logged out successfully');
    }
}

