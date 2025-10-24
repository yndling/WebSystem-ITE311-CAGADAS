<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;
use App\Models\UserModel;


class Auth extends BaseController
{


    public function register()
    {
        log_message('info', 'Register method called. Request method: ' . $this->request->getMethod());
        log_message('info', 'Request URI: ' . $this->request->getUri());

        if ($this->request->getMethod() === 'POST') {
            log_message('info', 'POST request detected in register.');


             $rules = [
                'name' => 'required|min_length[3]|max_length[255]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]',
                'role' => 'required|in_list[student,teacher,admin]',
            ];

          
            $postData = $this->request->getPost();
            if (isset($postData['password'])) unset($postData['password']);
            if (isset($postData['password_confirm'])) unset($postData['password_confirm']);
            log_message('info', 'POST data: ' . $this->safeJsonEncode($postData));

      

            if (!$this->validate($rules)) {
                log_message('error', 'Validation failed: ' . $this->safeJsonEncode($this->validator->getErrors()));
                return view('auth/register', ['validation' => $this->validator]);
            }

            log_message('info', 'Validation passed.');

       
            $hashedPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

    
            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'password' => $hashedPassword,
                'role' => $this->request->getPost('role'),
            ];

            log_message('info', 'Prepared data for insert: ' . $this->safeJsonEncode($data));

    
            $db = \Config\Database::connect();
            if (!$db->connID) {
                log_message('error', 'Database connection failed.');
                return view('auth/register', ['error' => 'Database connection failed. Please try again later.']);
            }
            log_message('info', 'Database connection successful.');

 
            $userModel = new UserModel();
            try {
                $userId = $userModel->createUser($data);
                log_message('info', 'UserModel createUser returned: ' . $userId);

                if ($userId) {
                    log_message('info', 'Registration successful, redirecting to login.');
                    
                    session()->setFlashdata('success', 'Registration successful! Please log in.');
                    return redirect()->to('/login');
                } else {
                    // Handle insert failure
                    $error = $db->error();
                    log_message('error', 'Registration failed: UserModel->insert returned false. DB Error: ' . $this->safeJsonEncode($error));
                    return view('auth/register', ['error' => 'Registration failed. Please try again.']);
                }
            } catch (\Exception $e) {
                // Handle database error
                log_message('error', 'Registration error: ' . $e->getMessage());
                return view('auth/register', ['error' => 'An error occurred during registration. Please try again.']);
            }
        } else {
            log_message('info', 'GET request to register, showing form.');
        }

        return view('auth/register');
    }

    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            // Validation rules
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required',
                'role' => 'required|in_list[student,teacher,admin]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('validation', $this->validator);
            }

            // Find user by email using UserModel
            $userModel = new UserModel();
            try {
                $user = $userModel->findByEmail($this->request->getPost('email'));

                if (!$user || !$userModel->verifyPassword($this->request->getPost('password'), $user['password'])) {
                    return redirect()->back()->withInput()->with('error', 'Invalid email or password');
                }

                // Check if the provided role matches the user's role in the database
                $providedRole = $this->request->getPost('role');
                if ($providedRole !== $user['role']) {
                    return redirect()->back()->withInput()->with('role_error', 'Invalid role.');
                }

                // Set session
                session()->set([
                    'user_id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'logged_in' => true,
                ]);

                // Set flash message
                session()->setFlashdata('success', 'Welcome, ' . $user['name'] . '!');

                // Redirect to unified dashboard for all roles
                return redirect()->to('/dashboard');
            } catch (\Exception $e) {
                // Handle database error
                log_message('error', 'Login error: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'An error occurred during login. Please try again.');
            }
        }

        return view('auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function dashboard()
    {
        $response = service('response');
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        $userModel = new UserModel();
        $data = [
            'role' => $role,
            'user_id' => session()->get('user_id'),
            'name' => session()->get('name'),
            'email' => session()->get('email')
        ];

        if ($role === 'admin') {
            $data['total_users'] = $userModel->getTotalUsers();
            $data['admin_count'] = $userModel->getUserCountByRole('admin');
            $data['teacher_count'] = $userModel->getUserCountByRole('teacher');
            $data['student_count'] = $userModel->getUserCountByRole('student');
        } elseif ($role === 'teacher') {
            $data['total_students'] = $userModel->getUserCountByRole('student');
            $db = \Config\Database::connect();
            $data['my_courses'] = $db->table('courses')->where('teacher_id', session()->get('user_id'))->countAllResults();
        } elseif ($role === 'student') {
            try {
                $enrollmentModel = new \App\Models\EnrollmentModel();
                $data['enrolled_courses'] = $enrollmentModel->getUserEnrollments(session()->get('user_id')) ?: [];

                $db = \Config\Database::connect();
                $user_id = session()->get('user_id');
                $data['available_courses'] = $db->query("SELECT * FROM courses WHERE id NOT IN (SELECT course_id FROM enrollments WHERE user_id = ?)", [$user_id])->getResultArray() ?: [];

                // Fetch enrolled courses data for materials
                $data['enrolled_courses_data'] = $db->query("SELECT c.id, c.title as name FROM courses c JOIN enrollments e ON c.id = e.course_id WHERE e.user_id = ?", [$user_id])->getResultArray() ?: [];
            } catch (\Exception $e) {
                log_message('error', 'Error fetching student dashboard data: ' . $e->getMessage());
                $data['enrolled_courses'] = [];
                $data['available_courses'] = [];
                $data['enrolled_courses_data'] = [];
            }
        } else {
            return redirect()->to('/login')->with('error', 'Invalid role. Please log in again.');
        }

        return view('auth/dashboard', $data);
    }

    /**
     * Safely encode data to JSON, handling failures
     */
    private function safeJsonEncode($data)
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            return '[JSON Encode Failed: ' . json_last_error_msg() . ']';
        }
        return $json;
    }
}
