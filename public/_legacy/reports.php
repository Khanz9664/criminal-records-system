<?php
// public/reports.php
require_once '../config/db.php';
require_once '../includes/functions.php';

require_role(['admin', 'officer', 'detective']);

// Data for Charts
// 1. Crimes by Type
$typeStmt = $pdo->query("SELECT type, COUNT(*) as count FROM cases GROUP BY type");
$typeData = $typeStmt->fetchAll();

// 2. Crimes by Status
$statusStmt = $pdo->query("SELECT status, COUNT(*) as count FROM cases GROUP BY status");
$statusData = $statusStmt->fetchAll();

$page_title = "Reports & Analytics";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reports - CRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex">

    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        <?php include '../includes/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6">

            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Crime Statistics & Reports</h2>
                    <p class="text-gray-500 text-sm">Visual analysis of departmental data.</p>
                </div>
                <div>
                    <button onclick="window.print()"
                        class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-2 px-4 rounded-lg shadow-sm transition mr-2">
                        <i class="fas fa-print mr-2"></i> Print Report
                    </button>
                    <button onclick="alert('PDF Export generated in background.')"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition">
                        <i class="fas fa-download mr-2"></i> Export CSV
                    </button>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

                <!-- Chart 1: Crime Types -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Cases by Crime Type</h3>
                    <div class="h-64">
                        <canvas id="typeChart"></canvas>
                    </div>
                </div>

                <!-- Chart 2: Case Status -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Case Status Distribution</h3>
                    <div class="h-64 flex justify-center">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Summary Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h5 class="font-bold text-gray-700">Detailed Breakdown</h5>
                </div>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Count</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Percentage
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php
                        $total = array_sum(array_column($typeData, 'count'));
                        foreach ($typeData as $row):
                            $percent = $total > 0 ? round(($row['count'] / $total) * 100, 1) : 0;
                            ?>
                            <tr>
                                <td class="px-6 py-3 text-gray-700 font-medium">
                                    <?php echo htmlspecialchars($row['type']); ?>
                                </td>
                                <td class="px-6 py-3 text-right text-gray-600">
                                    <?php echo $row['count']; ?>
                                </td>
                                <td class="px-6 py-3 text-right text-gray-500 flex justify-end items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo $percent; ?>%">
                                        </div>
                                    </div>
                                    <?php echo $percent; ?>%
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>

    <!-- Chart Config -->
    <script>
        const typeCtx = document.getElementById('typeChart').getContext('2d');
        const statusCtx = document.getElementById('statusChart').getContext('2d');

        new Chart(typeCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($typeData, 'type')); ?>,
                    datasets: [{
                        label: '# of Cases',
                        data: <?php echo json_encode(array_column($typeData, 'count')); ?>,
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                }]
            },
        options: { responsive: true, maintainAspectRatio: false }
        });

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($statusData, 'status')); ?>,
                    datasets: [{
                        data: <?php echo json_encode(array_column($statusData, 'count')); ?>,
                        backgroundColor: [
                        '#10B981', // Green (Open)
                        '#F59E0B', // Yellow (Investigating)
                        '#6B7280', // Gray (Closed)
                        '#3B82F6', // Blue (Cold)
                        '#EF4444'  // Red (Appealed)
                    ]
                }]
            },
        options: { responsive: true, maintainAspectRatio: false }
        });
    </script>
</body>

</html>