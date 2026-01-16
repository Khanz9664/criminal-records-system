<?php
// config/db.php

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'CriminalRecordsDB');
define('DB_USER', 'root');
define('DB_PASS', '1234'); // Updated based on user input

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>