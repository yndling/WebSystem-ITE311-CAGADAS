<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Learning Management System</title>
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
        .course-card {
            transition: transform 0.2s;
        }
        .course-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <h5 class="px-3">Student Portal</h5>
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#browseCoursesModal"><i class="fas fa-search"></i> Browse Courses</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#myEnrollmentsModal"><i class="fas fa-list"></i> My Enrollments</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#gradesModal"><i class="fas fa-graduation-cap"></i> Grades</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#assignmentsModal"><i class="fas fa-tasks"></i> Assignments</a>
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
                    <h1 class="h2">Welcome, <?= session()->get('name') ?>!</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#notificationsModal">
                                <i class="fas fa-bell"></i> Notifications
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#browseCoursesModal">
                                <i class="fas fa-plus"></i> Browse Courses
                            </button>
                        </div>
                    </div>
                </div>

<?php
// Check if user is logged in and has student role
if (!session()->get('logged_in') || session()->get('role') !== 'student') {
    return redirect()->to('/login')->with('error', 'Access denied. You must be a student to access this page.');
}
?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

                <!-- Student Info Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Student Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <?= session()->get('email') ?></p>
                                <p><strong>Student ID:</strong> <?= session()->get('user_id') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Major:</strong> Computer Science</p>
                                <p><strong>Year:</strong> 3rd Year</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <i class="fas fa-book text-primary"></i>
                                <h5 class="card-title">Enrolled Courses</h5>
                                <p class="card-text">5</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <i class="fas fa-check-circle text-success"></i>
                                <h5 class="card-title">Completed Courses</h5>
                                <p class="card-text">12</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <i class="fas fa-star text-warning"></i>
                                <h5 class="card-title">GPA</h5>
                                <p class="card-text">3.8</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <i class="fas fa-tasks text-info"></i>
                                <h5 class="card-title">Pending Assignments</h5>
                                <p class="card-text">3</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Enrollments and Quick Actions -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Enrollments</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center course-card">
                                        <div>
                                            <h6 class="mb-1">Web Development Fundamentals</h6>
                                            <p class="mb-1 text-muted">Enrolled 2 days ago</p>
                                        </div>
                                        <span class="badge bg-primary">Active</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center course-card">
                                        <div>
                                            <h6 class="mb-1">Database Design</h6>
                                            <p class="mb-1 text-muted">Enrolled 1 week ago</p>
                                        </div>
                                        <span class="badge bg-success">Active</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center course-card">
                                        <div>
                                            <h6 class="mb-1">Software Engineering</h6>
                                            <p class="mb-1 text-muted">Enrolled 2 weeks ago</p>
                                        </div>
                                        <span class="badge bg-warning">In Progress</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#browseCoursesModal">
                                        <i class="fas fa-search"></i> Browse New Courses
                                    </button>
                                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#myEnrollmentsModal">
                                        <i class="fas fa-list"></i> View My Enrollments
                                    </button>
                                    <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#gradesModal">
                                        <i class="fas fa-graduation-cap"></i> Check Grades
                                    </button>
                                    <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#assignmentsModal">
                                        <i class="fas fa-tasks"></i> View Assignments
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modals for interactivity -->
    <!-- Browse Courses Modal -->
    <div class="modal fade" id="browseCoursesModal" tabindex="-1" aria-labelledby="browseCoursesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="browseCoursesModalLabel">Browse Courses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Here you can browse and enroll in available courses.</p>
                    <!-- Add course browsing functionality here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- My Enrollments Modal -->
    <div class="modal fade" id="myEnrollmentsModal" tabindex="-1" aria-labelledby="myEnrollmentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myEnrollmentsModalLabel">My Enrollments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>View and manage your enrolled courses.</p>
                    <!-- Add enrollment management here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grades Modal -->
    <div class="modal fade" id="gradesModal" tabindex="-1" aria-labelledby="gradesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gradesModalLabel">My Grades</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>View your grades for completed courses.</p>
                    <!-- Add grades display here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments Modal -->
    <div class="modal fade" id="assignmentsModal" tabindex="-1" aria-labelledby="assignmentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignmentsModalLabel">Assignments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>View and submit your assignments.</p>
                    <!-- Add assignments functionality here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Manage your profile information.</p>
                    <!-- Add profile form here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Modal -->
    <div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationsModalLabel">Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>No new notifications.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Example: Add click handler for notifications
            const notificationsBtn = document.querySelector('[data-bs-target="#notificationsModal"]');
            if (notificationsBtn) {
                notificationsBtn.addEventListener('click', function() {
                    alert('No new notifications.');
                });
            }
        });
    </script>
</body>
</html>
