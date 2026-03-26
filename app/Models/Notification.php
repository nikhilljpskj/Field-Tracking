<?php
namespace App\Models;

use App\Core\Model;

class Notification extends Model {
    public function create($user_id, $type, $message) {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)");
        return $stmt->execute([$user_id, $type, $message]);
    }

    public function getUnread($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getCount($user_id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$user_id]);
        return $stmt->fetch()['count'];
    }
}
