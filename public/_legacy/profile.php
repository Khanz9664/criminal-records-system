<?php
// public/profile.php
require_once '../config/db.php';
require_once '../includes/functions.php';

require_role(['admin', 'officer', 'detective', 'forensics', 'guest']);

$user_id = $_SESSION['user_id'];

// Initial Fetch
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$page_title = "My Profile";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile - CRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex">

    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        <?php include '../includes/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6 md:p-12">

            <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">

                <div class="bg-indigo-600 h-32"></div>
                <div class="px-8 pb-8">
                    <div class="relative flex justify-between items-end -mt-12 mb-6">
                        <div class="flex items-end">
                            <img class="h-24 w-24 rounded-full border-4 border-white bg-white"
                                src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['full_name']); ?>&size=128&background=0D8ABC&color=fff"
                                alt="Avatar">
                            <div class="ml-4 mb-2">
                                <h1 class="text-2xl font-bold text-gray-800">
                                    <?php echo htmlspecialchars($user['full_name']); ?>
                                </h1>
                                <p class="text-sm text-gray-500">@
                                    <?php echo htmlspecialchars($user['username']); ?> •
                                    <?php echo ucfirst($user['role']); ?>
                                </p>
                            </div>
                        </div>
                        <div class="mb-2">
                            <span
                                class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold border border-indigo-200">
                                Active Account
                            </span>
                        </div>
                    </div>

                    <form action="../views/user_actions.php" method="POST" class="space-y-6">
                        <input type="hidden" name="action" value="update_profile">
                        <!-- We need to add 'update_profile' logic or reuse 'update_user' but restricted to self -->
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed"
                                    readonly title="Contact Admin to change email">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Badge Number</label>
                                <input type="text"
                                    value="<?php echo htmlspecialchars($user['badge_number'] ?? 'N/A'); ?>"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed"
                                    readonly>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Security Settings</h3>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-lock text-yellow-600 mt-1"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Change Password</h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <div class="grid grid-cols-1 gap-4">
                                                <input type="password" name="new_password" placeholder="New Password"
                                                    class="w-full px-3 py-2 border border-yellow-300 rounded bg-white placeholder-yellow-400 focus:ring-yellow-500 focus:border-yellow-500">
                                                <input type="password" name="confirm_password"
                                                    placeholder="Confirm New Password"
                                                    class="w-full px-3 py-2 border border-yellow-300 rounded bg-white placeholder-yellow-400 focus:ring-yellow-500 focus:border-yellow-500">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                                Update Credentials
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>
</body>

</html>