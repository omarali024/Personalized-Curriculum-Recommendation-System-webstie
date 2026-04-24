<?php
/**
 * Completed Course Model
 * Handles all database operations for completed courses
 */

require_once __DIR__ . '/BaseModel.php';

class CompletedCourse extends BaseModel {
    protected $table = 'completed_courses';
    
    /**
     * Get completed courses for a student
     */
    public function getByStudent($studentId) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, cc.completed_at, cc.grade, cc.id as completion_id
                FROM {$this->table} cc 
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
    public function add($studentId, $courseId, $grade = null) {
        try {
            // Check if course is already completed
            $existing = $this->findOne([
                'student_id' => $studentId,
                'course_id' => $courseId
            ]);
            
            if ($existing) {
                return ['success' => false, 'message' => 'Course already marked as completed'];
            }
            
            $data = [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'grade' => $grade ? sanitizeInput($grade) : null
            ];
            
            return $this->create($data);
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Remove completed course
     */
    public function remove($studentId, $courseId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE student_id = ? AND course_id = ?");
            $result = $stmt->execute([$studentId, $courseId]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Course removed from completed list'];
            }
            return ['success' => false, 'message' => 'Failed to remove course'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get completed course IDs for a student
     */
    public function getCompletedIds($studentId) {
        try {
            $stmt = $this->db->prepare("SELECT course_id FROM {$this->table} WHERE student_id = ?");
            $stmt->execute([$studentId]);
            return array_column($stmt->fetchAll(), 'course_id');
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

