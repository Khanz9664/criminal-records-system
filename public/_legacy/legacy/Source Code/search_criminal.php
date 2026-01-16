<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search_query = $_POST['search_query'];

    $sql = "SELECT * FROM Criminals WHERE name LIKE ? OR aliases LIKE ?";
    $stmt = $conn->prepare($sql);
    $like_query = "%" . $search_query . "%";
    $stmt->bind_param("ss", $like_query, $like_query);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Criminal Records</title>
    <link rel="stylesheet" href="search.css">
</head>
<body>
    <div class="form-container">
        <h1>Search Criminal Records</h1>
        <form method="POST" action="search_criminal.php">
            <label for="search_query">Search by Name or Alias</label>
            <input type="text" id="search_query" name="search_query" required>
            <button type="submit">Search</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>

    <?php if (isset($result)): ?>
        <div class="results-container">
            <h2>Search Results</h2>
            <?php if ($result->num_rows > 0): ?>
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
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['criminal_id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['age']; ?></td>
                                <td><?php echo $row['aliases']; ?></td>
                                <td><?php echo $row['arrest_date']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No records found.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html>
