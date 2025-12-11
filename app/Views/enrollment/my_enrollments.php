<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Enrollments</h2>
        <a href="<?= site_url('courses') ?>" class="btn btn-outline-primary">
            <i class="fas fa-plus me-1"></i> Enroll in a Course
        </a>
    </div>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-4">
                    <label for="school_year" class="form-label">School Year</label>
                    <select class="form-select" id="school_year" name="school_year" onchange="this.form.submit()">
                        <option value="">All Years</option>
                        <?php 
                        $currentYear = date('Y');
                        for ($i = -1; $i <= 1; $i++): 
                            $year = $currentYear + $i;
                            $nextYear = $year + 1;
                            $schoolYear = "$year-$nextYear";
                            $selected = ($filters['school_year'] ?? '') === $schoolYear ? 'selected' : '';
                        ?>
                            <option value="<?= $schoolYear ?>" <?= $selected ?>><?= $schoolYear ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="semester" class="form-label">Semester</label>
                    <select class="form-select" id="semester" name="semester" onchange="this.form.submit()">
                        <option value="">All Semesters</option>
                        <option value="1st" <?= ($filters['semester'] ?? '') === '1st' ? 'selected' : '' ?>>1st Semester</option>
                        <option value="2nd" <?= ($filters['semester'] ?? '') === '2nd' ? 'selected' : '' ?>>2nd Semester</option>
                        <option value="summer" <?= ($filters['semester'] ?? '') === 'summer' ? 'selected' : '' ?>>Summer</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="<?= site_url('enrollments/my') ?>" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-sync-alt me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <?php if (empty($enrollments)): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5>No enrollments found</h5>
                <p class="text-muted">You don't have any enrollments matching your criteria.</p>
                <a href="<?= site_url('enrollments/my') ?>" class="btn btn-outline-primary mt-2">
                    <i class="fas fa-sync-alt me-1"></i> Reset Filters
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Course</th>
                        <th>Instructor</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th>Enrolled On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enrollments as $enrollment): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-light rounded p-2 text-center" style="width: 50px;">
                                            <i class="fas fa-book text-primary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?= esc($enrollment['course_title']) ?></h6>
                                        <small class="text-muted"><?= esc($enrollment['course_code'] ?? 'N/A') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar-sm rounded-circle bg-light text-primary d-flex align-items-center justify-content-center" 
                                             style="width: 32px; height: 32px;">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <?= esc($enrollment['teacher_name']) ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="d-block"><?= esc($enrollment['schedule']) ?></span>
                                    <small class="text-muted">
                                        <?= esc($enrollment['school_year']) ?> • 
                                        <?= ucfirst(esc($enrollment['semester'])) ?>
                                    </small>
                                </div>
                            </td>
                            <td>
                                <?php 
                                $statusClass = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger'
                                ][$enrollment['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $statusClass ?>">
                                    <?= ucfirst(esc($enrollment['status'])) ?>
                                </span>
                                <?php if ($enrollment['status'] === 'rejected' && !empty($enrollment['rejection_reason'])): ?>
                                    <div class="mt-1">
                                        <small class="text-muted" data-bs-toggle="tooltip" title="<?= esc($enrollment['rejection_reason']) ?>">
                                            <i class="fas fa-info-circle"></i> View reason
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="text-nowrap">
                                    <?= date('M j, Y', strtotime($enrollment['enrollment_date'])) ?>
                                    <div class="text-muted small">
                                        <?= date('g:i A', strtotime($enrollment['enrollment_date'])) ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="<?= site_url('course/view/' . $enrollment['course_id']) ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="View Course">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($enrollment['status'] === 'pending'): ?>
                                        <button onclick="cancelEnrollment(<?= $enrollment['id'] ?>)" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Cancel Request">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if (isset($pager) && $pager): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing <?= $pager->getCurrentPage() * $pager->getPerPage() - $pager->getPerPage() + 1 ?> 
                    to <?= min($pager->getCurrentPage() * $pager->getPerPage(), $pager->getTotal()) ?> 
                    of <?= $pager->getTotal() ?> entries
                </div>
                <nav aria-label="Page navigation">
                    <?= $pager->links('default', 'custom_pagination') ?>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Cancel Enrollment Confirmation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Enrollment Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this enrollment request? This action cannot be undone.</p>
                <input type="hidden" id="cancelEnrollmentId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It</button>
                <button type="button" class="btn btn-danger" onclick="confirmCancel()">Yes, Cancel Request</button>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

let currentEnrollmentId = null;

// Show cancel confirmation modal
function cancelEnrollment(enrollmentId) {
    currentEnrollmentId = enrollmentId;
    const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
    modal.show();
}

// Confirm cancel enrollment
async function confirmCancel() {
    if (!currentEnrollmentId) return;
    
    try {
        const response = await fetch('<?= site_url('api/enrollment/cancel/') ?>' + currentEnrollmentId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        });
        
        const result = await response.json();
        
        if (response.ok) {
            // Show success message and reload
            showToast('Enrollment request cancelled successfully', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(result.message || 'Failed to cancel enrollment request');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast(error.message || 'An error occurred. Please try again.', 'danger');
    }
}

// Show toast notification
function showToast(message, type = 'info') {
    // You can implement a toast notification system here
    // For now, we'll use a simple alert
    alert(message);
}
</script>

<style>
/* Custom styles for better UI */
.avatar-sm {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: #f8f9fa;
    color: #6c757d;
}

/* Custom pagination styles */
.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.pagination .page-link {
    color: #0d6efd;
}

/* Responsive table */
@media (max-width: 768px) {
    .table-responsive {
        border: 0;
    }
    
    .table thead {
        display: none;
    }
    
    .table tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    
    .table td {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem;
        text-align: right;
        border-bottom: 1px solid #dee2e6;
    }
    
    .table td::before {
        content: attr(data-label);
        font-weight: bold;
        margin-right: 1rem;
    }
    
    .table td:last-child {
        border-bottom: 0;
    }
}
</style>

<?= $this->endSection() ?>
