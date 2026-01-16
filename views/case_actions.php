<?php
// views/case_actions.php
require_once '../config/db.php';
require_once '../includes/functions.php';

require_role(['admin', 'officer', 'detective', 'forensics']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    // Helper: Redirect back to case or list
    $redirect = isset($_POST['case_id']) ? "../public/view_case.php?id=" . $_POST['case_id'] : "../public/cases.php";

    try {
        // ACTION: Update Case Status
        if ($_POST['action'] === 'update_status') {
            $status = sanitize($_POST['status']);
            $case_id = $_POST['case_id'];

            $stmt = $pdo->prepare("UPDATE cases SET status = ? WHERE id = ?");
            $stmt->execute([$status, $case_id]);

            // Auto-add a system note
            $note = "Status updated to: " . $status;
            $nStmt = $pdo->prepare("INSERT INTO case_notes (case_id, user_id, note) VALUES (?, ?, ?)");
            $nStmt->execute([$case_id, $_SESSION['user_id'], $note]);

            log_activity($pdo, "Updated status of Case #$case_id to $status");
            header("Location: $redirect&msg=Status Updated");
            exit();
        }

        // ACTION: Add Note
        if ($_POST['action'] === 'add_note') {
            $note = sanitize($_POST['note']);
            $case_id = $_POST['case_id'];

            if (!empty($note)) {
                $stmt = $pdo->prepare("INSERT INTO case_notes (case_id, user_id, note) VALUES (?, ?, ?)");
                $stmt->execute([$case_id, $_SESSION['user_id'], $note]);
                // No main activity log needed for every note to avoid clutter, or maybe yes? Let's log.
                log_activity($pdo, "Added note to Case #$case_id");
            }
            header("Location: $redirect#notes");
            exit();
        }

        // ACTION: Upload Evidence
        if ($_POST['action'] === 'upload_evidence') {
            $title = sanitize($_POST['title']);
            $case_id = $_POST['case_id'];

            if (isset($_FILES['evidence_file']) && $_FILES['evidence_file']['error'] === UPLOAD_ERR_OK) {

                $uploadDir = '../public/uploads/';
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0777, true);

                $fileExt = strtolower(pathinfo($_FILES['evidence_file']['name'], PATHINFO_EXTENSION));
                $newFileName = uniqid('ev_') . '.' . $fileExt;
                $fileType = $_FILES['evidence_file']['type'];

                if (move_uploaded_file($_FILES['evidence_file']['tmp_name'], $uploadDir . $newFileName)) {
                    $stmt = $pdo->prepare("INSERT INTO evidence (case_id, title, file_path, file_type, uploaded_by) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$case_id, $title, $newFileName, $fileType, $_SESSION['user_id']]);

                    log_activity($pdo, "Uploaded evidence to Case #$case_id");
                    header("Location: $redirect&msg=Evidence Uploaded");
                    exit();
                } else {
                    die("Upload failed");
                }
            }
            header("Location: $redirect&error=No file selected");
            exit();
        }

        // Keep existing 'create_case' logic if present or if I overwrote it, I need to restore it. 
        // Wait, I am overwriting the file. I MUST restore the 'create_case' logic from Step 13 (approx).
        // Restoring 'create_case' logic within this file:

        if ($_POST['action'] === 'create_case') {
            $title = sanitize($_POST['title']);
            $type = sanitize($_POST['type']);
            $priority = sanitize($_POST['priority']);
            $status = 'Open';
            $location = sanitize($_POST['location']);
            $description = sanitize($_POST['description']);
            $incident_date = $_POST['incident_date'];
            $officer_id = !empty($_POST['assigned_officer']) ? $_POST['assigned_officer'] : null;

            $sql = "INSERT INTO cases (title, type, priority, status, location, description, incident_date, assigned_officer_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $type, $priority, $status, $location, $description, $incident_date, $officer_id]);

            $newId = $pdo->lastInsertId();
            log_activity($pdo, "Created new case: $title");

            header("Location: ../public/view_case.php?id=$newId&msg=Case Filed Successfully");
            exit();
        }

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>