<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ActivityLog;

class LogsController extends Controller
{
    public function index()
    {
        // Sec: Only Admin
        $this->requireRole('admin');

        $logModel = new ActivityLog();
        // Since we don't have pagination yet, we'll fetch a larger limit
        // Ideally we would add getPaginated() to Model
        // For now, let's reuse getRecent but increase limit or add getAll
        // Modifying ActivityLog model briefly to add getAll
        $db = \App\Core\Database::getInstance()->getConnection();

        // Simple pagination logic
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $stmt = $db->prepare("
            SELECT activity_logs.*, users.username 
            FROM activity_logs 
            LEFT JOIN users ON activity_logs.user_id = users.id 
            ORDER BY activity_logs.created_at DESC 
            LIMIT ? OFFSET ?
        ");

        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $logs = $stmt->fetchAll();

        // Count total for pagination
        $countStmt = $db->query("SELECT COUNT(*) FROM activity_logs");
        $totalLogs = $countStmt->fetchColumn();
        $totalPages = ceil($totalLogs / $limit);

        $this->view('logs/index', [
            'logs' => $logs,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }
}
