<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Courses - Learning Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        
                    </ol>
                </nav>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Browse Courses</h2>
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex mb-2">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" id="courseSearch" class="form-control" 
                                       placeholder="Search courses by title or description..."
                                       data-no-results="No courses found matching your search.">
                                <button id="clearSearch" class="btn btn-outline-secondary" type="button" style="display: none;">
                                    <i class="fas fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-3" id="filterButtons">
                            <button class="btn btn-sm btn-outline-primary filter-btn active" data-filter="all">All Courses</button>
                            <button class="btn btn-sm btn-outline-primary filter-btn" data-filter="enrolled">My Enrolled</button>
                            <button class="btn btn-sm btn-outline-primary filter-btn" data-filter="available">Available</button>
                        </div>
                    </div>
                </div>

                <!-- Courses Container -->
                <div class="row mb-5">
                    <div class="col-12">
                        <div id="coursesContainer">
                            <!-- Available Courses -->
                            <div id="availableCourses">
                                <h3 class="mb-4">Available Courses</h3>
                                <div id="coursesList" class="row">
                                    <?php if (!empty($available_courses)): ?>
                                        <?php foreach ($available_courses as $course): ?>
                                            <div class="col-md-6 mb-4 course-card" 
                                                 data-title="<?= strtolower(esc($course['title'])) ?>"
                                                 data-description="<?= strtolower(esc($course['description'])) ?>"
                                                 data-course-id="<?= $course['id'] ?>">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                                        <p class="card-text text-muted"><?= esc($course['description']) ?></p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="badge bg-primary">Available</span>
                                                            <button class="btn btn-primary btn-sm enroll-btn" data-course-id="<?= esc($course['id']) ?>">
                                                                <i class="fas fa-plus"></i> Enroll
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <div class="alert alert-info no-courses-message">
                                                <i class="fas fa-info-circle"></i> No courses available at the moment.
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enrolled Courses and Materials Section -->
                <?php if (!empty($enrolled_courses_data)): ?>
                    <div class="row">
                        <div class="col-12">
                            <h3>My Enrolled Courses & Materials</h3>
                            <?php $materialModel = new \App\Models\MaterialModel(); ?>
                            <?php foreach ($enrolled_courses_data as $course): ?>
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Course: <?= esc($course['name']) ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span>Course Materials</span>
                                            <a href="<?= base_url('course/' . $course['id']) ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> View Full Course
                                            </a>
                                        </div>

                                        <?php $materials = $materialModel->getMaterialsByCourse($course['id']); ?>
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
                                                                <a href="<?= base_url('materials/download/' . $material['id']) ?>" class="btn btn-primary btn-sm" target="_blank">
                                                                    <i class="fas fa-download"></i> Download
                                                                </a>
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
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.11/jquery.lazy.min.js"></script>
    <script>
    $(document).ready(function() {
        // Initialize lazy loading for images
        $('.lazy').lazy();
        
        const searchInput = $('#courseSearch');
        const clearSearch = $('#clearSearch');
        const coursesContainer = $('#coursesList');
        const filterButtons = $('.filter-btn');
        let activeFilter = 'all';
        let searchTimeout;
        
        // Function to load enrolled courses
        function loadEnrolledCourses() {
            $.get('<?= base_url('course/my-enrolled') ?>')
                .done(function(response) {
                    if (response.status === 'success') {
                        updateCoursesList(response.data);
                    } else {
                        showErrorMessage('Failed to load enrolled courses');
                    }
                })
                .fail(function() {
                    showErrorMessage('Error connecting to server');
                });
        }

        // Function to load available courses
        function loadAvailableCourses() {
            $.get('<?= base_url('course/available') ?>')
                .done(function(response) {
                    if (response.status === 'success') {
                        updateCoursesList(response.data);
                    } else {
                        showErrorMessage('Failed to load available courses');
                    }
                })
                .fail(function() {
                    showErrorMessage('Error connecting to server');
                });
        }

        // Function to show error message
        function showErrorMessage(message) {
            coursesContainer.html(`
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> ${message}
                    </div>
                </div>
            `);
        }
        
        // Debounce function to limit how often the search function runs
        function debounce(func, wait) {
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => func.apply(context, args), wait);
            };
        }

        // Function to update the courses list with new data
        function updateCoursesList(courses) {
            if (courses.length === 0) {
                coursesContainer.html(`
                    <div class="col-12">
                        <div class="alert alert-info no-results-message">
                            <i class="fas fa-info-circle"></i> No courses found matching your search.
                        </div>
                    </div>
                `);
                return;
            }
            
            let html = '';
            courses.forEach(function(course) {
                // Show different UI if the user is already enrolled in this course
                if (course.is_enrolled) {
                    html += `
                        <div class="col-md-6 mb-4 course-card" 
                             data-title="${course.title.toLowerCase()}"
                             data-description="${(course.description || '').toLowerCase()}"
                             data-course-id="${course.id}">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">${escapeHtml(course.title)}</h5>
                                    <p class="card-text text-muted">${escapeHtml(course.description || '')}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-success">Enrolled</span>
                                        <a href="<?= base_url('course/') ?>${course.id}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    html += `
                        <div class="col-md-6 mb-4 course-card" 
                             data-title="${course.title.toLowerCase()}"
                             data-description="${(course.description || '').toLowerCase()}"
                             data-course-id="${course.id}">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">${escapeHtml(course.title)}</h5>
                                    <p class="card-text text-muted">${escapeHtml(course.description || '')}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-primary">Available</span>
                                        <button class="btn btn-primary btn-sm enroll-btn" data-course-id="${course.id}">
                                            <i class="fas fa-plus"></i> Enroll
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });
            
            coursesContainer.html(html);
            
            // Re-attach event handlers to the new enroll buttons
            $('.enroll-btn').off('click').on('click', function(e) {
                e.preventDefault();
                var button = $(this);
                var courseId = button.data('course-id');

                if (confirm('Are you sure you want to enroll in this course?')) {
                    $.post('<?= base_url('course/enroll') ?>', { 
                        course_id: courseId, 
                        csrf_test_name: '<?= csrf_token() ?>' 
                    })
                    .done(function(data) {
                        if (data.status === 'success') {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message || 'Enrollment failed');
                        }
                    })
                    .fail(function() {
                        alert('An error occurred. Please try again.');
                    });
                }
            });
        }
        
        // Helper function to escape HTML
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
        
        // Function to perform the search
        function performSearch(searchTerm) {
            if (searchTerm.length < 2) {
                // If search term is too short, don't search
                return;
            }
            
            // Show loading state
            coursesContainer.html('<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            
            // Make AJAX request to server (include scope to allow searching enrolled/available/all)
            $.get('<?= base_url('course/search') ?>', { searchTerm: searchTerm, scope: activeFilter })
                .done(function(response) {
                    if (response.status === 'success') {
                        updateCoursesList(response.data);
                    } else {
                        coursesContainer.html(`
                            <div class="col-12">
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i> Error loading search results.
                                </div>
                            </div>
                        `);
                    }
                })
                .fail(function() {
                    coursesContainer.html(`
                        <div class="col-12">
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> Failed to connect to the server. Please try again.
                            </div>
                        </div>
                    `);
                });
        }
        
        // Search input handler with debounce
        searchInput.on('input', debounce(function() {
            const searchTerm = $(this).val().trim();
            
            if (searchTerm.length > 0) {
                clearSearch.show();
                performSearch(searchTerm);
            } else {
                clearSearch.hide();
                // If search is cleared, reload the page to show all courses
                if (searchTerm === '') {
                    window.location.href = '<?= base_url('course/browse') ?>';
                }
            }
        }, 500));
        
        // Clear search
        clearSearch.on('click', function() {
            searchInput.val('').trigger('input');
            clearSearch.hide();
            window.location.href = '<?= base_url('course/browse') ?>';
        });
        
        // Handle keyboard navigation
        searchInput.on('keydown', function(e) {
            if (e.key === 'Escape') {
                $(this).val('').trigger('input');
                clearSearch.hide();
                window.location.href = '<?= base_url('course/browse') ?>';
            }
        });
        
        // Initialize with any existing search term
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        if (searchParam) {
            searchInput.val(searchParam);
            clearSearch.show();
            performSearch(searchParam);
        }
        
        // Filter buttons
        filterButtons.on('click', function() {
            const filter = $(this).data('filter');
            filterButtons.removeClass('active');
            $(this).addClass('active');
            activeFilter = filter; // update current active filter for searches
            
            // Show loading state
            coursesContainer.html('<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            
            // Handle different filter types
            if (filter === 'enrolled') {
                loadEnrolledCourses();
            } else if (filter === 'available') {
                loadAvailableCourses();
            } else {
                // All courses
                window.location.href = '<?= base_url('course/browse') ?>';
            }
        });
        
        // Make sure the search input has focus when the page loads
        searchInput.focus();
    });
    </script>
    <script>
        $(document).ready(function() {
            // Handle enroll button clicks with AJAX
            $('.enroll-btn').click(function(e) {
                e.preventDefault();
                var button = $(this);
                var courseId = button.data('course-id');

                if (confirm('Are you sure you want to enroll in this course?')) {
                    $.post('<?= base_url('course/enroll') ?>', { course_id: courseId, csrf_test_name: '<?= csrf_token() ?>' })
                        .done(function(data) {
                            if (data.status === 'success') {
                                alert(data.message);
                                location.reload(); // Reload to update the page
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
</body>
</html>
