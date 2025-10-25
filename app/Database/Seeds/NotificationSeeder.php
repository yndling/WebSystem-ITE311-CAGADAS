<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Clear existing notifications first
        $this->db->table('notifications')->truncate();
        
        $data = [
            [
                'user_id' => 1,
                'message' => 'Welcome to the LMS! You have been enrolled in Introduction to Programming.',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => 1,
                'message' => 'New course material uploaded for Web Development.',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => 1,
                'message' => 'Test notification with Mark as Read button - should be visible!',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => 5,
                'message' => 'Your assignment for Data Structures has been graded.',
                'is_read' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('notifications')->insertBatch($data);
        echo "Fresh notifications inserted for testing!\n";
    }
}
