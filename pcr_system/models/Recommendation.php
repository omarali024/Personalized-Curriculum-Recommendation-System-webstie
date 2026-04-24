<?php
/**
 * Recommendation Model
 * Handles all database operations for recommendations
 */

require_once __DIR__ . '/BaseModel.php';

class Recommendation extends BaseModel {
    protected $table = 'recommendations';
    
    /**
     * Get recommendations for a student
     */
    public function getByStudent($studentId) {
        try {
            $stmt = $this->db->prepare("
                SELECT r.*, c.name as course_name, c.category, c.difficulty, c.description, c.credits 
                FROM {$this->table} r 
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
     * Generate recommendations for a student
     */
    public function generate($studentId) {
        try {
            // Get student's interests
            $studentModel = new Student();
            $student = $studentModel->findById($studentId);
            
            if (!$student || empty($student['interests'])) {
                return [];
            }
            
            // Get completed course IDs
            $completedCourseModel = new CompletedCourse();
            $completedCourseIds = $completedCourseModel->getCompletedIds($studentId);
            
            // Clear existing recommendations
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE student_id = ?");
            $stmt->execute([$studentId]);
            
            $recommendations = [];
            $studentInterests = explode(',', strtolower($student['interests']));
            
            // Get all courses not completed by student
            $courseModel = new Course();
            $allCourses = $courseModel->getAll();
            
            foreach ($allCourses as $course) {
                // Skip if already completed
                if (in_array($course['id'], $completedCourseIds)) {
                    continue;
                }
                
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
                    $data = [
                        'student_id' => $studentId,
                        'course_id' => $course['id'],
                        'reason' => $reason,
                        'confidence_score' => min($confidenceScore, 1.0)
                    ];
                    
                    $this->create($data);
                    
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
     * Get statistics
     */
    public function getStats() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table}");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
}

