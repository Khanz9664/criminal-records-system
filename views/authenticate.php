<?php
// views/authenticate.php
require_once '../config/db.php';
require_once '../includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        header("Location: ../public/login.php?error=All fields are required");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Login Success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];

            // Update Last Login
            $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);

            // Log Activity
            log_activity($pdo, "User logged in: " . $username);

            header("Location: ../public/dashboard.php");
            exit();
        } else {
            // Login Failed
            header("Location: ../public/login.php?error=Invalid username or password");
            exit();
        }
    } catch (PDOException $e) {
        // In production, log this error to a file instead of showing it
        header("Location: ../public/login.php?error=System Error. Please try again.");
        exit();
    }
} else {
    header("Location: ../public/login.php");
    exit();
}
?>