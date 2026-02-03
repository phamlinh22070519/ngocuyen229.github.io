<?php
require_once '../config.php';
if (!hasPermission(['admin', 'staff'])) {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->query("SELECT hc.*, d.full_name, d.code FROM health_checks hc JOIN donors d ON hc.donor_id = d.id ORDER BY hc.check_date DESC");
$checks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Health Checks</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Manage Health Checks</h1>
        <a href="index.php">Dashboard</a>
    </header>

    <div class="container">
        <?php if (empty($checks)): ?>
            <p style="text-align:center;">No health check records yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donor</th>
                        <th>Check Date</th>
                        <th>Weight</th>
                        <th>Hemoglobin</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($checks as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><strong>[<?= $c['code'] ?>]</strong> <?= htmlspecialchars($c['full_name']) ?></td>
                        <td><?= date('M d, Y H:i', strtotime($c['check_date'])) ?></td>
                        <td><?= $c['weight'] ?> kg</td>
                        <td><?= $c['hemoglobin'] ?> g/dL</td>
                        <td style="color:<?= $c['is_normal'] ? 'green' : 'red' ?>;font-weight:bold;">
                            <?= $c['is_normal'] ? 'Eligible' : 'Not Eligible' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>