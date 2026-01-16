<?php

/**
 * Application Entry Point
 * 
 * This is the single entry point for all HTTP requests.
 * Handles autoloading, configuration, and routing.
 * 
 * @package App
 */

// Load Composer autoloader (if available)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    // Fallback manual autoloader for development
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $base_dir = __DIR__ . '/../app/';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    });
}

use App\Core\Config;
use App\Core\Session;
use App\Core\Router;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\CSRFMiddleware;
use App\Core\Middleware\RateLimitMiddleware;
use App\Core\Middleware\RoleMiddleware;

// Initialize configuration
$config = Config::getInstance();

// Set error reporting based on environment
if ($config->isDebug()) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

// Start secure session
Session::start();

// Define BASE_URL constant for backward compatibility
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
define('BASE_URL', rtrim($protocol . "://" . $host . $scriptDir, '/'));

// Initialize router
$router = new Router();

// Define Routes with Middleware

// Public routes (no authentication required)
$router->get('/login', [App\Controllers\AuthController::class, 'login']);
$router->post('/login', [App\Controllers\AuthController::class, 'authenticate'], [
    new RateLimitMiddleware(),
    new CSRFMiddleware()
]);

// Protected routes (require authentication)
$authMiddleware = new AuthMiddleware();
$csrfMiddleware = new CSRFMiddleware();

// Dashboard
$router->get('/dashboard', [App\Controllers\DashboardController::class, 'index'], [$authMiddleware]);

// Criminals
$router->get('/criminals', [App\Controllers\CriminalController::class, 'index'], [$authMiddleware]);
$router->get('/criminals/create', [App\Controllers\CriminalController::class, 'create'], [
    $authMiddleware,
    new RoleMiddleware(['admin', 'officer'])
]);
$router->post('/criminals/store', [App\Controllers\CriminalController::class, 'store'], [
    $authMiddleware,
    new RoleMiddleware(['admin', 'officer']),
    $csrfMiddleware
]);
$router->get('/criminals/show', [App\Controllers\CriminalController::class, 'show'], [$authMiddleware]);
$router->get('/criminals/edit', [App\Controllers\CriminalController::class, 'edit'], [
    $authMiddleware,
    new RoleMiddleware(['admin', 'officer'])
]);
$router->post('/criminals/update', [App\Controllers\CriminalController::class, 'update'], [
    $authMiddleware,
    new RoleMiddleware(['admin', 'officer']),
    $csrfMiddleware
]);
$router->post('/criminals/link-case', [App\Controllers\CriminalController::class, 'linkCase'], [
    $authMiddleware,
    $csrfMiddleware
]);

// Cases
$router->get('/cases', [App\Controllers\CaseController::class, 'index'], [$authMiddleware]);
$router->get('/cases/create', [App\Controllers\CaseController::class, 'create'], [$authMiddleware]);
$router->post('/cases/store', [App\Controllers\CaseController::class, 'store'], [
    $authMiddleware,
    $csrfMiddleware
]);
$router->get('/cases/show', [App\Controllers\CaseController::class, 'show'], [$authMiddleware]);
$router->post('/cases/status', [App\Controllers\CaseController::class, 'updateStatus'], [
    $authMiddleware,
    $csrfMiddleware
]);
$router->post('/cases/note', [App\Controllers\CaseController::class, 'addNote'], [
    $authMiddleware,
    $csrfMiddleware
]);
$router->post('/cases/upload', [App\Controllers\CaseController::class, 'uploadEvidence'], [
    $authMiddleware,
    $csrfMiddleware
]);

// Users (Admin only)
$router->get('/users', [App\Controllers\UserController::class, 'index'], [
    $authMiddleware,
    new RoleMiddleware('admin')
]);
$router->get('/users/create', [App\Controllers\UserController::class, 'create'], [
    $authMiddleware,
    new RoleMiddleware('admin')
]);
$router->post('/users/store', [App\Controllers\UserController::class, 'store'], [
    $authMiddleware,
    new RoleMiddleware('admin'),
    $csrfMiddleware
]);
$router->post('/users/delete', [App\Controllers\UserController::class, 'delete'], [
    $authMiddleware,
    new RoleMiddleware('admin'),
    $csrfMiddleware
]);

// Profile
$router->get('/profile', [App\Controllers\UserController::class, 'profile'], [$authMiddleware]);
$router->post('/profile/update', [App\Controllers\UserController::class, 'updateProfile'], [
    $authMiddleware,
    $csrfMiddleware
]);

// Reports
$router->get('/reports', [App\Controllers\ReportController::class, 'index'], [$authMiddleware]);

// Evidence
$router->get('/evidence', [App\Controllers\EvidenceController::class, 'index'], [$authMiddleware]);
$router->get('/evidence/create', [App\Controllers\EvidenceController::class, 'create'], [$authMiddleware]);
$router->post('/evidence/store', [App\Controllers\EvidenceController::class, 'store'], [
    $authMiddleware,
    $csrfMiddleware
]);

// Logs
$router->get('/logs', [App\Controllers\LogsController::class, 'index'], [
    $authMiddleware,
    new RoleMiddleware(['admin', 'detective'])
]);

// Logout
$router->get('/logout', [App\Controllers\AuthController::class, 'logout'], [$authMiddleware]);

// Resolve and execute route
$router->resolve();
