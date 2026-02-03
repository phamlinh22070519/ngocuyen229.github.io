<?php
require_once 'config.php';
if (!hasPermission(['donor'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT d.* FROM donations d JOIN donors dn ON d.donor_id = dn.id WHERE dn.user_id = ? ORDER BY d.donation_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$donations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Donation History</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>My Donation History</h1>
        <a href="index.php">Dashboard</a>
    </header>

    <div class="container">
        <?php if (empty($donations)): ?>
            <p style="text-align:center;">You have not donated yet. Thank you for considering donating!</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Donation Code</th>
                        <th>Date</th>
                        <th>Volume</th>
                        <th>Blood Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donations as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['donation_code']) ?></td>
                        <td><?= date('M d, Y', strtotime($d['donation_date'])) ?></td>
                        <td><?= $d['volume_ml'] ?> ml</td>
                        <td><?= $d['blood_type_collected'] ?></td>
                        <td><?= ucfirst($d['status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>