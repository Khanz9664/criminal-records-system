<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-10 w-full">
    <div class="flex items-center">
        <!-- Mobile menu button -->
        <button class="md:hidden p-2 rounded-md hover:bg-gray-100 mr-2 text-gray-600">
            <i class="fas fa-bars"></i>
        </button>
        <h2 class="text-lg font-bold text-gray-800 uppercase tracking-tight">
            <?php echo isset($title) ? $title : 'CRMS'; ?>
        </h2>
    </div>

    <div class="flex items-center space-x-6">

        <!-- Profile Dropdown -->
        <div class="relative group">
            <button class="flex items-center space-x-2 focus:outline-none">
                <div
                    class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold border border-blue-200">
                    <?php echo strtoupper(substr($_SESSION['username'], 0, 2)); ?>
                </div>
                <span
                    class="text-gray-700 font-medium hidden md:block"><?php echo htmlspecialchars($_SESSION['user_full_name'] ?? 'User'); ?></span>
                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
            </button>
            <!-- Dropdown Menu -->
            <div
                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden group-hover:block border border-gray-100 z-50">
                <a href="<?php echo BASE_URL; ?>/profile"
                    class="block px-4 py-2 text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition"><i
                        class="fas fa-user-circle mr-2"></i> Your Profile</a>
                <div class="border-t border-gray-100 my-1"></div>
                <!-- Logout is handled by AuthController now -->
                <a href="<?php echo BASE_URL; ?>/logout"
                    class="block px-4 py-2 text-red-600 hover:bg-red-50 transition"><i
                        class="fas fa-sign-out-alt mr-2"></i> Sign out</a>
            </div>
        </div>
    </div>
</header>