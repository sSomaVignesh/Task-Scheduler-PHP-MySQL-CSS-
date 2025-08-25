<?php
// register.php (Sign Up)
require_once 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please fill all required fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check if username exists
        $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Username already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $stmt2->bind_param('ss', $username, $hash);
            if ($stmt2->execute()) {
                $success = 'Account created! You can now log in.';
            } else {
                $error = 'Could not create account. Try again.';
            }
            $stmt2->close();
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register · Task Scheduler</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Create Account</h1>
<?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
<?php if ($success): ?><p class="success"><?= e($success) ?></p><?php endif; ?>
<form method="POST" action="register.php">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Username
        <input type="text" name="username" required>
    </label>
    <label>Password
        <input type="password" name="password" required minlength="6">
    </label>
    <label>Confirm Password
        <input type="password" name="confirm" required minlength="6">
    </label>
    <button type="submit">Register</button>
</form>
<p>Already have an account? <a href="index.php">Login</a></p>
</body>
</html>
