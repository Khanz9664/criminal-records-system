<?php
// public/edit_user.php
require_once '../config/db.php';
require_once '../includes/functions.php';

require_role('admin');

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user)
    die("User not found.");

$page_title = "Edit User: " . $user['username'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit User - CRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex">

    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        <?php include '../includes/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6 md:p-12">

            <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg border border-gray-100 p-8">
                <div class="border-b border-gray-100 pb-6 mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Edit User Account</h2>
                        <p class="text-gray-500 mt-1">Update settings for @
                            <?php echo htmlspecialchars($user['username']); ?>
                        </p>
                    </div>
                    <a href="users.php" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times"></i>
                        Cancel</a>
                </div>

                <form action="../views/user_actions.php" method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" name="username"
                                value="<?php echo htmlspecialchars($user['username']); ?>" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-gray-50"
                                readonly>
                            <p class="text-xs text-gray-400 mt-1">Username cannot be changed.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Badge Number</label>
                            <input type="text" name="badge_number"
                                value="<?php echo htmlspecialchars($user['badge_number'] ?? ''); ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role / Access Level</label>
                            <select name="role"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="officer" <?php echo $user['role'] == 'officer' ? 'selected' : ''; ?>
                                    >Officer</option>
                                <option value="detective" <?php echo $user['role'] == 'detective' ? 'selected' : ''; ?>
                                    >Detective</option>
                                <option value="forensics" <?php echo $user['role'] == 'forensics' ? 'selected' : ''; ?>
                                    >Forensics</option>
                                <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>
                                    >Administrator</option>
                                <option value="guest" <?php echo $user['role'] == 'guest' ? 'selected' : ''; ?>>Guest
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reset Password</label>
                            <input type="password" name="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="Leave blank to keep current">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition transform hover:scale-105">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>