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

        $data = [];

        // Assign each student to 2 courses (cycling through courses)
        foreach ($studentIds as $index => $studentId) {
            $assignedCourses = [];
            for ($i = 0; $i < 2; $i++) {
                $courseIndex = ($index + $i) % count($courseIds);
                $courseId = $courseIds[$courseIndex];
                if (!in_array($courseId, $assignedCourses)) {
                    $assignedCourses[] = $courseId;
                    $data[] = [
                        'user_id'         => $studentId,
                        'course_id'       => $courseId,
                        'enrollment_date' => date('Y-m-d H:i:s'),
                    ];
                }
            }
        }

        // Using Query Builder
        foreach ($data as $enrollment) {
            $existing = $this->db->table('enrollments')
                ->where('user_id', $enrollment['user_id'])
                ->where('course_id', $enrollment['course_id'])
                ->get()->getRow();
            if (!$existing) {
                $this->db->table('enrollments')->insert($enrollment);
            }
        }
    }
}
