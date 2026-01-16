<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Testing DB Connection...<br>";

if (!file_exists('../config/db.php')) {
    die("Error: config/db.php not found!");
}

require_once '../config/db.php';

// If we reached here, db.php was included.
// db.php usually initiates connection immediately in the try-catch block.
// Let's verify manually if $pdo is set.

if (isset($pdo)) {
    echo "Database connection variable \$pdo is set.<br>";
    echo "Connection status: " . $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
} else {
    echo "Error: \$pdo variable is not set.";
}
