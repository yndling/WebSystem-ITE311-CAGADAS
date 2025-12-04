<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use Config\Services;

class Admin extends BaseController
{
    public function index()
    {
        //
    }

    public function dashboard()
    {
        return view('admin_dashboard');
    }

    /**
     * Return JSON list of users (include deleted state)
     */
    public function users()
    {
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $userModel = new UserModel();
        // include soft-deleted records so admin can see deleted flag
        $users = $userModel->withDeleted()->findAll();

        // include whether deleted
        $out = [];
        foreach ($users as $u) {
            $out[] = [
                'id' => $u['id'],
                'name' => $u['name'],
                'email' => $u['email'],
                'role' => $u['role'],
                'deleted_at' => isset($u['deleted_at']) ? $u['deleted_at'] : null,
            ];
        }

        return $this->response->setJSON(['users' => $out, 'current_user_id' => session()->get('user_id')]);
    }

    /**
     * Create a new user (admin)
     */
    public function createUser()
    {
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role' => 'required|in_list[student,teacher,admin]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON(['errors' => $this->validator->getErrors()]);
        }

        $userModel = new UserModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
        ];

        try {
            $id = $userModel->insert($data);
            return $this->response->setJSON(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update user (name, email, role). Admin cannot change their own role.
     */
    public function updateUser($id = null)
    {
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Missing user id']);
        }

        $userModel = new UserModel();
        $existing = $userModel->withDeleted()->find($id);
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }

        $post = $this->request->getPost();

        // If trying to change role of self, forbid
        if (isset($post['role']) && (int)$id === (int)session()->get('user_id')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Cannot change your own role']);
        }

        $update = [];
        if (isset($post['name'])) $update['name'] = $post['name'];
        if (isset($post['email'])) $update['email'] = $post['email'];
        if (isset($post['role'])) $update['role'] = $post['role'];

        // If password provided, update it
        if (!empty($post['password'])) {
            $update['password'] = password_hash($post['password'], PASSWORD_DEFAULT);
        }

        try {
            $userModel->update($id, $update);
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }

    /**
     * Soft-delete a user (mark deleted_at). Admin cannot delete themselves.
     */
    public function deleteUser($id = null)
    {
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Missing user id']);
        }

        if ((int)$id === (int)session()->get('user_id')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Cannot delete your own account']);
        }

        $userModel = new UserModel();
        $existing = $userModel->find($id);
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }

        try {
            $userModel->delete($id);
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }
}
