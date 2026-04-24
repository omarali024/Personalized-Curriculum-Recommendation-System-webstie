<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block sidebar">
            <div class="position-sticky pt-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-user-circle fa-4x text-primary mb-3"></i>
                        <h5 class="card-title"><?php echo htmlspecialchars($studentName); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($studentEmail); ?></p>
                        <a href="profile.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit Profile
                        </a>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h6 class="text-muted mb-3">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                            <i class="fas fa-plus me-1"></i>Add Completed Course
                        </button>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="action" value="generate_recommendations">
                            <button type="submit" class="btn btn-success btn-sm w-100">
                                <i class="fas fa-magic me-1"></i>Generate Recommendations
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <span class="badge bg-primary"><?php echo count($completedCourses); ?> Completed</span>
                        <span class="badge bg-success"><?php echo count($recommendations); ?> Recommendations</span>
                    </div>
                </div>
            </div>

            <?php if (isset($message) && $message): ?>
                <div class="alert alert-info alert-dismissible fade show">
                    <i class="fas fa-info-circle me-2"></i><?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Completed Courses</h6>
                                    <h3><?php echo count($completedCourses); ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Recommendations</h6>
                                    <h3><?php echo count($recommendations); ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-star fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Available Courses</h6>
                                    <h3><?php echo $courseStats['total_courses'] ?? 0; ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-book fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Interests</h6>
                                    <h3><?php echo !empty($studentInterests) ? count(explode(',', $studentInterests)) : 0; ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-heart fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Recommendations -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-star text-warning me-2"></i>Course Recommendations
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recommendations)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-magic fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No recommendations yet. Generate some based on your interests!</p>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="generate_recommendations">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-magic me-1"></i>Generate Recommendations
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($recommendations as $rec): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 border-start border-4 border-success">
                                                <div class="card-body">
                                                    <h6 class="card-title"><?php echo htmlspecialchars($rec['course_name']); ?></h6>
                                                    <p class="card-text text-muted small">
                                                        <span class="badge bg-secondary me-1"><?php echo htmlspecialchars($rec['category']); ?></span>
                                                        <span class="badge bg-info"><?php echo htmlspecialchars($rec['difficulty']); ?></span>
                                                    </p>
                                                    <p class="card-text small"><?php echo htmlspecialchars($rec['reason']); ?></p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-success">
                                                            <i class="fas fa-chart-line me-1"></i>
                                                            <?php echo number_format($rec['confidence_score'] * 100, 0); ?>% match
                                                        </small>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="action" value="add_completed_course">
                                                            <input type="hidden" name="course_id" value="<?php echo $rec['course_id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                                <i class="fas fa-plus me-1"></i>Add to Completed
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Completed Courses -->
                <div class="col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-check-circle text-success me-2"></i>Completed Courses
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($completedCourses)): ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-book-open fa-2x text-muted mb-2"></i>
                                    <p class="text-muted small">No completed courses yet</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($completedCourses as $course): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                        <div>
                                            <h6 class="mb-0 small"><?php echo htmlspecialchars($course['name']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo $course['grade'] ? 'Grade: ' . htmlspecialchars($course['grade']) : 'Completed'; ?>
                                            </small>
                                        </div>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="remove_completed_course">
                                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Remove this course from completed list?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Completed Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_completed_course">
                    <div class="mb-3">
                        <label for="course_id" class="form-label">Course</label>
                        <select class="form-select" id="course_id" name="course_id" required>
                            <option value="">Select a course...</option>
                            <?php foreach ($allCourses as $course): ?>
                                <option value="<?php echo $course['id']; ?>">
                                    <?php echo htmlspecialchars($course['name']); ?> 
                                    (<?php echo htmlspecialchars($course['category']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="grade" class="form-label">Grade (Optional)</label>
                        <input type="text" class="form-control" id="grade" name="grade" 
                               placeholder="e.g., A, B+, 85">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.sidebar {
    background-color: #f8f9fa;
    min-height: calc(100vh - 76px);
}
</style>

