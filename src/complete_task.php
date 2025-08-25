<?php
// complete_task.php (POST only)
require_once 'db.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method not allowed.');
}
verify_csrf();

$id = (int)($_POST['id'] ?? 0);
$uid = current_user_id();

$stmt = $conn->prepare('UPDATE tasks SET status = "completed" WHERE id = ? AND user_id = ?');
$stmt->bind_param('ii', $id, $uid);
$stmt->execute();
$stmt->close();

header('Location: dashboard.php');
exit();
