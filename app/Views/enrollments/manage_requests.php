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
                                <td><?= $enrollment['created_at'] ? date('M j, Y', strtotime($enrollment['created_at'])) : 'N/A' ?></td>
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
    document.getElementById('rejectionReason').value = '';
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

<?= $this->endSection() ?>
