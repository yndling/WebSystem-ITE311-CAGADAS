<?= $this->extend('template') ?>

<?php $this->section('content') ?>
<div class="container">
    <h2>Manage Enrollment Requests</h2>
    <div class="mb-4">
        <?php if (empty($enrollments)): ?>
            <div class="alert alert-info">
                No pending enrollment requests at this time.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Requested On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrollments as $enrollment): ?>
                            <tr id="request-<?= $enrollment['id'] ?>">
                                <td><?= esc($enrollment['first_name'] . ' ' . $enrollment['last_name']) ?></td>
                                <td><?= esc($enrollment['email']) ?></td>
                                <td><?= esc($enrollment['course_title']) ?></td>
                                <td><?= $enrollment['enrollment_date'] ? date('M j, Y', strtotime($enrollment['enrollment_date'])) : 'N/A' ?></td>
                                <td><?= ucfirst($enrollment['status'] ?? 'pending') ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button onclick="approveRequest(<?= $enrollment['id'] ?>)" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button onclick="showRejectModal(<?= $enrollment['id'] ?>)" class="btn btn-sm btn-danger">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Enrollment Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rejectRequestId">
                <div class="mb-3">
                    <label for="rejectReason" class="form-label">Reason for Rejection</label>
                    <textarea id="rejectReason" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="rejectRequest()">Reject Request</button>
            </div>
        </div>
    </div>
</div>

