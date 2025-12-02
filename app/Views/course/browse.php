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
        const courseCards = $('.course-card');
        const filterButtons = $('.filter-btn');
        let activeFilter = 'all';
        
        // Debounce function to limit how often the search function runs
        function debounce(func, wait) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }

        // Filter courses based on search term and active filter
        function filterCourses() {
            const searchTerm = searchInput.val().toLowerCase().trim();
            let visibleCount = 0;
            
            // Remove any existing no results messages
            $('.no-results-message, .no-courses-message').remove();
            
            // If no search term and no filter is active, show all courses
            if (searchTerm === '' && activeFilter === 'all') {
                $('.course-card').removeClass('d-none');
                return;
            }
            
            // Check each course card
            courseCards.each(function() {
                const $card = $(this);
                const title = $card.data('title') || '';
                const description = $card.data('description') || '';
                
                // Check if card matches search term
                const matchesSearch = searchTerm === '' || 
                                    title.includes(searchTerm) || 
                                    description.includes(searchTerm);
                
                // Check if card matches current filter
                const matchesFilter = activeFilter === 'all' || 
                                    (activeFilter === 'enrolled' && $card.hasClass('enrolled')) ||
                                    (activeFilter === 'available' && !$card.hasClass('enrolled'));
                
                if (matchesSearch && matchesFilter) {
                    $card.removeClass('d-none');
                    visibleCount++;
                } else {
                    $card.addClass('d-none');
                }
            });
            
            // Show appropriate message if no courses match
            if (visibleCount === 0) {
                const message = searchTerm !== '' 
                    ? searchInput.data('no-results') 
                    : 'No courses available';
                
                $('#coursesList').append(`
                    <div class="col-12">
                        <div class="alert alert-info ${searchTerm !== '' ? 'no-results-message' : 'no-courses-message'}">
                            <i class="fas fa-info-circle"></i> ${message}
                        </div>
                    </div>
                `);
            }
        }
        
        // Search input handler with debounce
        searchInput.on('input', debounce(function() {
            if ($(this).val().length > 0) {
                clearSearch.show();
            } else {
                clearSearch.hide();
            }
            filterCourses();
        }, 300));
        
        // Clear search
        clearSearch.on('click', function() {
            searchInput.val('').trigger('input');
            clearSearch.hide();
        });
        
        // Filter buttons
        filterButtons.on('click', function() {
            filterButtons.removeClass('active');
            $(this).addClass('active');
            activeFilter = $(this).data('filter');
            filterCourses();
        });
        
        // Initialize courses
        filterCourses();
        
        // Handle keyboard navigation
        searchInput.on('keydown', function(e) {
            if (e.key === 'Escape') {
                $(this).val('').trigger('input');
                clearSearch.hide();
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
