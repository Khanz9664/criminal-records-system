<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>File New Case - CRMS</title>
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
                        <h2 class="text-2xl font-bold text-gray-800">File Incident Report</h2>
                        <p class="text-gray-500 mt-1">Fill in the details below to open a new case file.</p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/cases" class="text-gray-500 hover:text-gray-700"><i
                            class="fas fa-times"></i> Cancel</a>
                </div>

                <form action="<?php echo BASE_URL; ?>/cases/store" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Case Title / Incident
                                    Name</label>
                                <input type="text" name="title" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g. Robbery at Downtown Bank">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Crime Type</label>
                                    <select name="type"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                                        <option value="Theft">Theft</option>
                                        <option value="Assault">Assault</option>
                                        <option value="Fraud">Fraud</option>
                                        <option value="Homicide">Homicide</option>
                                        <option value="Cybercrime">Cybercrime</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority Level</label>
                                    <select name="priority"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                                        <option value="Low">Low</option>
                                        <option value="Medium" selected>Medium</option>
                                        <option value="High">High</option>
                                        <option value="Critical">Critical</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Incident Date & Time</label>
                                <input type="datetime-local" name="incident_date" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Incident Location</label>
                                <input type="text" name="location"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    placeholder="Street Address, City">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assign Officer
                                    (Optional)</label>
                                <select name="assigned_officer_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                                    <option value="">-- Start Unassigned --</option>
                                    <?php foreach ($officers as $officer): ?>
                                        <option value="<?php echo $officer['id']; ?>">
                                            <?php echo htmlspecialchars($officer['full_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Detailed Description /
                            Statement</label>
                        <textarea name="description" rows="6"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Provide a detailed account of the incident..."></textarea>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition transform hover:scale-105">
                            Submit Case File
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>