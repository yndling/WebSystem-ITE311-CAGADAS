<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('courses') ?>">Courses</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('course/view/' . $course['id']) ?>"><?= esc($course['title']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Request Enrollment</li>
        </ol>
    </nav>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="h4 mb-0">Request Course Enrollment</h2>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="text-primary"><?= esc($course['title']) ?></h5>
                        <?php if (!empty($course['code'])): ?>
                            <p class="text-muted mb-1">Course Code: <?= esc($course['code']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($course['description'])): ?>
                            <p class="mb-0"><?= esc($course['description']) ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if (session()->has('errors')) : ?>
                        <div class="alert alert-danger">
                            <?= session('errors') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->has('success')) : ?>
                        <div class="alert alert-success">
                            <?= session('success') ?>
                        </div>
                    <?php endif; ?>

                    <?= form_open('', ['id' => 'enrollmentForm', 'class' => 'needs-validation', 'novalidate' => '']) ?>
                        <?= csrf_field() ?>
                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                        
                        <div class="mb-3">
                            <label for="school_year" class="form-label">School Year <span class="text-danger">*</span></label>
                            <select class="form-select" id="school_year" name="school_year" required>
                                <option value="">Select School Year</option>
                                <?php 
                                $currentYear = $currentYear ?? date('Y');
                                for ($i = -1; $i <= 1; $i++): 
                                    $year = $currentYear + $i;
                                    $nextYear = $year + 1;
                                    $schoolYear = "$year-$nextYear";
                                    $selected = ($i === 0) ? 'selected' : '';
                                ?>
                                    <option value="<?= $schoolYear ?>" <?= $selected ?>><?= $schoolYear ?></option>
                                <?php endfor; ?>
                            </select>
                            <div class="invalid-feedback">
                                Please select a school year.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                            <select class="form-select" id="semester" name="semester" required>
                                <option value="">Select Semester</option>
                                <option value="1st" <?= ($currentSemester ?? '') === '1st' ? 'selected' : '' ?>>1st Semester (August - December)</option>
                                <option value="2nd" <?= ($currentSemester ?? '') === '2nd' ? 'selected' : '' ?>>2nd Semester (January - April)</option>
                                <option value="summer" <?= ($currentSemester ?? '') === 'summer' ? 'selected' : '' ?>>Summer (May - July)</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a semester.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="schedule" class="form-label">Preferred Schedule <span class="text-danger">*</span></label>
                            <select class="form-select" id="schedule" name="schedule" required>
                                <option value="">Select a schedule</option>
                                <option value="MWF 07:00-08:30">MWF 07:00-08:30</option>
                                <option value="MWF 08:30-10:00">MWF 08:30-10:00</option>
                                <option value="MWF 09:00-10:30">MWF 09:00-10:30</option>
                                <option value="MWF 10:00-11:30">MWF 10:00-11:30</option>
                                <option value="MWF 11:30-13:00">MWF 11:30-13:00</option>
                                <option value="MWF 13:00-14:30">MWF 13:00-14:30</option>
                                <option value="MWF 14:30-16:00">MWF 14:30-16:00</option>
                                <option value="MWF 16:00-17:30">MWF 16:00-17:30</option>
                                <option value="TTH 07:00-08:30">TTH 07:00-08:30</option>
                                <option value="TTH 08:30-10:00">TTH 08:30-10:00</option>
                                <option value="TTH 09:00-10:30">TTH 09:00-10:30</option>
                                <option value="TTH 10:00-11:30">TTH 10:00-11:30</option>
                                <option value="TTH 11:30-13:00">TTH 11:30-13:00</option>
                                <option value="TTH 13:00-14:30">TTH 13:00-14:30</option>
                                <option value="TTH 14:30-16:00">TTH 14:30-16:00</option>
                                <option value="TTH 16:00-17:30">TTH 16:00-17:30</option>
                                <option value="SAT 08:00-12:00">SAT 08:00-12:00</option>
                                <option value="SAT 13:00-17:00">SAT 13:00-17:00</option>
                            </select>
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> Select your preferred class schedule
                            </div>
                            <div class="invalid-feedback">
                                Please select a schedule.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                     placeholder="Any additional information you'd like to include..."></textarea>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="<?= site_url('course/view/' . $course['id']) ?>" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-1"></i> Back to Course
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Submit Request
                            </button>
                        </div>
                    <?= form_close() ?>
                </div>
                <div class="card-footer text-muted">
                    <small>
                        <i class="fas fa-info-circle"></i> Your request will be reviewed by the course instructor. 
                        You'll be notified once it's approved or if additional information is needed.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enable form validation
    (function () {
        'use strict'
        
        // Fetch the form we want to apply custom Bootstrap validation styles to
        const form = document.getElementById('enrollmentForm');
        
        // Add submit event listener
        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            // Check form validity
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            
            // Get form data
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            try {
                // Disable button and show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
                
                // Submit form via AJAX
                const response = await fetch('<?= site_url('api/enrollment/request') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    },
                    body: JSON.stringify(Object.fromEntries(formData.entries()))
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    // Show success message and redirect
                    const successMessage = document.createElement('div');
                    successMessage.className = 'alert alert-success';
                    successMessage.innerHTML = `
                        <i class="fas fa-check-circle me-2"></i>
                        Enrollment request submitted successfully! Redirecting...
                    `;
                    
                    const cardBody = form.closest('.card-body');
                    cardBody.insertBefore(successMessage, cardBody.firstChild);
                    
                    // Scroll to top and redirect after delay
                    window.scrollTo(0, 0);
                    setTimeout(() => {
                        window.location.href = '<?= site_url('enrollments/my') ?>';
                    }, 1500);
                } else {
                    throw new Error(result.message || 'An error occurred while submitting your request.');
                }
            } catch (error) {
                console.error('Error:', error);
                
                // Show error message
                const errorAlert = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${error.message || 'An error occurred. Please try again.'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                
                const cardBody = form.closest('.card-body');
                cardBody.insertAdjacentHTML('afterbegin', errorAlert);
                window.scrollTo(0, 0);
            } finally {
                // Re-enable button and restore original text
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }, false);
    })();
    
    // Add schedule examples on click
    const scheduleInput = document.getElementById('schedule');
    const scheduleExamples = [
        'MWF 9:00-10:30',
        'TTH 1:00-2:30',
        'MW 2:00-3:30',
        'F 9:00-12:00',
        'MWF 10:00-11:30'
    ];
    
    scheduleInput.addEventListener('click', function() {
        if (!this.value) {
            this.setAttribute('placeholder', 'e.g., ' + scheduleExamples[Math.floor(Math.random() * scheduleExamples.length)]);
        }
    });
});
</script>

