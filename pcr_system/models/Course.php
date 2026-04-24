<?php
/**
 * Course Model
 * Handles all database operations for courses
 */

require_once __DIR__ . '/BaseModel.php';

class Course extends BaseModel {
    protected $table = 'courses';
    
    /**
     * Get all courses with optional filters
     */
    public function getAll($limit = null, $category = null, $difficulty = null) {
        try {
            $sql = "SELECT * FROM {$this->table}";
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
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Search courses
     */
    public function search($query, $category = null, $difficulty = null) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE (name LIKE ? OR keywords LIKE ? OR description LIKE ?)";
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
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get all categories
     */
    public function getCategories() {
        try {
            $stmt = $this->db->prepare("SELECT DISTINCT category FROM {$this->table} ORDER BY category ASC");
            $stmt->execute();
            return array_column($stmt->fetchAll(), 'category');
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get course statistics
     */
    public function getStats() {
        try {
            $stats = [];
            
            $stats['total_courses'] = $this->count();
            
            // Courses by category
            $stmt = $this->db->prepare("SELECT category, COUNT(*) as count FROM {$this->table} GROUP BY category ORDER BY count DESC");
            $stmt->execute();
            $stats['by_category'] = $stmt->fetchAll();
            
            // Courses by difficulty
            $stmt = $this->db->prepare("SELECT difficulty, COUNT(*) as count FROM {$this->table} GROUP BY difficulty ORDER BY difficulty");
            $stmt->execute();
            $stats['by_difficulty'] = $stmt->fetchAll();
            
            return $stats;
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Create course with validation
     */
    public function createCourse($data) {
        // Validate required fields
        if (empty($data['name']) || empty($data['category']) || empty($data['difficulty'])) {
            return ['success' => false, 'message' => 'Name, category, and difficulty are required'];
        }
        
        $courseData = [
            'name' => sanitizeInput($data['name']),
            'category' => sanitizeInput($data['category']),
            'difficulty' => sanitizeInput($data['difficulty']),
            'keywords' => sanitizeInput($data['keywords'] ?? ''),
            'description' => sanitizeInput($data['description'] ?? ''),
            'credits' => (int)($data['credits'] ?? 3)
        ];
        
        return $this->create($courseData);
    }
    
    /**
     * Update course with validation
     */
    public function updateCourse($id, $data) {
        if (empty($data['name']) || empty($data['category']) || empty($data['difficulty'])) {
            return ['success' => false, 'message' => 'Name, category, and difficulty are required'];
        }
        
        $courseData = [
            'name' => sanitizeInput($data['name']),
            'category' => sanitizeInput($data['category']),
            'difficulty' => sanitizeInput($data['difficulty']),
            'keywords' => sanitizeInput($data['keywords'] ?? ''),
            'description' => sanitizeInput($data['description'] ?? ''),
            'credits' => (int)($data['credits'] ?? 3)
        ];
        
        return $this->update($id, $courseData);
    }
}

