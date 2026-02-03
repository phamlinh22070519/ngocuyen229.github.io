<?php
require_once 'config.php';
if (!hasPermission(['donor'])) {
    header('Location: login.php');
    exit;
}

$unread = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$unread->execute([$_SESSION['user_id']]);
$unread_count = $unread->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Donor Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Blood Donation System</h1>
        <span>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></span>
        <a href="logout.php">Logout</a>
    </header>

    <nav>
        <ul>
            <li><a href="my_donations.php">My Donation History</a></li>
            <li><a href="my_appointments.php">My Appointments</a></li>
            <li><a href="notifications.php">Notifications <?php if ($unread_count > 0): ?>(<?= $unread_count ?>)<?php endif; ?></a></li>
        </ul>
    </nav>

    <main class="container">
        <h2>Welcome back!</h2>
        <p>Thank you for your continued support in saving lives through blood donation.</p>
    </main>
</body>
</html>