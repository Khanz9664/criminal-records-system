<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Evidence - CRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex">

    <?php include dirname(__DIR__) . '/layouts/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        <?php include dirname(__DIR__) . '/layouts/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6 md:p-12">
            <div class="max-w-xl mx-auto bg-white rounded-xl shadow-lg border border-gray-100 p-8">
                <div class="border-b border-gray-100 pb-6 mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">New Evidence</h2>
                        <p class="text-gray-500 mt-1">Upload digital evidence to the locker.</p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/evidence" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>

                <form action="<?php echo BASE_URL; ?>/evidence/store" method="POST" enctype="multipart/form-data"
                    class="space-y-6">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Related Case</label>
                        <select name="case_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">Select a Case...</option>
                            <?php foreach ($cases as $case): ?>
                                <option value="<?php echo $case['id']; ?>">
                                    #
                                    <?php echo $case['id']; ?> -
                                    <?php echo htmlspecialchars($case['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Only active (open) cases are listed.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Evidence Title</label>
                        <input type="text" name="title" required placeholder="e.g. CCTV Footage, Forensic Report"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">File Attachment</label>
                        <div
                            class="border-2 border-dashed border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center text-center bg-gray-50 hover:bg-gray-100 transition relative cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-4xl text-blue-400 mb-2"></i>
                            <span class="text-sm text-gray-600 font-medium">Click to upload file</span>
                            <span class="text-xs text-gray-400 mt-1">Supports Images, PDF, Word, Text</span>
                            <input type="file" name="evidence_file" required
                                class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md transition transform hover:scale-[1.02]">
                            Upload to Locker
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>