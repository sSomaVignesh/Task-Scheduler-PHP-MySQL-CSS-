<?php
// dashboard.php (Pending tasks list with highlight)
require_once 'db.php';
require_login();

$user_id = current_user_id();

// Fetch pending tasks, soonest first
$stmt = $conn->prepare('SELECT id, title, description, deadline FROM tasks WHERE user_id = ? AND status = "pending" ORDER BY deadline ASC');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

function deadline_color($deadlineDate) {
    $today = new DateTime('today');
    $deadline = new DateTime($deadlineDate);
    $diffDays = (int)$today->diff($deadline)->format('%r%a'); // signed days

    // Red: less than 1 day remaining (deadline today or past)
    if ($diffDays < 1) return 'red';
    // Yellow: 1–3 days remaining (inclusive)
    if ($diffDays <= 3) return 'yellow';
    // White: > 3 days
    return 'white';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard · Task Scheduler</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Welcome, <?= e($_SESSION['username'] ?? '') ?></h1>
<nav>
    <a href="add_task.php">Add Task</a> |
    <a href="completed.php">Completed Tasks</a> |
    <a href="logout.php">Logout</a>
</nav>

<h2>Pending Tasks</h2>
<table>
    <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Deadline</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): 
        $color = deadline_color($row['deadline']); ?>
        <tr class="row-<?= e($color) ?>">
            <td><?= e($row['title']) ?></td>
            <td><?= e($row['description']) ?></td>
            <td><?= e($row['deadline']) ?></td>
            <td>
                <a href="edit_task.php?id=<?= e($row['id']) ?>">Edit</a>
                <!-- Use POST with CSRF for destructive actions -->
                <form method="POST" action="complete_task.php" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                    <button type="submit">Complete</button>
                </form>
                <form method="POST" action="delete_task.php" style="display:inline;" onsubmit="return confirm('Delete this task?');">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
    <?php endwhile; $stmt->close(); ?>
</table>
</body>
</html>
