<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $aliases = $_POST['aliases'];
    $physical_desc = $_POST['physical_desc'];
    $arrest_date = $_POST['arrest_date'];

    $sql = "INSERT INTO Criminals (name, age, aliases, physical_desc, arrest_date) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisss", $name, $age, $aliases, $physical_desc, $arrest_date);

    if ($stmt->execute()) {
        echo "Criminal record added successfully!";
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
    <title>Add Criminal Record</title>
    <link rel="stylesheet" href="add_criminal.css">
</head>
<body>
    <div class="form-container">
        <h1>Add Criminal Record</h1>
        <form method="POST" action="add_criminal.php">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>
            
            <label for="age">Age</label>
            <input type="number" id="age" name="age" required>
            
            <label for="aliases">Aliases</label>
            <input type="text" id="aliases" name="aliases">
            
            <label for="physical_desc">Physical Description</label>
            <textarea id="physical_desc" name="physical_desc"></textarea>
            
            <label for="arrest_date">Arrest Date</label>
            <input type="date" id="arrest_date" name="arrest_date" required>
            
            <button type="submit">Add Record</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
