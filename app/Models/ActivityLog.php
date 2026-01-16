<?php

namespace App\Models;

use App\Core\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    public function getRecentWithUser($limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT activity_logs.*, users.username 
            FROM {$this->table} 
            LEFT JOIN users ON activity_logs.user_id = users.id 
            ORDER BY activity_logs.created_at DESC 
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function log($userId, $action)
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, action, ip_address) VALUES (?, ?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        return $stmt->execute([$userId, $action, $ip]);
    }
}
