<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Enrollments - Learning Management System</title>
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
            margin-bottom: 20px;
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
                            <a class="nav-link" href="<?= base_url('dashboard') ?>"><i class="fas fa-tachometer-alt"></i> Overview</a>
                        </li>
                        <?php if (session()->get('role') === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#manageUsersModal"><i class="fas fa-users"></i> Manage Users</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('enrollments/manage') ?>"><i class="fas fa-check-circle"></i> Enrollment Requests</a>
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
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('enrollments/manage') ?>"><i class="fas fa-tasks"></i> Enrollment Requests</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= base_url('enrollments/my') ?>"><i class="fas fa-book-open"></i> My Enrollments</a>
                        </li>
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
                    <h1 class="h2">My Enrollments</h1>
                </div>

                <div class="card">
                    <div class="card-body">
                        <?php if (empty($enrollments)): ?>
                            <div class="alert alert-info">
                                You have no enrollments yet.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Course</th>
                                            <th>Course Description</th>
                                            <th>Teacher</th>
                                            <th>Status</th>
                                            <th>School Year</th>
                                            <th>Semester</th>
                                            <th>Schedule</th>
                                            <th>Enrolled On</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Filter out rejected enrollments
                                        $filteredEnrollments = array_filter($enrollments, function($enrollment) {
                                            return strtolower($enrollment['status'] ?? '') !== 'rejected';
                                        });
                                        
                                        foreach ($filteredEnrollments as $enrollment): ?>
                                            <tr>
                                                <td><?= esc($enrollment['course_title'] ?? 'N/A') ?></td>
                                                <td><?= esc($enrollment['course_description'] ?? 'No description available') ?></td>
                                                <td><?= esc($enrollment['teacher_name'] ?? 'N/A') ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $enrollment['status'] === 'approved' ? 'success' : ($enrollment['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                        <?= ucfirst((string)($enrollment['status'] ?? 'unknown')) ?>
                                                        <?php if ($enrollment['status'] === 'pending'): ?>
                                                            <i class="fas fa-clock ms-1"></i>
                                                        <?php endif; ?>
                                                    </span>
                                                </td>
                                                <td><?= esc($enrollment['school_year'] ?? 'N/A') ?></td>
                                                <td><?= ucfirst(esc($enrollment['semester'] ?? 'N/A')) ?></td>
                                                <td>
                                                    <?php if ($enrollment['status'] === 'approved' && !empty($enrollment['schedule'])): ?>
                                                        <?= esc($enrollment['schedule']) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">To be scheduled</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $enrollment['enrollment_date'] ? date('M j, Y', strtotime($enrollment['enrollment_date'])) : 'N/A' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                    <nav aria-label="Enrollment pagination">
                        <?= $pager->links('default', 'default_full') ?>
                    </nav>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Set base URL for JS -->
    <script>
        window.baseUrl = '<?= base_url() ?>';
    </script>
</body>
</html>