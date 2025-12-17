<?php
// admin_inventory_list.php
require_once 'config.php';
$user = requireRole($pdo, ['admin','staff']);

$blood_type = $_GET['blood_type'] ?? '';
$status     = $_GET['status'] ?? '';

$sql = "
    SELECT bi.*, d.donation_code, dn.full_name
    FROM blood_inventory bi
    JOIN donations d ON d.id = bi.donation_id
    JOIN donors dn ON dn.id = d.donor_id
    WHERE 1
";
$params = [];

if ($blood_type !== '') {
    $sql .= " AND bi.blood_type = ?";
    $params[] = $blood_type;
}
if ($status !== '') {
    $sql .= " AND bi.status = ?";
    $params[] = $status;
}
$sql .= " ORDER BY bi.expiry_date ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bags = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>
<div class="card">
    <h2>Kho máu</h2>
    <form method="get" style="display:flex;gap:8px;margin-bottom:10px;">
        <select name="blood_type">
            <option value="">Nhóm máu (tất cả)</option>
            <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt): ?>
                <option value="<?= $bt ?>" <?= $blood_type===$bt?'selected':'' ?>><?= $bt ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status">
            <option value="">Trạng thái (tất cả)</option>
            <?php foreach (['available','reserved','used','expired','discarded'] as $st): ?>
                <option value="<?= $st ?>" <?= $status===$st?'selected':'' ?>><?= $st ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn">Lọc</button>
        <a class="btn-primary" href="admin_inventory_add.php">Nhập túi máu</a>
    </form>

    <table class="table">
        <tr>
            <th>Mã túi</th><th>Người hiến</th><th>Nhóm máu</th>
            <th>Thể tích</th><th>Ngày lấy</th><th>Hạn dùng</th>
            <th>Vị trí</th><th>Trạng thái</th>
        </tr>
        <?php foreach ($bags as $b): ?>
            <tr>
                <td><?= htmlspecialchars($b['blood_bag_code']) ?></td>
                <td><?= htmlspecialchars($b['full_name']) ?></td>
                <td><?= $b['blood_type'] ?></td>
                <td><?= $b['volume_ml'] ?> ml</td>
                <td><?= $b['collection_date'] ?></td>
                <td><?= $b['expiry_date'] ?></td>
                <td><?= htmlspecialchars($b['storage_location']) ?></td>
                <td><?= $b['status'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'footer.php'; ?>
