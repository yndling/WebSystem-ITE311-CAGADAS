<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Learning Management System - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= site_url('/') ?>">LMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="<?= site_url('/') ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/about') ?>">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/contact') ?>">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-5">
        <h1>Welcome to the Learning Management System</h1>
        <p class="lead">Manage your courses, lessons, quizzes, and submissions all in one place.</p>
        <div class="text-center my-4">
            <img src="https://via.placeholder.com/800x300?text=LMS+Dashboard" alt="LMS Dashboard" class="img-fluid rounded" />
        </div>
        <p>Use the navigation above to explore the system and learn more about its features.</p>
        <a href="<?= site_url('/about') ?>" class="btn btn-primary">Learn More About Us</a>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
