<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CoursesSeeder extends Seeder
{
    public function run()
    {
        // Get teacher IDs
        $instructors = $this->db->table('users')->where('role', 'teacher')->get()->getResultArray();
        if (empty($instructors)) {
            echo "No teachers found. Please run UsersSeeder first.\n";
            return;
        }

        $teacherIds = array_column($instructors, 'id');

        $data = [
            [
                'title'       => 'Introduction to PHP',
                'description' => 'Learn the basics of PHP programming language.',
                'teacher_id'  => $teacherIds[0] ?? 2, // Default to ID 2 if not found
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Advanced JavaScript',
                'description' => 'Deep dive into advanced JavaScript concepts and frameworks.',
                'teacher_id'  => $teacherIds[1] ?? 3, // Default to ID 3
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Web Development Basics',
                'description' => 'Fundamentals of HTML, CSS, and responsive design.',
                'teacher_id'  => $teacherIds[0] ?? 2,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Database Design',
                'description' => 'Principles of relational database design and SQL.',
                'teacher_id'  => $teacherIds[1] ?? 3,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        // Using Query Builder
        foreach ($data as $course) {
            $existing = $this->db->table('courses')->where('title', $course['title'])->get()->getRow();
            if (!$existing) {
                $this->db->table('courses')->insert($course);
            }
        }
    }
}
