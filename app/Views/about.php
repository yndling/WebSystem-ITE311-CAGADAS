<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Learning Management System - About</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">LMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="<?= site_url('/about') ?>">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/contact') ?>">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/register') ?>">Register</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/login') ?>">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-5">
        <h1>About Our Learning Management System</h1>
        <p class="lead">Our LMS is designed to provide a seamless learning experience for students and educators.</p>
        <div class="row">
            <div class="col-md-6">
                <h3>Features</h3>
                <ul>
                    <li>Course Management</li>
                    <li>Lesson Creation</li>
                    <li>Quiz and Assessment Tools</li>
                    <li>Submission Tracking</li>
                    <li>User Enrollment</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h3>Benefits</h3>
                <ul>
                    <li>Easy to Use Interface</li>
                    <li>Scalable for Any Institution</li>
                    <li>Secure and Reliable</li>
                    <li>24/7 Access</li>
                </ul>
            </div>
        </div>
        <div class="text-center my-4">
            <img src="https://via.placeholder.com/600x300?text=About+LMS" alt="About LMS" class="img-fluid rounded" />
        </div>
        <a href="/contact" class="btn btn-primary">Get in Touch</a>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
