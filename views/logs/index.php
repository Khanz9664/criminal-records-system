<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Audit Logs - CRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex">

    <?php include dirname(__DIR__) . '/layouts/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        <?php include dirname(__DIR__) . '/layouts/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6 md:p-12">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">System Audit Logs</h2>
                    <p class="text-gray-500 mt-1">Track all user activities and system events.</p>
                </div>
                <button onclick="window.print()"
                    class="bg-white border border-gray-300 text-gray-600 hover:text-gray-800 px-4 py-2 rounded shadow-sm transition">
                    <i class="fas fa-print mr-2"></i> Print Log
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase">
                            <th class="px-6 py-3 font-semibold">ID</th>
                            <th class="px-6 py-3 font-semibold">User</th>
                            <th class="px-6 py-3 font-semibold">Action</th>
                            <th class="px-6 py-3 font-semibold">IP Address</th>
                            <th class="px-6 py-3 font-semibold">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($logs as $log): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-400">#
                                    <?php echo $log['id']; ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-700">
                                    <?php echo htmlspecialchars($log['username'] ?? 'System'); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?php echo htmlspecialchars($log['action']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 font-mono">
                                    <?php echo htmlspecialchars($log['ip_address']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php echo date('M d, Y H:i:s', strtotime($log['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                    <span class="text-sm text-gray-500">Page
                        <?php echo $page; ?> of
                        <?php echo $totalPages; ?>
                    </span>
                    <div class="space-x-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>"
                                class="px-3 py-1 bg-white border border-gray-300 rounded hover:bg-gray-100 text-sm">Previous</a>
                        <?php endif; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>"
                                class="px-3 py-1 bg-white border border-gray-300 rounded hover:bg-gray-100 text-sm">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>