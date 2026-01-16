<?php
// public/users.php
require_once '../config/db.php';
require_once '../includes/functions.php';

require_role('admin');

// Fetch Users
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}

$page_title = "User Management";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Management - CRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex">

    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        <?php include '../includes/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">System Users</h2>
                    <p class="text-gray-500 text-sm">Manage access and roles for department personnel.</p>
                </div>
                <!-- TODO: Implement Add User Modal/Page if desired, for now placeholder -->
                <a href="add_user.php"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i> Add New User
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">User ID</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Identity</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Last Login</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    #
                                    <?php echo $user['id']; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold mr-3">
                                            <?php echo strtoupper(substr($user['username'], 0, 2)); ?>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($user['full_name']); ?>
                                            </div>
                                            <div class="text-xs text-gray-500">@
                                                <?php echo htmlspecialchars($user['username']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $roleColors = [
                                        'admin' => 'bg-purple-100 text-purple-800',
                                        'officer' => 'bg-blue-100 text-blue-800',
                                        'detective' => 'bg-indigo-100 text-indigo-800',
                                        'forensics' => 'bg-amber-100 text-amber-800'
                                    ];
                                    $color = $roleColors[$user['role']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2 py-1 text-xs font-bold rounded-full uppercase <?php echo $color; ?>">
                                        <?php echo htmlspecialchars($user['role']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php echo $user['last_login'] ? date('M d, H:i', strtotime($user['last_login'])) : 'Never'; ?>
                                </td>
                                <td class="px-6 py-4 text-right flex justify-end items-center">
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>"
                                        class="text-gray-400 hover:text-blue-600 transition mx-1"><i
                                            class="fas fa-edit"></i></a>

                                    <form action="../views/user_actions.php" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this user?');"
                                        class="inline">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit"
                                            class="text-gray-400 hover:text-red-600 transition mx-1 bg-transparent border-0 cursor-pointer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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