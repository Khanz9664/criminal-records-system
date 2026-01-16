<?php
// public/logout.php
require_once '../config/db.php';
require_once '../includes/functions.php';

log_activity($pdo, "User logged out: " . ($_SESSION['username'] ?? 'Unknown'));

session_unset();
session_destroy();

header("Location: login.php");
exit();
?>