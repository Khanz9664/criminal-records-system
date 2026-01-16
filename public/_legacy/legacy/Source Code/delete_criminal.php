<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
include 'db_connection.php';

// Check if a criminal_id is passed
if (isset($_GET['criminal_id'])) {
    $criminal_id = $_GET['criminal_id'];

    // Get the criminal record details before deletion
    $sql = "SELECT * FROM Criminals WHERE criminal_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $criminal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $criminal = $result->fetch_assoc();

    // Check if criminal record exists
    if (!$criminal) {
        echo "Record not found!";
        exit();
    }

    // Delete the record after confirmation
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $delete_sql = "DELETE FROM Criminals WHERE criminal_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $criminal_id);
        if ($delete_stmt->execute()) {
            echo "<p>Criminal record deleted successfully!</p>";
            echo "<p><a href='dashboard.php'>Back to Dashboard</a></p>";
            exit();
        } else {
            echo "Error: " . $delete_stmt->error;
        }
    }
} else {
    echo "No criminal ID provided!";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Criminal Record</title>
    <link rel="stylesheet" href="delete_criminal.css">
</head>
<body>
    <div class="delete-container">
        <h1>Delete Criminal Record</h1>
        <p>Are you sure you want to delete the following record?</p>
        <div class="criminal-details">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($criminal['name']); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($criminal['age']); ?></p>
            <p><strong>Aliases:</strong> <?php echo htmlspecialchars($criminal['aliases']); ?></p>
            <p><strong>Physical Description:</strong> <?php echo htmlspecialchars($criminal['physical_desc']); ?></p>
        </div>
        <form method="POST" action="">
            <button type="submit" class="delete-btn">Delete Record</button>
        </form>
        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
