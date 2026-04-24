<?php
/**
 * Database Connection Test
 * Use this to verify your database connection is working
 */

require_once 'config/config.php';

echo "<h2>Database Connection Test</h2>";

try {
    // Test basic connection
    echo "<p><strong>✓ Database connection successful!</strong></p>";
    
    // Test if tables exist
    $tables = ['students', 'courses', 'completed_courses', 'recommendations'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM $table");
            $stmt->execute();
            $count = $stmt->fetch()['count'];
            echo "<p>✓ Table '$table' exists with $count records</p>";
        } catch (PDOException $e) {
            echo "<p>✗ Table '$table' error: " . $e->getMessage() . "</p>";
        }
    }
    
    // Test admin user
    try {
        $stmt = $db->prepare("SELECT * FROM students WHERE is_admin = TRUE LIMIT 1");
        $stmt->execute();
        $admin = $stmt->fetch();
        if ($admin) {
            echo "<p>✓ Admin user found: " . htmlspecialchars($admin['email']) . "</p>";
        } else {
            echo "<p>✗ No admin user found</p>";
        }
    } catch (PDOException $e) {
        echo "<p>✗ Admin user check error: " . $e->getMessage() . "</p>";
    }
    
    // Test course operations
    try {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM courses");
        $stmt->execute();
        $count = $stmt->fetch()['count'];
        echo "<p>✓ Courses table accessible with $count courses</p>";
        
        if ($count > 0) {
            $stmt = $db->prepare("SELECT * FROM courses LIMIT 1");
            $stmt->execute();
            $course = $stmt->fetch();
            echo "<p>✓ Sample course: " . htmlspecialchars($course['name']) . "</p>";
        }
    } catch (PDOException $e) {
        echo "<p>✗ Course operations error: " . $e->getMessage() . "</p>";
    }
    
} catch (PDOException $e) {
    echo "<p><strong>✗ Database connection failed:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in config/config.php</p>";
}

echo "<hr>";
echo "<p><a href='admin/courses.php'>Go to Course Management</a></p>";
echo "<p><a href='admin/dashboard.php'>Go to Admin Dashboard</a></p>";
?>
