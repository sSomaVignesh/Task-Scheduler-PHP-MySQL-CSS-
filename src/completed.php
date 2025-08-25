<?php
// completed.php (Completed tasks list)
require_once 'db.php';
require_login();

$uid = current_user_id();
$stmt = $conn->prepare('SELECT id, title, description, deadline FROM tasks WHERE user_id = ? AND status = "completed" ORDER BY deadline DESC');
$stmt->bind_param('i', $uid);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Completed Tasks · Task Scheduler</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Completed Tasks</h1>
<nav>
    <a href="dashboard.php">Back to Dashboard</a> |
    <a href="logout.php">Logout</a>
</nav>
<table>
    <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Deadline</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= e($row['title']) ?></td>
            <td><?= e($row['description']) ?></td>
            <td><?= e($row['deadline']) ?></td>
        </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
