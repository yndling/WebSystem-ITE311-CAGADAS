<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;
use App\Models\UserModel;

/**
 * Authentication Controller
 * Handles user registration, login, logout, and dashboard access
 */
class Auth extends BaseController
{
    protected $db;
    protected $userModel;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->userModel = new UserModel();
    }

    public function register()
    {
        log_message('info', 'Register method called. Request method: ' . $this->request->getMethod());
        log_message('info', 'Request URI: ' . $this->request->getUri());

        if ($this->request->getMethod() === 'POST') {
            log_message('info', 'POST request detected in register.');

            // Log POST data (without sensitive info)
            $postData = $this->request->getPost();
            if (isset($postData['password'])) unset($postData['password']);
            if (isset($postData['password_confirm'])) unset($postData['password_confirm']);
            log_message('info', 'POST data: ' . json_encode($postData));

            // Validation rules
            $rules = [
                'name' => 'required|min_length[3]|max_length[255]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]',
                'role' => 'required|in_list[student,instructor,admin]',
            ];

            if (!$this->validate($rules)) {
                log_message('error', 'Validation failed: ' . json_encode($this->validator->getErrors()));
                return view('auth/register', ['validation' => $this->validator]);
            }

            log_message('info', 'Validation passed.');

            // Hash password
            $hashedPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

            // Prepare user data
            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'password' => $hashedPassword,
                'role' => $this->request->getPost('role'),
            ];

            log_message('info', 'Prepared data for insert: ' . json_encode($data));

            // Verify database connection
            $db = \Config\Database::connect();
            if (!$db->connID) {
                log_message('error', 'Database connection failed.');
                return view('auth/register', ['error' => 'Database connection failed. Please try again later.']);
            }
            log_message('info', 'Database connection successful.');

            // Save to database using UserModel
            try {
                $userId = $this->userModel->createUser($data);
                log_message('info', 'UserModel createUser returned: ' . $userId);

                if ($userId) {
                    log_message('info', 'Registration successful, redirecting to login.');
                    // Set flash message
                    session()->setFlashdata('success', 'Registration successful! Please log in.');
                    return redirect()->to('/login');
                } else {
                    // Handle insert failure
                    $error = $db->error();
                    log_message('error', 'Registration failed: UserModel->insert returned false. DB Error: ' . json_encode($error));
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
                'role' => 'required|in_list[student,instructor,admin]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('validation', $this->validator);
            }

            // Find user by email using UserModel
            try {
                $user = $this->userModel->findByEmail($this->request->getPost('email'));

                if (!$user || !$this->userModel->verifyPassword($this->request->getPost('password'), $user['password'])) {
                    return redirect()->back()->withInput()->with('error', 'Invalid email or password');
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
                session()->setFlashdata('success', 'Welcome back, ' . $user['name'] . '!');

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

    /**
     * Override the dashboard method to add cache control headers
     */
    public function dashboard()
    {
        $response = service('response');
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('auth/dashboard');
    }
}
