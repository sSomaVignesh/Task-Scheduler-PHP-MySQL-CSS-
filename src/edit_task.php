<?php
// edit_task.php
require_once 'db.php';
require_login();

$uid = current_user_id();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch task (ensure ownership)
$stmt = $conn->prepare('SELECT id, title, description, deadline, status FROM tasks WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->bind_param('ii', $id, $uid);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$task) {
    http_response_code(404);
    die('Task not found.');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $deadline = $_POST['deadline'] ?? '';

    if ($title === '' || $deadline === '') {
        $error = 'Title and deadline are required.';
    } else {
        $stmt2 = $conn->prepare('UPDATE tasks SET title = ?, description = ?, deadline = ? WHERE id = ? AND user_id = ?');
        $stmt2->bind_param('sssii', $title, $description, $deadline, $id, $uid);
        if ($stmt2->execute()) {
            $success = 'Task updated.';
            // Refresh $task
            $task['title'] = $title;
            $task['description'] = $description;
            $task['deadline'] = $deadline;
        } else {
            $error = 'Could not update task.';
        }
        $stmt2->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Task · Task Scheduler</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Edit Task</h1>
<?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
<?php if ($success): ?><p class="success"><?= e($success) ?></p><?php endif; ?>
<form method="POST" action="edit_task.php?id=<?= e($task['id']) ?>">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Title
        <input type="text" name="title" value="<?= e($task['title']) ?>" required>
    </label>
    <label>Description
        <textarea name="description" rows="4"><?= e($task['description']) ?></textarea>
    </label>
    <label>Deadline
        <input type="date" name="deadline" value="<?= e($task['deadline']) ?>" required>
    </label>
    <button type="submit">Save</button>
</form>
<p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
