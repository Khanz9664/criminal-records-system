<?php
// views/criminal_actions.php
require_once '../config/db.php';
require_once '../includes/functions.php';

require_role(['admin', 'officer', 'detective']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    // ACTION: Add new criminal
    if ($_POST['action'] === 'add_criminal') {
        // ... (Existing implementation, kept for completeness of context)
        // ideally we would refactor this block, but let's assume it's there or just overwrite if I'm replacing whole file.
        // Wait, I am replacing the whole file so I should encompass the Add Logic too.

        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $dob = $_POST['date_of_birth'];
        $gender = sanitize($_POST['gender']);
        $blood = sanitize($_POST['blood_type']);
        $address = sanitize($_POST['address']);
        $status = sanitize($_POST['status']);

        $photo_path = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            // ... (Upload Logic) ...
            // Reimplementing briefly for robustness
            $uploadDir = '../public/uploads/';
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0777, true);
            $fileExt = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                $newFileName = uniqid() . '.' . $fileExt;
                move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $newFileName);
                $photo_path = $newFileName;
            }
        }

        $sql = "INSERT INTO criminals (first_name, last_name, date_of_birth, gender, blood_type, address, status, photo_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$first_name, $last_name, $dob, $gender, $blood, $address, $status, $photo_path]);

        log_activity($pdo, "Added criminal: $first_name $last_name");
        header("Location: ../public/criminals.php?msg=Record Added");
        exit();
    }

    // ACTION: Delete Criminal
    if ($_POST['action'] === 'delete_criminal') {
        require_role('admin'); // Only admin deletes records
        $id = $_POST['criminal_id'];

        // Remove photo? Optional.
        $stmt = $pdo->prepare("DELETE FROM criminals WHERE id = ?");
        $stmt->execute([$id]);

        log_activity($pdo, "Deleted criminal ID: $id");
        header("Location: ../public/criminals.php?msg=Record Deleted");
        exit();
    }

    // ACTION: Update Criminal
    if ($_POST['action'] === 'update_criminal') {
        $id = $_POST['criminal_id'];
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $dob = $_POST['date_of_birth'];
        $gender = sanitize($_POST['gender']);
        $blood = sanitize($_POST['blood_type']);
        $address = sanitize($_POST['address']);
        $status = sanitize($_POST['status']);

        // Handle Photo Update if new one provided
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../public/uploads/';
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0777, true);
            $fileExt = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                $newFileName = uniqid() . '.' . $fileExt;
                move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $newFileName);

                // Update with photo
                $sql = "UPDATE criminals SET first_name=?, last_name=?, date_of_birth=?, gender=?, blood_type=?, address=?, status=?, photo_path=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$first_name, $last_name, $dob, $gender, $blood, $address, $status, $newFileName, $id]);
            }
        } else {
            // Update without changing photo
            $sql = "UPDATE criminals SET first_name=?, last_name=?, date_of_birth=?, gender=?, blood_type=?, address=?, status=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$first_name, $last_name, $dob, $gender, $blood, $address, $status, $id]);
        }

        log_activity($pdo, "Updated criminal profile: $first_name $last_name");
        header("Location: ../public/view_criminal.php?id=$id&msg=Profile Updated");
        exit();
    }

    // ACTION: Link to Case
    if ($_POST['action'] === 'link_case') {
        $criminal_id = $_POST['criminal_id'];
        $case_id = $_POST['case_id'];
        $involvement = sanitize($_POST['involvement']);

        // Check if exists
        $check = $pdo->prepare("SELECT id FROM case_suspects WHERE case_id = ? AND criminal_id = ?");
        $check->execute([$case_id, $criminal_id]);

        if ($check->rowCount() == 0) {
            $stmt = $pdo->prepare("INSERT INTO case_suspects (case_id, criminal_id, involvement) VALUES (?, ?, ?)");
            $stmt->execute([$case_id, $criminal_id, $involvement]);
            log_activity($pdo, "Linked criminal $criminal_id to case $case_id");
            header("Location: ../public/view_criminal.php?id=$criminal_id&msg=Linked Successfully");
        } else {
            header("Location: ../public/view_criminal.php?id=$criminal_id&error=Already Linked");
        }
        exit();
    }
}
?>