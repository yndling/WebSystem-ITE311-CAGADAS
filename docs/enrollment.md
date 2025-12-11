# Enrollment Approval & Force-Enroll

This document describes the enrollment request/approval workflow added to the application, the related validation rules, and the force-enroll capability for teachers and admins.

Endpoints
- `POST /api/enrollment/request` (AJAX JSON)
  - Request enrollment (student)
  - Required fields: `course_id`, `school_year`, `semester`, `schedule`
  - The backend sets `user_id` from session and `status = 'pending'`.
  - Validations:
    - `schedule` must match pattern like `MWF 09:00-10:30`.
    - No schedule conflict with user's existing approved enrollments for same `school_year` and `semester`.
    - No duplicate/pending enrollment for same `course_id` in the same term.

- `POST /api/enrollment/approve/{enrollmentId}` (AJAX)
  - Approve a pending enrollment (teacher or admin).
  - Teachers can only approve enrollments for courses they teach.
  - Approval checks for schedule conflicts before approving.

- `POST /api/enrollment/reject/{enrollmentId}` (AJAX JSON)
  - Reject a pending enrollment (teacher or admin).
  - Body: `{ "reason": "..." }`.

- `POST /api/enrollment/force-enroll` (AJAX JSON)
  - Force-enroll a student (teacher or admin).
  - Required fields: `user_id`, `course_id`, `school_year`, `semester`, `schedule`.
  - Optional: `force: true` — when present and true, conflict/duplicate checks are skipped. If omitted or false, the system enforces the same validation as normal requests.

Validation and schedule format
- Schedule should be in the format: `DAYS HH:MM-HH:MM` where `DAYS` is letter codes (e.g., `M`, `T`, `W`, `Th`, `F`; the implementation uses single-character tokens in the model like `MWF` or `TTH`).
- The system converts times to minutes and checks for overlapping days and overlapping time windows.

UI
- Teachers/Admins: A pending requests page (`app/Views/enrollment/manage_requests.php`) shows pending requests with Approve / Reject actions and an optional Force Enroll modal.

Notes & Recommendations
- Course uniqueness: enrollments are keyed by `course_id`. Because course `code` and `title` are not unique in this system, enrollment duplicate checks are performed using the `course_id` primary key. If you want to prevent courses with identical codes/titles being created, add uniqueness checks during course creation.
- Tests: Basic schedule-format validation tests were added in `tests/unit/EnrollmentValidationTest.php`.

Contact
- If you'd like stricter schedule parsing (support `Th` token, timezone awareness, or recurring exceptions), I can extend the parser and tests.
