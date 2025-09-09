<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;
use App\Models\UserModel;

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
                // Debug: print validation errors
                // dd($this->validator->getErrors());
                return view('auth/register', ['validation' => $this->validator]);
            }

            // Hash password
            $hashedPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

            // Save to database
            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'password' => $hashedPassword,
                'role' => $this->request->getPost('role'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->table('users')->insert($data);

            // Set flash message
            session()->setFlashdata('success', 'Registration successful! Please log in.');

            return redirect()->to('/login');
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
                return view('auth/login', ['validation' => $this->validator]);
            }

            // Fix: Add redirect after failed validation to prevent silent failure
            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('validation', $this->validator);
            }

            // Check database
            $user = $this->db->table('users')->where('email', $this->request->getPost('email'))->get()->getRow();

            if (!$user || !password_verify($this->request->getPost('password'), $user->password)) {
                return view('auth/login', ['error' => 'Invalid email or password']);
            }

            // Remove role check here to allow login regardless of selected role
            // if ($user->role !== $this->request->getPost('role')) {
            //     return view('auth/login', ['error' => 'Selected role does not match your account role']);
            // }

            // Set session
            session()->set([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'logged_in' => true,
            ]);

            // Set flash message
            session()->setFlashdata('success', 'Welcome back, ' . $user->name . '!');

            return redirect()->to('/dashboard');
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
