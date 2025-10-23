<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Teacher extends BaseController
{
    public function index()
    {
        //
    }

    public function dashboard()
    {
        return view('teacher_dashboard');
    }
}
