<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use CodeIgniter\HTTP\ResponseInterface;

class Notifications extends BaseController
{
    public function get()
    {
        $notificationModel = new NotificationModel();
        $userId = session()->get('user_id');

        if (!$userId) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $unreadCount = $notificationModel->getUnreadCount($userId);
        $notifications = $notificationModel->getNotificationsForUser($userId);

        return $this->response->setJSON([
            'unreadCount' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    public function mark_as_read($id)
    {
        $notificationModel = new NotificationModel();
        $userId = session()->get('user_id');

        if (!$userId) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        // Verify the notification belongs to the user
        $notification = $notificationModel->find($id);
        if (!$notification || $notification['user_id'] != $userId) {
            return $this->response->setJSON(['error' => 'Notification not found'])->setStatusCode(404);
        }

        $success = $notificationModel->markAsRead($id);

        if ($success) {
                        return $this->response->setJSON([
                'success' => true,
                'csrf_token' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON(['error' => 'Failed to mark as read'])->setStatusCode(500);
        }
    }
}
