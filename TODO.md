# TODO: Implement Conditional Role Checks for Admin and Instructor Dashboards

## Completed Steps:
- [x] Added role check to `app/Views/instructordashboard.php` to ensure only instructors can access it.
- [x] Updated `app/Controllers/Auth.php` to route to the appropriate dashboard based on user role.
- [x] Added role check to `app/Views/dashboard.php` to ensure only admins can access it.

## Remaining Steps:
- [ ] Test the implementation by logging in as different roles and verifying correct dashboard access.
- [ ] Ensure that unauthorized access is properly handled and redirected.
- [ ] Check for any additional role-based features or restrictions needed.
