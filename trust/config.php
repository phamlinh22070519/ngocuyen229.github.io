<?php
// config.php - Database connection and utilities
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'blood_donation';
$username = 'root';
$password = '040903'; // Thay nếu bạn có mật khẩu khác

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Permission check
function hasPermission($requiredRoles = ['donor']) {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], $requiredRoles);
}

// Generate unique code
function generateCode($prefix = 'DON') {
    return $prefix . '-' . date('Ymd') . '-' . rand(100000, 999999);
}

// Send notification
function sendNotification($user_id, $title, $message, $type = 'info') {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$user_id, $title, $message, $type]);
}
?>