<style>
/* Custom styles for the enrollment form */
.needs-validation .form-control:invalid,
.needs-validation .form-select:invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.needs-validation .form-control:focus:invalid,
.needs-validation .form-select:focus:invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

.was-validated .form-control:invalid,
.was-validated .form-select:invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.was-validated .form-control:focus:invalid,
.was-validated .form-select:focus:invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}
</style>

<?= $this->endSection() ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enable form validation
    (function () {
        'use strict'
        
        // Fetch the form we want to apply custom Bootstrap validation styles to
        const form = document.getElementById('enrollmentForm');
        
        // Add submit event listener
        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            // Check form validity
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            
            // Get form data
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            try {
                // Disable button and show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
                
                // Submit form via AJAX
                const response = await fetch('<?= site_url('api/enrollment/request') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    },
                    body: JSON.stringify(Object.fromEntries(formData.entries()))
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    // Show success message and redirect
                    const successMessage = document.createElement('div');
                    successMessage.className = 'alert alert-success';
                    successMessage.innerHTML = `
                        <i class="fas fa-check-circle me-2"></i>
                        Enrollment request submitted successfully! Redirecting...
                    `;
                    
                    const cardBody = form.closest('.card-body');
                    cardBody.insertBefore(successMessage, cardBody.firstChild);
                    
                    // Scroll to top and redirect after delay
                    window.scrollTo(0, 0);
                    setTimeout(() => {
                        window.location.href = '<?= site_url('enrollments/my') ?>';
                    }, 1500);
                } else {
                    throw new Error(result.message || 'An error occurred while submitting your request.');
                }
            } catch (error) {
                console.error('Error:', error);
                
                // Show error message
                const errorAlert = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${error.message || 'An error occurred. Please try again.'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                
                const cardBody = form.closest('.card-body');
                cardBody.insertAdjacentHTML('afterbegin', errorAlert);
                window.scrollTo(0, 0);
            } finally {
                // Re-enable button and restore original text
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }, false);
    })();
    
    // Add schedule examples on click
    const scheduleInput = document.getElementById('schedule');
    const scheduleExamples = [
        'MWF 9:00-10:30',
        'TTH 1:00-2:30',
        'MW 2:00-3:30',
        'F 9:00-12:00',
        'MWF 10:00-11:30'
    ];
    
    scheduleInput.addEventListener('click', function() {
        if (!this.value) {
            this.setAttribute('placeholder', 'e.g., ' + scheduleExamples[Math.floor(Math.random() * scheduleExamples.length)]);
        }
    });
});
</script>

<style>
/* Custom styles for the enrollment form */
.needs-validation .form-control:invalid,
.needs-validation .form-select:invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.needs-validation .form-control:focus:invalid,
.needs-validation .form-select:focus:invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

.was-validated .form-control:invalid,
.was-validated .form-select:invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.was-validated .form-control:focus:invalid,
.was-validated .form-select:focus:invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}
</style>

<?= $this->endSection() ?>
