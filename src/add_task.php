<?php
// add_task.php
require_once 'db.php';
require_login();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $deadline = $_POST['deadline'] ?? '';

    if ($title === '' || $deadline === '') {
        $error = 'Title and deadline are required.';
    } else {
        $stmt = $conn->prepare('INSERT INTO tasks (user_id, title, description, deadline, status) VALUES (?, ?, ?, ?, "pending")');
        $uid = current_user_id();
        $stmt->bind_param('isss', $uid, $title, $description, $deadline);
        if ($stmt->execute()) {
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Could not add task.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Task · Task Scheduler</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Add Task</h1>
<?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
<form method="POST" action="add_task.php">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Title
        <input type="text" name="title" required>
    </label>
    <label>Description
        <textarea name="description" rows="4"></textarea>
    </label>
    <label>Deadline
        <input type="date" name="deadline" required>
    </label>
    <button type="submit">Add</button>
</form>
<p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
