<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = trim($_POST['name']);
    $role = 'donor';

    $check = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $check->execute([$email, $username]);

    if ($check->rowCount() > 0) {
        $error = "Email or username already exists!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, name, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $name, $role]);
        $success = "Registration successful! <a href='login.php'>Login now</a>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register as Donor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-card">
            <h1>Blood Donation System</h1>
            <p>Saving Lives, One Drop at a Time</p>
            <h2>Register as Donor</h2>
            <?php if ($error): ?><p class="alert-error"><?= $error ?></p><?php endif; ?>
            <?php if ($success): ?><p class="alert-success"><?= $success ?></p><?php endif; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required minlength="6">
                <button type="submit">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>