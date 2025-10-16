<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course: <?= esc($course['title']) ?> - Learning Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">Course: <?= esc($course['title']) ?></li>
                    </ol>
                </nav>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?= esc($course['title']) ?></h2>
                    <?php
                    $backUrl = (session()->get('role') === 'teacher' || session()->get('role') === 'admin') ? base_url('course/my') : base_url('dashboard');
                    ?>
                    <a href="<?= $backUrl ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to <?= (session()->get('role') === 'teacher' || session()->get('role') === 'admin') ? 'My Courses' : 'Dashboard' ?>
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title mb-0"><?= esc($course['title']) ?></h2>
                    </div>
                    <div class="card-body">
                        <?php if (session()->has('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->has('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= session('success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <p class="card-text"><?= esc($course['description']) ?></p>

                        <?php if (session()->get('role') === 'teacher' || session()->get('role') === 'admin'): ?>
                            <div class="mb-4">
                                <h5>Upload New Material</h5>
                                <form action="<?= base_url('material/upload/' . $course_id) ?>" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                    <input type="hidden" name="course_id" value="<?= $course_id ?>" />
                                    <div class="row">
                                        <div class="col-md-8">
                                            <input type="file" class="form-control" id="material_file" name="material_file" required>
                                            <div class="form-text">Allowed file types: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG, MP4, AVI. Max size: 10MB.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-upload"></i> Upload
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        <h4>Course Materials</h4>
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
                                                <div class="d-flex gap-2">
                                                    <a href="<?= base_url('materials/download/' . $material['id']) ?>" class="btn btn-primary btn-sm" target="_blank">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                    <?php if (session()->get('role') === 'teacher' || session()->get('role') === 'admin'): ?>
                                                        <a href="<?= base_url('materials/delete/' . $material['id']) ?>" class="btn btn-danger btn-sm"
                                                           onclick="return confirm('Are you sure you want to delete this material?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
