<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Evidence Locker - CRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex">

    <?php include dirname(__DIR__) . '/layouts/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        <?php include dirname(__DIR__) . '/layouts/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6 md:p-12">

            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Evidence Locker</h2>
                    <p class="text-gray-500 mt-1">Secure storage for all case-related digital evidence.</p>
                </div>
                <div class="flex gap-3">
                    <a href="<?php echo BASE_URL; ?>/evidence/create"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm font-medium text-sm flex items-center transition">
                        <i class="fas fa-plus mr-2"></i> Add Evidence
                    </a>
                    <form action="" method="GET" class="relative">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" name="search" placeholder="Search evidence..."
                            value="<?php echo htmlspecialchars($search); ?>"
                            class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white shadow-sm w-64">
                    </form>
                </div>
            </div>

            <!-- Evidence Grid -->
            <?php if (count($evidenceList) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($evidenceList as $item): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between mb-4">
                                <div
                                    class="p-3 rounded-lg <?php echo strpos($item['file_type'], 'image') !== false ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600'; ?>">
                                    <i
                                        class="fas <?php echo strpos($item['file_type'], 'image') !== false ? 'fa-image' : 'fa-file-alt'; ?> text-xl"></i>
                                </div>
                                <span class="text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded">
                                    #
                                    <?php echo $item['case_id']; ?>
                                </span>
                            </div>

                            <h4 class="font-bold text-gray-800 mb-1 truncate"
                                title="<?php echo htmlspecialchars($item['title']); ?>">
                                <?php echo htmlspecialchars($item['title']); ?>
                            </h4>
                            <a href="<?php echo BASE_URL; ?>/cases/show?id=<?php echo $item['case_id']; ?>"
                                class="text-xs text-blue-500 hover:underline block mb-3">
                                Case:
                                <?php echo htmlspecialchars($item['case_title']); ?>
                            </a>

                            <div class="flex justify-between items-center text-xs text-gray-400 border-t border-gray-50 pt-3">
                                <span>
                                    <?php echo date('M d, Y', strtotime($item['created_at'])); ?>
                                </span>
                                <a href="<?php echo BASE_URL; ?>/uploads/<?php echo $item['file_path']; ?>" target="_blank"
                                    class="text-gray-600 hover:text-blue-600 font-medium">
                                    Download <i class="fas fa-download ml-1"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12 bg-white rounded-xl border border-gray-200 border-dashed">
                    <div class="text-gray-400 mb-3">
                        <i class="fas fa-folder-open text-4xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No particular evidence found.</p>
                </div>
            <?php endif; ?>

        </main>
    </div>
</body>

</html>