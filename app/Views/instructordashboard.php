<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard - Learning Management System</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/font-awesome/6.0.0/css/all.min.css">
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
                    <h5 class="px-3">Instructor Portal</h5>
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#createCourseModal"><i class="fas fa-plus-circle"></i> Create Course</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#viewCoursesModal"><i class="fas fa-book"></i> My Courses</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#manageStudentsModal"><i class="fas fa-users"></i> Manage Students</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#courseAnalyticsModal"><i class="fas fa-chart-bar"></i> Analytics</a>
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
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                                <i class="fas fa-plus"></i> New Course
                            </button>
                        </div>
                    </div>
                </div>

<?php
// Check if user is logged in and has instructor role
if (!session()->get('logged_in') || session()->get('role') !== 'instructor') {
    return redirect()->to('/login')->with('error', 'Access denied. You must be an instructor to access this page.');
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

                <!-- Instructor Info Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Instructor Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <?= session()->get('email') ?></p>
                                <p><strong>Department:</strong> Computer Science</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Instructor ID:</strong> <?= session()->get('user_id') ?></p>
                                <p><strong>Experience:</strong> 5+ years</p>
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
                                <h5 class="card-title">My Courses</h5>
                                <p class="card-text">5</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <i class="fas fa-users text-success"></i>
                                <h5 class="card-title">Total Students</h5>
                                <p class="card-text">127</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <i class="fas fa-star text-warning"></i>
                                <h5 class="card-title">Avg Rating</h5>
                                <p class="card-text">4.8</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <i class="fas fa-chart-line text-info"></i>
                                <h5 class="card-title">Completion Rate</h5>
                                <p class="card-text">94%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Courses -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Courses</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center course-card">
                                        <div>
                                            <h6 class="mb-1">Web Development Fundamentals</h6>
                                            <p class="mb-1 text-muted">45 students enrolled</p>
                                        </div>
                                        <span class="badge bg-primary">Active</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center course-card">
                                        <div>
                                            <h6 class="mb-1">Database Design</h6>
                                            <p class="mb-1 text-muted">32 students enrolled</p>
                                        </div>
                                        <span class="badge bg-success">Active</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center course-card">
                                        <div>
                                            <h6 class="mb-1">Software Engineering</h6>
                                            <p class="mb-1 text-muted">28 students enrolled</p>
                                        </div>
                                        <span class="badge bg-warning">Draft</span>
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
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                                        <i class="fas fa-plus-circle"></i> Create New Course
                                    </button>
                                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewCoursesModal">
