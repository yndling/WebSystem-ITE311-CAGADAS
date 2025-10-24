<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Check if users already exist
        $existingUsers = $this->db->table('users')->whereIn('email', [
            'student@example.com',
            'teacher@example.com',
            'admin@example.com'
        ])->countAllResults();

        if ($existingUsers > 0) {
            echo "Sample users already exist. Skipping seeder.\n";
            return;
        }

        $data = [
            [
                'name' => 'Student User',
                'email' => 'student@example.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Teacher User',
                'email' => 'teacher@example.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'teacher',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
        echo "Sample users inserted successfully.\n";
    }
}
