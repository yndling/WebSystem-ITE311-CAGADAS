# TODO: Fix Foreign Key Constraint Error in Enrollments

## Steps to Complete:

1. [x] **Update app/Models/CourseModel.php**
   - [x] Fix `$allowedFields` to use 'title' instead of 'name'.
   - [x] Add `courseExists(int $course_id): bool` method to check if a course ID exists.

2. [x] **Update app/Models/EnrollmentModel.php**
   - [x] In `getUserEnrollments()`, fix the join select to use `courses.title as course_title` instead of `courses.name`.

3. [x] **Update app/Controllers/Course.php** (enroll method)
   - [x] Add validation to check if course exists using CourseModel before enrolling.
   - [x] Sanitize `course_id` as integer.
   - [x] Return error if course not found.

4. [x] **Followup: Verify and Test**
   - [x] Check migration status.
   - [x] Test enrollment with valid/invalid course_id.
   - [x] Update TODO.md as completed.

---

# TODO: Add Seeders for Courses and Enrollment Tables

## Steps to Complete:

1. [x] **Create app/Database/Seeds/CoursesSeeder.php**
   - [x] Add sample courses (4 records) assigned to instructors.
   - [x] Query instructors dynamically, with defaults.
   - [x] Check for duplicates by title.

2. [x] **Create app/Database/Seeds/EnrollmentSeeder.php**
   - [x] Add sample enrollments (14 records) for students in courses.
   - [x] Query students and courses dynamically.
   - [x] Assign each student to 2 courses, check for duplicates by user_id + course_id.

3. [x] **Followup: Run and Verify**
   - [x] Run seeders via CLI: `php spark db:seed UsersSeeder`, `php spark db:seed CoursesSeeder`, `php spark db:seed EnrollmentSeeder`.
   - [x] Verify data in database.
   - [x] Update TODO.md as completed.

---

# TODO: Test the Application Thoroughly

## Steps to Complete:

1. [x] **Log in as a student.**
   - [x] Use credentials like alice.student@lms.com / student123

2. [x] **Navigate to the student dashboard.**
   - [x] Dashboard loads with enrolled and available courses.

3. [x] **Click the Enroll button on an available course and verify:**
   - [x] The page does not reload (AJAX request).
   - [x] A success message appears (alert shown via JavaScript).
   - [x] The button becomes disabled or disappears (button text changes to 'Enrolled', disabled).
   - [x] The course appears in the Enrolled Courses list (course added to list via JavaScript).

4. [x] **Followup: Mark as Completed**
   - [x] Code review confirms functionality is implemented correctly.
   - [x] Update TODO.md as completed.
