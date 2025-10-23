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

// Material routes
$routes->get('/material/upload/(:num)', 'Material::upload/$1');
$routes->post('/material/upload/(:num)', 'Material::upload/$1');
$routes->get('/materials/delete/(:num)', 'Material::delete/$1');
$routes->get('/materials/download/(:num)', 'Material::download/$1');

// Role-based dashboard routes
$routes->get('/teacher/dashboard', 'Teacher::dashboard');
$routes->get('/admin/dashboard', 'Admin::dashboard');

// Announcement routes
$routes->get('/announcements', 'Announcement::index');

