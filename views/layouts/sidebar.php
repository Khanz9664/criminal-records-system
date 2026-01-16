<?php
$role = $_SESSION['role'] ?? 'guest';
$current_uri = $_SERVER['REQUEST_URI'];
$base_url_path = parse_url(BASE_URL, PHP_URL_PATH) ?? '';

function isActive($path)
{
    global $current_uri, $base_url_path;
    // Check if current URI starts with the base path + route
    $full_path = $base_url_path . $path;
    if ($path === '/dashboard' && $current_uri === $full_path)
        return 'bg-gray-800 text-blue-400 border-r-4 border-blue-500';
    // For other paths, simple loose match
    return strpos($current_uri, $path) !== false ? 'bg-gray-800 text-blue-400 border-r-4 border-blue-500' : 'text-gray-400 hover:bg-gray-800 hover:text-white transition-colors';
}
?>
<aside class="w-64 bg-gray-900 border-r border-gray-800 hidden md:flex flex-col h-screen fixed top-0 left-0 z-20">
    <div class="h-16 flex items-center px-6 border-b border-gray-800 bg-gray-900">
        <h1 class="text-xl font-bold tracking-wider text-white">CRMS <span class="text-blue-500 text-xs">PRO</span></h1>
    </div>

    <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Main</p>

        <a href="<?php echo BASE_URL; ?>/dashboard"
            class="<?php echo isActive('/dashboard'); ?> flex items-center px-3 py-3 text-sm font-medium rounded-md group">
            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                </path>
            </svg>
            Dashboard
        </a>

        <?php if (in_array($role, ['admin'])): ?>
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Administration</p>
            <a href="<?php echo BASE_URL; ?>/users"
                class="<?php echo isActive('/users'); ?> flex items-center px-3 py-3 text-sm font-medium rounded-md group">
                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                User Management
            </a>
            <a href="<?php echo BASE_URL; ?>/logs"
                class="<?php echo isActive('/logs'); ?> flex items-center px-3 py-3 text-sm font-medium rounded-md group">
                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                Audit Logs
            </a>
        <?php endif; ?>

        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Records & Cases</p>

        <?php if (in_array($role, ['admin', 'officer', 'detective'])): ?>
            <a href="<?php echo BASE_URL; ?>/cases"
                class="<?php echo isActive('/cases'); ?> flex items-center px-3 py-3 text-sm font-medium rounded-md group">
                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                </svg>
                All Cases
            </a>
        <?php endif; ?>

        <a href="<?php echo BASE_URL; ?>/criminals"
            class="<?php echo isActive('/criminals'); ?> flex items-center px-3 py-3 text-sm font-medium rounded-md group">
            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                </path>
            </svg>
            Criminal Records
        </a>

        <?php if (in_array($role, ['admin', 'detective', 'forensics'])): ?>
            <a href="<?php echo BASE_URL; ?>/evidence"
                class="<?php echo isActive('/evidence'); ?> flex items-center px-3 py-3 text-sm font-medium rounded-md group">
                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                    </path>
                </svg>
                Evidence Locker
            </a>
        <?php endif; ?>

        <a href="<?php echo BASE_URL; ?>/reports"
            class="<?php echo isActive('/reports'); ?> flex items-center px-3 py-3 text-sm font-medium rounded-md group">
            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            Reports
        </a>
    </nav>
</aside>