<!DOCTYPE html>
<html lang="en">
<head>

    <title>Login</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Login</h3>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success">
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= site_url('/login') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('email')): ?>
                                    <div class="text-danger mt-1">
                                        <small><?= $validation->getError('email') ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <?php if (isset($validation) && $validation->hasError('password')): ?>
                                    <div class="text-danger mt-1">
                                        <small><?= $validation->getError('password') ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="student" <?= old('role') == 'student' ? 'selected' : '' ?>>Student</option>
                                    <option value="teacher" <?= old('role') == 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                    <option value="admin" <?= old('role') == 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <?php if (isset($validation) && $validation->hasError('role')): ?>
                                    <div class="text-danger mt-1">
                                        <small><?= $validation->getError('role') ?></small>
                                    </div>
                                <?php endif; ?>
                                <?php if (session()->getFlashdata('role_error')): ?>
                                    <div class="alert alert-danger mt-2 py-2">
                                        <small><?= session()->getFlashdata('role_error') ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>

<p class="mt-3">Don't have an account? <a href="<?= site_url('/register') ?>">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
