<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Admin extends BaseController
{
    public function index()
    {
        //
    }

    public function dashboard()
    {
        return view('admin_dashboard');
    }
}
