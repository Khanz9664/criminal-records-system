<?php
// public/cases.php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Check Role
require_role(['admin', 'officer', 'detective']);

// Filters
$status = $_GET['status'] ?? 'All';
$search = $_GET['search'] ?? '';

// Build Query
$sql = "SELECT cases.*, users.full_name as officer_name 
        FROM cases 
        LEFT JOIN users ON cases.assigned_officer_id = users.id 
        WHERE 1=1";
$params = [];

if ($status !== 'All') {
    $sql .= " AND cases.status = ?";
    $params[] = $status;
}

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $cases = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching cases: " . $e->getMessage());
}

$page_title = "Case Management";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cases - CRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex overflow-hidden">

    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        <?php include '../includes/header.php'; ?>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">

            <!-- Actions Toolbar -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div class="flex space-x-2 w-full md:w-auto">
                    <form action="" method="GET" class="flex w-full md:w-auto">
                        <div class="relative w-full md:w-64">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <i class="fas fa-search text-gray-400"></i>
                            </span>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                                class="w-full pl-10 pr-4 py-2 rounded-l-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Search cases...">
                        </div>
                        <select name="status"
                            class="bg-white border-y border-r border-gray-300 text-gray-700 py-2 px-4 rounded-r-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="All">All Status</option>
                            <option value="Open" <?php echo $status == 'Open' ? 'selected' : ''; ?>>Open</option>
                            <option value="Under Investigation" <?php echo $status == 'Under Investigation' ? 'selected' : ''; ?>>Investigating</option>
                            <option value="Closed" <?php echo $status == 'Closed' ? 'selected' : ''; ?>>Closed</option>
                        </select>
                        <button type="submit" class="hidden"></button>
                    </form>
                </div>

                <a href="create_case.php"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition flex items-center">
                    <i class="fas fa-plus mr-2"></i> File New Case
                </a>
            </div>

            <!-- Cases Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Case ID
                            </th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Title /
                                Type</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To
                            </th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Priority
                            </th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($cases as $case): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #
                                    <?php echo str_pad($case['id'], 5, '0', STR_PAD_LEFT); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($case['title']); ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?php echo htmlspecialchars($case['type']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $case['officer_name'] ? htmlspecialchars($case['officer_name']) : '<span class="text-gray-400 italic">Unassigned</span>'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $color = match ($case['status']) {
                                        'Open' => 'bg-green-100 text-green-800',
                                        'Under Investigation' => 'bg-yellow-100 text-yellow-800',
                                        'Closed' => 'bg-gray-100 text-gray-800',
                                        'Cold Case' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                    ?>
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                                        <?php echo htmlspecialchars($case['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if ($case['priority'] == 'Critical'): ?>
                                        <span class="text-red-600 font-bold"><i class="fas fa-exclamation-triangle mr-1"></i>
                                            Critical</span>
                                    <?php elseif ($case['priority'] == 'High'): ?>
                                        <span class="text-orange-600 font-bold">High</span>
                                    <?php else: ?>
                                        <span class="text-gray-500">Normal</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('M d, Y', strtotime($case['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="view_case.php?id=<?php echo $case['id']; ?>"
                                        class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1 rounded hover:bg-blue-100 transition">View
                                        Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (empty($cases)): ?>
                    <div class="p-12 text-center text-gray-500">
                        <i class="fas fa-folder-open text-4xl mb-4 text-gray-300"></i>
                        <p>No cases found matching your criteria.</p>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>
</body>

</html>