<script>
function approveRequest(requestId) {
    if (confirm('Are you sure you want to approve this enrollment request?')) {
        fetch(`/enrollments/approve/${requestId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById(`request-${requestId}`).remove();
                alert('Enrollment request approved successfully.');
                // Reload the page to update the list
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to approve request.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request.');
        });
    }
}

let currentRequestId = null;

function showRejectModal(requestId) {
    currentRequestId = requestId;
    document.getElementById('rejectionReason').value = ''; // Clear previous reason
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

async function approveRequest(requestId) {
    if (!confirm('Are you sure you want to approve this enrollment request?')) {
        return;
    }
    
    try {
        const response = await fetch(`/enrollments/approve/${requestId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            document.getElementById(`request-${requestId}`).remove();
            alert('Enrollment request approved successfully.');
            window.location.reload();
        } else {
            throw new Error(data.message || 'Failed to approve request');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error: ' + (error.message || 'An error occurred while approving the request.'));
    }
}

async function rejectRequest() {
    const reason = document.getElementById('rejectionReason').value.trim();
    
    if (!reason) {
        alert('Please provide a reason for rejection.');
        return;
    }

    try {
        const response = await fetch(`/enrollments/reject/${currentRequestId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: JSON.stringify({ reason: reason })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            document.getElementById(`request-${currentRequestId}`).remove();
            const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
            modal.hide();
            alert('Enrollment request has been rejected.');
            window.location.reload();
        } else {
            throw new Error(data.message || 'Failed to reject request');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error: ' + (error.message || 'An error occurred while processing your request.'));
    }
}
</script>
                        <option value="TTH 14:30-16:00">TTH 14:30-16:00</option>
                        <option value="TTH 16:00-17:30">TTH 16:00-17:30</option>
                        <option value="SAT 08:00-12:00">SAT 08:00-12:00</option>
                        <option value="SAT 13:00-17:00">SAT 13:00-17:00</option>
                    </select>
                </div>
                <input type="hidden" id="forceCourseId" />
                <input type="hidden" id="forceUserId" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitForceEnroll()">Enroll Student</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Enrollment Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Please provide a reason for rejecting this enrollment request:</p>
                <textarea id="rejectionReason" class="form-control" rows="3" required></textarea>
                <input type="hidden" id="rejectRequestId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="rejectRequest()">Reject</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentRequestId = null;

function showRejectModal(requestId) {
    currentRequestId = requestId;
    document.getElementById('rejectRequestId').value = requestId;
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

async function approveRequest(requestId) {
    if (!confirm('Are you sure you want to approve this enrollment request?')) {
        return;
    }
    
    try {
        const response = await fetch(`<?= site_url('api/enrollment/approve/') ?>${requestId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        });
        
        const result = await response.json();
        
        if (response.ok) {
            // Remove the row from the table
            document.getElementById(`request-${requestId}`).remove();
            showToast('success', 'Enrollment approved successfully!');
        } else {
            throw new Error(result.message || 'Failed to approve enrollment');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', error.message || 'An error occurred while approving the enrollment.');
    }
}

async function rejectRequest() {
    const reason = document.getElementById('rejectionReason').value.trim();
    if (!reason) {
        alert('Please provide a reason for rejection.');
        return;
    }
    
    const requestId = currentRequestId;
    
    try {
        const response = await fetch(`<?= site_url('api/enrollment/reject/') ?>${requestId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: JSON.stringify({ reason })
        });
        
        const result = await response.json();
        
        if (response.ok) {
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
            modal.hide();
            
            // Remove the row from the table
            document.getElementById(`request-${requestId}`).remove();
            showToast('success', 'Enrollment request has been rejected.');
        } else {
            throw new Error(result.message || 'Failed to reject enrollment');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', error.message || 'An error occurred while rejecting the enrollment.');
    }
}

// Helper function to show toast notifications
function showToast(type, message) {
    // You can implement a toast notification system here
    // For simplicity, we'll use alert for now
    alert(message);
}

function openForceEnrollModal(courseId, userId) {
    document.getElementById('forceCourseId').value = courseId;
    document.getElementById('forceUserId').value = userId;
    document.getElementById('forceSchedule').value = '';
    const modal = new bootstrap.Modal(document.getElementById('forceEnrollModal'));
    modal.show();
}

async function submitForceEnroll() {
    const userId = document.getElementById('forceUserId').value;
    const courseId = document.getElementById('forceCourseId').value;
    const schoolYear = document.getElementById('forceSchoolYear').value.trim();
    const semester = document.getElementById('forceSemester').value;
    const schedule = document.getElementById('forceSchedule').value;
    let submitBtn;

    try {
        // Validate required fields
        if (!userId || !courseId || !schoolYear || !semester || !schedule) {
            throw new Error('Please fill all required fields.');
        }

        // Show loading state
        submitBtn = document.querySelector('#forceEnrollModal .btn-primary');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        const requestData = {
            user_id: parseInt(userId, 10),
            course_id: parseInt(courseId, 10),
            school_year: schoolYear,
            semester: semester,
            schedule: schedule,
            force: true
        };

        console.log('Sending request with data:', requestData);

        const response = await fetch('<?= site_url("api/enrollment/force-enroll") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: JSON.stringify(requestData)
        });

        console.log('Response status:', response.status);
        
        let result;
        const responseText = await response.text();
        console.log('Raw response:', responseText);
        
        try {
            result = responseText ? JSON.parse(responseText) : {};
        } catch (e) {
            console.error('Failed to parse JSON response:', e);
            throw new Error('Received invalid response from server. Please check the console for details.');
        }

        // Restore button state
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Enroll Student';
        }
        
        if (response.ok) {
            // Show success message
            showToast('success', result.message || 'Student enrolled successfully.');
            
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('forceEnrollModal'));
            if (modal) {
                modal.hide();
            }
            
            // Reload the page to show the updated enrollment
            setTimeout(() => window.location.reload(), 1500);
        } else {
            // Show error message from server
            const errorMsg = result.message || `Server returned status: ${response.status}`;
            throw new Error(errorMsg);
        }
    } catch (error) {
        console.error('Enrollment error:', error);
        
        // Show error message to user
        showToast('error', error.message || 'An error occurred while processing your request. Please try again.');
        
        // Make sure to restore button state on error
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Enroll Student';
        }
    }
}
</script>
<?= $this->endSection() ?>
