<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CrimeCase;
use App\Models\Criminal;
use App\Models\ActivityLog;

class DashboardController extends Controller
{

    public function index()
    {
        $this->requireRole(['admin', 'officer', 'detective', 'forensics']);

        $casesModel = new CrimeCase();

        $totalCases = $casesModel->countAll();
        $activeCases = $casesModel->countActive();
        $closedCases = $casesModel->countClosed();

        $clearanceRate = $totalCases > 0 ? round(($closedCases / $totalCases) * 100, 1) : 0;

        $criminalModel = new Criminal();
        $totalCriminals = $criminalModel->countAll();

        $activityLog = new ActivityLog();
        $recentActivities = $activityLog->getRecentWithUser(5);

        $this->view('dashboard/index', [
            'totalCases' => $totalCases,
            'activeCases' => $activeCases,
            'clearanceRate' => $clearanceRate,
            'totalCriminals' => $totalCriminals,
            'recentActivities' => $recentActivities
        ]);
    }
}
