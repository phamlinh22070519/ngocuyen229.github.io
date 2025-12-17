<?php
// admin_donor_view.php
require_once 'config.php';
$user = requireRole($pdo, ['admin','staff']);

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM donors WHERE id = ?");
$stmt->execute([$id]);
$donor = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$donor) {
    die('Không tìm thấy người hiến.');
}

$stmtHc = $pdo->prepare("
    SELECT * FROM health_checks
    WHERE donor_id = ?
    ORDER BY check_date DESC
");
$stmtHc->execute([$id]);
$healthChecks = $stmtHc->fetchAll(PDO::FETCH_ASSOC);

$stmtDon = $pdo->prepare("
    SELECT * FROM donations
    WHERE donor_id = ?
    ORDER BY donation_date DESC
");
$stmtDon->execute([$id]);
$donations = $stmtDon->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>
<div class="card">
    <h2>Thông tin người hiến: <?= htmlspecialchars($donor['full_name']) ?></h2>
    <p>Mã: <?= htmlspecialchars($donor['code']) ?> | Nhóm máu: <?= $donor['blood_type'] ?></p>
    <p>SĐT: <?= htmlspecialchars($donor['phone']) ?> | Email: <?= htmlspecialchars($donor['email']) ?></p>
    <p>Địa chỉ: <?= nl2br(htmlspecialchars($donor['address'] ?? '')) ?></p>
    <p>Lần gần nhất: <?= $donor['last_donation_date'] ?> | Tổng số lần: <?= (int)$donor['total_donations'] ?></p>
    <p style="margin-top:8px;">
        <a class="btn-primary" href="admin_health_check_add.php?donor_id=<?= $donor['id'] ?>">Thêm kiểm tra sức khỏe</a>
    </p>
</div>

<div class="card">
    <h2>Kiểm tra sức khỏe</h2>
    <table class="table">
        <tr>
            <th>Ngày</th><th>Cân nặng</th><th>Huyết áp</th><th>Hemoglobin</th><th>Kết luận</th>
        </tr>
        <?php foreach ($healthChecks as $hc): ?>
            <tr>
                <td><?= $hc['check_date'] ?></td>
                <td><?= $hc['weight'] ?></td>
                <td><?= htmlspecialchars($hc['blood_pressure']) ?></td>
                <td><?= $hc['hemoglobin'] ?></td>
                <td><?= $hc['is_normal'] ? 'Đạt' : 'Không đạt' ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="card">
    <h2>Lịch sử hiến máu</h2>
    <table class="table">
        <tr>
            <th>Mã hiến</th><th>Ngày</th><th>Thể tích</th><th>Nhóm máu</th><th>Trạng thái</th>
        </tr>
        <?php foreach ($donations as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['donation_code']) ?></td>
                <td><?= $d['donation_date'] ?></td>
                <td><?= $d['volume_ml'] ?> ml</td>
                <td><?= $d['blood_type_collected'] ?></td>
                <td><?= $d['status'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'footer.php'; ?>
