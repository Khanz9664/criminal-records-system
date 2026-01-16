<?php
// includes/functions.php

session_start();

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Require a specific role to access the page
 */
function require_role($allowed_roles) {
    if (!is_logged_in()) {
        header("Location: ../public/login.php");
        exit();
    }

    // Convert string to array if single role passed
    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }

    if (!in_array($_SESSION['role'], $allowed_roles)) {
        // Redirect to their dashboard or unauthorized page
        header("Location: ../public/403.php");
        exit();
    }
}

/**
 * Sanitize User Input
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Log user activity
 */
function log_activity($pdo, $action) {
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip_address) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $action, $_SERVER['REMOTE_ADDR']]);
    }
}

/**
 * Get current user data
 */
function get_current_user_data() {
    return $_SESSION ?? null;
}
?>
