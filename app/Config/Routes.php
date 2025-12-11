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
$routes->match(['GET', 'POST'], '/course/search', 'Course::search');
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

// Enrollment routes
$routes->group('enrollments', ['filter' => 'auth'], function($routes) {
    // View enrollment requests (for teachers/admins)
    $routes->get('manage', 'Enrollment::manageEnrollments', ['filter' => 'roleauth']);
    
    // View my enrollments (for students)
    $routes->get('my', 'Enrollment::myEnrollments');
    
    // Request enrollment (for students)
    $routes->get('request/(:num)', 'Enrollment::requestForm/$1');
    $routes->post('request', 'Enrollment::request');
    
    // Enrollment actions
    $routes->post('(:num)/cancel', 'Enrollment::cancel/$1');
    $routes->post('(:num)/approve', 'Enrollment::approve/$1', ['filter' => 'roleauth']);
    $routes->post('(:num)/reject', 'Enrollment::reject/$1', ['filter' => 'roleauth']);
});

// API endpoints for enrollment (AJAX)
$routes->group('api/enrollment', ['filter' => 'csrf:api'], function($routes) {
    $routes->post('request', 'Enrollment::request');
    $routes->post('approve/(:num)', 'Enrollment::approve/$1');
    $routes->post('reject/(:num)', 'Enrollment::reject/$1');
    $routes->post('cancel/(:num)', 'Enrollment::cancel/$1');
    $routes->post('force-enroll', 'Enrollment::forceEnroll', ['filter' => 'csrf:api,csrf:except[force-enroll]']);
    $routes->get('pending', 'Enrollment::pendingRequests');
    $routes->get('student/(:num)', 'Enrollment::studentEnrollments/$1');
    $routes->get('my', 'Enrollment::myEnrollments');
});

