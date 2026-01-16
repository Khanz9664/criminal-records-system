<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Force login
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

// Fetch some quick stats
try {
    // Total Cases
    $casesStmt = $pdo->query("SELECT COUNT(*) FROM cases");
    $totalCases = $casesStmt->fetchColumn();

    // Active Cases
    $activeCasesStmt = $pdo->query("SELECT COUNT(*) FROM cases WHERE status = 'Open' OR status = 'Under Investigation'");
    $activeCases = $activeCasesStmt->fetchColumn();

    // Total Criminals
    $criminalsStmt = $pdo->query("SELECT COUNT(*) FROM criminals");
    $totalCriminals = $criminalsStmt->fetchColumn();

    // Recent Activities (Limit 5)
    $logsStmt = $pdo->prepare("
        SELECT activity_logs.*, users.username 
        FROM activity_logs 
        LEFT JOIN users ON activity_logs.user_id = users.id 
        ORDER BY activity_logs.created_at DESC 
        LIMIT 5
    ");
    $logsStmt->execute();
    $recentActivities = $logsStmt->fetchAll();

} catch (PDOException $e) {
    // Graceful fail for stats
    $totalCases = $activeCases = $totalCriminals = 0;
    $recentActivities = [];
}

$page_title = "Dashboard Overview";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen overflow-hidden flex">

    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">

        <!-- Header -->
        <?php include '../includes/header.php'; ?>

        <!-- Content Body -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">

            <div class="mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Welcome back,
                    <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                </h3>
                <p class="text-sm text-gray-500">Here's what's happening in your department today.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Stat Card 1 -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-folder-open text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Cases</p>
                        <h4 class="text-2xl font-bold text-gray-800">
                            <?php echo number_format($totalCases); ?>
                        </h4>
                    </div>
                </div>

                <!-- Stat Card 2 -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-search text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Active Investigations</p>
                        <h4 class="text-2xl font-bold text-gray-800">
                            <?php echo number_format($activeCases); ?>
                        </h4>
                    </div>
                </div>

                <!-- Stat Card 3 -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                        <i class="fas fa-user-lock text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Criminal Records</p>
                        <h4 class="text-2xl font-bold text-gray-800">
                            <?php echo number_format($totalCriminals); ?>
                        </h4>
                    </div>
                </div>

                <!-- Stat Card 4 -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Clearance Rate</p>
                        <h4 class="text-2xl font-bold text-gray-800">85%</h4>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h5 class="font-bold text-gray-700">Recent System Activity</h5>
                    <a href="logs.php" class="text-xs text-blue-600 hover:text-blue-800 font-medium">View All Logs</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase bg-gray-50">User</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase bg-gray-50">Action
                                </th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase bg-gray-50">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (count($recentActivities) > 0): ?>
                                <?php foreach ($recentActivities as $log): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 flex items-center">
                                            <div
                                                class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-600 font-bold mr-3">
                                                <?php echo strtoupper(substr($log['username'], 0, 2)); ?>
                                            </div>
                                            <?php echo htmlspecialchars($log['username']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <?php echo htmlspecialchars($log['action']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <?php echo date('M d, H:i', strtotime($log['created_at'])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">No recent activity
                                        found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

</body>

</html>