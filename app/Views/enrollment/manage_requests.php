<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Manage Enrollment Requests') ?> - Learning Management System</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <h5 class="px-3">Dashboard</h5>
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('dashboard') ?>"><i class="fas fa-tachometer-alt"></i> Overview</a>
                        </li>
                        <?php if (session()->get('role') === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#manageUsersModal"><i class="fas fa-users"></i> Manage Users</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="<?= base_url('enrollments/manage') ?>"><i class="fas fa-check-circle"></i> Enrollment Requests</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#systemSettingsModal"><i class="fas fa-cogs"></i> System Settings</a>
                            </li>
                        <?php elseif (session()->get('role') === 'teacher'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('course/create') ?>"><i class="fas fa-plus-circle"></i> Create Course</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('course/my') ?>"><i class="fas fa-book"></i> View My Courses</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="<?= base_url('enrollments/manage') ?>"><i class="fas fa-tasks"></i> Enrollment Requests</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('enrollments/my') ?>"><i class="fas fa-book-open"></i> My Enrollments</a>
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
                    <h1 class="h2"><?= esc($title ?? 'Manage Enrollment Requests') ?></h1>
                </div>

                <div class="card">
                    <div class="card-body">
                        <?php if (empty($enrollments)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>No pending enrollment requests at this time.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Course</th>
                                            <th>Course Description</th>
                                            <th>Teacher</th>
                                            <th>Status</th>
                                            <th>School Year</th>
                                            <th>Semester</th>
                                            <th>Schedule</th>

                                            <th>Requested On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Filter out rejected enrollments
                                        $filteredEnrollments = array_filter($enrollments, function($enrollment) {
                                            return strtolower($enrollment['status'] ?? '') !== 'rejected';
                                        });
                                        
                                        foreach ($filteredEnrollments as $enrollment): 
                                            $statusClass = $enrollment['status'] === 'approved' ? 'success' : 'warning';
                                            $statusText = ucfirst($enrollment['status']);
                                            $scheduleText = !empty($enrollment['schedule']) && $enrollment['schedule'] !== 'To be scheduled by teacher' 
                                                ? esc($enrollment['schedule']) 
                                                : '<span class="text-muted">To be scheduled</span>';
                                        ?>
                                            <tr id="request-<?= $enrollment['id'] ?>">
                                                <td><?= esc($enrollment['student_name'] ?? 'N/A') ?></td>
                                                <td><?= esc($enrollment['course_title'] ?? 'N/A') ?></td>
                                                <td><?= esc($enrollment['course_description'] ?? 'No description available') ?></td>
                                                <td><?= esc($enrollment['teacher_name'] ?? 'N/A') ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $statusClass ?>">
                                                        <?= $statusText ?>
                                                        <?php if ($enrollment['status'] === 'pending'): ?>
                                                            <i class="fas fa-clock ms-1"></i>
                                                        <?php endif; ?>
                                                    </span>
                                                </td>
                                                <td><?= esc($enrollment['school_year'] ?? 'N/A') ?></td>
                                                <td><?= ucfirst(esc($enrollment['semester'] ?? 'N/A')) ?></td>
                                                <td><?= $scheduleText ?></td>

                                                <td><?= $enrollment['enrollment_date'] ? date('M j, Y g:i A', strtotime($enrollment['enrollment_date'])) : 'N/A' ?>
                                                <td>
                                                    <?php if ($enrollment['status'] === 'pending'): ?>
                                                        <button class="btn btn-success btn-sm me-2 mb-1" onclick="approveRequest(<?= $enrollment['id'] ?>)">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                        <button class="btn btn-danger btn-sm mb-1" onclick="rejectRequest(<?= $enrollment['id'] ?>)">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="text-muted small">No actions available</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Enrollment Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Please select a schedule for this enrollment:</p>
                    <form id="approveForm">
                        <div class="mb-3">
                            <label for="schedule" class="form-label">Schedule</label>
                            <select id="schedule" name="schedule" class="form-select" required>
                                <option value="">Select a schedule</option>
                                <option value="MWF 08:00-09:30">MWF 08:00-09:30</option>
                                <option value="MWF 09:30-11:00">MWF 09:30-11:00</option>
                                <option value="MWF 11:00-12:30">MWF 11:00-12:30</option>
                                <option value="MWF 13:00-14:30">MWF 13:00-14:30</option>
                                <option value="MWF 14:30-16:00">MWF 14:30-16:00</option>
                                <option value="MWF 16:00-17:30">MWF 16:00-17:30</option>
                                <option value="TTH 08:00-09:30">TTH 08:00-09:30</option>
                                <option value="TTH 09:30-11:00">TTH 09:30-11:00</option>
                                <option value="TTH 11:00-12:30">TTH 11:00-12:30</option>
                                <option value="TTH 13:00-14:30">TTH 13:00-14:30</option>
                                <option value="TTH 14:30-16:00">TTH 14:30-16:00</option>
                                <option value="TTH 16:00-17:30">TTH 16:00-17:30</option>
                                <option value="SAT 08:00-12:00">SAT 08:00-12:00</option>
                                <option value="SAT 13:00-17:00">SAT 13:00-17:00</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmApproveBtn">Approve</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Enrollment Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Please provide a reason for rejecting this enrollment request:</p>
                    <form id="rejectForm">
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason</label>
                            <textarea id="reason" name="reason" class="form-control" rows="3" placeholder="Enter rejection reason..." required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRejectBtn">Reject</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-info-circle me-2" id="toastIcon"></i>
                <strong class="me-auto" id="toastTitle">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage"></div>
        </div>
    </div>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Set base URL for JS -->
    <script>
        window.baseUrl = '<?= base_url() ?>';
        let currentRequestId = null;

        function showToast(type, message) {
            const toast = document.getElementById('toast');
            const toastIcon = document.getElementById('toastIcon');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');

            toast.className = 'toast';
            toastIcon.className = 'fas me-2';

            if (type === 'success') {
                toast.classList.add('bg-success', 'text-white');
                toastIcon.classList.add('fa-check-circle');
                toastTitle.textContent = 'Success';
            } else if (type === 'error') {
                toast.classList.add('bg-danger', 'text-white');
                toastIcon.classList.add('fa-exclamation-triangle');
                toastTitle.textContent = 'Error';
            } else {
                toast.classList.add('bg-info', 'text-white');
                toastIcon.classList.add('fa-info-circle');
                toastTitle.textContent = 'Info';
            }

            toastMessage.textContent = message;

            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }

        function approveRequest(requestId) {
            currentRequestId = requestId;
            const modal = new bootstrap.Modal(document.getElementById('approveModal'));
            modal.show();
        }

        function rejectRequest(requestId) {
            currentRequestId = requestId;
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }

        document.getElementById('confirmApproveBtn').addEventListener('click', async function() {
            const schedule = document.getElementById('schedule').value;
            if (!schedule) {
                showToast('error', 'Please select a schedule.');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Approving...';

            try {
                const response = await fetch(`${window.baseUrl}enrollments/approve/${currentRequestId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ schedule: schedule })
                });

                const result = await response.json();

                if (response.ok) {
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('approveModal'));
                    modal.hide();

                    // Remove the row from the table
                    document.getElementById(`request-${currentRequestId}`).remove();

                    showToast('success', 'Enrollment approved successfully!');
                } else {
                    throw new Error(result.message || 'Failed to approve enrollment');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('error', error.message || 'An error occurred while approving the enrollment.');
            } finally {
                this.disabled = false;
                this.innerHTML = 'Approve';
            }
        });

        document.getElementById('confirmRejectBtn').addEventListener('click', async function() {
            const reason = document.getElementById('reason').value.trim();
            if (!reason) {
                showToast('error', 'Please provide a reason for rejection.');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Rejecting...';

            try {
                const response = await fetch(`${window.baseUrl}enrollments/reject/${currentRequestId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ reason: reason })
                });

                const result = await response.json();

                if (response.ok) {
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
                    modal.hide();

                    // Remove the row from the table
                    document.getElementById(`request-${currentRequestId}`).remove();

                    showToast('success', 'Enrollment rejected successfully!');
                } else {
                    throw new Error(result.message || 'Failed to reject enrollment');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('error', error.message || 'An error occurred while rejecting the enrollment.');
            } finally {
                this.disabled = false;
                this.innerHTML = 'Reject';
            }
        });

        // Reset forms when modals are hidden
        document.getElementById('approveModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('approveForm').reset();
        });

        document.getElementById('rejectModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('rejectForm').reset();
        });
    </script>
</body>
</html>
