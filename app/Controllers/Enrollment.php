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
    /** @var IncomingRequest */
    protected $request;
    
    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->courseModel = new CourseModel();
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
        
        $course = $this->courseModel->find($courseId);
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
            'currentSemester' => $this->getCurrentSemester()
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
        if (!$this->request->isAJAX()) {
            return $this->fail('Invalid request method', 405);
        }
        
        $data = $this->request->getJSON(true);
        $data['user_id'] = session()->get('user_id');
        
        try {
            $this->enrollmentModel->requestEnrollment($data);
            return $this->respondCreated(['message' => 'Enrollment request submitted successfully.']);
        } catch (\RuntimeException $e) {
            return $this->fail($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->failServerError('An error occurred while processing your request.');
        }
    }
    
    /**
     * Approve an enrollment request (for teachers/admins)
     */
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
            'status' => $this->request->getGet('status')
        ];
        
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

            $this->enrollmentModel->approveEnrollment($enrollmentId, $userId);
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
            $this->enrollmentModel->rejectEnrollment($enrollmentId, $reason, $userId);
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
     * Get pending enrollment requests for a teacher's courses
     */
    public function pendingRequests()
    {
        $userId = session()->get('user_id');
        $userRole = session()->get('role');
        
        // Only teachers and admins can view pending requests
        if (!in_array($userRole, ['teacher', 'admin'])) {
            return $this->failForbidden('You do not have permission to view enrollment requests.');
        }
        
        try {
            if ($userRole === 'admin') {
                $requests = $this->enrollmentModel->getPendingRequestsForTeacher(null);
            } else {
                $requests = $this->enrollmentModel->getPendingRequestsForTeacher($userId);
            }
            return $this->respond($requests);
        } catch (\Exception $e) {
            return $this->failServerError('An error occurred while fetching enrollment requests.');
        }
    }

    /**
     * Render manage requests page (web view) for teachers/admins
     */
    public function manageRequests()
    {
        $userId = session()->get('user_id');
        $userRole = session()->get('role');

        if (!in_array($userRole, ['teacher', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'You do not have permission to view enrollment requests.');
        }

        // Get pending requests (teachers see only their courses, admins see all)
        if ($userRole === 'admin') {
            $requests = $this->enrollmentModel->getPendingRequestsForTeacher(null);
        } else {
            $requests = $this->enrollmentModel->getPendingRequestsForTeacher($userId);
        }

        return view('enrollment/manage_requests', [
            'requests' => $requests
        ]);
    }
    
    /**
     * Get a student's enrollments
     */
    public function studentEnrollments($studentId = null)
    {
        $userId = session()->get('user_id');
        $userRole = session()->get('role');
        
        // If studentId is not provided, use the current user's ID
        if ($studentId === null) {
            $studentId = $userId;
        } else {
            // Only allow admins/teachers to view other students' enrollments
            if (!in_array($userRole, ['admin', 'teacher'])) {
                return $this->failForbidden('You do not have permission to view these enrollments.');
            }
        }
        
        $status = $this->request->getGet('status');
        $schoolYear = $this->request->getGet('school_year');
        $semester = $this->request->getGet('semester');
        
        try {
            $enrollments = $this->enrollmentModel->getStudentEnrollments($studentId, $status, $schoolYear, $semester);
            return $this->respond($enrollments);
        } catch (\Exception $e) {
            return $this->failServerError('An error occurred while fetching enrollments.');
        }
    }
}
