<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Courses - Learning Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        
                    </ol>
                </nav>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Browse Courses</h2>
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="row mb-4">
                    <div class="col-12">
                        <form method="GET" action="<?= base_url('course/browse') ?>" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search courses..." value="<?= esc($search ?? '') ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <?php if (!empty($search)): ?>
                                <a href="<?= base_url('course/browse') ?>" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Available Courses Section -->
                <div class="row mb-5">
                    <div class="col-12">
                        <h3>Available Courses</h3>
                        <?php if (!empty($available_courses)): ?>
                            <div class="row">
                                <?php foreach ($available_courses as $course): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                                <p class="card-text"><?= esc($course['description']) ?></p>
                                                <button class="btn btn-primary enroll-btn" data-course-id="<?= esc($course['id']) ?>">
                                                    <i class="fas fa-plus"></i> Enroll
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No courses available at the moment.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Enrolled Courses and Materials Section -->
                <?php if (!empty($enrolled_courses_data)): ?>
                    <div class="row">
                        <div class="col-12">
                            <h3>My Enrolled Courses & Materials</h3>
                            <?php $materialModel = new \App\Models\MaterialModel(); ?>
                            <?php foreach ($enrolled_courses_data as $course): ?>
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Course: <?= esc($course['name']) ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span>Course Materials</span>
                                            <a href="<?= base_url('course/' . $course['id']) ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> View Full Course
                                            </a>
                                        </div>

                                        <?php $materials = $materialModel->getMaterialsByCourse($course['id']); ?>
                                        <?php if (!empty($materials)): ?>
                                            <div class="row">
                                                <?php foreach ($materials as $material): ?>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h6 class="card-title">
                                                                    <i class="fas fa-file"></i> <?= esc($material['file_name']) ?>
                                                                </h6>
                                                                <p class="card-text small text-muted">
                                                                    Uploaded: <?= date('M d, Y', strtotime($material['created_at'])) ?>
                                                                </p>
                                                                <a href="<?= base_url('materials/download/' . $material['id']) ?>" class="btn btn-primary btn-sm" target="_blank">
                                                                    <i class="fas fa-download"></i> Download
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i> No materials have been uploaded for this course yet.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle enroll button clicks with AJAX
            $('.enroll-btn').click(function(e) {
                e.preventDefault();
                var button = $(this);
                var courseId = button.data('course-id');

                if (confirm('Are you sure you want to enroll in this course?')) {
                    $.post('<?= base_url('course/enroll') ?>', { course_id: courseId, csrf_test_name: '<?= csrf_token() ?>' })
                        .done(function(data) {
                            if (data.status === 'success') {
                                alert(data.message);
                                location.reload(); // Reload to update the page
                            } else {
                                alert(data.message);
                            }
                        })
                        .fail(function() {
                            alert('An error occurred. Please try again.');
                        });
                }
            });
        });
    </script>
</body>
</html>
