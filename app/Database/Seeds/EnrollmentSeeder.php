<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run()
    {
        // Get student IDs
        $students = $this->db->table('users')->where('role', 'student')->get()->getResultArray();
        if (empty($students)) {
            echo "No students found. Please run UsersSeeder first.\n";
            return;
        }

        // Get course IDs
        $courses = $this->db->table('courses')->select('id')->get()->getResultArray();
        if (empty($courses)) {
            echo "No courses found. Please run CoursesSeeder first.\n";
            return;
        }

        $studentIds = array_column($students, 'id');
        $courseIds = array_column($courses, 'id');

        echo "Student IDs: " . implode(', ', $studentIds) . "\n";
        echo "Course IDs: " . implode(', ', $courseIds) . "\n";

        $data = [
            [
                'user_id'         => $studentIds[0], // Alice Student
                'course_id'       => $courseIds[0], // Introduction to PHP
                'enrollment_date' => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'         => $studentIds[0], // Alice Student
                'course_id'       => $courseIds[1], // Advanced JavaScript
                'enrollment_date' => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'         => $studentIds[1], // Bob Student
                'course_id'       => $courseIds[2], // Web Development Basics
                'enrollment_date' => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'         => $studentIds[1], // Bob Student
                'course_id'       => $courseIds[3], // Database Design
                'enrollment_date' => date('Y-m-d H:i:s'),
            ],
        ];

        // Clear existing enrollments to ensure fresh data
        $this->db->table('enrollments')->emptyTable();
        echo "Cleared existing enrollments.\n";

        // Using Query Builder
        foreach ($data as $enrollment) {
            $insertId = $this->db->table('enrollments')->insert($enrollment);
            if ($insertId) {
                echo "Inserted enrollment: user_id={$enrollment['user_id']}, course_id={$enrollment['course_id']}, id=$insertId\n";
            } else {
                echo "Failed to insert enrollment: user_id={$enrollment['user_id']}, course_id={$enrollment['course_id']}\n";
            }
        }
        echo "Seeding completed. Total enrollments: " . $this->db->table('enrollments')->countAll() . "\n";
    }
}
