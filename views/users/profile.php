<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile - CRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex">

    <?php include dirname(__DIR__) . '/layouts/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        <?php include dirname(__DIR__) . '/layouts/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6 md:p-12">

            <?php if (isset($_GET['success'])): ?>
                <div class="max-w-2xl mx-auto bg-green-100 text-green-700 p-4 rounded mb-6 text-center">
                    Profile updated successfully.
                </div>
            <?php endif; ?>

            <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg border border-gray-100 p-8">
                <div class="border-b border-gray-100 pb-6 mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">My Profile Settings</h2>
                        <p class="text-gray-500 mt-1">Manage your account information.</p>
                    </div>
                </div>

                <form action="<?php echo BASE_URL; ?>/profile/update" method="POST" class="space-y-6">
                    <div class="flex items-center gap-6 mb-6">
                        <div
                            class="h-20 w-20 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-3xl font-bold">
                            <?php echo strtoupper(substr($user['username'], 0, 2)); ?>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">
                                <?php echo htmlspecialchars($user['full_name']); ?>
                            </h3>
                            <p class="text-gray-500">
                                <?php echo ucfirst($user['role']); ?>
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="border-t pt-6 mt-6">
                        <h4 class="text-lg font-bold mb-4 text-gray-800">Change Password</h4>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password (leave blank to keep
                            current)</label>
                        <input type="password" name="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition transform hover:scale-105">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>