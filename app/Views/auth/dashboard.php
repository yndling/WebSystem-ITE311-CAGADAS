<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Dashboard</h3>
<a href="<?= base_url('logout') ?>" class="btn btn-danger float-end">Logout</a>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success">
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <h4>Welcome, <?= session()->get('name') ?>!</h4>
                        <p>Email: <?= session()->get('email') ?></p>
                        <p>Role: <?= session()->get('role') ?></p>

                        <p>This is a protected page. Only logged-in users can see this.</p>

                        <?php if (session()->get('role') === 'admin'): ?>
                            <div class="alert alert-info">
                                <h5>Admin Panel</h5>
                                <p>As an admin, you have full access to manage users, courses, and system settings.</p>
                                <a href="#" class="btn btn-primary">Manage Users</a>
                                <a href="#" class="btn btn-secondary">System Settings</a>
                            </div>
                        <?php elseif (session()->get('role') === 'instructor'): ?>
                            <div class="alert alert-success">
                                <h5>Instructor Panel</h5>
                                <p>As an instructor, you can create and manage courses and lessons.</p>
                                <a href="#" class="btn btn-primary">Create Course</a>
                                <a href="#" class="btn btn-secondary">View My Courses</a>
                            </div>
                        <?php elseif (session()->get('role') === 'student'): ?>
                            <div class="alert alert-warning">
                                <h5>Student Panel</h5>
                                <p>As a student, you can enroll in courses and take quizzes.</p>
                                <a href="#" class="btn btn-primary">Browse Courses</a>
                                <a href="#" class="btn btn-secondary">My Enrollments</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                // Reload the page if it was loaded from cache (back/forward navigation)
                window.location.href = "<?= base_url('login') ?>";
            }
        });
    </script>
</body>
</html>
