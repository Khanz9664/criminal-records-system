<?php
// public/update_schema.php
require_once '../config/db.php';

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS case_notes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            case_id INT NOT NULL,
            user_id INT NOT NULL,
            note TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (case_id) REFERENCES cases(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    echo "<h1 style='color:green'>Database Updated Successfully!</h1>";
    echo "<p>Table <code>case_notes</code> created.</p>";
    echo "<a href='dashboard.php'>Back to Dashboard</a>";

} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage();
}
?>