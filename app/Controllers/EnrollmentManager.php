<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\CourseModel;

class EnrollmentManager extends BaseController
{
    protected $enrollmentModel;
    protected $courseModel;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->courseModel = new CourseModel();
        helper(['form', 'url']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Display the manage enrollments page for teachers/admins
     */
    public function index()
    {
        try {
            log_message('debug', 'EnrollmentManager::index accessed');
            
            $userRole = session()->get('role');
            $userId = session()->get('user_id');
            
            if (!in_array($userRole, ['admin', 'teacher'])) {
                return redirect()->to('/dashboard')->with('error', 'Access denied.');
            }

            // Get enrollment requests based on user role
            $enrollments = [];
            
            // Base query
            $this->enrollmentModel->select('enrollments.*, users.first_name, users.last_name, users.email, courses.title as course_title')
                                ->join('users', 'users.id = enrollments.user_id')
                                ->join('courses', 'courses.id = enrollments.course_id')
                                ->where('enrollments.status', 'pending');
            
            // Add role-specific conditions
            if ($userRole !== 'admin') {
                $this->enrollmentModel->where('courses.user_id', $userId);
            }
            
            // Execute the query
            $enrollments = $this->enrollmentModel->findAll();
            
            // Debug: Log the query and results
            log_message('debug', 'Enrollment query: ' . $this->enrollmentModel->getLastQuery());
            log_message('debug', 'Found ' . count($enrollments) . ' enrollment requests');

            return view('enrollment/manage_requests', [
                'title' => 'Manage Enrollment Requests',
                'enrollments' => $enrollments,
                'isAdmin' => ($userRole === 'admin')
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in EnrollmentManager::index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading enrollment requests.');
        }
    }

    /**
     * Reject an enrollment request
     * Handles both AJAX and regular form submissions
     */
    public function reject($enrollmentId = null)
    {
        // Check if this is an AJAX request
        $isAjax = $this->request->isAJAX();
        
        // Set up response array
        $response = [
            'success' => false,
            'message' => '',
            'errors' => []
        ];

        try {
            if (!$enrollmentId) {
                throw new \RuntimeException('No enrollment ID provided.');
            }

            // Check if user has permission
            $userRole = session()->get('role');
            if (!in_array($userRole, ['admin', 'teacher'])) {
                throw new \RuntimeException('Access denied.');
            }

            $userId = session()->get('user_id');
            
            // Get the enrollment with course info
            $enrollment = $this->enrollmentModel->find($enrollmentId);
            
            if (!$enrollment) {
                throw new \RuntimeException('Enrollment not found.');
            }

            // If teacher, verify they teach this course
            if ($userRole === 'teacher') {
                $course = $this->courseModel->find($enrollment['course_id']);
                if (!$course || $course['teacher_id'] != $userId) {
                    throw new \RuntimeException('You do not have permission to manage this enrollment.');
                }
            }

            // Get rejection reason from post data
            $reason = $this->request->getPost('reason') ?? 'Enrollment request was rejected.';
            
            // If it's an AJAX request, get JSON data
            if ($isAjax && $this->request->getJSON()) {
                $json = $this->request->getJSON();
                if (isset($json->reason)) {
                    $reason = $json->reason;
                }
            }

            // Update enrollment status to rejected
            $updated = $this->enrollmentModel->update($enrollmentId, [
                'status' => 'rejected',
                'approved_by' => $userId,
                'approved_at' => date('Y-m-d H:i:s'),
                'rejection_reason' => $reason
            ]);

            if ($updated) {
                $response['success'] = true;
                $response['message'] = 'Enrollment request has been rejected successfully.';
                
                if ($isAjax) {
                    return $this->response->setJSON($response);
                }
                return redirect()->back()->with('success', $response['message']);
            } else {
                throw new \RuntimeException('Failed to update enrollment status.');
            }
            
        } catch (\Exception $e) {
            $errorMsg = 'Error rejecting enrollment: ' . $e->getMessage();
            log_message('error', $errorMsg);
            
            $response['message'] = 'Failed to reject enrollment: ' . $e->getMessage();
            
            if ($isAjax) {
                return $this->response->setStatusCode(400)->setJSON($response);
            }
            return redirect()->back()->with('error', $response['message']);
        }
    }

    /**
     * Test method to debug enrollment requests
     */
    public function test()
    {
        try {
            // Test database connection
            $db = db_connect();
            if (!$db->connect()) {
                die('Could not connect to database');
            }
            
            // Test users table
            $users = $db->table('users')->countAllResults();
            echo "Total users: $users<br>";
            
            // Test courses table
            $courses = $db->table('courses')->countAllResults();
            echo "Total courses: $courses<br>";
            
            // Test enrollments table
            $enrollments = $db->table('enrollments')
                             ->join('users', 'users.id = enrollments.user_id')
                             ->join('courses', 'courses.id = enrollments.course_id')
                             ->where('enrollments.status', 'pending')
                             ->countAllResults();
            echo "Pending enrollments: $enrollments<br>";
            
            // Show the first 5 enrollment requests
            $pending = $db->table('enrollments')
                         ->select('enrollments.*, users.first_name, users.last_name, courses.title as course_title')
                         ->join('users', 'users.id = enrollments.user_id')
                         ->join('courses', 'courses.id = enrollments.course_id')
                         ->where('enrollments.status', 'pending')
                         ->get(5)
                         ->getResultArray();
            
            echo "<h3>First 5 pending enrollments:</h3>";
            echo "<pre>";
            print_r($pending);
            echo "</pre>";
            
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
