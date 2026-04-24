<?php
/**
 * Core Functions
 * Personalized Curriculum Recommendation System
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Get all courses
 */
function getAllCourses($limit = null, $category = null, $difficulty = null) {
    global $db;
    
    try {
        $sql = "SELECT * FROM courses";
        $params = [];
        $conditions = [];
        
        if ($category) {
            $conditions[] = "category = ?";
            $params[] = $category;
        }
        
        if ($difficulty) {
            $conditions[] = "difficulty = ?";
            $params[] = $difficulty;
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY name ASC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get course by ID
 */
function getCourseById($courseId) {
    global $db;
    
    try {
        $stmt = $db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$courseId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Get student's completed courses
 */
function getCompletedCourses($studentId) {
    global $db;
    
    try {
        $stmt = $db->prepare("
            SELECT c.*, cc.completed_at, cc.grade 
            FROM completed_courses cc 
            JOIN courses c ON cc.course_id = c.id 
            WHERE cc.student_id = ? 
            ORDER BY cc.completed_at DESC
        ");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Add completed course for student
 */
function addCompletedCourse($studentId, $courseId, $grade = null) {
    global $db;
    
    try {
        // Check if course is already completed
        $stmt = $db->prepare("SELECT id FROM completed_courses WHERE student_id = ? AND course_id = ?");
        $stmt->execute([$studentId, $courseId]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Course already marked as completed'];
        }
        
        // Add completed course
        $stmt = $db->prepare("INSERT INTO completed_courses (student_id, course_id, grade) VALUES (?, ?, ?)");
        $result = $stmt->execute([$studentId, $courseId, $grade]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Course marked as completed'];
        } else {
            return ['success' => false, 'message' => 'Failed to mark course as completed'];
        }
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Remove completed course for student
 */
function removeCompletedCourse($studentId, $courseId) {
    global $db;
    
    try {
        $stmt = $db->prepare("DELETE FROM completed_courses WHERE student_id = ? AND course_id = ?");
        $result = $stmt->execute([$studentId, $courseId]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Course removed from completed list'];
        } else {
            return ['success' => false, 'message' => 'Failed to remove course'];
        }
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Generate course recommendations for student
 */
function generateRecommendations($studentId) {
    global $db;
    
    try {
        // Get student's interests
        $stmt = $db->prepare("SELECT interests FROM students WHERE id = ?");
        $stmt->execute([$studentId]);
        $student = $stmt->fetch();
        
        if (!$student || empty($student['interests'])) {
            return [];
        }
        
        // Get student's completed course IDs
        $stmt = $db->prepare("SELECT course_id FROM completed_courses WHERE student_id = ?");
        $stmt->execute([$studentId]);
        $completedCourseIds = array_column($stmt->fetchAll(), 'course_id');
        
        // Clear existing recommendations
        $stmt = $db->prepare("DELETE FROM recommendations WHERE student_id = ?");
        $stmt->execute([$studentId]);
        
        $recommendations = [];
        $studentInterests = explode(',', strtolower($student['interests']));
        
        // Get all courses not completed by student
        $excludeClause = !empty($completedCourseIds) ? "AND id NOT IN (" . implode(',', $completedCourseIds) . ")" : "";
        $stmt = $db->prepare("SELECT * FROM courses WHERE 1=1 $excludeClause ORDER BY name ASC");
        $stmt->execute();
        $courses = $stmt->fetchAll();
        
        foreach ($courses as $course) {
            $courseKeywords = explode(',', strtolower($course['keywords']));
            $matchedInterests = [];
            $confidenceScore = 0;
            
            // Check for keyword matches
            foreach ($studentInterests as $interest) {
                $interest = trim($interest);
                foreach ($courseKeywords as $keyword) {
                    $keyword = trim($keyword);
                    if (strpos($keyword, $interest) !== false || strpos($interest, $keyword) !== false) {
                        $matchedInterests[] = ucfirst($interest);
                        $confidenceScore += 0.3;
                        break;
                    }
                }
            }
            
            // Check for category match
            foreach ($studentInterests as $interest) {
                $interest = trim($interest);
                if (strpos(strtolower($course['category']), $interest) !== false || 
                    strpos($interest, strtolower($course['category'])) !== false) {
                    if (!in_array($course['category'], $matchedInterests)) {
                        $matchedInterests[] = $course['category'];
                    }
                    $confidenceScore += 0.2;
                    break;
                }
            }
            
            // Only recommend if there's at least one match and confidence > 0.2
            if (!empty($matchedInterests) && $confidenceScore > 0.2) {
                $reason = "Matched by interest: " . implode(", ", array_unique($matchedInterests));
                
                // Insert recommendation
                $stmt = $db->prepare("INSERT INTO recommendations (student_id, course_id, reason, confidence_score) VALUES (?, ?, ?, ?)");
                $stmt->execute([$studentId, $course['id'], $reason, min($confidenceScore, 1.0)]);
                
                $recommendations[] = [
                    'course' => $course,
                    'reason' => $reason,
                    'confidence_score' => min($confidenceScore, 1.0)
                ];
            }
        }
        
        // Sort by confidence score
        usort($recommendations, function($a, $b) {
            return $b['confidence_score'] <=> $a['confidence_score'];
        });
        
        return $recommendations;
        
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get student recommendations
 */
function getStudentRecommendations($studentId) {
    global $db;
    
    try {
        $stmt = $db->prepare("
            SELECT r.*, c.name as course_name, c.category, c.difficulty, c.description, c.credits 
            FROM recommendations r 
            JOIN courses c ON r.course_id = c.id 
            WHERE r.student_id = ? 
            ORDER BY r.confidence_score DESC, r.created_at DESC
        ");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Search courses
 */
function searchCourses($query, $category = null, $difficulty = null) {
    global $db;
    
    try {
        $sql = "SELECT * FROM courses WHERE (name LIKE ? OR keywords LIKE ? OR description LIKE ?)";
        $params = ["%$query%", "%$query%", "%$query%"];
        
        if ($category) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
        
        if ($difficulty) {
            $sql .= " AND difficulty = ?";
            $params[] = $difficulty;
        }
        
        $sql .= " ORDER BY name ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get all categories
 */
function getAllCategories() {
    global $db;
    
    try {
        $stmt = $db->prepare("SELECT DISTINCT category FROM courses ORDER BY category ASC");
        $stmt->execute();
        return array_column($stmt->fetchAll(), 'category');
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get course statistics
 */
function getCourseStats() {
    global $db;
    
    try {
        $stats = [];
        
        // Total courses
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM courses");
        $stmt->execute();
        $stats['total_courses'] = $stmt->fetch()['total'];
        
        // Courses by category
        $stmt = $db->prepare("SELECT category, COUNT(*) as count FROM courses GROUP BY category ORDER BY count DESC");
        $stmt->execute();
        $stats['by_category'] = $stmt->fetchAll();
        
        // Courses by difficulty
        $stmt = $db->prepare("SELECT difficulty, COUNT(*) as count FROM courses GROUP BY difficulty ORDER BY difficulty");
        $stmt->execute();
        $stats['by_difficulty'] = $stmt->fetchAll();
        
        return $stats;
        
    } catch (PDOException $e) {
        return [];
    }
}
?>
