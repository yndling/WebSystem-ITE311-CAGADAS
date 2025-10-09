<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'teacher_id', 'created_at', 'updated_at'];
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
}
