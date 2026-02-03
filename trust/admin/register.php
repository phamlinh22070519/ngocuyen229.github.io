<?php
// admin/register.php - First-time admin registration
require_once '../config.php';

// Nếu đã có admin/staff rồi → chuyển về login
$hasAdmin = $pdo->query("SELECT COUNT(*) FROM users WHERE role IN ('admin', 'staff')")->fetchColumn() > 0;
if ($hasAdmin) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $role = $_POST['role']; // admin or staff

    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long!";
    } else {
        // Kiểm tra trùng
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->execute([$email, $username]);
        if ($check->rowCount() > 0) {
            $error = "Email or username already exists!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, name, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashed, $name, $role]);
            $success = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>First Admin Registration</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Admin Panel</h1>
                <p>First Time Setup</p>
            </div>

            <h2>Create First Admin Account</h2>
            <p><strong>Note:</strong> This page is only available once. After creating an account, it will be disabled.</p>

            <?php if ($error): ?>
                <p class="alert-error"><?= $error ?></p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p class="alert-success">
                    Account created successfully!<br><br>
                    <a href="login.php"><strong>→ Click here to login</strong></a>
                </p>
            <?php else: ?>
                <form method="POST" class="auth-form">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="text" name="name" placeholder="Full Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password (min 8 chars)" required minlength="8">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>

                    <label style="display:block; margin:15px 0 10px; text-align:left;">Role</label>
                    <select name="role" required style="width:100%; padding:12px; border-radius:10px;">
                        <option value="admin">Administrator (Full Access)</option>
                        <option value="staff">Staff (Limited Access)</option>
                    </select>

                    <button type="submit">Create Admin Account</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>