<?php
// public/edit_criminal.php
require_once '../config/db.php';
require_once '../includes/functions.php';

require_role(['admin', 'officer', 'detective']);

if (!isset($_GET['id'])) {
    header("Location: criminals.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM criminals WHERE id = ?");
$stmt->execute([$id]);
$criminal = $stmt->fetch();

if (!$criminal)
    die("Record not found.");

$page_title = "Edit Record: " . $criminal['last_name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Criminal - CRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex">

    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        <?php include '../includes/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6 md:p-12">

            <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg border border-gray-100 p-8">
                <div class="border-b border-gray-100 pb-6 mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Edit Criminal Record</h2>
                        <p class="text-gray-500 mt-1">Update details for ID #
                            <?php echo str_pad($criminal['id'], 6, '0', STR_PAD_LEFT); ?>
                        </p>
                    </div>
                </div>

                <form action="../views/criminal_actions.php" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    <input type="hidden" name="action" value="update_criminal">
                    <!-- Need to implement this in backend! -->
                    <input type="hidden" name="criminal_id" value="<?php echo $criminal['id']; ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Section 1: Personal Info -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold text-gray-700 border-b pb-2">Personal Information</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                    <input type="text" name="first_name"
                                        value="<?php echo htmlspecialchars($criminal['first_name']); ?>" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                    <input type="text" name="last_name"
                                        value="<?php echo htmlspecialchars($criminal['last_name']); ?>" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                <input type="date" name="date_of_birth"
                                    value="<?php echo $criminal['date_of_birth']; ?>" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                    <select name="gender"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white">
                                        <option value="Male" <?php echo $criminal['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo $criminal['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="Other" <?php echo $criminal['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Blood Type</label>
                                    <select name="blood_type"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white">
                                        <option value="Unknown" <?php echo $criminal['blood_type'] == 'Unknown' ? 'selected' : ''; ?>>Unknown</option>
                                        <option value="A+" <?php echo $criminal['blood_type'] == 'A+' ? 'selected' : ''; ?>>A+</option>
                                        <option value="A-" <?php echo $criminal['blood_type'] == 'A-' ? 'selected' : ''; ?>>A-</option>
                                        <option value="B+" <?php echo $criminal['blood_type'] == 'B+' ? 'selected' : ''; ?>>B+</option>
                                        <option value="B-" <?php echo $criminal['blood_type'] == 'B-' ? 'selected' : ''; ?>>B-</option>
                                        <option value="O+" <?php echo $criminal['blood_type'] == 'O+' ? 'selected' : ''; ?>>O+</option>
                                        <option value="O-" <?php echo $criminal['blood_type'] == 'O-' ? 'selected' : ''; ?>>O-</option>
                                        <option value="AB+" <?php echo $criminal['blood_type'] == 'AB+' ? 'selected' : ''; ?>>AB+</option>
                                        <option value="AB-" <?php echo $criminal['blood_type'] == 'AB-' ? 'selected' : ''; ?>>AB-</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Status & Address -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold text-gray-700 border-b pb-2">Status & Contact</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Current Status</label>
                                <select name="status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white">
                                    <option value="Wanted" <?php echo $criminal['status'] == 'Wanted' ? 'selected' : ''; ?>>Wanted</option>
                                    <option value="In Custody" <?php echo $criminal['status'] == 'In Custody' ? 'selected' : ''; ?>>In Custody</option>
                                    <option value="Released" <?php echo $criminal['status'] == 'Released' ? 'selected' : ''; ?>>Released</option>
                                    <option value="Deceased" <?php echo $criminal['status'] == 'Deceased' ? 'selected' : ''; ?>>Deceased</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Known Address</label>
                                <textarea name="address" rows="3" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($criminal['address']); ?></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Update Mugshot
                                    (Optional)</label>
                                <input type="file" name="photo" accept="image/*"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                                <p class="text-xs text-gray-500 mt-1">Current:
                                    <?php echo $criminal['photo_path'] ? $criminal['photo_path'] : 'None'; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 border-t border-gray-100">
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