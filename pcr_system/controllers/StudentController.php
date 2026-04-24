<?php
/**
 * Student Controller
 * Handles student dashboard and profile
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/CompletedCourse.php';
require_once __DIR__ . '/../models/Recommendation.php';

class StudentController extends BaseController {
    private $studentModel;
    private $courseModel;
    private $completedCourseModel;
    private $recommendationModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        
        $this->studentModel = new Student();
        $this->courseModel = new Course();
        $this->completedCourseModel = new CompletedCourse();
        $this->recommendationModel = new Recommendation();
    }
    
    /**
     * Show student dashboard
     */
    public function dashboard() {
        $studentId = $_SESSION['user_id'];
        $message = '';
        
        // Handle actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $this->getPost('action');
            
            switch ($action) {
                case 'generate_recommendations':
                    $this->recommendationModel->generate($studentId);
                    $message = 'Recommendations generated successfully!';
                    break;
                    
                case 'add_completed_course':
                    $courseId = (int)$this->getPost('course_id');
                    $grade = sanitizeInput($this->getPost('grade', ''));
                    $result = $this->completedCourseModel->add($studentId, $courseId, $grade);
                    $message = $result['message'];
                    break;
                    
                case 'remove_completed_course':
                    $courseId = (int)$this->getPost('course_id');
                    $result = $this->completedCourseModel->remove($studentId, $courseId);
                    $message = $result['message'];
                    break;
            }
        }
        
        // Get data for dashboard
        $completedCourses = $this->completedCourseModel->getByStudent($studentId);
        $recommendations = $this->recommendationModel->getByStudent($studentId);
        $allCourses = $this->courseModel->getAll(20);
        $courseStats = $this->courseModel->getStats();
        
        $this->render('student/dashboard', [
            'pageTitle' => 'Student Dashboard',
            'message' => $message,
            'completedCourses' => $completedCourses,
            'recommendations' => $recommendations,
            'allCourses' => $allCourses,
            'courseStats' => $courseStats,
            'studentName' => $_SESSION['user_name'],
            'studentEmail' => $_SESSION['user_email'],
            'studentInterests' => $_SESSION['user_interests']
        ]);
    }
    
    /**
     * Show and handle profile page
     */
    public function profile() {
        $studentId = $_SESSION['user_id'];
        $error = '';
        $success = '';
        
        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $this->getPost('action');
            
            switch ($action) {
                case 'update_profile':
                    $name = sanitizeInput($this->getPost('name'));
                    $interests = sanitizeInput($this->getPost('interests', ''));
                    
                    if (empty($name)) {
                        $error = 'Name is required';
                    } else {
                        $result = $this->studentModel->updateProfile($studentId, $name, $interests);
                        if ($result['success']) {
                            $success = $result['message'];
                        } else {
                            $error = $result['message'];
                        }
                    }
                    break;
                    
                case 'change_password':
                    $currentPassword = $this->getPost('current_password');
                    $newPassword = $this->getPost('new_password');
                    $confirmPassword = $this->getPost('confirm_password');
                    
                    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                        $error = 'All password fields are required';
                    } elseif ($newPassword !== $confirmPassword) {
                        $error = 'New passwords do not match';
                    } else {
                        $result = $this->studentModel->changePassword($studentId, $currentPassword, $newPassword);
                        if ($result['success']) {
                            $success = $result['message'];
                        } else {
                            $error = $result['message'];
                        }
                    }
                    break;
            }
        }
        
        $this->render('student/profile', [
            'pageTitle' => 'Profile Settings',
            'error' => $error,
            'success' => $success
        ]);
    }
}

