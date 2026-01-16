<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';

// Manual connection since we want to be explicit
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

// FIX: Auto-add missing columns if they don't exist
try {
    $cStmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'status'");
    if ($cStmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN status ENUM('Active', 'Suspended') DEFAULT 'Active' AFTER role");
        echo "Added 'status' column to users table.<br>";
    }

    $cStmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'avatar'");
    if ($cStmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT 'default_avatar.png'");
        echo "Added 'avatar' column to users table.<br>";
    }
} catch (Exception $e) {
    echo "Schema update warning: " . $e->getMessage() . "<br>";
}

$password = 'password123';
$hash = password_hash($password, PASSWORD_DEFAULT);

$username = 'admin';
$email = 'admin@police.local';
$fullName = 'System Administrator';

// Check if user exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user) {
    // Update existing
    $sql = "UPDATE users SET password_hash = ?, role = 'admin', status = 'Active' WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$hash, $username]);
    echo "Admin user '$username' updated. Password reset to '$password'.<br>";
} else {
    // Create new
    $sql = "INSERT INTO users (full_name, username, email, password_hash, role, status) VALUES (?, ?, ?, ?, 'admin', 'Active')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$fullName, $username, $email, $hash]);
    echo "Admin user '$username' created. Password is '$password'.<br>";
}

echo "<a href='index.php/login'>Go to Login</a>";
