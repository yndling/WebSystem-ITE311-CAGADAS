<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Learning Management System</title>
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
        }
        .stats-card {
            text-align: center;
        }
        .stats-card i {
            font-size: 2rem;
            margin-bottom: 10px;
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
                            <a class="nav-link active" href="#"><i class="fas fa-tachometer-alt"></i> Overview</a>
                        </li>
                        <?php if (session()->get('role') === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#manageUsersModal"><i class="fas fa-users"></i> Manage Users</a>
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
                        <?php elseif (session()->get('role') === 'student'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('course/browse') ?>"><i class="fas fa-search"></i> Browse Courses</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#myEnrollmentsModal"><i class="fas fa-list"></i> My Enrollments</a>
                            </li>
                        <?php endif; ?>
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
                    <h1 class="h2">Welcome, <?= isset($name) ? $name : 'User' ?>!</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-bell"></i> Notifications <span id="notification-badge" class="badge bg-danger" style="display: none;"></span>
                                </button>
                                <ul id="notifications-dropdown" class="dropdown-menu" aria-labelledby="notificationDropdown">
                                    <!-- Notifications will be loaded here -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

                <!-- User Info Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">User Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <?= isset($email) ? $email : '' ?></p>
                                <p><strong>Role:</strong> <?= isset($role) ? ucfirst($role) : '' ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>User ID:</strong> <?= isset($user_id) ? $user_id : '' ?></p>
                                <p><strong>Last Login:</strong> <?= date('Y-m-d H:i:s') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role-specific Content -->
                <?php if (isset($role) && $role === 'admin'): ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <i class="fas fa-users text-primary"></i>
                                    <h5 class="card-title">Total Users</h5>
                                    <p class="card-text"><?= isset($total_users) ? $total_users : 0 ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <i class="fas fa-chalkboard-teacher text-success"></i>
                                    <h5 class="card-title">Teachers</h5>
                                    <p class="card-text"><?= isset($teacher_count) ? $teacher_count : 0 ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <i class="fas fa-user-graduate text-warning"></i>
                                    <h5 class="card-title">Students</h5>
                                    <p class="card-text"><?= isset($student_count) ? $student_count : 0 ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif (isset($role) && $role === 'teacher'): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">My Courses</h5>
                                    <p class="card-text">You have <?= isset($my_courses) ? $my_courses : 0 ?> active courses.</p>
                                    <a href="<?= base_url('course/my') ?>" class="btn btn-primary">View Courses</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Students</h5>
                                    <p class="card-text">You have <?= isset($total_students) ? $total_students : 0 ?> students enrolled in your courses.</p>
                                    <a href="#" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createCourseModal">Create New Course</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Materials Management for Teachers -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Course Materials Management</h5>
                                </div>
                                <div class="card-body">
                                    <p>Upload materials for your courses or manage existing materials.</p>
                                    <a href="<?= base_url('admin/course/1/upload') ?>" class="btn btn-success">Upload Material</a>
                                    <a href="#" class="btn btn-info ms-2" data-bs-toggle="modal" data-bs-target="#viewMaterialsModal">View All Materials</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif (isset($role) && $role === 'student'): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Enrolled Courses</h5>
                                    <ul id="enrolled-courses-list" class="list-group">
                                        <?php if (!empty($enrolled_courses)): ?>
                                            <?php foreach ($enrolled_courses as $course): ?>
                                                <li class="list-group-item">
                                                    <strong><?= esc($course['course_title']) ?></strong><br>
                                                    <small><?= esc($course['course_description']) ?></small>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li class="list-group-item text-muted">No courses enrolled yet.</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Available Courses</h5>
                                    <?php if (!empty($available_courses)): ?>
                                        <ul class="list-group">
                                            <?php foreach ($available_courses as $course): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong><?= esc($course['title']) ?></strong><br>
                                                        <small><?= esc($course['description']) ?></small>
                                                    </div>
                                                    <button class="btn btn-sm btn-primary enroll-btn" data-course-id="<?= esc($course['id']) ?>">Enroll</button>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p>No courses available at the moment.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>


                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Modals for interactivity -->
    <!-- Manage Users Modal (Admin) -->
    <div class="modal fade" id="manageUsersModal" tabindex="-1" aria-labelledby="manageUsersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageUsersModalLabel">Manage Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Here you can manage all users in the system. Deleted users are marked and remain visible.</p>

                    <div class="mb-3">
                        <h6>Create New User</h6>
                        <form id="createUserForm" class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="name" class="form-control" placeholder="Name" required>
                            </div>
                            <div class="col-md-4">
                                <input type="email" name="email" class="form-control" placeholder="Email" required>
                            </div>
                            <div class="col-md-4">
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                            <div class="col-md-4">
                                <select name="role" class="form-control form-select" required>
                                    <option value="student">Student</option>
                                    <option value="teacher">Teacher</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary" type="submit">Create</button>
                            </div>
                        </form>
                    </div>

                    <hr>

                    <div>
                        <h6>All Users</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="manageUsersTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Deleted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Filled by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Other modals can be added similarly -->

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Load notification count and data on page load
            loadNotifications();
            
            // Set interval to fetch notifications every 60 seconds for real-time updates
            setInterval(loadNotifications, 60000);

            // Function to load notifications
            function loadNotifications() {
                $.get('<?= base_url('notifications') ?>')
                    .done(function(data) {
                        console.log('Notifications data:', data); // Debug log
                        
                        // Update notification badge
                        var badge = $('#notification-badge');
                        if (data.unreadCount > 0) {
                            badge.text(data.unreadCount).show();
                        } else {
                            badge.hide();
                        }

                        // Update notifications dropdown
                        var dropdown = $('#notifications-dropdown');
                        dropdown.empty();
                        if (data.notifications && data.notifications.length > 0) {
                            data.notifications.forEach(function(notification) {
                                console.log('Processing notification:', notification); // Debug log
                                
                                // Create notification item
                                var item = $('<div class="dropdown-item p-2"></div>');
                                
                                // Add message and timestamp
                                item.append('<small>' + notification.message + '</small><br>');
                                item.append('<small class="text-muted">' + notification.created_at + '</small>');
                                
                                // Add Mark as Read button for unread notifications
                                if (notification.is_read == 0) {
                                    var button = $('<button class="btn btn-sm btn-outline-primary ms-2 mark-read-btn" data-id="' + notification.id + '">Mark as Read</button>');
                                    item.append(button);
                                    item.addClass('fw-bold');
                                }
                                
                                dropdown.append(item);
                            });
                            dropdown.append('<div class="dropdown-divider"></div>');
                            dropdown.append('<a class="dropdown-item text-center" href="#">View All Notifications</a>');
                        } else {
                            dropdown.append('<span class="dropdown-item-text">No notifications</span>');
                        }
                    })
                    .fail(function() {
                        console.log('Failed to load notifications');
                    });
            }

            // Handle mark as read button clicks
            $(document).on('click', '.mark-read-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var button = $(this);
                var notificationId = button.data('id');
                var item = button.closest('.dropdown-item');

                console.log('Mark as read clicked for notification:', notificationId); // Debug log

                $.post('<?= base_url('notifications/mark_read/') ?>' + notificationId)
                    .done(function(data) {
                        console.log('Mark as read response:', data); // Debug log
                        if (data.success) {
                            item.removeClass('fw-bold');
                            button.remove();
                            loadNotifications(); // Reload to update badge
                        }
                    })
                    .fail(function() {
                        console.log('Failed to mark notification as read');
                    });
            });

            // Hide alert after 3 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 3000);

            // Handle enroll button clicks with AJAX
            $('.enroll-btn').click(function(e) {
                e.preventDefault();
                var button = $(this);
                var courseId = button.data('course-id');

                if (confirm('Are you sure you want to enroll in this course?')) {
                    $.post('<?= base_url('course/enroll') ?>', { course_id: courseId, csrf_test_name: '<?= csrf_token() ?>' })
                        .done(function(data) {
                            if (data.status === 'success') {
                                // Show success alert
                                var alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                    data.message +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                    '</div>';
                                $('.main-content').prepend(alertHtml);

                                // Disable the enroll button
                                button.prop('disabled', true).text('Enrolled');

                                // Add the course to the enrolled courses list
                                var courseItem = button.closest('li').clone();
                                courseItem.find('.enroll-btn').remove();
                                var enrolledList = $('#enrolled-courses-list');
                                var placeholder = enrolledList.find('li.text-muted');
                                if (placeholder.length) {
                                    placeholder.remove();
                                }
                                enrolledList.append(courseItem);

                                // Remove the course from available courses list
                                button.closest('li').remove();
                            } else {
                                alert(data.message);
                            }
                        })
                        .fail(function() {
                            alert('An error occurred. Please try again.');
                        });
                }
            });
        });
    </script>

    <script>
        // Manage Users modal JS
        (function() {
            var csrfName = '<?= csrf_token() ?>';
            var csrfHash = '<?= csrf_hash() ?>';

            function getCsrfData() {
                var obj = {};
                obj[csrfName] = csrfHash;
                return obj;
            }

            function loadUsers() {
                $.get('<?= base_url('/admin/users') ?>')
                    .done(function(res) {
                        var tbody = $('#manageUsersTable tbody');
                        tbody.empty();
                        var currentUserId = res.current_user_id;
                        res.users.forEach(function(u) {
                            var tr = $('<tr></tr>');
                            tr.append('<td>' + u.id + '</td>');
                            // Add disabled class and readonly attribute to inputs if user is deleted
                            var nameInput = $('<input class="form-control form-control-sm user-name" data-id="' + u.id + '" value="' + (u.name ? u.name : '') + '">');
                            var emailInput = $('<input class="form-control form-control-sm user-email" data-id="' + u.id + '" value="' + (u.email ? u.email : '') + '">');
                            
                            if (u.deleted_at) {
                                nameInput.addClass('bg-light').prop('readonly', true);
                                emailInput.addClass('bg-light').prop('readonly', true);
                            }
                            
                            tr.append($('<td></td>').append(nameInput));
                            tr.append($('<td></td>').append(emailInput));

                            var roleSelect = $('<select class="form-select form-select-sm user-role" data-id="' + u.id + '">'
                                + '<option value="student">Student</option>'
                                + '<option value="teacher">Teacher</option>'
                                + '<option value="admin">Admin</option>'
                                + '</select>');
                            roleSelect.val(u.role);
                            if (u.id === currentUserId || u.deleted_at) {
                                roleSelect.prop('disabled', true);
                            }
                            tr.append($('<td></td>').append(roleSelect));

                            tr.append('<td>' + (u.deleted_at ? '<span class="text-danger">Yes</span>' : 'No') + '</td>');

                            var actions = $('<td class="d-flex gap-1"></td>');
                            
                            // Always show save button for all users (active or deleted)
                            var saveBtn = $('<button class="btn btn-sm btn-success me-1">Save</button>');
                            saveBtn.click(function() {
                                var id = u.id;
                                var name = tr.find('.user-name').val();
                                var email = tr.find('.user-email').val();
                                var data = getCsrfData();
                                data.name = name;
                                data.email = email;
                                
                                // Only include role in the data if the user is not the current user and the role has changed
                                if (id !== res.current_user_id) {
                                    var role = tr.find('.user-role').val();
                                    data.role = role;
                                }
                                $.post('<?= base_url('/admin/user/update/') ?>' + id, data)
                                    .done(function(resp) {
                                        if (resp.success) {
                                            // Show success message
                                            var message = 'User information updated successfully';
                                            if (id === res.current_user_id) {
                                                message = 'Your information has been updated successfully';
                                            }
                                            
                                            // Create and show alert
                                            var alertDiv = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                                message +
                                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                                '</div>');
                                            
                                            // Add alert to the top of the modal content
                                            $('#manageUsersModal .modal-body').prepend(alertDiv);
                                            
                                            // Auto-remove alert after 3 seconds
                                            setTimeout(function() {
                                                alertDiv.alert('close');
                                            }, 3000);
                                            
                                            // Reload the users list
                                            loadUsers();
                                        } else if (resp.error) {
                                            alert(resp.error);
                                        }
                                    })
                                    .fail(function(xhr) {
                                        alert('Update failed: ' + (xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : '')); 
                                    });
                            });
                            
                            // Add delete or restore button based on user status
                            if (u.deleted_at) {
                                // User is deleted, show restore button
                                var restoreBtn = $('<button class="btn btn-sm btn-warning me-1">Restore</button>');
                                restoreBtn.click(function() {
                                    if (!confirm('Are you sure you want to restore this user?')) return;
                                    var data = getCsrfData();
                                    $.post('<?= base_url('/admin/user/restore/') ?>' + u.id, data)
                                        .done(function(resp) {
                                            if (resp.success) loadUsers();
                                            else if (resp.error) alert(resp.error);
                                        })
                                        .fail(function(xhr) {
                                            alert('Restore failed: ' + (xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : ''));
                                        });
                                });
                                actions.append(restoreBtn);
                            } else {
                                // User is active, show delete button
                                var delBtn = $('<button class="btn btn-sm btn-danger me-1">Delete</button>');
                                if (u.id === currentUserId) {
                                    delBtn.prop('disabled', true).attr('title', 'Cannot delete your own account');
                                }
                                delBtn.click(function() {
                                    if (!confirm('Are you sure you want to delete this user? This will mark them as deleted.')) return;
                                    var data = getCsrfData();
                                    $.post('<?= base_url('/admin/user/delete/') ?>' + u.id, data)
                                        .done(function(resp) {
                                            if (resp.success) loadUsers();
                                            else if (resp.error) alert(resp.error);
                                        })
                                        .fail(function(xhr) {
                                            alert('Delete failed: ' + (xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : ''));
                                        });
                                });
                                actions.append(delBtn);
                            }
                            
                            actions.append(saveBtn);

                            tr.append(actions);
                            tbody.append(tr);
                        });
                    })
                    .fail(function() {
                        alert('Failed to load users');
                    });
            }

            // Handle create
            $(document).on('submit', '#createUserForm', function(e) {
                e.preventDefault();
                var form = $(this);
                var data = getCsrfData();
                $.each(form.serializeArray(), function(i, field) { data[field.name] = field.value; });
                $.post('<?= base_url('/admin/user/create') ?>', data)
                    .done(function(res) {
                        if (res.success) {
                            form[0].reset();
                            loadUsers();
                        } else if (res.errors) {
                            alert('Validation error: ' + JSON.stringify(res.errors));
                        } else if (res.error) {
                            alert(res.error);
                        }
                    })
                    .fail(function(xhr) {
                        alert('Create failed: ' + (xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : ''));
                    });
            });

            // Load when modal is shown
            $('#manageUsersModal').on('shown.bs.modal', function() {
                loadUsers();
            });
        })();
    </script>

</body>
</html>
