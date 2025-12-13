<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\API\ResponseTrait;

class Enrollment extends BaseController
{
    use ResponseTrait;

    protected $enrollmentModel;
    protected $courseModel;
    protected $notificationModel;
    /** @var IncomingRequest */
    protected $request;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->courseModel = new CourseModel();
        $this->notificationModel = new \App\Models\NotificationModel();
        helper(['form', 'url']);

        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Show enrollment request form
     */
    public function requestForm($courseId)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'student') {
            return redirect()->to('/login')->with('error', 'Please log in as a student to request enrollment.');
        }

        // Get course with teacher's name
        $db = \Config\Database::connect();
        $course = $db->table('courses')
                    ->select('courses.*, users.name as teacher_name')
                    ->join('users', 'users.id = courses.teacher_id')
                    ->where('courses.id', $courseId)
                    ->get()
                    ->getRowArray();
                    
        if (!$course) {
            return redirect()->back()->with('error', 'Course not found.');
        }

        // Check if already enrolled
        $isEnrolled = $this->enrollmentModel->isAlreadyEnrolled(
            session()->get('user_id'),
            $courseId,
            date('Y') . '-' . (date('Y') + 1), // Current school year
            $this->getCurrentSemester() // Current semester
        );

        if ($isEnrolled) {
            return redirect()->back()->with('info', 'You are already enrolled in this course.');
        }

        return view('enrollment/request', [
            'course' => $course,
            'currentYear' => date('Y'),
            'currentSemester' => $this->getCurrentSemester(),
            'schoolYear' => date('Y') . '-' . (date('Y') + 1)
        ]);
    }

    /**
     * Get current semester based on month
     */
    private function getCurrentSemester()
    {
        $month = date('n');
        if ($month >= 1 && $month <= 4) {
            return '2nd'; // 2nd semester: January to April
        } elseif ($month >= 8 && $month <= 12) {
            return '1st'; // 1st semester: August to December
        } else {
            return 'summer'; // Summer: May to July
        }
    }

    /**
     * Request enrollment in a course (for students)
     */
    public function request()
    {
        log_message('debug', 'Starting enrollment request');
        
        if (!$this->request->isAJAX()) {
            log_message('debug', 'Non-AJAX request received');
            return $this->fail('Invalid request method', 405);
        }

        // Only students can request enrollment
        if (session()->get('role') !== 'student') {
            return $this->failForbidden('Only students can request enrollment.');
        }

        // Get JSON data from the request body
        $json = $this->request->getJSON(true);
        
        // If no JSON data, try getting from POST
        $data = !empty($json) ? $json : $this->request->getPost();
        
        // Log the received data for debugging
        log_message('debug', 'Enrollment request data: ' . print_r($data, true));
        
        // Validate required fields
        $required = ['course_id', 'school_year', 'semester'];
        $missingFields = [];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            $errorMsg = 'Missing required fields: ' . implode(', ', $missingFields);
            log_message('error', $errorMsg);
            return $this->fail([
                'status' => 'error',
                'message' => $errorMsg,
                'errors' => [$errorMsg]
            ], 400);
        }
        
        try {
            // Set default values
            $data['user_id'] = session()->get('user_id');
            
            // Set default schedule if not provided
            if (empty($data['schedule'])) {
                $data['schedule'] = 'To be scheduled by teacher';
            }
            
            log_message('debug', 'Processing enrollment with data: ' . print_r($data, true));
            
            // Process the enrollment
            $enrollmentId = $this->enrollmentModel->requestEnrollment($data);
            
            if ($enrollmentId) {
                log_message('info', "Enrollment request successful. ID: $enrollmentId");

                // Send notification to teacher
                $course = $this->courseModel->find($data['course_id']);
                if ($course) {
                    log_message('debug', 'Creating notification for teacher: ' . $course['teacher_id'] . ' for course: ' . $course['title']);
                    $notificationResult = $this->notificationModel->save([
                        'user_id' => $course['teacher_id'],
                        'message' => "New enrollment request from student for course: {$course['title']}",
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    log_message('debug', 'Notification creation result: ' . ($notificationResult ? 'success' : 'failed'));
                    if (!$notificationResult) {
                        log_message('error', 'Notification creation errors: ' . print_r($this->notificationModel->errors(), true));
                    }
                }
                return $this->respondCreated([
                    'status' => 'success',
                    'message' => 'Enrollment request submitted successfully.',
                    'enrollment_id' => $enrollmentId
                ]);
            } else {
                $errors = $this->enrollmentModel->errors();
                $errorMsg = 'Failed to create enrollment: ' . print_r($errors, true);
                log_message('error', $errorMsg);
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Failed to process enrollment',
                    'errors' => $errors ?: ['Unknown error occurred']
                ], 400);
            }
        } catch (\RuntimeException $e) {
            log_message('error', 'RuntimeException in enrollment: ' . $e->getMessage());
            log_message('debug', 'Stack trace: ' . $e->getTraceAsString());
            return $this->fail([
                'status' => 'error',
                'message' => $e->getMessage(),
                'type' => 'runtime_exception'
            ], 400);
        } catch (\Exception $e) {
            log_message('error', 'Exception in enrollment: ' . $e->getMessage());
            log_message('debug', 'Stack trace: ' . $e->getTraceAsString());
            return $this->fail([
                'status' => 'error',
                'message' => 'An error occurred while processing your request.',
                'type' => 'exception'
            ], 500);
        }
    }

    /**
     * Display user's enrollments
     */
    public function myEnrollments()
    {
        $userId = session()->get('user_id');

        // Get filter parameters
        $filters = [
            'school_year' => $this->request->getGet('school_year'),
            'semester' => $this->request->getGet('semester'),
            'status' => $this->request->getGet('status') ?? 'all' // Show all enrollments by default
        ];
        
        // If no status filter is set, include both pending and approved enrollments
        if ($filters['status'] === 'all') {
            $filters['status'] = ['pending', 'approved'];
        }

        // Get enrollments with filters
        $enrollments = $this->enrollmentModel->getUserEnrollments($userId, $filters);

        // Get pagination
        $pager = service('pager');
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;
        $total = count($enrollments);
        $pager->makeLinks($page, $perPage, $total, 'default_full');

        // Paginate results
        $enrollments = array_slice($enrollments, ($page - 1) * $perPage, $perPage);

        return view('enrollment/my_enrollments', [
            'enrollments' => $enrollments,
            'filters' => $filters,
            'pager' => $pager
        ]);
    }

    /**
     * Approve an enrollment request (for teachers/admins)
     */
    public function approve($enrollmentId)
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Invalid request method', 405);
        }

        $userId = session()->get('user_id');
        $userRole = session()->get('role');

        // Only teachers and admins can approve enrollments
        if (!in_array($userRole, ['teacher', 'admin'])) {
            return $this->failForbidden('You do not have permission to approve enrollments.');
        }

        try {
            // Ensure the teacher is the owner of the course for this enrollment unless admin
            $enrollment = $this->enrollmentModel->find($enrollmentId);
            if (!$enrollment) {
                return $this->failNotFound('Enrollment not found.');
            }

            if ($userRole === 'teacher') {
                $course = $this->courseModel->find($enrollment['course_id']);
                if (!$course || (int) $course['teacher_id'] !== (int) $userId) {
                    return $this->failForbidden('You do not have permission to approve requests for this course.');
                }
            }

            // Get schedule from request
            $data = $this->request->getJSON(true);
            $schedule = $data['schedule'] ?? null;

            if (empty($schedule)) {
                return $this->fail('Schedule is required for approval.', 400);
            }

            // Update enrollment with schedule and approve
            $this->enrollmentModel->update($enrollmentId, [
                'schedule' => $schedule,
                'status' => 'approved',
                'approved_by' => $userId,
                'approved_at' => date('Y-m-d H:i:s')
            ]);
            // Send notification to student
            $course = $this->courseModel->find($enrollment['course_id']);
            if ($course) {
                log_message('debug', 'Creating approval notification for student: ' . $enrollment['user_id'] . ' for course: ' . $course['title']);
                $notificationResult = $this->notificationModel->save([
                    'user_id' => $enrollment['user_id'],
                    'message' => "Your enrollment request for course '{$course['title']}' has been approved!",
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                log_message('debug', 'Student notification creation result: ' . ($notificationResult ? 'success' : 'failed'));
                if (!$notificationResult) {
                    log_message('error', 'Student notification creation errors: ' . print_r($this->notificationModel->errors(), true));
                }
            }

            return $this->respond(['message' => 'Enrollment approved successfully.']);
        } catch (\RuntimeException $e) {
            return $this->fail($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->failServerError('An error occurred while approving the enrollment.');
        }
    }

    /**
     * Reject an enrollment request (for teachers/admins)
     */
    public function reject($enrollmentId)
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Invalid request method', 405);
        }

        $userId = session()->get('user_id');
        $userRole = session()->get('role');
        $reason = $this->request->getJSON(true)['reason'] ?? '';

        // Only teachers and admins can reject enrollments
        if (!in_array($userRole, ['teacher', 'admin'])) {
            return $this->failForbidden('You do not have permission to reject enrollments.');
        }

        try {
            // Find enrollment before rejecting to get course info
            $enrollment = $this->enrollmentModel->find($enrollmentId);
            if (!$enrollment) {
                return $this->failNotFound('Enrollment not found.');
            }

            $this->enrollmentModel->rejectEnrollment($enrollmentId, $reason, $userId);
                        // Send notification to student
            $course = $this->courseModel->find($enrollment['course_id']);
            if ($course) {
                $message = "Your enrollment request for course '{$course['title']}' has been rejected.";
                if (!empty($reason)) {
                    $message .= " Reason: {$reason}";
                }
                log_message('debug', 'Creating rejection notification for student: ' . $enrollment['user_id'] . ' for course: ' . $course['title']);
                $notificationResult = $this->notificationModel->save([
                    'user_id' => $enrollment['user_id'],
                    'message' => $message,
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                log_message('debug', 'Student rejection notification creation result: ' . ($notificationResult ? 'success' : 'failed'));
                if (!$notificationResult) {
                    log_message('error', 'Student rejection notification creation errors: ' . print_r($this->notificationModel->errors(), true));
                }
            }

            return $this->respond(['message' => 'Enrollment rejected.']);
        } catch (\Exception $e) {
            return $this->failServerError('An error occurred while rejecting the enrollment.');
        }
    }

    /**
     * Force enroll a student (for admins/teachers)
     */
    public function forceEnroll()
    {
        try {
            // Set JSON response header
            $this->response->setContentType('application/json');

            if (!$this->request->isAJAX()) {
                throw new \RuntimeException('Invalid request method', 405);
            }

            $userId = session()->get('user_id');
            $userRole = session()->get('role');

            // Only admins and teachers can force enroll
            if (!in_array($userRole, ['admin', 'teacher'])) {
                throw new \RuntimeException('You do not have permission to force enroll students.', 403);
            }

            // Get and validate JSON input
            $input = $this->request->getJSON(true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Invalid JSON input', 400);
            }

            log_message('debug', 'Force enroll data: ' . print_r($input, true));

            // Manually extract and validate required fields
            $required = ['user_id', 'course_id', 'school_year', 'semester', 'schedule'];
            $data = [];
            $missing = [];

            foreach ($required as $field) {
                if (!isset($input[$field]) || $input[$field] === '') {
                    $missing[] = $field;
                } else {
                    log_message('error', 'Course not found for notification: ' . $data['course_id']);
                    $data[$field] = $input[$field];
                }
            }

            if (!empty($missing)) {
                throw new \RuntimeException('Missing required fields: ' . implode(', ', $missing), 400);
            }

            // Determine if we should skip conflict checks
            $skipConflict = !empty($input['force']);
            log_message('debug', 'Skip conflict: ' . ($skipConflict ? 'true' : 'false'));

            // Attempt to enroll the student
            $result = $this->enrollmentModel->forceEnroll($data, $userId, $skipConflict);

            if ($result === false) {
                throw new \RuntimeException('Failed to save enrollment', 500);
            }

            log_message('debug', 'Enrollment successful');

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Student enrolled successfully.'
            ]);

        } catch (\RuntimeException $e) {
            $statusCode = $e->getCode() ?: 400;
            log_message('error', 'Enrollment error: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode($statusCode);

        } catch (\Exception $e) {
            log_message('error', 'Unexpected error in forceEnroll: ' . $e->getMessage());
            log_message('error', $e->getTraceAsString());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Cancel an enrollment request (student or admin)
     */
    public function cancel($enrollmentId)
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Invalid request method', 405);
        }

        $userId = session()->get('user_id');
        $userRole = session()->get('role');

        // Find enrollment
        $enrollment = $this->enrollmentModel->find($enrollmentId);
        if (!$enrollment) {
            return $this->failNotFound('Enrollment not found.');
        }

        // Only the student who requested it may cancel a pending request, or admins can cancel
        if ($userRole === 'student') {
            if ((int)$enrollment['user_id'] !== (int)$userId) {
                return $this->failForbidden('You do not have permission to cancel this enrollment.');
            }

            if ($enrollment['status'] !== 'pending') {
                return $this->fail('Only pending requests can be cancelled.', 400);
            }
        }

        try {
            // Use model to update status to cancelled
            $this->enrollmentModel->cancelEnrollment($enrollmentId, $userId);
            return $this->respond(['message' => 'Enrollment request cancelled.']);
        } catch (\RuntimeException $e) {
            return $this->fail($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->failServerError('An error occurred while cancelling the enrollment.');
        }
    }

    /**
     * Display the manage requests page for teachers/admins
     */
    public function manageRequests() {
        try {
            // Debug: Log session data
            log_message('debug', 'Manage requests accessed');

            // Check if user is logged in
            if (!session()->get('logged_in')) {
                log_message('error', 'User not logged in');
                return redirect()->to('/login')->with('error', 'Please log in to access this page.');
            }

            // Get user role and ID
            $userRole = session()->get('role');
            $userId = session()->get('user_id');

            // Check if user has the right role
            if (!in_array($userRole, ['admin', 'teacher'])) {
                log_message('error', 'User role not authorized: ' . $userRole);
                return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to access this page.');
            }

            // Get enrollment requests based on user role
            $enrollments = [];

            if ($userRole === 'admin') {
                // Admin can see all pending enrollments
                $enrollments = $this->enrollmentModel
                    ->where('status', 'pending')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->join('users as teachers', 'teachers.id = courses.teacher_id', 'left')
                    ->select('enrollments.*, users.name as student_name, users.first_name, users.last_name, users.email, courses.title as course_title, courses.description as course_description, teachers.name as teacher_name, enrollments.school_year, enrollments.semester, enrollments.schedule, enrollments.enrollment_date')
                    ->findAll();
            } else {
                // Teacher can only see enrollments for their own courses
                $enrollments = $this->enrollmentModel
                    ->where('enrollments.status', 'pending')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->join('users as teachers', 'teachers.id = courses.teacher_id', 'left')
                    ->where('courses.teacher_id', $userId)
                    ->select('enrollments.*, users.name as student_name, users.first_name, users.last_name, users.email, courses.title as course_title, courses.description as course_description, teachers.name as teacher_name, enrollments.school_year, enrollments.semester, enrollments.schedule, enrollments.enrollment_date')
                    ->findAll();
            }

            // Load the view with data
            return view('enrollment/manage_requests', [
                'title' => 'Manage Enrollment Requests',
                'enrollments' => $enrollments,
                'isAdmin' => ($userRole === 'admin')
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in manageRequests: ' . $e->getMessage());
            $enrollments = [];
            return view('enrollment/manage_requests', [
                'title' => 'Manage Enrollment Requests',
                'enrollments' => $enrollments,
                'isAdmin' => ($userRole === 'admin')
            ]);
        }
    }

    /**
     * Get pending enrollment requests for a teacher's courses (API endpoint)
     */
    public function pendingRequests()
    {
        $userId = session()->get('user_id');
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'You must be logged in to view this page.');
        }

        $userId = session()->get('user_id');
        $userRole = session()->get('role');

        // Only teachers and admins can access this page
        if (!in_array($userRole, ['teacher', 'admin'])) {
            return redirect()->back()->with('error', 'You do not have permission to view this page.');
        }

        // Log the user ID and role for debugging
        log_message('debug', 'User ID: ' . $userId . ', Role: ' . $userRole);

        // Get pending requests (teachers see only their courses, admins see all)
        $data = [
            'title' => 'Manage Enrollment Requests',
            'requests' => []
        ];


        $status = $this->request->getGet('status');
        $schoolYear = $this->request->getGet('school_year');
        $semester = $this->request->getGet('semester');

        try {
            $teacherId = ($userRole === 'teacher') ? $userId : null;
            $enrollments = $this->enrollmentModel->getPendingRequestsForTeacher($teacherId);
            return $this->respond($enrollments);
        } catch (\Exception $e) {
            return $this->failServerError('An error occurred while fetching enrollments.');
        }
    }
}
