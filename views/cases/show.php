<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Case #
        <?php echo $case['id']; ?> - CRMS
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex">

    <?php include dirname(__DIR__) . '/layouts/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        <?php include dirname(__DIR__) . '/layouts/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6 md:p-8">

            <a href="<?php echo BASE_URL; ?>/cases" class="text-gray-500 hover:text-blue-600 mb-6 inline-block">
                <i class="fas fa-arrow-left mr-1"></i> Back to Case Board
            </a>

            <!-- Case Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-mono font-bold">CASE-
                                <?php echo str_pad($case['id'], 6, '0', STR_PAD_LEFT); ?>
                            </span>
                            <?php
                            $statusColor = match ($case['status']) {
                                'Open' => 'bg-green-100 text-green-800',
                                'Closed' => 'bg-gray-100 text-gray-800',
                                'Cold' => 'bg-blue-100 text-blue-800',
                                default => 'bg-yellow-100 text-yellow-800'
                            };
                            ?>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide <?php echo $statusColor; ?>">
                                <?php echo $case['status']; ?>
                            </span>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            <?php echo htmlspecialchars($case['title']); ?>
                        </h1>
                        <p class="text-gray-500 flex items-center mt-2">
                            <i class="fas fa-map-marker-alt w-5 text-center mr-1"></i>
                            <?php echo htmlspecialchars($case['location']); ?>
                            <span class="mx-3">•</span>
                            <i class="far fa-calendar-alt w-5 text-center mr-1"></i>
                            <?php echo date('M d, Y', strtotime($case['incident_date'])); ?>
                        </p>
                    </div>

                    <div class="mt-4 md:mt-0 flex gap-3">
                        <?php if ($_SESSION['role'] !== 'forensics'): ?>
                            <form action="<?php echo BASE_URL; ?>/cases/status" method="POST">
                                <input type="hidden" name="case_id" value="<?php echo $case['id']; ?>">
                                <select name="status" onchange="this.form.submit()"
                                    class="bg-white border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500">
                                    <option value="Open" <?php echo $case['status'] == 'Open' ? 'selected' : ''; ?>>Mark as
                                        Open</option>
                                    <option value="Under Investigation" <?php echo $case['status'] == 'Under Investigation' ? 'selected' : ''; ?>>Investigating</option>
                                    <option value="Cold" <?php echo $case['status'] == 'Cold' ? 'selected' : ''; ?>>Mark Cold
                                    </option>
                                    <option value="Closed" <?php echo $case['status'] == 'Closed' ? 'selected' : ''; ?>>Close
                                        Case</option>
                                </select>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="prose max-w-none text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <h5 class="text-xs font-bold text-gray-400 uppercase mb-2">Incident Description</h5>
                    <p>
                        <?php echo nl2br(htmlspecialchars($case['description'])); ?>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left Column: Primary Data -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Evidence Section -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-800"><i
                                    class="fas fa-box-open mr-2 text-indigo-500"></i> Evidence Locker</h3>
                            <button onclick="document.getElementById('uploadForm').classList.toggle('hidden')"
                                class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded font-bold hover:bg-indigo-100 transition">
                                <i class="fas fa-plus mr-1"></i> Add Item
                            </button>
                        </div>

                        <div id="uploadForm" class="hidden bg-gray-50 p-4 rounded-lg mb-4 border border-indigo-100">
                            <form action="<?php echo BASE_URL; ?>/cases/upload" method="POST"
                                enctype="multipart/form-data" class="flex flex-col gap-3">
                                <input type="hidden" name="case_id" value="<?php echo $case['id']; ?>">
                                <input type="text" name="title" placeholder="Item Title (e.g. CCTV Footage)" required
                                    class="border p-2 rounded text-sm">
                                <input type="file" name="evidence_file" required class="text-sm">
                                <button type="submit"
                                    class="bg-indigo-600 text-white py-1 rounded text-sm font-bold">Upload</button>
                            </form>
                        </div>

                        <?php if (empty($evidence)): ?>
                            <div class="text-center py-6 text-gray-400 text-sm">No evidence collected yet.</div>
                        <?php else: ?>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <?php foreach ($evidence as $ev): ?>
                                    <div class="border rounded p-3 hover:shadow-md transition bg-white relative group">
                                        <div
                                            class="h-20 bg-gray-100 mb-2 flex items-center justify-center rounded overflow-hidden">
                                            <?php if (strpos($ev['file_type'], 'image') !== false): ?>
                                                <img src="<?php echo BASE_URL; ?>/uploads/<?php echo htmlspecialchars($ev['file_path']); ?>"
                                                    class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <i class="fas fa-file-alt text-2xl text-gray-400"></i>
                                            <?php endif; ?>
                                        </div>
                                        <h4 class="font-bold text-gray-800 text-sm truncate">
                                            <?php echo htmlspecialchars($ev['title']); ?>
                                        </h4>
                                        <a href="<?php echo BASE_URL; ?>/uploads/<?php echo htmlspecialchars($ev['file_path']); ?>"
                                            target="_blank" class="absolute inset-0 z-10"></a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Notes / Investigation Timeline -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-6"><i
                                class="fas fa-clipboard-list mr-2 text-blue-500"></i> Investigation Updates</h3>

                        <div class="space-y-6 mb-6 max-h-96 overflow-y-auto pr-2">
                            <?php if (empty($notes)): ?>
                                <p class="text-gray-400 text-sm italic">No updates recorded.</p>
                            <?php else: ?>
                                <?php foreach ($notes as $note): ?>
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                                <?php echo strtoupper(substr($note['username'], 0, 2)); ?>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3 w-full">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="font-bold text-sm text-gray-900">
                                                    <?php echo htmlspecialchars($note['username']); ?> <span
                                                        class="text-xs font-normal text-gray-500">(
                                                        <?php echo $note['role']; ?>)
                                                    </span>
                                                </span>
                                                <span class="text-xs text-gray-400">
                                                    <?php echo date('M d, H:i', strtotime($note['created_at'])); ?>
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-700">
                                                <?php echo nl2br(htmlspecialchars($note['note'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Add Note Form -->
                        <?php if ($_SESSION['role'] !== 'guest'): ?>
                            <form action="<?php echo BASE_URL; ?>/cases/note" method="POST" class="relative">
                                <input type="hidden" name="case_id" value="<?php echo $case['id']; ?>">
                                <textarea name="note" rows="3" required placeholder="Log new findings or updates..."
                                    class="w-full border border-gray-300 rounded-lg p-3 pr-12 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none"></textarea>
                                <button type="submit" class="absolute bottom-3 right-3 text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-paper-plane text-xl"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column: Suspects -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4"><i
                                class="fas fa-user-secret mr-2 text-red-500"></i> Linked Suspects</h3>

                        <?php if (empty($suspects)): ?>
                            <div class="text-center py-4 bg-gray-50 rounded border border-dashed border-gray-300">
                                <p class="text-gray-400 text-sm">No suspects linked.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($suspects as $s): ?>
                                    <div
                                        class="flex items-center gap-3 p-3 rounded hover:bg-gray-50 transition border border-gray-100">
                                        <img src="<?php echo BASE_URL; ?>/uploads/<?php echo $s['photo_path'] ? $s['photo_path'] : 'default.png'; ?>"
                                            class="w-10 h-10 rounded-full object-cover bg-gray-200">
                                        <div class="flex-1 min-w-0">
                                            <a href="<?php echo BASE_URL; ?>/criminals/show?id=<?php echo $s['id']; ?>"
                                                class="text-sm font-bold text-gray-900 hover:text-blue-600 truncate block">
                                                <?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?>
                                            </a>
                                            <p class="text-xs text-brand-red font-bold">
                                                <?php echo htmlspecialchars($s['involvement']); ?>
                                            </p>
                                        </div>
                                        <span class="text-xs font-bold text-gray-400 border px-1 rounded">
                                            <?php echo $s['status']; ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

        </main>
    </div>
</body>

</html>