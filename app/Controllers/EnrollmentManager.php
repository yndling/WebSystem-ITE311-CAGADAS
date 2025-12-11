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
            
            if ($userRole === 'admin') {
                $enrollments = $this->enrollmentModel
                    ->where('status', 'pending')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->select('enrollments.*, users.first_name, users.last_name, users.email, courses.title as course_title')
                    ->findAll();
            } else {
                $enrollments = $this->enrollmentModel
                    ->where('enrollments.status', 'pending')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('courses.user_id', $userId)
                    ->select('enrollments.*, users.first_name, users.last_name, users.email, courses.title as course_title')
                    ->findAll();
            }

            return view('enrollments/manage_requests', [
                'title' => 'Manage Enrollment Requests',
                'enrollments' => $enrollments,
                'isAdmin' => ($userRole === 'admin')
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in EnrollmentManager::index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading enrollment requests.');
        }
    }
}
