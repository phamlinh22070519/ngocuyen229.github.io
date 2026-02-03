<?php
require_once '../config.php';
if (!hasPermission(['admin', 'staff'])) {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM donors ORDER BY id DESC");
$donors = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Donors</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Manage Donors</h1>
        <a href="index.php">Dashboard</a>
    </header>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Blood Type</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donors as $d): ?>
                <tr>
                    <td><?= $d['id'] ?></td>
                    <td><?= htmlspecialchars($d['code']) ?></td>
                    <td><?= htmlspecialchars($d['full_name']) ?></td>
                    <td><?= $d['blood_type'] ?></td>
                    <td><?= $d['phone'] ?></td>
                    <td>
                        <a href="add_donation.php?donor_id=<?= $d['id'] ?>">Donate</a> |
                        <a href="add_health_check.php?donor_id=<?= $d['id'] ?>">Health Check</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="add_donor.php" class="add-new-btn">+ Add New Donor</a>
    </div>
</body>
</html>