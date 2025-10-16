<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Learning Management System</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            height: 100vh;
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }
        .sidebar .nav-link {
            color: #495057;
            padding: 10px 15px;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
            color: #007bff;
        }
        .sidebar .nav-link.active {
            background-color: #007bff;
            color: white;
        }
        .main-content {
            padding: 20px;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        .stats-card {
            text-align: center;
        }
        .stats-card i {
            font-size: 2rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <h5 class="px-3">Dashboard</h5>
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#"><i class="fas fa-tachometer-alt"></i> Overview</a>
                        </li>
                        <?php if (session()->get('role') === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#manageUsersModal"><i class="fas fa-users"></i> Manage Users</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#systemSettingsModal"><i class="fas fa-cogs"></i> System Settings</a>
                            </li>
                        <?php elseif (session()->get('role') === 'teacher'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('course/create') ?>"><i class="fas fa-plus-circle"></i> Create Course</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('course/my') ?>"><i class="fas fa-book"></i> View My Courses</a>
                            </li>
                        <?php elseif (session()->get('role') === 'student'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('course/browse') ?>"><i class="fas fa-search"></i> Browse Courses</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#myEnrollmentsModal"><i class="fas fa-list"></i> My Enrollments</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-user"></i> Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Welcome, <?= isset($name) ? $name : 'User' ?>!</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#notificationsModal">
                                <i class="fas fa-bell"></i> Notifications
                            </button>
                        </div>
                    </div>
                </div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

                <!-- User Info Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">User Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <?= isset($email) ? $email : '' ?></p>
                                <p><strong>Role:</strong> <?= isset($role) ? ucfirst($role) : '' ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>User ID:</strong> <?= isset($user_id) ? $user_id : '' ?></p>
                                <p><strong>Last Login:</strong> <?= date('Y-m-d H:i:s') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role-specific Content -->
                <?php if (isset($role) && $role === 'admin'): ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <i class="fas fa-users text-primary"></i>
                                    <h5 class="card-title">Total Users</h5>
                                    <p class="card-text"><?= isset($total_users) ? $total_users : 0 ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <i class="fas fa-chalkboard-teacher text-success"></i>
                                    <h5 class="card-title">Teachers</h5>
                                    <p class="card-text"><?= isset($teacher_count) ? $teacher_count : 0 ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <i class="fas fa-user-graduate text-warning"></i>
                                    <h5 class="card-title">Students</h5>
                                    <p class="card-text"><?= isset($student_count) ? $student_count : 0 ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif (isset($role) && $role === 'teacher'): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">My Courses</h5>
                                    <p class="card-text">You have <?= isset($my_courses) ? $my_courses : 0 ?> active courses.</p>
                                    <a href="<?= base_url('course/my') ?>" class="btn btn-primary">View Courses</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Students</h5>
                                    <p class="card-text">You have <?= isset($total_students) ? $total_students : 0 ?> students enrolled in your courses.</p>
                                    <a href="#" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createCourseModal">Create New Course</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Materials Management for Teachers -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Course Materials Management</h5>
                                </div>
                                <div class="card-body">
                                    <p>Upload materials for your courses or manage existing materials.</p>
                                    <a href="<?= base_url('admin/course/1/upload') ?>" class="btn btn-success">Upload Material</a>
                                    <a href="#" class="btn btn-info ms-2" data-bs-toggle="modal" data-bs-target="#viewMaterialsModal">View All Materials</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif (isset($role) && $role === 'student'): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Enrolled Courses</h5>
                                    <ul id="enrolled-courses-list" class="list-group">
                                        <?php if (!empty($enrolled_courses)): ?>
                                            <?php foreach ($enrolled_courses as $course): ?>
                                                <li class="list-group-item">
                                                    <strong><?= esc($course['course_title']) ?></strong><br>
                                                    <small><?= esc($course['course_description']) ?></small>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li class="list-group-item text-muted">No courses enrolled yet.</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Available Courses</h5>
                                    <?php if (!empty($available_courses)): ?>
                                        <ul class="list-group">
                                            <?php foreach ($available_courses as $course): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong><?= esc($course['title']) ?></strong><br>
                                                        <small><?= esc($course['description']) ?></small>
                                                    </div>
                                                    <button class="btn btn-sm btn-primary enroll-btn" data-course-id="<?= esc($course['id']) ?>">Enroll</button>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p>No courses available at the moment.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>


                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Modals for interactivity -->
    <!-- Manage Users Modal (Admin) -->
    <div class="modal fade" id="manageUsersModal" tabindex="-1" aria-labelledby="manageUsersModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageUsersModalLabel">Manage Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Here you can manage all users in the system.</p>
                    <!-- Add user management form or table here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Other modals can be added similarly -->

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Hide alert after 3 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 3000);

            // Handle enroll button clicks with AJAX
            $('.enroll-btn').click(function(e) {
                e.preventDefault();
                var button = $(this);
                var courseId = button.data('course-id');

                if (confirm('Are you sure you want to enroll in this course?')) {
                    $.post('<?= base_url('course/enroll') ?>', { course_id: courseId, csrf_test_name: '<?= csrf_token() ?>' })
                        .done(function(data) {
                            if (data.status === 'success') {
                                // Show success alert
                                var alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                    data.message +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                    '</div>';
                                $('.main-content').prepend(alertHtml);

                                // Disable the enroll button
                                button.prop('disabled', true).text('Enrolled');

                                // Add the course to the enrolled courses list
                                var courseItem = button.closest('li').clone();
                                courseItem.find('.enroll-btn').remove();
                                var enrolledList = $('#enrolled-courses-list');
                                var placeholder = enrolledList.find('li.text-muted');
                                if (placeholder.length) {
                                    placeholder.remove();
                                }
                                enrolledList.append(courseItem);

                                // Remove the course from available courses list
                                button.closest('li').remove();
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
