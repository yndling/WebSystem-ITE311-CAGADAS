<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\MaterialModel;
use App\Models\NotificationModel;
use App\Models\CourseModel;

class Course extends BaseController
{
    public function enroll()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not logged in']);
        }

        $course_id = (int) $this->request->getPost('course_id');
        if (!$course_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Course ID required']);
        }

        $db = \Config\Database::connect();
        if ($db->table('courses')->where('id', $course_id)->countAllResults() === 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Course not found']);
        }

        $enrollmentModel = new EnrollmentModel();
        if ($enrollmentModel->isAlreadyEnrolled(session()->get('user_id'), $course_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Already enrolled in this course']);
        }

        $result = $enrollmentModel->enrollUser([
            'user_id' => session()->get('user_id'),
            'course_id' => $course_id
        ]);

        if ($result) {
            // Create notification for successful enrollment
            $course = $db->table('courses')->where('id', $course_id)->get()->getRowArray();
            $notificationModel = new NotificationModel();
            $notificationModel->insert([
                'user_id' => session()->get('user_id'),
                'message' => 'You have been enrolled in ' . $course['title'],
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            return $this->response->setJSON(['status' => 'success', 'message' => 'Enrolled successfully']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Enrollment failed']);
        }
    }

    public function myCourses()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $db = \Config\Database::connect();
        $courses = $db->table('courses')->where('teacher_id', session()->get('user_id'))->get()->getResultArray();

        return view('course/list', [
            'courses' => $courses
        ]);
    }

    public function create()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'title' => 'required|min_length[3]|max_length[255]',
                'description' => 'required|min_length[10]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $db = \Config\Database::connect();
            $data = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'teacher_id' => session()->get('user_id'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $db->table('courses')->insert($data);

            return redirect()->to('/course/my')->with('success', 'Course created successfully!');
        }

        return view('course/create');
    }

    public function browse()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        if ($userRole !== 'student') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $db = \Config\Database::connect();
        $user_id = session()->get('user_id');
        $search = $this->request->getGet('search') ?? '';

        // Get available courses (not enrolled), with optional search
        $query = "SELECT * FROM courses WHERE id NOT IN (SELECT course_id FROM enrollments WHERE user_id = ?)";
        $params = [$user_id];
        if (!empty($search)) {
            $query .= " AND (title LIKE ? OR description LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }
        $available_courses = $db->query($query, $params)->getResultArray() ?: [];

        // Get enrolled courses data for materials
        $enrolled_courses_data = $db->query("SELECT c.id, c.title as name FROM courses c JOIN enrollments e ON c.id = e.course_id WHERE e.user_id = ?", [$user_id])->getResultArray() ?: [];

        return view('course/browse', [
            'available_courses' => $available_courses,
            'enrolled_courses_data' => $enrolled_courses_data,
            'search' => $search
        ]);
    }

    public function view($course_id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        $course = $db->table('courses')->where('id', $course_id)->get()->getRowArray();

        if (!$course) {
            return redirect()->to('/dashboard')->with('error', 'Course not found');
        }

        // Check if user is enrolled (for students) or is teacher/admin
        $userRole = session()->get('role');
        $enrollmentModel = new EnrollmentModel();

        if ($userRole === 'student' && !$enrollmentModel->isAlreadyEnrolled(session()->get('user_id'), $course_id)) {
            return redirect()->to('/dashboard')->with('error', 'You are not enrolled in this course');
        }

        $materialModel = new MaterialModel();
        $materials = $materialModel->getMaterialsByCourse($course_id);

        return view('course/view', [
            'course' => $course,
            'materials' => $materials,
            'course_id' => $course_id
        ]);
    }

    /**
     * Search for courses by name or description
     *
     * @return \CodeIgniter\HTTP\ResponseInterface|string
     */
    public function search()
    {
        $searchTerm = $this->request->getGet('searchTerm') ?? $this->request->getPost('searchTerm');
        $scope = $this->request->getGet('scope') ?? $this->request->getPost('scope') ?? 'available';
        $user_id = session()->get('user_id');
        
        $db = \Config\Database::connect();
        $builder = $db->table('courses');
        
        // If not admin/teacher, only show courses the user is not already enrolled in
        $userRole = session()->get('role');
        if ($userRole === 'student') {
            // Respect optional scope: 'available' (default), 'enrolled', or 'all'
            if ($scope === 'available') {
                $builder->whereNotIn('id', function($builder) use ($user_id) {
                    return $builder->select('course_id')
                                 ->from('enrollments')
                                 ->where('user_id', $user_id);
                });
            } elseif ($scope === 'enrolled') {
                // Only return courses the user is enrolled in
                $builder->join('enrollments e', 'e.course_id = courses.id')
                        ->where('e.user_id', $user_id);
            } // if 'all' do not limit by enrollment
        }
        
        if (!empty($searchTerm)) {
            $builder->groupStart()
                   ->like('title', $searchTerm, 'both')
                   ->orLike('description', $searchTerm, 'both')
                   ->groupEnd();
        }
        
        $courses = $builder->get()->getResultArray();

        // Add a simple flag indicating whether the current user is enrolled in each returned course
        if ($userRole === 'student') {
            $enrolledRows = $db->table('enrollments')->select('course_id')->where('user_id', $user_id)->get()->getResultArray();
            $enrolledIds = array_column($enrolledRows, 'course_id');
            foreach ($courses as &$c) {
                $c['is_enrolled'] = in_array($c['id'], $enrolledIds);
            }
            unset($c);
        }
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $courses
            ]);
        }
        
        // For non-AJAX requests, redirect to browse with search term
        return redirect()->to('/course/browse?search=' . urlencode($searchTerm));
    }
    
    /**
     * Get enrolled courses for the current user
     */
    public function myEnrolled()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not logged in'
            ]);
        }
        
        $user_id = session()->get('user_id');
        $db = \Config\Database::connect();
        
        $courses = $db->table('enrollments e')
                     ->select('c.*')
                     ->join('courses c', 'e.course_id = c.id')
                     ->where('e.user_id', $user_id)
                     ->get()
                     ->getResultArray();

        // mark returned courses as enrolled
        foreach ($courses as &$c) {
            $c['is_enrolled'] = true;
        }
        unset($c);
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $courses
        ]);
    }
    
    /**
     * Get available courses (not enrolled by the current user)
     */
    public function available()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not logged in'
            ]);
        }
        
        $user_id = session()->get('user_id');
        $db = \Config\Database::connect();
        
        // Get courses not enrolled by the user
        $courses = $db->table('courses c')
                     ->whereNotIn('c.id', function($builder) use ($user_id) {
                         return $builder->select('course_id')
                                      ->from('enrollments')
                                      ->where('user_id', $user_id);
                     })
                     ->get()
                     ->getResultArray();

        // mark available courses as not enrolled
        foreach ($courses as &$c) {
            $c['is_enrolled'] = false;
        }
        unset($c);
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $courses
        ]);
    }
}
