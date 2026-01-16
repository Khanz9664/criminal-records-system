<?php
include 'db_connection.php';

// Fetch all users
$sql = "SELECT user_id, password_hash FROM Users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Hash the password
        $hashed_password = password_hash($row['password'], PASSWORD_DEFAULT);

        // Update the password with the hashed value
        $update_sql = "UPDATE Users SET password_hash = ? WHERE user_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $hashed_password, $row['user_id']);
        $stmt->execute();
    }

    echo "Passwords updated successfully!";
} else {
    echo "No users found to update.";
}

$conn->close();
?>
