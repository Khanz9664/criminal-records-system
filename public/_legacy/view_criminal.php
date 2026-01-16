<?php
// public/view_criminal.php (Actually needs to be linked from criminals.php, so previously I used '#' in criminals.php list)
// This file handles viewing a single criminal's full profile.

require_once '../config/db.php';
require_once '../includes/functions.php';

require_role(['admin', 'officer', 'detective']);

if (!isset($_GET['id'])) {
    header("Location: criminals.php");
    exit();
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM criminals WHERE id = ?");
    $stmt->execute([$id]);
    $criminal = $stmt->fetch();

    if (!$criminal) {
        die("Record not found.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$page_title = "Criminal Profile: " . $criminal['last_name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile - CRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex">

    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        <?php include '../includes/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6">

            <a href="criminals.php" class="text-gray-500 hover:text-blue-600 mb-4 inline-block"><i
                    class="fas fa-arrow-left mr-1"></i> Back to Database</a>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <!-- ID Card / Photo -->
                <div class="col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="h-64 bg-gray-200 relative group">
                            <?php if ($criminal['photo_path']): ?>
                                <img src="../public/uploads/<?php echo htmlspecialchars($criminal['photo_path']); ?>"
                                    class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-6xl"><i
                                        class="fas fa-user"></i></div>
                            <?php endif; ?>
                            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                                <h2 class="text-white text-2xl font-bold">
                                    <?php echo htmlspecialchars($criminal['first_name'] . ' ' . $criminal['last_name']); ?>
                                </h2>
                                <p class="text-gray-300">DOB:
                                    <?php echo $criminal['date_of_birth']; ?>
                                </p>
                            </div>
                        </div>
                        <div class="p-6">
                            <?php
                            $badge = match ($criminal['status']) {
                                'Wanted' => 'bg-red-500 text-white',
                                'In Custody' => 'bg-orange-500 text-white',
                                'Released' => 'bg-green-500 text-white',
                                default => 'bg-gray-500 text-white'
                            };
                            ?>
                            <div class="text-center mb-6">
                                <span
                                    class="px-4 py-2 text-sm font-bold rounded-full shadow-sm <?php echo $badge; ?> uppercase tracking-wider">
                                    <?php echo htmlspecialchars($criminal['status']); ?>
                                </span>
                            </div>

                            <div class="space-y-4">
                                <div class="flex justify-between border-b border-gray-100 pb-2">
                                    <span class="text-gray-500">Gender</span>
                                    <span class="font-medium">
                                        <?php echo $criminal['gender']; ?>
                                    </span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-2">
                                    <span class="text-gray-500">Blood Type</span>
                                    <span class="font-medium text-red-600">
                                        <?php echo $criminal['blood_type']; ?>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block mb-1">Last Known Address</span>
                                    <span class="font-medium block bg-gray-50 p-3 rounded-lg text-sm">
                                        <?php echo htmlspecialchars($criminal['address']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History & Details -->
                <div class="col-span-2 space-y-6">

                    <!-- Linked Cases & Linking Form -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Criminal History & Case
                            Involvement</h3>

                        <?php
                        // Fetch Linked Cases
                        $linkStmt = $pdo->prepare("SELECT cases.id, cases.title, cases.status, case_suspects.involvement 
                                                 FROM case_suspects 
                                                 JOIN cases ON case_suspects.case_id = cases.id 
                                                 WHERE case_suspects.criminal_id = ?");
                        $linkStmt->execute([$id]);
                        $linkedCases = $linkStmt->fetchAll();
                        ?>

                        <?php if (count($linkedCases) > 0): ?>
                            <ul class="space-y-3 mb-6">
                                <?php foreach ($linkedCases as $case): ?>
                                    <li class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                        <div>
                                            <a href="#"
                                                class="font-bold text-blue-600 hover:underline"><?php echo htmlspecialchars($case['title']); ?></a>
                                            <span class="text-xs text-gray-500 block">Involvement:
                                                <?php echo htmlspecialchars($case['involvement']); ?></span>
                                        </div>
                                        <span
                                            class="text-xs font-bold px-2 py-1 rounded bg-gray-200 text-gray-700"><?php echo $case['status']; ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="text-center py-4 text-gray-400 mb-6">
                                <i class="fas fa-folder-open text-3xl mb-2"></i>
                                <p>No active case files linked to this ID.</p>
                            </div>
                        <?php endif; ?>

                        <!-- Link New Case Form -->
                        <div class="bg-indigo-50 p-4 rounded-lg">
                            <h4 class="text-sm font-bold text-indigo-900 mb-2">Link to Investigation</h4>
                            <form action="../views/criminal_actions.php" method="POST" class="space-y-3">
                                <input type="hidden" name="action" value="link_case">
                                <input type="hidden" name="criminal_id" value="<?php echo $id; ?>">

                                <select name="case_id" required
                                    class="w-full text-sm border-gray-300 rounded focus:ring-indigo-500">
                                    <option value="">Select Case File...</option>
                                    <?php
                                    $allCases = $pdo->query("SELECT id, title FROM cases WHERE status != 'Closed' ORDER BY created_at DESC")->fetchAll();
                                    foreach ($allCases as $c) {
                                        echo "<option value='{$c['id']}'>#{$c['id']} - " . htmlspecialchars($c['title']) . "</option>";
                                    }
                                    ?>
                                </select>
                                <input type="text" name="involvement" placeholder="Role (e.g. Primary Suspect)" required
                                    class="w-full text-sm border-gray-300 rounded focus:ring-indigo-500">
                                <button type="submit"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2 rounded transition">
                                    Link Suspect
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Notes / Remarks -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Admin Actions</h3>
                        <div class="flex space-x-3">
                            <a href="edit_criminal.php?id=<?php echo $id; ?>"
                                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-center py-2 rounded shadow-sm transition">
                                <i class="fas fa-edit mr-2"></i> Edit Profile
                            </a>

                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <form action="../views/criminal_actions.php" method="POST"
                                    onsubmit="return confirm('PERMANENTLY DELETE this record?');" class="flex-1">
                                    <input type="hidden" name="action" value="delete_criminal">
                                    <input type="hidden" name="criminal_id" value="<?php echo $id; ?>">
                                    <button type="submit"
                                        class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded shadow-sm transition">
                                        <i class="fas fa-trash mr-2"></i> Delete Record
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

        </main>
    </div>
</body>

</html>