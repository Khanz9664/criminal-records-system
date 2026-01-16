<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $criminal_id = $_POST['criminal_id'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $aliases = $_POST['aliases'];
    $physical_desc = $_POST['physical_desc'];

    $sql = "UPDATE Criminals SET name = ?, age = ?, aliases = ?, physical_desc = ? WHERE criminal_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissi", $name, $age, $aliases, $physical_desc, $criminal_id);

    if ($stmt->execute()) {
        echo "Criminal record updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Criminal Record</title>
    <link rel="stylesheet" href="update_criminal.css">
</head>
<body>
    <div class="form-container">
        <h1>Update Criminal Record</h1>
        <form method="POST" action="update_criminal.php">
            <label for="criminal_id">Criminal ID</label>
            <input type="number" id="criminal_id" name="criminal_id" required>
            
            <label for="name">Name</label>
            <input type="text" id="name" name="name">
            
            <label for="age">Age</label>
            <input type="number" id="age" name="age">
            
            <label for="aliases">Aliases</label>
            <input type="text" id="aliases" name="aliases">
            
            <label for="physical_desc">Physical Description</label>
            <textarea id="physical_desc" name="physical_desc"></textarea>
            
            <button type="submit">Update Record</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
