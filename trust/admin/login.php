<?php
// admin/login.php - Admin/Staff Login
session_start();
require_once '../config.php';

$error = '';

// Kiểm tra xem đã có tài khoản admin/staff nào chưa
$hasAdmin = $pdo->query("SELECT COUNT(*) FROM users WHERE role IN ('admin', 'staff')")->fetchColumn() > 0;

if (hasPermission(['admin', 'staff'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role IN ('admin', 'staff')");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Blood Donation System</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Admin Panel</h1>
                <p>Blood Donation Management System</p>
            </div>

            <h2>Admin / Staff Login</h2>

            <?php if ($error): ?>
                <p class="alert-error"><?= $error ?></p>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>

            <div class="auth-footer">
                <?php if (!$hasAdmin): ?>
                    Don't have an account? <a href="register.php"><strong>Register as Admin</strong></a>
                <?php else: ?>
                    <small>Contact system administrator if you need access.</small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>