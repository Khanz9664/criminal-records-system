<?php
// public/logs.php
require_once '../config/db.php';
require_once '../includes/functions.php';

require_role('admin');

try {
    $stmt = $pdo->query("
        SELECT activity_logs.*, users.username, users.role 
        FROM activity_logs 
        LEFT JOIN users ON activity_logs.user_id = users.id 
        ORDER BY activity_logs.created_at DESC 
        LIMIT 100
    ");
    $logs = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching logs: " . $e->getMessage());
}

$page_title = "Audit Logs";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Audit Logs - CRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex">

    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        <?php include '../includes/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6">

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">System Activity Logs</h2>
                <p class="text-gray-500 text-sm">Tracking recent 100 actions performed within the system.</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Timestamp</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Action</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">IP Address
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-mono text-sm">
                        <?php foreach ($logs as $log): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3 text-gray-500 whitespace-nowrap">
                                    <?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?>
                                </td>
                                <td class="px-6 py-3">
                                    <?php if ($log['username']): ?>
                                        <span class="font-bold text-gray-700">
                                            <?php echo htmlspecialchars($log['username']); ?>
                                        </span>
                                        <span class="text-xs text-gray-400">(
                                            <?php echo $log['role']; ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">System / Deleted</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-3 text-gray-800">
                                    <?php echo htmlspecialchars($log['action']); ?>
                                </td>
                                <td class="px-6 py-3 text-right text-gray-400">
                                    <?php echo htmlspecialchars($log['ip_address']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</body>

</html>