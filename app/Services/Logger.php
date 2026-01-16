<?php

namespace App\Services;

use App\Models\ActivityLog;

class Logger
{
    public static function log($action)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // If no user is logged in (e.g. login failure), we might skip or log as 'Guest'/'System'
        // For this app, we mostly care about logged-in user actions.
        if (!isset($_SESSION['user_id'])) {
            return;
        }

        $userId = $_SESSION['user_id'];

        $logModel = new ActivityLog();
        $logModel->log($userId, $action);
    }
}
