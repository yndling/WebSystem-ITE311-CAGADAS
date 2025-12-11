<?= $this->extend('template') ?>

<?php $this->section('content') ?>
<div class="mb-4">
    <?php if (empty($requests)): ?>
        <div class="alert alert-info">
            No pending enrollment requests at this time.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>School Year</th>
                        <th>Semester</th>
                        <th>Schedule</th>
                        <th>Requested On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr id="request-<?= $request['id'] ?>">
                            <td><?= esc($request['name']) ?></td>
                            <td><?= esc($request['course_title']) ?></td>
                            <td><?= esc($request['school_year']) ?></td>
                            <td><?= ucfirst(esc($request['semester'])) ?> Semester</td>
                            <td><?= esc($request['schedule']) ?></td>
                            <td><?= date('M j, Y', strtotime($request['enrollment_date'])) ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button onclick="approveRequest(<?= $request['id'] ?>)" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button onclick="showRejectModal(<?= $request['id'] ?>)" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                    <?php if (in_array(session()->get('role'), ['teacher','admin'])): ?>
                                    <button onclick="openForceEnrollModal(<?= $request['course_id'] ?>)" class="btn btn-sm btn-primary">
                                        <i class="fas fa-user-plus"></i> Force Enroll
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Force Enroll Modal -->
<div class="modal fade" id="forceEnrollModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Force Enroll Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label for="forceStudentId" class="form-label">Student ID</label>
                    <input id="forceStudentId" class="form-control" type="number" />
                </div>
                <div class="mb-2">
                    <label for="forceSchoolYear" class="form-label">School Year</label>
                    <input id="forceSchoolYear" class="form-control" type="text" value="<?= date('Y') . '-' . (date('Y') + 1) ?>" />
                </div>
                <div class="mb-2">
                    <label for="forceSemester" class="form-label">Semester</label>
                    <select id="forceSemester" class="form-select">
                        <option value="1st">1st</option>
                        <option value="2nd">2nd</option>
                        <option value="summer">Summer</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label for="forceSchedule" class="form-label">Schedule</label>
                    <input id="forceSchedule" class="form-control" placeholder="e.g. MWF 09:00-10:30" />
                </div>
                <input type="hidden" id="forceCourseId" />
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

function openForceEnrollModal(courseId) {
    document.getElementById('forceCourseId').value = courseId;
    document.getElementById('forceStudentId').value = '';
    document.getElementById('forceSchedule').value = '';
    const modal = new bootstrap.Modal(document.getElementById('forceEnrollModal'));
    modal.show();
}

async function submitForceEnroll() {
    const studentId = document.getElementById('forceStudentId').value.trim();
    const courseId = document.getElementById('forceCourseId').value;
    const schoolYear = document.getElementById('forceSchoolYear').value.trim();
    const semester = document.getElementById('forceSemester').value;
    const schedule = document.getElementById('forceSchedule').value.trim();
    let submitBtn;

    try {
        // Validate required fields
        if (!studentId || !courseId || !schoolYear || !semester || !schedule) {
            throw new Error('Please fill all required fields.');
        }

        // Show loading state
        submitBtn = document.querySelector('#forceEnrollModal .btn-primary');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        const requestData = {
            user_id: parseInt(studentId, 10),
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
