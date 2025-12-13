<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Enrollment - Learning Management System</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Request Enrollment</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                           
                            <div class="col-md-8">
                                <h5><?= esc($course['title']) ?></h5>
                                <p class="text-muted"><?= esc($course['description'] ?? 'No description available.') ?></p>
                                <p><strong>Teacher:</strong> <?= esc($course['teacher_name'] ?? 'N/A') ?></p>
                                <p><strong>School Year:</strong> <?= $currentYear ?>-<?= $currentYear + 1 ?></p>
                                <p><strong>Semester:</strong> <?= ucfirst($currentSemester) ?></p>
                            </div>
                        </div>

                        <hr>

                        <form id="enrollmentForm">
                            <?= csrf_field() ?>
                            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                            <input type="hidden" name="school_year" value="<?= $schoolYear ?>">
                            <input type="hidden" name="semester" value="<?= $currentSemester ?>">
                            <input type="hidden" name="schedule" value="To be scheduled by teacher">
                            
                            <div class="mb-3">
                                <label class="form-label">School Year</label>
                                <div class="form-control"><?= $schoolYear ?></div>
                                <small class="form-text text-muted">Current school year</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                <select id="semester" name="semester" class="form-select" required>
                                    <option value="1st" <?= $currentSemester === '1st' ? 'selected' : '' ?>>1st Semester</option>
                                    <option value="2nd" <?= $currentSemester === '2nd' ? 'selected' : '' ?>>2nd Semester</option>
                                    <option value="summer" <?= $currentSemester === 'summer' ? 'selected' : '' ?>>Summer</option>
                                </select>
                                <small class="form-text text-muted">Select the semester for enrollment</small>
                            </div>
                            

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="<?= base_url('course/browse') ?>" class="btn btn-secondary me-md-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Submit Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('enrollmentForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';

            try {
                const form = document.getElementById('enrollmentForm');
                const formData = new FormData(form);
                const formObject = {};
                
                // Convert FormData to object
                formData.forEach((value, key) => {
                    formObject[key] = value;
                });

                // Add CSRF token
                const csrfInput = document.querySelector('[name="csrf_test_name"]');
                if (csrfInput) {
                    formObject.csrf_test_name = csrfInput.value;
                }

                const response = await fetch('<?= site_url("enrollments/request") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfInput ? csrfInput.value : ''
                    },
                    body: JSON.stringify(formObject)
                });

                const result = await response.json();
                if (response.ok && result.status === 'success') {
                    // Show success message
                    const successToast = new bootstrap.Toast(document.getElementById('successToast'));
                    document.getElementById('toastMessage').textContent = 'Enrollment request submitted successfully!';
                    successToast.show();
                    
                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = '<?= site_url('enrollments/my') ?>';
                    }, 1500);
                } else {
                    throw new Error(result.message || 'Failed to submit enrollment request');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error: ' + (error.message || 'An error occurred while submitting your request.'));
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    </script>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="successToast" class="toast bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <div id="errorToast" class="toast bg-danger text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="errorMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>