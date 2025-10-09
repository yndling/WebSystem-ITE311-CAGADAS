<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'enrolled_at'];
    protected $useTimestamps = false;

    /**
     * Enroll a user in a course
     */
    public function enrollUser(array $data)
    {
        if (!isset($data['enrolled_at'])) {
            $data['enrolled_at'] = date('Y-m-d H:i:s');
        }
        return $this->insert($data);
    }

    /**
     * Get all courses a user is enrolled in
     */
    public function getUserEnrollments(int $user_id)
    {
        return $this->select('enrollments.*, courses.title as course_name, courses.description as course_description')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('enrollments.user_id', $user_id)
                    ->findAll();
    }

    /**
     * Check if a user is already enrolled in a specific course
     */
    public function isAlreadyEnrolled(int $user_id, int $course_id): bool
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->countAllResults() > 0;
    }
}
