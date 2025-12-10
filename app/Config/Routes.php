<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Home page routes
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Authentication routes
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::dashboard');

// Course routes
$routes->get('/course/my', 'Course::myCourses');
$routes->get('/course/create', 'Course::create');
$routes->post('/course/create', 'Course::create');
$routes->get('/course/browse', 'Course::browse');
$routes->get('/course/(:num)', 'Course::view/$1');
$routes->post('/course/enroll', 'Course::enroll');
$routes->match(['get', 'post'], '/course/search', 'Course::search');
$routes->get('/course/my-enrolled', 'Course::myEnrolled');
$routes->get('/course/available', 'Course::available');

// Material routes
$routes->get('/material/upload/(:num)', 'Material::upload/$1');
$routes->post('/material/upload/(:num)', 'Material::upload/$1');
$routes->get('/materials/delete/(:num)', 'Material::delete/$1');
$routes->get('/materials/download/(:num)', 'Material::download/$1');

// Role-based dashboard routes
$routes->get('/teacher/dashboard', 'Teacher::dashboard');
$routes->get('/admin/dashboard', 'Admin::dashboard');
// Admin user management (CRUD)
$routes->get('/admin/users', 'Admin::users');
$routes->post('/admin/user/create', 'Admin::createUser');
$routes->post('/admin/user/update/(:num)', 'Admin::updateUser/$1');
$routes->post('/admin/user/delete/(:num)', 'Admin::deleteUser/$1');
$routes->post('/admin/user/restore/(:num)', 'Admin::restoreUser/$1');

// Announcement routes
$routes->get('/announcements', 'Announcement::index');

// Notification routes
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');

