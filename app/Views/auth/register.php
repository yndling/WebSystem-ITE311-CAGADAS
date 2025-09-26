<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Register</h3>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success">
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= site_url('/register') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('name')): ?>
                                    <div class="text-danger"><?= $validation->getError('name') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('email')): ?>
                                    <div class="text-danger"><?= $validation->getError('email') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <?php if (isset($validation) && $validation->hasError('password')): ?>
                                    <div class="text-danger"><?= $validation->getError('password') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                                <?php if (isset($validation) && $validation->hasError('password_confirm')): ?>
                                    <div class="text-danger"><?= $validation->getError('password_confirm') ?></div>
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
                                    <div class="text-danger"><?= $validation->getError('role') ?></div>
                                <?php endif; ?>
                            </div>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <button type="submit" class="btn btn-primary">Register</button>
                        </form>

<p class="mt-3">Already have an account? <a href="<?= site_url('/login') ?>">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
