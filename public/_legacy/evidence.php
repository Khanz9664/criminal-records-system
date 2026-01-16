<?php
// public/evidence.php
require_once '../config/db.php';
require_once '../includes/functions.php';

require_role(['admin', 'detective', 'forensics']);

// Fetch all evidence
// Note: In MVP, we only had basic file uploads on 'Criminals', not 'Cases'. 
// To make this page functional without refactoring the whole case schema, 
// we will show the Criminal Photos as "Evidence" and any future Case files.
// Ideally, we'd have a separate 'evidence' table uploads. 
// For now, let's query the 'evidence' table defined in schema (if populated) 
// AND criminals photos to show "All Media".

// Actually, schema.sql DID define an `evidence` table. Let's list from there.
// If empty, user sees empty. We should probably add a form to upload evidence to a case here.

$sql = "SELECT evidence.*, cases.title as case_title, users.username as uploader 
        FROM evidence 
        JOIN cases ON evidence.case_id = cases.id 
        JOIN users ON evidence.uploaded_by = users.id 
        ORDER BY evidence.created_at DESC";
try {
    $stmt = $pdo->query($sql);
    $evidenceList = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$page_title = "Evidence Locker";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Evidence Locker - CRMS</title>
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
                    <h2 class="text-2xl font-bold text-gray-800">Digital Evidence Locker</h2>
                    <p class="text-gray-500 text-sm">Secure storage and chain of custody for digital assets.</p>
                </div>
                <!-- Upload Modal Trigger -->
                <button onclick="document.getElementById('evidenceModal').classList.remove('hidden')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition">
                    <i class="fas fa-cloud-upload-alt mr-2"></i> Upload New Item
                </button>
            </div>

            <!-- Upload Modal Overlay -->
            <div id="evidenceModal"
                class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Upload Digital Evidence</h3>
                        <button onclick="document.getElementById('evidenceModal').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="../views/case_actions.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="upload_evidence">
                        <!-- Redirect back to evidence page instead of case page -->
                        <!-- Note: case_actions.php behavior might need tweak or we accept redirect to case view. 
                             Ideally we add a hidden input 'redirect_to' but let's stick to standard behavior for now. -->

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Select Related Case</label>
                            <select name="case_id" required
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                                <option value="">-- Choose Case File --</option>
                                <?php
                                $cases = $pdo->query("SELECT id, title FROM cases WHERE status != 'Closed' ORDER BY created_at DESC")->fetchAll();
                                foreach ($cases as $c) {
                                    echo "<option value='{$c['id']}'>#{$c['id']} - " . htmlspecialchars($c['title']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Evidence Title</label>
                            <input type="text" name="title" required placeholder="e.g. Crime Scene Photo A"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        </div>
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">File Attachment</label>
                            <input type="file" name="evidence_file" required
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <div class="flex justify-end">
                            <button type="button"
                                onclick="document.getElementById('evidenceModal').classList.add('hidden')"
                                class="mr-2 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 font-bold">Upload</button>
                        </div>
                    </form>
                </div>
            </div>


    <?php if (empty($evidenceList)): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fas fa-box-open text-6xl text-gray-200 mb-4"></i>
            <h3 class="text-xl font-medium text-gray-800">Locker is Empty</h3>
            <p class="text-gray-500 mt-2">No digital evidence has been logged yet.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($evidenceList as $item): ?>
                <div
                    class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition group">
                    <div class="h-40 bg-gray-100 flex items-center justify-center relative overflow-hidden">
                        <!-- Preview Logic -->
                        <?php if (strpos($item['file_type'], 'image') !== false): ?>
                            <img src="../public/uploads/<?php echo htmlspecialchars($item['file_path']); ?>"
                                class="w-full h-full object-cover">
                        <?php else: ?>
                            <i class="fas fa-file-alt text-4xl text-gray-400"></i>
                        <?php endif; ?>

                        <div
                            class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <a href="../public/uploads/<?php echo htmlspecialchars($item['file_path']); ?>" target="_blank"
                                class="text-white bg-indigo-600Hover px-3 py-1 rounded border border-white">
                                View File
                            </a>
                        </div>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-800 truncate">
                            <?php echo htmlspecialchars($item['title']); ?>
                        </h4>
                        <p class="text-xs text-gray-500 mb-2">
                            <?php echo htmlspecialchars($item['case_title']); ?>
                        </p>
                        <div class="flex justify-between items-center text-xs text-gray-400">
                            <span><i class="fas fa-user mr-1"></i>
                                <?php echo htmlspecialchars($item['uploader']); ?>
                            </span>
                            <span>
                                <?php echo date('M d', strtotime($item['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    </main>
    </div>
</body>

</html>