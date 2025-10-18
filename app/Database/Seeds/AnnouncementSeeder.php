<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Welcome to the New Semester',
                'content' => 'We are excited to start the new semester. Please check your course schedules and enroll in your classes.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'System Maintenance Notice',
                'content' => 'The portal will undergo maintenance on Sunday from 2 AM to 4 AM. Please plan accordingly.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('announcements')->insertBatch($data);
    }
}
