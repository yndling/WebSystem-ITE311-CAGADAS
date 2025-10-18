<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;
use CodeIgniter\HTTP\ResponseInterface;

class Announcement extends BaseController
{
    public function index()
    {
        $announcementModel = new AnnouncementModel();
        $data['announcements'] = $announcementModel->orderBy('created_at', 'DESC')->findAll();

        return view('announcements', $data);
    }
}
