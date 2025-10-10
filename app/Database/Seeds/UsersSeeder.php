<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'     => 'Admin User',
                'email'    => 'admin@lms.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'     => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'     => 'John Teacher',
                'email'    => 'john.teacher@lms.com',
                'password' => password_hash('teacher123', PASSWORD_DEFAULT),
                'role'     => 'teacher',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'     => 'Jane Teacher',
                'email'    => 'jane.teacher@lms.com',
                'password' => password_hash('teacher123', PASSWORD_DEFAULT),
                'role'     => 'teacher',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'     => 'Alice Student',
                'email'    => 'alice.student@lms.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'role'     => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'     => 'Bob Student',
                'email'    => 'bob.student@lms.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'role'     => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'     => 'Charlie Student',
                'email'    => 'charlie.student@lms.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'role'     => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'     => 'Diana Student',
                'email'    => 'diana.student@lms.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'role'     => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'     => 'Eve Student',
                'email'    => 'eve.student@lms.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'role'     => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Using Query Builder
        foreach ($data as $user) {
            $existing = $this->db->table('users')->where('email', $user['email'])->get()->getRow();
            if (!$existing) {
                $this->db->table('users')->insert($user);
            }
        }
    }
}
