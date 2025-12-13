<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'first_name', 'last_name', 'email', 'password', 'role', 'created_at', 'updated_at', 'deleted_at'];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    /**
     * Find user by email
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Create a new user
     */
    public function createUser(array $data)
    {
        return $this->insert($data);
    }

    /**
     * Verify user password
     */
    public function verifyPassword(string $password, string $hashedPassword): bool
    {
        return password_verify($password, $hashedPassword);
    }

    /**
     * Get total number of users
     */
    public function getTotalUsers()
    {
        return $this->countAll();
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $role)
    {
        return $this->where('role', $role)->findAll();
    }

    /**
     * Get count of users by role
     */
    public function getUserCountByRole(string $role)
    {
        return $this->where('role', $role)->countAllResults();
    }
}
