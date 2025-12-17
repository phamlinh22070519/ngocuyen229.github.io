<?php
// admin_appointments_list.php
require_once 'config.php';
$user = requireRole($pdo, ['admin','staff']);

$stmt = $pdo->query("
    SELECT a.*, d.full_name, d.blood_type
    FROM appointments a
    JOIN donors d ON d.id = a.donor_id
    ORDER BY a.appointment_date DESC
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>
<div class="card">
    <h2>Lịch hẹn hiến máu</h2>
    <p><a class="btn-primary" href="admin_appointment_add.php">Tạo lịch hẹn</a></p>
    <table class="table">
        <tr>
            <th>Người hiến</th><th>Nhóm máu</th><th>Thời gian</th>
            <th>Địa điểm</th><th>Trạng thái</th><th>Ghi chú</th>
        </tr>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['full_name']) ?></td>
                <td><?= $r['blood_type'] ?></td>
                <td><?= $r['appointment_date'] ?></td>
                <td><?= htmlspecialchars($r['location']) ?></td>
                <td><?= $r['status'] ?></td>
                <td><?= nl2br(htmlspecialchars($r['notes'] ?? '')) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'footer.php'; ?>
