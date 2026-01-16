<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $report_name = $_POST['report_name'];
    $user_id = $_SESSION['user_id'];

    // Fetch all criminal records
    $sql = "SELECT * FROM Criminals";
    $result = $conn->query($sql);

    // Generate report data
    $report_data = [];
    while ($row = $result->fetch_assoc()) {
        $report_data[] = $row;
    }

    // Save the report to the Reports table
    $sql = "INSERT INTO Reports (report_name, generated_by, data) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $serialized_data = json_encode($report_data);
    $stmt->bind_param("sis", $report_name, $user_id, $serialized_data);

    if ($stmt->execute()) {
        echo "Report generated successfully!";
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
    <title>Generate Report</title>
    <link rel="stylesheet" href="report.css">
</head>
<body>
    <div class="form-container">
        <h1>Generate Report</h1>
        <form method="POST" action="generate_report.php">
            <label for="report_name">Report Name</label>
            <input type="text" id="report_name" name="report_name" required>
            <button type="submit">Generate</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
