<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Criminal - CRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex">

    <?php include dirname(__DIR__) . '/layouts/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        <?php include dirname(__DIR__) . '/layouts/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6 md:p-12">

            <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg border border-gray-100 p-8">
                <div class="border-b border-gray-100 pb-6 mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Edit Criminal Record</h2>
                        <p class="text-gray-500 mt-1">Update profile for ID: #
                            <?php echo $criminal['id']; ?>
                        </p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/criminals/show?id=<?php echo $criminal['id']; ?>"
                        class="text-gray-500 hover:text-gray-700"><i class="fas fa-times"></i> Cancel</a>
                </div>

                <form action="<?php echo BASE_URL; ?>/criminals/update" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    <input type="hidden" name="id" value="<?php echo $criminal['id']; ?>">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Photo Upload Section -->
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mugshot / Photo</label>
                            <div
                                class="border-2 border-dashed border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center text-center h-64 bg-gray-50 cursor-pointer hover:bg-gray-100 transition relative">
                                <?php if (!empty($criminal['photo_path']) && $criminal['photo_path'] !== 'default_avatar.png'): ?>
                                    <img src="<?php echo BASE_URL; ?>/uploads/<?php echo htmlspecialchars($criminal['photo_path']); ?>"
                                        class="h-32 w-32 object-cover rounded-full mb-2">
                                <?php else: ?>
                                    <i class="fas fa-camera text-4xl text-gray-400 mb-2"></i>
                                <?php endif; ?>
                                <span class="text-sm text-gray-500">Click to change photo</span>
                                <input type="file" name="photo" class="absolute inset-0 opacity-0 cursor-pointer">
                            </div>
                        </div>

                        <!-- Info Section -->
                        <div class="col-span-2 space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                    <input type="text" name="first_name" required
                                        value="<?php echo htmlspecialchars($criminal['first_name']); ?>"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                    <input type="text" name="last_name" required
                                        value="<?php echo htmlspecialchars($criminal['last_name']); ?>"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                    <input type="date" name="date_of_birth"
                                        value="<?php echo $criminal['date_of_birth']; ?>"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="status"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                                        <?php $statuses = ['Wanted', 'In Custody', 'Released', 'Deceased']; ?>
                                        <?php foreach ($statuses as $s): ?>
                                            <option value="<?php echo $s; ?>" <?php echo $criminal['status'] === $s ? 'selected' : ''; ?>>
                                                <?php echo $s; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                    <select name="gender"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                                        <?php $genders = ['Male', 'Female', 'Other']; ?>
                                        <?php foreach ($genders as $g): ?>
                                            <option value="<?php echo $g; ?>" <?php echo $criminal['gender'] === $g ? 'selected' : ''; ?>>
                                                <?php echo $g; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Blood Type</label>
                                    <select name="blood_type"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                                        <option value="">Unknown</option>
                                        <?php $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']; ?>
                                        <?php foreach ($bloodTypes as $bt): ?>
                                            <option value="<?php echo $bt; ?>" <?php echo $criminal['blood_type'] === $bt ? 'selected' : ''; ?>>
                                                <?php echo $bt; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea name="address" rows="2"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($criminal['address']); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition transform hover:scale-105">
                            Update Record
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>