<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'db_connection.php';

// Get the report ID from the query string
$report_id = $_GET['report_id'];

// Fetch the specific report from the database
$sql = "SELECT * FROM Reports WHERE report_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $report = $result->fetch_assoc();
    $report_data = json_decode($report['data'], true);  // Decode the JSON data into an array
} else {
    echo "Report not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Details</title>
    <link rel="stylesheet" href="view_reports.css">
</head>
<body>
    <div class="report-detail-container">
        <h1><?php echo htmlspecialchars($report['report_name']); ?> - Report Details</h1>

        <p><strong>Generated By:</strong> <?php echo htmlspecialchars($report['generated_by']); ?></p>
        <p><strong>Date Generated:</strong> <?php echo date('Y-m-d H:i:s', strtotime($report['created_at'])); ?></p>

        <h2>Criminal Data</h2>

        <?php if (count($report_data) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Aliases</th>
                        <th>Arrest Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report_data as $criminal): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($criminal['criminal_id']); ?></td>
                            <td><?php echo htmlspecialchars($criminal['name']); ?></td>
                            <td><?php echo htmlspecialchars($criminal['age']); ?></td>
                            <td><?php echo htmlspecialchars($criminal['aliases']); ?></td>
                            <td><?php echo htmlspecialchars($criminal['arrest_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No data available for this report.</p>
        <?php endif; ?>

        <a href="view_reports.php">Back to Reports List</a>
    </div>
</body>
</html>
