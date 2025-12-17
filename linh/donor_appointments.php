<?php
// donor_appointments.php
require_once 'config.php';

$user = requireRole($pdo, ['donor']);

// tìm donor tương ứng với user
$stmtDonor = $pdo->prepare("SELECT id FROM donors WHERE user_id = ? LIMIT 1");
$stmtDonor->execute([$user['id']]);
$donor = $stmtDonor->fetch(PDO::FETCH_ASSOC);

$appointments = [];
if ($donor) {
    $stmt = $pdo->prepare("
        SELECT * FROM appointments
        WHERE donor_id = ?
        ORDER BY appointment_date DESC
    ");
    $stmt->execute([$donor['id']]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'header.php';
?>
<div class="card">
    <h2>Lịch hẹn hiến máu của tôi</h2>

    <?php if (!$donor): ?>
        <p>Tài khoản của bạn chưa được gắn với hồ sơ người hiến máu.
           Vui lòng liên hệ nhân viên.</p>
    <?php elseif (!$appointments): ?>
        <p>Hiện bạn chưa có lịch hẹn hiến máu nào trong hệ thống.</p>
    <?php else: ?>
        <table class="table">
            <tr>
                <th>Thời gian</th>
                <th>Địa điểm</th>
                <th>Trạng thái</th>
                <th>Ghi chú</th>
            </tr>
            <?php foreach ($appointments as $a): ?>
                <tr>
                    <td><?= $a['appointment_date'] ?></td>
                    <td><?= htmlspecialchars($a['location']) ?></td>
                    <td><?= $a['status'] ?></td>
                    <td><?= nl2br(htmlspecialchars($a['notes'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
