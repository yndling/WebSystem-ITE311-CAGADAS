<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        $role = $session->get('role');
        $uri = $request->getUri()->getPath();

        if ($role === 'admin') {
            // Admin can access any route starting with /admin
            // No restriction for admin within /admin routes
        } elseif ($role === 'teacher') {
            // Teacher can access routes starting with /teacher, /course, /material, /announcements, /dashboard, /logout, /notifications, /enrollments
            $allowedRoutes = ['/teacher', '/course', '/material', '/announcements', '/dashboard', '/logout', '/notifications', '/enrollments/manage-requests', '/enrollments'];
            $allowed = false;
            foreach ($allowedRoutes as $route) {
                if (strpos($uri, $route) === 0) {
                    $allowed = true;
                    break;
                }
            }
            if (!$allowed) {
                return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
            }
        } elseif ($role === 'student') {
            // Student can only access routes starting with /student and the /announcements route
            if (strpos($uri, '/student') !== 0 && strpos($uri, '/announcements') !== 0) {
                return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
            }
        } else {
            // Invalid role, redirect to login
            return redirect()->to('/login')->with('error', 'Invalid role. Please log in again.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after the request
    }
}
