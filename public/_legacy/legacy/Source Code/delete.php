<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'db_connection.php';  // Include the database connection

// Fetch criminal records from the database
$sql = "SELECT * FROM Criminals";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criminal Records - Criminal Records Management System</title>
    <link rel="stylesheet" href="delete.css"> <!-- Add your CSS file here -->
</head>
<body>
    <div class="criminal-records">
        <h2>Criminal Records</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['criminal_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['age']); ?></td>
                        <td>
                            <!-- Link to delete criminal record -->
                            <a href="delete_criminal.php?criminal_id=<?php echo $row['criminal_id']; ?>" class="delete-link">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
