<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Criminal Records Management System</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome to the Dashboard</h1>
        <h2>User Role: <?php echo $_SESSION['designation']; ?></h2>
        
        <div class="dashboard-links">
            <a href="add_criminal.php">Add New Criminal Record</a>
            <a href="update_criminal.php">Update Criminal Record</a>
            <a href="search_criminal.php">Search Criminal Records</a>
            <a href="generate_report.php">Generate Reports</a>
            <a href="view_reports.php">View Generated Reports</a>
            <a href="delete.php">Delete Criminal Record</a>
            <a href="logout.php">Logout</a>

        </div>
    </div>
</body>
</html>
