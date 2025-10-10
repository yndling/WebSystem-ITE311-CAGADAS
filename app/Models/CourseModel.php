<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'teacher_id', 'created_at', 'updated_at'];
    protected $useTimestamps = true;

    /**
     * Get all courses
     */
    public function getAllCourses()
    {
        return $this->findAll();
    }

    /**
     * Get course by ID
     */
    public function getCourseById(int $id)
    {
        return $this->find($id);
    }

    /**
     * Check if a course exists by ID
     */
    public function courseExists(int $course_id): bool
    {
        return $this->where('id', $course_id)->countAllResults() > 0;
    }

    /**
     * Get available courses for a specific user (excluding enrolled ones)
     */
    public function getAvailableCoursesForUser(int $user_id)
    {
        return $this->select('courses.*')
                    ->whereNotIn('courses.id', function($builder) use ($user_id) {
                        $builder->select('course_id')
                                ->from('enrollments')
                                ->where('user_id', $user_id);
                    })
                    ->findAll();
    }
}
