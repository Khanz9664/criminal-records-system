// views/user_actions.php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Allow any logged in user to access this script, but actions are protected individually.
require_role(['admin', 'officer', 'detective', 'forensics', 'guest']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

// ADMIN ACTIONS
if (in_array($_POST['action'], ['add_user', 'delete_user', 'update_user'])) {
require_role('admin');

// ACTION: Add User
if ($_POST['action'] === 'add_user') {
$username = sanitize($_POST['username']);
$email = sanitize($_POST['email']);
$password = $_POST['password'];
$role = sanitize($_POST['role']);
$full_name = sanitize($_POST['full_name']);
$badge = sanitize($_POST['badge_number']);

if (empty($username) || empty($password) || empty($email)) {
header("Location: ../public/add_user.php?error=Missing Fields");
exit();
}

$check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$check->execute([$username, $email]);
if ($check->rowCount() > 0) {
header("Location: ../public/add_user.php?error=Username or Email already exists");
exit();
}

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
$sql = "INSERT INTO users (username, email, password_hash, role, full_name, badge_number) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$username, $email, $hash, $role, $full_name, $badge]);

log_activity($pdo, "Created new user: " . $username . " ($role)");
header("Location: ../public/users.php?msg=User Created Successfully");
exit();
} catch (PDOException $e) {
die("Error creating user: " . $e->getMessage());
}
}

// ACTION: Update User (Admin editing someone else)
if ($_POST['action'] === 'update_user') {
$id = $_POST['user_id'];
$email = sanitize($_POST['email']);
$role = sanitize($_POST['role']);
$full_name = sanitize($_POST['full_name']);
$badge = sanitize($_POST['badge_number']);
$password = $_POST['password'];

try {
if (!empty($password)) {
$hash = password_hash($password, PASSWORD_DEFAULT);
$sql = "UPDATE users SET email=?, role=?, full_name=?, badge_number=?, password_hash=? WHERE id=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email, $role, $full_name, $badge, $hash, $id]);
} else {
$sql = "UPDATE users SET email=?, role=?, full_name=?, badge_number=? WHERE id=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email, $role, $full_name, $badge, $id]);
}

log_activity($pdo, "Updated user profile ID: " . $id);
header("Location: ../public/users.php?msg=User Updated Successfully");
exit();
} catch (PDOException $e) {
die("Error updating user: " . $e->getMessage());
}
}

// ACTION: Delete User
if ($_POST['action'] === 'delete_user') {
$id = $_POST['user_id'];

if ($id == $_SESSION['user_id']) {
header("Location: ../public/users.php?error=Cannot delete yourself");
exit();
}

try {
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);
log_activity($pdo, "Deleted user ID: " . $id);
header("Location: ../public/users.php?msg=User Deleted");
exit();
} catch (PDOException $e) {
die("Error deleting user: " . $e->getMessage());
}
}
}

// SELF ACTIONS
if ($_POST['action'] === 'update_profile') {
$id = $_SESSION['user_id']; // Force ID to self
// Note: In profile.php I made email/badge readonly. Users typically only change password here.
// But let's allow password change.

$password = $_POST['new_password'];
$confirm = $_POST['confirm_password'];

if (!empty($password)) {
if ($password !== $confirm) {
header("Location: ../public/profile.php?error=Passwords do not match");
exit();
}
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
$stmt->execute([$hash, $id]);

log_activity($pdo, "User changed their password");
header("Location: ../public/profile.php?msg=Password Updated");
} else {
header("Location: ../public/profile.php?msg=No changes made");
}
exit();
}
}