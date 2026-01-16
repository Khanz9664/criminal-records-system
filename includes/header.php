<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-10">
    <div class="flex items-center">
        <!-- Mobile menu button -->
        <button class="md:hidden p-2 rounded-md hover:bg-gray-100 mr-2 text-gray-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>
        <h2 class="text-lg font-bold text-gray-800 uppercase tracking-tight">
            <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?>
        </h2>
    </div>

    <div class="flex items-center space-x-6">
        <!-- Notifications -->
        <button
            class="relative p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <span class="sr-only">View notifications</span>
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                </path>
            </svg>
            <!-- Badge -->
            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-500"></span>
        </button>

        <!-- Profile Dropdown -->
        <div class="relative group">
            <button class="flex items-center text-sm focus:outline-none">
                <img class="h-8 w-8 rounded-full border border-gray-300"
                    src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['full_name']); ?>&background=0D8ABC&color=fff"
                    alt="Avatar">
                <span class="ml-3 font-medium text-gray-700 hidden md:block">
                    <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                </span>
                <span class="ml-1 text-xs text-gray-400 uppercase border border-gray-200 rounded px-1 hidden md:block">
                    <?php echo htmlspecialchars($_SESSION['role']); ?>
                </span>
                <svg class="ml-2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Dropdown menu -->
            <div
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 hidden group-hover:block transition ease-out duration-100">
                <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                <div class="border-t border-gray-100 my-1"></div>
                <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Sign out</a>
            </div>
        </div>
    </div>
</header>