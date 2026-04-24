<?php
/**
 * Admin Controller
 * Handles admin dashboard and course management
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/CompletedCourse.php';
require_once __DIR__ . '/../models/Recommendation.php';

class AdminController extends BaseController {
    private $courseModel;
    private $studentModel;
    private $completedCourseModel;
    private $recommendationModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireAdmin();
        
        $this->courseModel = new Course();
        $this->studentModel = new Student();
        $this->completedCourseModel = new CompletedCourse();
        $this->recommendationModel = new Recommendation();
    }
    
    /**
     * Show admin dashboard
     */
    public function dashboard() {
        $error = '';
        $success = '';
        
        // Handle actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $this->getPost('action');
            
            if ($action === 'add_course') {
                $result = $this->courseModel->createCourse([
                    'name' => $this->getPost('name'),
                    'category' => $this->getPost('category'),
                    'difficulty' => $this->getPost('difficulty'),
                    'keywords' => $this->getPost('keywords', ''),
                    'description' => $this->getPost('description', ''),
                    'credits' => $this->getPost('credits', 3)
                ]);
                
                if ($result['success']) {
                    $success = 'Course added successfully!';
                } else {
                    $error = $result['message'];
                }
            }
        }
        
        // Get dashboard statistics
        $courseStats = $this->courseModel->getStats();
        $recentCourses = $this->courseModel->getAll(10);
        $categories = $this->courseModel->getCategories();
        
        // Get student statistics
        $studentStats = $this->studentModel->getStats();
        $totalCompletions = $this->completedCourseModel->getStats();
        $totalRecommendations = $this->recommendationModel->getStats();
        
        $this->render('admin/dashboard', [
            'pageTitle' => 'Admin Dashboard',
            'error' => $error,
            'success' => $success,
            'courseStats' => $courseStats,
            'recentCourses' => $recentCourses,
            'categories' => $categories,
            'totalStudents' => $studentStats['total_students'] ?? 0,
            'totalCompletions' => $totalCompletions,
            'totalRecommendations' => $totalRecommendations
        ]);
    }
    
    /**
     * Show and handle course management
     */
    public function courses() {
        $error = '';
        $success = '';
        $editCourse = null;
        
        // Handle POST actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $this->getPost('action');
            
            switch ($action) {
                case 'add_course':
                    $result = $this->courseModel->createCourse([
                        'name' => $this->getPost('name'),
                        'category' => $this->getPost('category'),
                        'difficulty' => $this->getPost('difficulty'),
                        'keywords' => $this->getPost('keywords', ''),
                        'description' => $this->getPost('description', ''),
                        'credits' => $this->getPost('credits', 3)
                    ]);
                    
                    if ($result['success']) {
                        $success = 'Course added successfully!';
                    } else {
                        $error = $result['message'];
                    }
                    break;
                    
                case 'update_course':
                    $courseId = (int)$this->getPost('course_id');
                    $result = $this->courseModel->updateCourse($courseId, [
                        'name' => $this->getPost('name'),
                        'category' => $this->getPost('category'),
                        'difficulty' => $this->getPost('difficulty'),
                        'keywords' => $this->getPost('keywords', ''),
                        'description' => $this->getPost('description', ''),
                        'credits' => $this->getPost('credits', 3)
                    ]);
                    
                    if ($result['success']) {
                        $success = 'Course updated successfully!';
                    } else {
                        $error = $result['message'];
                    }
                    break;
            }
        }
        
        // Handle GET actions
        if (isset($_GET['edit'])) {
            $courseId = (int)$_GET['edit'];
            $editCourse = $this->courseModel->findById($courseId);
            if (!$editCourse) {
                $error = 'Course not found';
            }
        }
        
        if (isset($_GET['delete'])) {
            $courseId = (int)$_GET['delete'];
            
            // Check if course has any completions or recommendations
            $completions = $this->completedCourseModel->count(['course_id' => $courseId]);
            $recommendations = $this->recommendationModel->count(['course_id' => $courseId]);
            
            if ($completions > 0 || $recommendations > 0) {
                $error = 'Cannot delete course: It has ' . ($completions + $recommendations) . ' associated records';
            } else {
                $result = $this->courseModel->delete($courseId);
                if ($result['success']) {
                    $success = 'Course deleted successfully!';
                } else {
                    $error = $result['message'];
                }
            }
        }
        
        // Get all courses
        $search = $this->getGet('search', '');
        $categoryFilter = $this->getGet('category', '');
        $difficultyFilter = $this->getGet('difficulty', '');
        
        if ($search || $categoryFilter || $difficultyFilter) {
            $courses = $this->courseModel->search($search, $categoryFilter, $difficultyFilter);
        } else {
            $courses = $this->courseModel->getAll();
        }
        
        $categories = $this->courseModel->getCategories();
        
        $this->render('admin/courses', [
            'pageTitle' => 'Course Management',
            'error' => $error,
            'success' => $success,
            'courses' => $courses,
            'categories' => $categories,
            'editCourse' => $editCourse,
            'search' => $search,
            'categoryFilter' => $categoryFilter,
            'difficultyFilter' => $difficultyFilter
        ]);
    }
}

