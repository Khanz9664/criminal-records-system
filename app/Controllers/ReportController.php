<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class ReportController extends Controller
{

    public function index()
    {
        $this->requireRole(['admin', 'officer', 'detective']);

        $db = Database::getInstance()->getConnection();

        // 1. Crimes by Type
        $typeStmt = $db->query("SELECT type, COUNT(*) as count FROM cases GROUP BY type");
        $typeData = $typeStmt->fetchAll();

        // 2. Crimes by Status
        $statusStmt = $db->query("SELECT status, COUNT(*) as count FROM cases GROUP BY status");
        $statusData = $statusStmt->fetchAll();

        // 3. Cases per Month (Trend)
        $trendStmt = $db->query("SELECT DATE_FORMAT(incident_date, '%Y-%m') as month, COUNT(*) as count FROM cases GROUP BY month ORDER BY month DESC LIMIT 6");
        $trendData = $trendStmt->fetchAll();

        $this->view('reports/index', [
            'typeData' => $typeData,
            'statusData' => $statusData,
            'trendData' => $trendData
        ]);
    }
}
