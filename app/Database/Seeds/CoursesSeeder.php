<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CoursesSeeder extends Seeder
{
    public function run()
    {
        try {
            // Check if the courses table exists
            if (!$this->db->tableExists('courses')) {
                echo "Error: The 'courses' table does not exist. Please run migrations first.\n";
                echo "Run: php spark migrate\n";
                return;
            }

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

            // Using Query Builder with transaction for better error handling
            $this->db->transStart();
            
            foreach ($data as $course) {
                // First check if the course already exists
                $existing = $this->db->table('courses')
                    ->where('title', $course['title'])
                    ->countAllResults();
                
                if ($existing === 0) {
                    $this->db->table('courses')->insert($course);
                    echo "Added course: " . $course['title'] . "\n";
                } else {
                    echo "Skipped existing course: " . $course['title'] . "\n";
                }
            }
            
            $this->db->transComplete();
            
            $error = $this->db->error();
            if (!empty($error['message'])) {
                echo "Error occurred while seeding courses.\n";
                print_r($error);
            } else {
                echo "\nCourses seeded successfully!\n";
            }
            
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            if ($this->db->transStatus() !== false) {
                $this->db->transRollback();
            }
        }
    }
}
