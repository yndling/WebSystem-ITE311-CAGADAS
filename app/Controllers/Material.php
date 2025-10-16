<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MaterialModel;
use App\Models\EnrollmentModel;

class Material extends BaseController
{
    protected $materialModel;
    protected $enrollmentModel;

    public function upload($course_id)
    {
        log_message('info', 'Upload method called with course_id: ' . $course_id);

        if (!session()->get('logged_in')) {
            log_message('error', 'User not logged in');
            return redirect()->to('/auth/login');
        }

        // Check if user is teacher or admin (assuming teachers can upload materials)
        $userRole = session()->get('role');
        log_message('info', 'User role: ' . $userRole);
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            log_message('error', 'Access denied for role: ' . $userRole);
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        log_message('info', 'Request method: ' . $this->request->getMethod());
        log_message('info', 'POST data: ' . print_r($this->request->getPost(), true));
        log_message('info', 'FILES data: ' . print_r($_FILES, true));

        if ($this->request->getMethod() === 'POST') {
            log_message('info', 'POST request detected');
            $file = $this->request->getFile('material_file');

            // Debug: Log file information
            log_message('info', 'File upload attempt: ' . print_r([
                'isValid' => $file->isValid(),
                'hasMoved' => $file->hasMoved(),
                'getName' => $file->getName(),
                'getClientName' => $file->getClientName(),
                'getExtension' => $file->getExtension(),
                'getSize' => $file->getSize(),
                'getError' => $file->getError(),
                'getErrorString' => $file->getErrorString()
            ], true));

            if (!$file->isValid()) {
                log_message('error', 'File is not valid: ' . $file->getErrorString());
                return redirect()->to(base_url('course/' . $course_id))->with('error', 'Invalid file upload: ' . $file->getErrorString());
            }

            if ($file->hasMoved()) {
                log_message('error', 'File has already moved');
                return redirect()->to(base_url('course/' . $course_id))->with('error', 'File already moved');
            }

            // Check file size (10MB max)
            if ($file->getSize() > 10240 * 1024) {
                log_message('error', 'File size exceeds limit: ' . $file->getSize());
                return redirect()->to(base_url('course/' . $course_id))->with('error', 'File size exceeds 10MB limit');
            }

            // Check file extension
            $allowedExtensions = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'mp4', 'avi', 'pptx'];
            if (!in_array($file->getExtension(), $allowedExtensions)) {
                log_message('error', 'Invalid file extension: ' . $file->getExtension());
                return redirect()->to(base_url('course/' . $course_id))->with('error', 'Invalid file type. Allowed types: ' . implode(', ', $allowedExtensions));
            }

            // Generate unique filename
            $newName = $file->getRandomName();
            log_message('info', 'Generated new filename: ' . $newName);

            // Move file to uploads directory
            $uploadPath = WRITEPATH . 'uploads';
            log_message('info', 'Upload path: ' . $uploadPath);

            if ($file->move($uploadPath, $newName)) {
                log_message('info', 'File moved successfully to: ' . $uploadPath . '/' . $newName);

                $materialModel = new MaterialModel();
                $data = [
                    'course_id' => $course_id,
                    'file_name' => $file->getClientName(),
                    'file_path' => $newName,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                log_message('info', 'Inserting material data: ' . print_r($data, true));

                if ($materialModel->insertMaterial($data)) {
                    log_message('info', 'Material inserted successfully');
                    return redirect()->to(base_url('course/' . $course_id))->with('success', 'Material uploaded successfully');
                } else {
                    // Delete the uploaded file if database insert failed
                    unlink($uploadPath . '/' . $newName);
                    log_message('error', 'Failed to insert material into database');
                    return redirect()->to(base_url('course/' . $course_id))->with('error', 'Failed to save material to database');
                }
            } else {
                log_message('error', 'Failed to move file. Error: ' . $file->getErrorString());
                return redirect()->to(base_url('course/' . $course_id))->with('error', 'Failed to upload file: ' . $file->getErrorString());
            }
        }

        // Since upload form is now inline, redirect back to course view
        return redirect()->to(base_url('course/' . $course_id));
    }

    public function delete($material_id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/login');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);
        if (!$material) {
            return redirect()->to('/dashboard')->with('error', 'Material not found');
        }

        // Check if user is teacher or admin (assuming teachers can delete materials)
        $userRole = session()->get('role');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        // Delete the file from storage
        $filePath = WRITEPATH . 'uploads/' . $material['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        if ($materialModel->delete($material_id)) {
            return redirect()->to(base_url('course/' . $material['course_id']))->with('success', 'Material deleted successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to delete material');
        }
    }

    public function download($material_id)
    {
        log_message('info', 'Download method called with material_id: ' . $material_id);
        log_message('info', 'User ID: ' . session()->get('user_id'));
        log_message('info', 'User role: ' . session()->get('role'));

        if (!session()->get('logged_in')) {
            log_message('error', 'User not logged in');
            return redirect()->to('/auth/login');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);
        if (!$material) {
            log_message('error', 'Material not found: ' . $material_id);
            return redirect()->to('/dashboard')->with('error', 'Material not found');
        }

        log_message('info', 'Material found: ' . print_r($material, true));

        // Check if user is enrolled in the course (only for students)
        $userRole = session()->get('role');
        if ($userRole === 'student') {
            $enrollmentModel = new EnrollmentModel();
            $isEnrolled = $enrollmentModel->isAlreadyEnrolled(session()->get('user_id'), $material['course_id']);
            log_message('info', 'Enrollment check for student: user_id=' . session()->get('user_id') . ', course_id=' . $material['course_id'] . ', enrolled=' . ($isEnrolled ? 'yes' : 'no'));
            if (!$isEnrolled) {
                log_message('error', 'Student not enrolled in course');
                return redirect()->to('/dashboard')->with('error', 'You are not enrolled in this course');
            }
        } else {
            log_message('info', 'User is teacher/admin, skipping enrollment check');
        }

        $filePath = WRITEPATH . 'uploads/' . $material['file_path'];
        log_message('info', 'File path: ' . $filePath);
        log_message('info', 'File exists: ' . (file_exists($filePath) ? 'yes' : 'no'));

        if (file_exists($filePath)) {
            log_message('info', 'Starting file download');
            return $this->response->download($filePath, null, true)->setFileName($material['file_name']);
        } else {
            log_message('error', 'File not found on disk');
            return redirect()->back()->with('error', 'File not found');
        }
    }
}
