<?php
// db.php
// Database connection (MySQLi). Reuse $conn everywhere by including this file.

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'task_scheduler';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

// ---- Common helpers ----
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF token helpers
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verify_csrf() {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        http_response_code(400);
        die('Invalid CSRF token.');
    }
}

// Auth helpers
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit();
    }
}
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

// Output escaping
function e($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}
