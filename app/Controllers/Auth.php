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
        if ($this->request->getMethod() === 'post') {
            // Validation rules
            $rules = [
                'name' => 'required|min_length[3]|max_length[255]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]',
                'role' => 'required|in_list[student,instructor,admin]',
            ];

            if (!$this->validate($rules)) {
                return view('auth/register', ['validation' => $this->validator]);
            }

            // Hash password
            $hashedPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

            // Prepare user data
            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'password' => $hashedPassword,
                'role' => $this->request->getPost('role'),
            ];

            // Save to database using UserModel
            try {
                $userId = $this->userModel->createUser($data);

                if ($userId) {
                    // Set flash message
                    session()->setFlashdata('success', 'Registration successful! Please log in.');
                    return redirect()->to('/login');
                } else {
                    // Handle insert failure
                    return view('auth/register', ['error' => 'Registration failed. Please try again.']);
                }
            } catch (\Exception $e) {
                // Handle database error
                log_message('error', 'Registration error: ' . $e->getMessage());
                return view('auth/register', ['error' => 'An error occurred during registration. Please try again.']);
            }
        }

        return view('auth/register');
    }

    public function login()
    {
        if ($this->request->getMethod() === 'post') {
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
        return redirect()->to('/');
    }

    public function dashboard()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('auth/dashboard');
    }
}
