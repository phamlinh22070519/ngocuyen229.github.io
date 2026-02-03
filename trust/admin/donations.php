<?php
require_once '../config.php';
if (!hasPermission(['admin', 'staff'])) {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->query("SELECT don.*, d.full_name, d.blood_type FROM donations don JOIN donors d ON don.donor_id = d.id ORDER BY don.donation_date DESC");
$donations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Donations</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Manage Donations</h1>
        <a href="index.php">Dashboard</a>
    </header>

    <div class="container">
        <?php if (empty($donations)): ?>
            <p style="text-align:center;">No donations recorded yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Donation Code</th>
                        <th>Donor</th>
                        <th>Date</th>
                        <th>Volume</th>
                        <th>Blood Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donations as $d): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($d['donation_code']) ?></strong></td>
                        <td><?= htmlspecialchars($d['full_name']) ?></td>
                        <td><?= date('M d, Y', strtotime($d['donation_date'])) ?></td>
                        <td><?= $d['volume_ml'] ?> ml</td>
                        <td><?= $d['blood_type_collected'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>