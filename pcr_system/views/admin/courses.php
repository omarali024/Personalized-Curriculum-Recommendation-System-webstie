<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-book me-2"></i>Course Management
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                    <i class="fas fa-plus me-1"></i>Add Course
                </button>
            </div>
        </div>
    </div>

    <?php if (isset($error) && $error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($success) && $success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Courses</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo htmlspecialchars($search ?? ''); ?>" placeholder="Search by name, keywords...">
                </div>
                
                <div class="col-md-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" 
                                    <?php echo (isset($categoryFilter) && $categoryFilter === $cat) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="difficulty" class="form-label">Difficulty</label>
                    <select class="form-select" id="difficulty" name="difficulty">
                        <option value="">All Levels</option>
                        <option value="Beginner" <?php echo (isset($difficultyFilter) && $difficultyFilter === 'Beginner') ? 'selected' : ''; ?>>Beginner</option>
                        <option value="Intermediate" <?php echo (isset($difficultyFilter) && $difficultyFilter === 'Intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                        <option value="Advanced" <?php echo (isset($difficultyFilter) && $difficultyFilter === 'Advanced') ? 'selected' : ''; ?>>Advanced</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($courses)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No courses found</h4>
                    <p class="text-muted">Add your first course to get started!</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                        <i class="fas fa-plus me-1"></i>Add Course
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Category</th>
                                <th>Difficulty</th>
                                <th>Credits</th>
                                <th>Keywords</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($course['name']); ?></strong>
                                        <?php if ($course['description']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars(substr($course['description'], 0, 80)) . '...'; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($course['category']); ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = [
                                            'Beginner' => 'bg-success',
                                            'Intermediate' => 'bg-warning',
                                            'Advanced' => 'bg-danger'
                                        ];
                                        $class = $badgeClass[$course['difficulty']] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?php echo $class; ?>"><?php echo htmlspecialchars($course['difficulty']); ?></span>
                                    </td>
                                    <td><?php echo $course['credits']; ?></td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars(substr($course['keywords'], 0, 50)) . '...'; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    onclick="editCourse(<?php echo htmlspecialchars(json_encode($course)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="?delete=<?php echo $course['id']; ?>" class="btn btn-outline-danger"
                                               onclick="return confirm('Are you sure you want to delete this course?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p class="text-muted">
                        Showing <?php echo count($courses); ?> course(s)
                        <?php if (isset($search) && ($search || $categoryFilter || $difficultyFilter)): ?>
                            <a href="courses.php" class="btn btn-sm btn-outline-secondary ms-2">
                                <i class="fas fa-times me-1"></i>Clear Filters
                            </a>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add/Edit Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="courseForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_course" id="formAction">
                    <input type="hidden" name="course_id" id="courseId">
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Course Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="credits" class="form-label">Credits</label>
                            <input type="number" class="form-control" id="credits" name="credits" value="3" min="1" max="6">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <input type="text" class="form-control" id="category" name="category" list="categories" required>
                            <datalist id="categories">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="difficulty" class="form-label">Difficulty *</label>
                            <select class="form-select" id="difficulty" name="difficulty" required>
                                <option value="">Select difficulty...</option>
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keywords" class="form-label">Keywords</label>
                        <input type="text" class="form-control" id="keywords" name="keywords" 
                               placeholder="programming, coding, algorithms, data structures...">
                        <div class="form-text">Separate keywords with commas</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCourse(course) {
    document.getElementById('modalTitle').textContent = 'Edit Course';
    document.getElementById('formAction').value = 'update_course';
    document.getElementById('courseId').value = course.id;
    document.getElementById('name').value = course.name || '';
    document.getElementById('category').value = course.category || '';
    document.getElementById('difficulty').value = course.difficulty || '';
    document.getElementById('keywords').value = course.keywords || '';
    document.getElementById('description').value = course.description || '';
    document.getElementById('credits').value = course.credits || 3;
    document.getElementById('submitBtn').textContent = 'Update Course';
    
    var modal = new bootstrap.Modal(document.getElementById('addCourseModal'));
    modal.show();
}

document.getElementById('addCourseModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('modalTitle').textContent = 'Add New Course';
    document.getElementById('formAction').value = 'add_course';
    document.getElementById('courseId').value = '';
    document.getElementById('courseForm').reset();
    document.getElementById('submitBtn').textContent = 'Add Course';
});

<?php if (isset($editCourse) && $editCourse): ?>
document.addEventListener('DOMContentLoaded', function() {
    editCourse(<?php echo json_encode($editCourse); ?>);
});
<?php endif; ?>
</script>

