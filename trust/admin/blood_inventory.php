<?php
require_once '../config.php';
if (!hasPermission(['admin', 'staff'])) {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->query("
    SELECT bi.*, d.full_name, d.code as donor_code,
           DATEDIFF(bi.expiry_date, CURDATE()) as days_left
    FROM blood_inventory bi
    LEFT JOIN donations don ON bi.donation_id = don.id
    LEFT JOIN donors d ON don.donor_id = d.id
    ORDER BY bi.expiry_date ASC
");
$inventory = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Blood Inventory</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Blood Inventory</h1>
        <a href="index.php">Dashboard</a>
    </header>

    <div class="container">
        <?php if (empty($inventory)): ?>
            <p style="text-align:center;">No blood bags in inventory.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Bag Code</th>
                        <th>Blood Type</th>
                        <th>Volume</th>
                        <th>Collection Date</th>
                        <th>Expiry Date</th>
                        <th>Days Left</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventory as $bag): 
                        $days_left = $bag['days_left'];
                        $status_color = $days_left < 0 ? '#f8d7da' : ($days_left <= 7 ? '#fff3cd' : '#d4edda');
                    ?>
                    <tr style="background:<?= $status_color ?>">
                        <td><strong><?= htmlspecialchars($bag['blood_bag_code']) ?></strong></td>
                        <td style="font-weight:bold;color:#d9534f;"><?= $bag['blood_type'] ?></td>
                        <td><?= $bag['volume_ml'] ?> ml</td>
                        <td><?= date('M d, Y', strtotime($bag['collection_date'])) ?></td>
                        <td><?= date('M d, Y', strtotime($bag['expiry_date'])) ?></td>
                        <td><?= $days_left >= 0 ? "$days_left days" : "Expired" ?></td>
                        <td><?= $bag['donor_code'] ? "[{$bag['donor_code']}] {$bag['full_name']}" : "Unknown" ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>