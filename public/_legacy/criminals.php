<?php
// public/criminals.php
require_once '../config/db.php';
require_once '../includes/functions.php';

require_role(['admin', 'officer', 'detective']);

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? 'All';

$sql = "SELECT * FROM criminals WHERE 1=1";
$params = [];

if ($status !== 'All') {
    $sql .= " AND status = ?";
    $params[] = $status;
}

if (!empty($search)) {
    $sql .= " AND (first_name LIKE ? OR last_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$criminals = $stmt->fetchAll();

$page_title = "Criminal Records";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Criminal Database - CRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex">

    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        <?php include '../includes/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6">

            <!-- Actions Toolbar -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div class="flex space-x-2 w-full md:w-auto">
                    <form action="" method="GET" class="flex w-full md:w-auto shadow-sm">
                        <div class="relative w-full md:w-64">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <i class="fas fa-search text-gray-400"></i>
                            </span>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                                class="w-full pl-10 pr-4 py-2 rounded-l-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Search records...">
                        </div>
                        <select name="status"
                            class="bg-white border-y border-r border-gray-300 text-gray-700 py-2 px-4 rounded-r-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="All">All Status</option>
                            <option value="Wanted" <?php echo $status == 'Wanted' ? 'selected' : ''; ?>>Wanted</option>
                            <option value="In Custody" <?php echo $status == 'In Custody' ? 'selected' : ''; ?>>In Custody
                            </option>
                            <option value="Released" <?php echo $status == 'Released' ? 'selected' : ''; ?>>Released
                            </option>
                        </select>
                        <button type="submit" class="hidden"></button>
                    </form>
                </div>

                <a href="add_criminal.php"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition flex items-center">
                    <i class="fas fa-user-plus mr-2"></i> Add New Record
                </a>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($criminals as $criminal): ?>
                    <div
                        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                        <div class="h-32 bg-gray-200 relative">
                            <div class="absolute inset-0 flex items-center justify-center text-gray-400 font-bold text-4xl">
                                <i class="fas fa-user"></i>
                            </div>
                            <?php if ($criminal['photo_path']): ?>
                                <img src="../public/uploads/<?php echo htmlspecialchars($criminal['photo_path']); ?>"
                                    class="w-full h-full object-cover">
                            <?php endif; ?>

                            <div class="absolute top-2 right-2">
                                <?php
                                $badge = match ($criminal['status']) {
                                    'Wanted' => 'bg-red-500 text-white',
                                    'In Custody' => 'bg-orange-500 text-white',
                                    'Released' => 'bg-green-500 text-white',
                                    default => 'bg-gray-500 text-white'
                                };
                                ?>
                                <span
                                    class="px-2 py-1 text-xs font-bold rounded-md shadow-sm <?php echo $badge; ?> uppercase tracking-wider">
                                    <?php echo htmlspecialchars($criminal['status']); ?>
                                </span>
                            </div>
                        </div>

                        <div class="p-4">
                            <h3 class="text-lg font-bold text-gray-800">
                                <?php echo htmlspecialchars($criminal['last_name'] . ', ' . $criminal['first_name']); ?>
                            </h3>
                            <p class="text-sm text-gray-500 mb-4">ID: CR-
                                <?php echo str_pad($criminal['id'], 6, '0', STR_PAD_LEFT); ?>
                            </p>

                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-birthday-cake w-6 text-gray-400"></i>
                                    <span>
                                        <?php echo $criminal['date_of_birth']; ?>
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt w-6 text-gray-400"></i>
                                    <span class="truncate">
                                        <?php echo htmlspecialchars(substr($criminal['address'], 0, 20)) . '...'; ?>
                                    </span>
                                </div>
                            </div>

                            <a href="view_criminal.php?id=<?php echo $criminal['id']; ?>"
                                class="block w-full text-center bg-gray-50 hover:bg-gray-100 text-gray-700 font-medium py-2 rounded border border-gray-200 transition">View
                                Full Profile</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </main>
    </div>
</body>

</html>