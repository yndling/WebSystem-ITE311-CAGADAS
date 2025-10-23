<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'message', 'is_read', 'created_at'];
    protected $useTimestamps = false;

    /**
     * Get the count of unread notifications for a user
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->where('user_id', $userId)->where('is_read', 0)->countAllResults();
    }

    /**
     * Get the latest notifications for a user (e.g., limit 5)
     */
    public function getNotificationsForUser(int $userId, int $limit = 5)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead(int $notificationId): bool
    {
        return $this->update($notificationId, ['is_read' => 1]);
    }
}
