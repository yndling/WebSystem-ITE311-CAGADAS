# TODO: Implement Announcements Module

## Steps to Complete
- [x] Generate migration for announcements table using `php spark make:migration CreateAnnouncementsTable`
- [x] Edit the migration file to add fields: id (primary key, auto-increment), title (VARCHAR), content (TEXT), created_at (DATETIME)
- [x] Create AnnouncementModel using `php spark make:model AnnouncementModel`
- [x] Edit AnnouncementModel to configure for announcements table
- [x] Create Announcement controller using `php spark make:controller Announcement`
- [x] Edit Announcement controller to add index() method that fetches announcements ordered by created_at DESC and passes to view
- [x] Create announcements.php view in app/Views/ to display list of announcements (title, content, date posted), handle empty list
- [x] Add route in app/Config/Routes.php: $routes->get('/announcements', 'Announcement::index');
- [x] Create AnnouncementSeeder using `php spark make:seeder AnnouncementSeeder`
- [x] Edit AnnouncementSeeder to insert at least two sample announcements
- [x] Run migration: `php spark migrate`
- [x] Run seeder: `php spark db:seed AnnouncementSeeder`
- [x] Test the /announcements page using browser to ensure it displays the list (server running, auth filter added for logged-in users)
