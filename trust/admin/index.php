<?php
require_once '../config.php';
if (!hasPermission(['admin', 'staff'])) {
    header('Location: login.php');
    exit;
}

$totalDonors = $pdo->query("SELECT COUNT(*) FROM donors")->fetchColumn();
$totalDonations = $pdo->query("SELECT COUNT(*) FROM donations")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <span>Welcome, <?= htmlspecialchars($_SESSION['name']) ?> (<?= $_SESSION['role'] ?>)</span>
        <a href="../logout.php">Logout</a>
    </header>

    <nav>
        <ul>
            <li><a href="donors.php">Manage Donors</a></li>
            <li><a href="appointments.php">Manage Appointments</a></li>
            <li><a href="donations.php">Manage Donations</a></li>
            <li><a href="health_checks.php">Health Checks</a></li>
            <li><a href="blood_inventory.php">Blood Inventory</a></li>
        </ul>
    </nav>

    <main>
        <h2>System Statistics</h2>
        <p>Total Donors: <?= $totalDonors ?></p>
        <p>Total Donations: <?= $totalDonations ?></p>
    </main>
</body>
</html>