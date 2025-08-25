<?php
// index.php (Login)
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $conn->prepare('SELECT id, password FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($uid, $hash);
        if ($stmt->fetch() && password_verify($password, $hash)) {
            $_SESSION['user_id'] = $uid;
            $_SESSION['username'] = $username;
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid credentials.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login · Task Scheduler</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Task Scheduler</h1>
<h2>Login</h2>
<?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
<form method="POST" action="index.php">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Username
        <input type="text" name="username" required>
    </label>
    <label>Password
        <input type="password" name="password" required>
    </label>
    <button type="submit">Login</button>
</form>
<p>New here? <a href="register.php">Create an account</a></p>
</body>
</html>
