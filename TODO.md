# TODO: Fix Login, Register, Auth, UserModel, Database Issues

## Tasks
- [x] Update UserModel with methods for user operations (findByEmail, createUser, verifyPassword)
- [x] Remove redundant validation in login method in Auth.php
- [x] Add error handling for database insert failure in register method
- [x] Add error handling for database query failure in login method
- [x] Refactor Auth.php to use UserModel for DB operations instead of direct DB calls
- [x] Verify password hashing and session management
- [x] Add comments for clarity in Auth.php
- [ ] Test registration and login flows after fixes
