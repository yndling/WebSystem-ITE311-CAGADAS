<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 
        'course_id', 
        'enrollment_date',
        'status',
        'school_year',
        'semester',
        'schedule',
        'approved_by',
        'approved_at',
        'rejection_reason'
    ];
    protected $useTimestamps = false;
    protected $createdField  = 'enrollment_date';
    protected $updatedField  = 'approved_at';
    /**
     * @var array Validation rules
     */
    protected $validationRules = [
        'user_id' => 'required|numeric',
        'course_id' => 'required|numeric',
        'school_year' => 'required|string|max_length[20]',
        'semester' => 'required|in_list[1st,2nd,summer]',
        'schedule' => [
            'rules' => 'required|string|max_length[100]',
            'label' => 'Schedule'
        ],
    ];
    
    /**
     * EnrollmentModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    } 
    
    /**
     * Custom validation: Check if schedule format is valid
     */
    public function validateScheduleFormat($schedule): bool
    {
        return (bool)preg_match('/^([A-Za-z]+)\s+([0-9]{1,2}:[0-9]{2})-([0-9]{1,2}:[0-9]{2})$/', trim($schedule));
    }
    
    /**
     * Request enrollment in a course
     */
    public function requestEnrollment(array $data)
    {
        log_message('debug', 'Enrollment data: ' . print_r($data, true));
        
        // Set default values
        $data['status'] = 'pending';
        $data['enrollment_date'] = date('Y-m-d H:i:s');
        
        // Skip schedule conflict check if schedule is 'To be scheduled by teacher'
        if (empty($data['schedule']) || $data['schedule'] !== 'To be scheduled by teacher') {
            if ($this->hasScheduleConflict($data['user_id'], $data['schedule'], $data['school_year'], $data['semester'])) {
                throw new \RuntimeException('Schedule conflict detected with existing enrollments.');
            }
        }
        
        // Check for duplicate enrollment
        if ($this->isAlreadyEnrolled($data['user_id'], $data['course_id'], $data['school_year'], $data['semester'])) {
            throw new \RuntimeException('You are already enrolled or have a pending request for this course in the selected term.');
        }
        
        $result = $this->save($data);
        
        if ($result === false) {
            $errors = $this->errors();
            log_message('error', 'Failed to save enrollment: ' . print_r($errors, true));
            throw new \RuntimeException('Failed to save enrollment: ' . implode(', ', $errors));
        }
        
        return $result;
    }
    
    /**
     * Approve an enrollment request
     */
    public function approveEnrollment(int $enrollmentId, int $approvedBy)
    {
        $enrollment = $this->find($enrollmentId);
        if (!$enrollment) {
            throw new \RuntimeException('Enrollment not found.');
        }
        
        // Check for schedule conflicts before approving
        if ($this->hasScheduleConflict($enrollment['user_id'], $enrollment['schedule'], $enrollment['school_year'], $enrollment['semester'], $enrollmentId)) {
            throw new \RuntimeException('Cannot approve: Schedule conflict detected with existing enrollments.');
        }
        
        return $this->update($enrollmentId, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Reject an enrollment request
     */
    public function rejectEnrollment(int $enrollmentId, string $reason, int $rejectedBy)
    {
        return $this->update($enrollmentId, [
            'status' => 'rejected',
            'approved_by' => $rejectedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'rejection_reason' => $reason
        ]);
    }
    
    /**
     * Force enroll a student (for admin/teacher use)
     *
     * @param array $data Enrollment data
     * @param int $enrolledBy User id who performed the enrollment
     * @param bool $skipConflict If true, skip duplicate and schedule conflict checks
     * @throws \RuntimeException on validation/conflict errors
     */
    public function forceEnroll(array $data, int $enrolledBy, bool $skipConflict = false)
    {
        // Remove the 'force' parameter from data as it's not a database field
        unset($data['force']);
        
        // Ensure required fields exist
        $required = ['user_id', 'course_id', 'school_year', 'semester', 'schedule'];
        $missing = [];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        if (!empty($missing)) {
            throw new \RuntimeException('Missing required fields: ' . implode(', ', $missing));
        }

        // If skipConflict is false, perform the same checks as a normal request
        if (!$skipConflict) {
            if ($this->hasScheduleConflict($data['user_id'], $data['schedule'], $data['school_year'], $data['semester'])) {
                throw new \RuntimeException('Schedule conflict detected with existing enrollments.');
            }

            if ($this->isAlreadyEnrolled($data['user_id'], $data['course_id'], $data['school_year'], $data['semester'])) {
                throw new \RuntimeException('The student is already enrolled or has a pending request for this course in the selected term.');
            }
        }

        // Prepare enrollment data
        $enrollmentData = [
            'user_id' => $data['user_id'],
            'course_id' => $data['course_id'],
            'school_year' => $data['school_year'],
            'semester' => $data['semester'],
            'schedule' => $data['schedule'],
            'status' => 'approved',
            'enrollment_date' => date('Y-m-d H:i:s'),
            'approved_by' => $enrolledBy,
            'approved_at' => date('Y-m-d H:i:s')
        ];

        // Save the enrollment
        $result = $this->save($enrollmentData);
        
        if ($result === false) {
            $errors = $this->errors();
            log_message('error', 'Enrollment save failed: ' . print_r($errors, true));
            throw new \RuntimeException('Failed to save enrollment. Please try again.');
        }
        
        return $result;
    }

    /**
     * Get all courses a user is enrolled in with optional filters
     */
    public function getUserEnrollments(int $userId, array $filters = [])
    {
        $query = $this->select('enrollments.*, 
                              courses.title as course_title, 
                              courses.description as course_description,
                              teacher.name as teacher_name')
                     ->join('courses', 'courses.id = enrollments.course_id')
                     ->join('users as teacher', 'teacher.id = courses.teacher_id', 'left')
                     ->where('enrollments.user_id', $userId);
        
        // Apply filters if provided
        if (!empty($filters['school_year'])) {
            $query->where('enrollments.school_year', $filters['school_year']);
        }
        
        if (!empty($filters['semester'])) {
            $query->where('enrollments.semester', $filters['semester']);
        }
        
        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $query->whereIn('enrollments.status', $filters['status']);
            } else {
                $query->where('enrollments.status', $filters['status']);
            }
        }
        
        return $query->orderBy('enrollments.enrollment_date', 'DESC')
                    ->findAll();
    }

    /**
     * Check if a user is already enrolled in a specific course for a given term
     * Returns true if there's an existing non-rejected enrollment for this course/term
     */
    public function isAlreadyEnrolled(int $userId, int $courseId, string $schoolYear, string $semester): bool
    {
        // Check for any non-rejected enrollment for this course/term
        return $this->where('user_id', $userId)
                   ->where('course_id', $courseId)
                   ->where('school_year', $schoolYear)
                   ->where('semester', $semester)
                   ->whereNotIn('status', ['rejected', 'withdrawn', 'dropped']) // Exclude rejected/withdrawn enrollments
                   ->countAllResults() > 0;
    }
    
    /**
     * Parse schedule string into days and time components
     * 
     * @param string $schedule Format: "MWF 9:00-10:30" or "TTH 14:00-15:30"
     * @return array [days => string[], startTime => string, endTime => string]
     */
    private function parseSchedule(string $schedule): array
    {
        // Handle 'To be scheduled by teacher' case
        if ($schedule === 'To be scheduled by teacher') {
            return [
                'days' => [],
                'startTime' => '00:00',
                'endTime' => '00:00',
                'startMinutes' => 0,
                'endMinutes' => 0
            ];
        }
        
        // Split into days and time parts
        if (!preg_match('/^([A-Za-z]+)\s+([0-9]{1,2}:[0-9]{2})-([0-9]{1,2}:[0-9]{2})$/', trim($schedule), $matches)) {
            throw new \RuntimeException('Invalid schedule format. Expected format: "MWF 9:00-10:30"');
        }

        $days = str_split(strtoupper($matches[1]));
        $startTime = $matches[2];
        $endTime = $matches[3];

        // Convert times to minutes since midnight for easier comparison
        $startMinutes = $this->timeToMinutes($startTime);
        $endMinutes = $this->timeToMinutes($endTime);

        if ($endMinutes <= $startMinutes) {
            throw new \RuntimeException('End time must be after start time');
        }

        return [
            'days' => $days,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'startMinutes' => $startMinutes,
            'endMinutes' => $endMinutes
        ];
    }

    /**
     * Convert time string to minutes since midnight
     */
    private function timeToMinutes(string $time): int
    {
        list($hours, $minutes) = array_map('intval', explode(':', $time));
        return $hours * 60 + $minutes;
    }

    /**
     * Check if two time slots overlap
     */
    private function doTimeSlotsOverlap(array $slot1, array $slot2): bool
    {
        // Check if any day overlaps
        $dayOverlap = !empty(array_intersect($slot1['days'], $slot2['days']));
        if (!$dayOverlap) {
            return false;
        }

        // Check time overlap
        return !($slot1['endMinutes'] <= $slot2['startMinutes'] || 
                $slot1['startMinutes'] >= $slot2['endMinutes']);
    }

    /**
     * Check for schedule conflicts with overlapping time slots
     * 
     * @param int $userId The ID of the user to check for conflicts
     * @param string $newSchedule The new schedule to check (format: "MWF 9:00-10:30")
     * @param string $schoolYear The school year to check for conflicts in
     * @param string $semester The semester to check for conflicts in
     * @param int|null $excludeEnrollmentId Optional enrollment ID to exclude from conflict check
     * @return bool True if there is a schedule conflict, false otherwise
     * @throws \RuntimeException If there's an error parsing the schedule
     */
    public function hasScheduleConflict(int $userId, string $newSchedule, string $schoolYear, string $semester, ?int $excludeEnrollmentId = null): bool
    {
        try {
            $newSlot = $this->parseSchedule($newSchedule);
        } catch (\RuntimeException $e) {
            // If schedule format is invalid, treat it as a conflict to prevent invalid data
            return true;
        }

        // Get all approved enrollments for the user in the same term
        $query = $this->where('user_id', $userId)
                     ->where('school_year', $schoolYear)
                     ->where('semester', $semester)
                     ->where('status', 'approved');
        
        if ($excludeEnrollmentId) {
            $query->where('id !=', $excludeEnrollmentId);
        }
        
        $enrollments = $query->findAll();

        foreach ($enrollments as $enrollment) {
            try {
                $existingSlot = $this->parseSchedule($enrollment['schedule']);
                if ($this->doTimeSlotsOverlap($newSlot, $existingSlot)) {
                    return true;
                }
            } catch (\RuntimeException $e) {
                // Skip invalid schedule formats in existing records
                continue;
            }
        }
        
        return false;
    }
    
    /**
     * Get pending enrollment requests for courses taught by a teacher
     */
    public function getPendingRequestsForTeacher(?int $teacherId = null)
    {
        $builder = $this->db->table('enrollments')
            ->select('enrollments.*, 
                     users.name as student_name, 
                     courses.title as course_title, 
                     courses.code as course_code, 
                     courses.description as course_description,
                     courses.teacher_id')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->join('users', 'users.id = enrollments.user_id')
            ->where('enrollments.status', 'pending')
            ->orderBy('enrollments.enrollment_date', 'ASC');

        // If a teacherId is provided, filter to that teacher's courses. If null, return all pending (admin).
        if ($teacherId !== null) {
            $builder->where('courses.teacher_id', $teacherId);
        }

        return $builder->get()->getResultArray();
    }
    
    /**
     * Get all enrollments for a student
     */
    public function getStudentEnrollments(int $userId, ?string $status = null, ?string $schoolYear = null, ?string $semester = null)
    {
        $query = $this->select('enrollments.*, courses.title as course_title, courses.code as course_code, courses.description as course_description, 
                               users.name as teacher_name')
                     ->join('courses', 'courses.id = enrollments.course_id')
                     ->join('users', 'users.id = courses.teacher_id')
                     ->where('enrollments.user_id', $userId);
        
        if ($status) {
            $query->where('enrollments.status', $status);
        }
        
        if ($schoolYear) {
            $query->where('enrollments.school_year', $schoolYear);
        }
        
        if ($semester) {
            $query->where('enrollments.semester', $semester);
        }
        
        return $query->orderBy('enrollments.enrollment_date', 'DESC')
                    ->findAll();
    }

    /**
     * Cancel an enrollment request (mark as cancelled)
     * Only updates status to 'cancelled' and records when
     */
    public function cancelEnrollment(int $enrollmentId, int $cancelledBy)
    {
        $enrollment = $this->find($enrollmentId);
        if (!$enrollment) {
            throw new \RuntimeException('Enrollment not found.');
        }

        // Only pending or approved requests can be cancelled by admin; students only cancel pending in controller
        return $this->update($enrollmentId, [
            'status' => 'cancelled',
            'approved_by' => $cancelledBy,
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }
}
