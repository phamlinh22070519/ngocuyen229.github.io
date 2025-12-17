<?php
// admin_inventory_add.php
require_once 'config.php';
$user = requireRole($pdo, ['admin','staff']);

$stmt = $pdo->query("
    SELECT d.id, d.donation_code, d.donation_date, dn.full_name, d.blood_type_collected
    FROM donations d
    JOIN donors dn ON dn.id = d.donor_id
    LEFT JOIN blood_inventory bi ON bi.donation_id = d.id
    WHERE bi.id IS NULL AND d.status = 'completed'
    ORDER BY d.donation_date DESC
");
$donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donation_id = (int)($_POST['donation_id'] ?? 0);
    $bag_code    = trim($_POST['blood_bag_code'] ?? '');
    $volume      = (int)($_POST['volume_ml'] ?? 0);
    $collection  = $_POST['collection_date'] ?? '';
    $expiry      = $_POST['expiry_date'] ?? '';
    $location    = trim($_POST['storage_location'] ?? '');

    if (!$donation_id || $bag_code === '' || !$volume || $collection === '' || $expiry === '') {
        $errors[] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
    }

    if (!$errors) {
        $stmtBT = $pdo->prepare("SELECT blood_type_collected FROM donations WHERE id = ?");
        $stmtBT->execute([$donation_id]);
        $row = $stmtBT->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            $errors[] = 'Không tìm thấy lần hiến tương ứng.';
        } else {
            $stmtIns = $pdo->prepare("
                INSERT INTO blood_inventory
                (donation_id, blood_bag_code, blood_type, volume_ml,
                 collection_date, expiry_date, storage_location)
                VALUES (?,?,?,?,?,?,?)
            ");
            $stmtIns->execute([
                $donation_id,
                $bag_code,
                $row['blood_type_collected'],
                $volume,
                $collection,
                $expiry,
                $location ?: null
            ]);
            header('Location: admin_inventory_list.php');
            exit;
        }
    }
}

include 'header.php';
?>
<div class="card">
    <h2>Nhập túi máu vào kho</h2>
    <?php if ($errors): ?>
        <div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars',$errors)); ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Lần hiến máu</label>
            <select name="donation_id">
                <option value="">-- Chọn --</option>
                <?php foreach ($donations as $d): ?>
                    <option value="<?= $d['id'] ?>">
                        <?= htmlspecialchars($d['donation_code']) ?> -
                        <?= htmlspecialchars($d['full_name']) ?> (<?= $d['blood_type_collected'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Mã túi máu quốc gia</label><input type="text" name="blood_bag_code"></div>
        <div class="form-group"><label>Thể tích (ml)</label><input type="number" name="volume_ml" value="350"></div>
        <div class="form-group"><label>Ngày lấy máu</label><input type="date" name="collection_date" value="<?= date('Y-m-d') ?>"></div>
        <div class="form-group"><label>Hạn dùng</label><input type="date" name="expiry_date"></div>
        <div class="form-group"><label>Vị trí lưu trữ</label><input type="text" name="storage_location"></div>
        <button class="btn-primary" type="submit">Lưu túi máu</button>
    </form>
</div>
<?php include 'footer.php'; ?>
