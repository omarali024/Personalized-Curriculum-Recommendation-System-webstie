<?php
/**
 * Database Viewer
 * Simple interface to view database contents
 */
require_once 'config/config.php';

$pageTitle = 'Database Viewer';
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-database me-2"></i>Database Contents
            </h2>
            
            <!-- Students Table -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-users me-2"></i>Students Table</h5>
                </div>
                <div class="card-body">
                    <?php
                    try {
                        $stmt = $db->query("SELECT id, name, email, interests, is_admin, created_at FROM students");
                        $students = $stmt->fetchAll();
                        ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Interests</th>
                                        <th>Admin</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo $student['id']; ?></td>
                                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo htmlspecialchars($student['interests']); ?></td>
                                        <td>
                                            <?php if ($student['is_admin']): ?>
                                                <span class="badge bg-danger">Admin</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Student</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $student['created_at']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                    ?>
                </div>
            </div>

            <!-- Courses Table -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-book me-2"></i>Courses Table</h5>
                </div>
                <div class="card-body">
                    <?php
                    try {
                        $stmt = $db->query("SELECT id, name, category, difficulty, credits FROM courses ORDER BY category, name");
                        $courses = $stmt->fetchAll();
                        ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Course Name</th>
                                        <th>Category</th>
                                        <th>Difficulty</th>
                                        <th>Credits</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?php echo $course['id']; ?></td>
                                        <td><?php echo htmlspecialchars($course['name']); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($course['category']); ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = '';
                                            switch($course['difficulty']) {
                                                case 'Beginner': $badgeClass = 'bg-success'; break;
                                                case 'Intermediate': $badgeClass = 'bg-warning'; break;
                                                case 'Advanced': $badgeClass = 'bg-danger'; break;
                                            }
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>"><?php echo $course['difficulty']; ?></span>
                                        </td>
                                        <td><?php echo $course['credits']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                    ?>
                </div>
            </div>

            <!-- Completed Courses -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-check-circle me-2"></i>Completed Courses</h5>
                </div>
                <div class="card-body">
                    <?php
                    try {
                        $stmt = $db->query("
                            SELECT cc.id, s.name as student_name, c.name as course_name, cc.grade, cc.completed_at 
                            FROM completed_courses cc 
                            JOIN students s ON cc.student_id = s.id 
                            JOIN courses c ON cc.course_id = c.id 
                            ORDER BY cc.completed_at DESC
                        ");
                        $completed = $stmt->fetchAll();
                        ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Grade</th>
                                        <th>Completed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($completed as $comp): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($comp['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($comp['course_name']); ?></td>
                                        <td>
                                            <span class="badge bg-success"><?php echo $comp['grade']; ?></span>
                                        </td>
                                        <td><?php echo $comp['completed_at']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                    ?>
                </div>
            </div>

            <!-- Database Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-bar me-2"></i>Database Statistics</h5>
                </div>
                <div class="card-body">
                    <?php
                    try {
                        $stats = [];
                        
                        // Get counts
                        $stmt = $db->query("SELECT COUNT(*) as count FROM students");
                        $stats['students'] = $stmt->fetch()['count'];
                        
                        $stmt = $db->query("SELECT COUNT(*) as count FROM courses");
                        $stats['courses'] = $stmt->fetch()['count'];
                        
                        $stmt = $db->query("SELECT COUNT(*) as count FROM completed_courses");
                        $stats['completed'] = $stmt->fetch()['count'];
                        
                        $stmt = $db->query("SELECT COUNT(*) as count FROM recommendations");
                        $stats['recommendations'] = $stmt->fetch()['count'];
                        
                        $stmt = $db->query("SELECT COUNT(*) as count FROM course_ratings");
                        $stats['ratings'] = $stmt->fetch()['count'];
                        ?>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="text-center">
                                    <h3 class="text-primary"><?php echo $stats['students']; ?></h3>
                                    <p class="text-muted">Students</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <h3 class="text-success"><?php echo $stats['courses']; ?></h3>
                                    <p class="text-muted">Courses</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <h3 class="text-warning"><?php echo $stats['completed']; ?></h3>
                                    <p class="text-muted">Completed</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <h3 class="text-info"><?php echo $stats['recommendations']; ?></h3>
                                    <p class="text-muted">Recommendations</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <h3 class="text-danger"><?php echo $stats['ratings']; ?></h3>
                                    <p class="text-muted">Ratings</p>
                                </div>
                            </div>
                        </div>
                        <?php
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

