<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;

class Course extends BaseController
{
    public function enroll()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not logged in']);
        }

        $course_id = (int) $this->request->getPost('course_id');
        if (!$course_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Course ID required']);
        }

        $db = \Config\Database::connect();
        if ($db->table('courses')->where('id', $course_id)->countAllResults() === 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Course not found']);
        }

        $enrollmentModel = new EnrollmentModel();
        if ($enrollmentModel->isAlreadyEnrolled(session()->get('user_id'), $course_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Already enrolled in this course']);
        }

        $result = $enrollmentModel->enrollUser([
            'user_id' => session()->get('user_id'),
            'course_id' => $course_id
        ]);

        if ($result) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Enrolled successfully']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Enrollment failed']);
        }
    }
}
