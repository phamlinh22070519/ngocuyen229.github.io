<?php
// admin_dashboard.php
require_once 'config.php';
$user = requireRole($pdo, ['admin','staff']);

// thống kê đơn giản
$totalDonors = (int)$pdo->query("SELECT COUNT(*) FROM donors")->fetchColumn();
$totalDonations = (int)$pdo->query("SELECT COUNT(*) FROM donations")->fetchColumn();
$totalBagsAvailable = (int)$pdo->query("
    SELECT COUNT(*) FROM blood_inventory WHERE status = 'available'
")->fetchColumn();

$upcomingAppointments = $pdo->query("
    SELECT a.*, d.full_name
    FROM appointments a
    JOIN donors d ON d.id = a.donor_id
    WHERE a.appointment_date >= NOW()
    ORDER BY a.appointment_date ASC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>
<div class="card">
    <h2>Bảng điều khiển quản trị</h2>
    <p>Tổng số người hiến: <strong><?= $totalDonors ?></strong></p>
    <p>Tổng số lần hiến: <strong><?= $totalDonations ?></strong></p>
    <p>Số túi máu còn sử dụng (available): <strong><?= $totalBagsAvailable ?></strong></p>
</div>

<div class="card">
    <h2>Lịch hẹn sắp tới</h2>
    <?php if (!$upcomingAppointments): ?>
        <p>Không có lịch hẹn nào sắp tới.</p>
    <?php else: ?>
        <table class="table">
            <tr>
                <th>Người hiến</th>
                <th>Thời gian</th>
                <th>Địa điểm</th>
                <th>Trạng thái</th>
            </tr>
            <?php foreach ($upcomingAppointments as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['full_name']) ?></td>
                    <td><?= $a['appointment_date'] ?></td>
                    <td><?= htmlspecialchars($a['location']) ?></td>
                    <td><?= $a['status'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
