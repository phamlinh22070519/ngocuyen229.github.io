<?php
// admin_donors_list.php
require_once 'config.php';
$user = requireRole($pdo, ['admin','staff']);

$keyword = trim($_GET['q'] ?? '');

$sql = "SELECT * FROM donors WHERE 1";
$params = [];

if ($keyword !== '') {
    $sql .= " AND (full_name LIKE ? OR phone LIKE ? OR code LIKE ?)";
    $like = "%$keyword%";
    $params = [$like, $like, $like];
}

$sql .= " ORDER BY created_at DESC LIMIT 200";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$donors = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>
<div class="card">
    <h2>Danh sách người hiến máu</h2>
    <form method="get" style="display:flex;gap:8px;margin-bottom:10px;">
        <input type="text" name="q" placeholder="Tìm theo tên, SĐT, mã hiến"
               value="<?= htmlspecialchars($keyword) ?>">
        <button class="btn">Tìm kiếm</button>
        <a class="btn-primary" href="admin_donor_add.php">Thêm người hiến</a>
    </form>

    <table class="table">
        <tr>
            <th>Mã</th>
            <th>Họ tên</th>
            <th>Nhóm máu</th>
            <th>SĐT</th>
            <th>Lần gần nhất</th>
            <th>Tổng lần hiến</th>
            <th>Thao tác</th>
        </tr>
        <?php foreach ($donors as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['code']) ?></td>
                <td><?= htmlspecialchars($d['full_name']) ?></td>
                <td><?= $d['blood_type'] ?></td>
                <td><?= htmlspecialchars($d['phone']) ?></td>
                <td><?= $d['last_donation_date'] ?></td>
                <td><?= (int)$d['total_donations'] ?></td>
                <td>
                    <a class="btn" href="admin_donor_view.php?id=<?= $d['id'] ?>">Chi tiết</a>
                    <a class="btn" href="admin_donor_edit.php?id=<?= $d['id'] ?>">Sửa</a>
                    <a class="btn" href="admin_donor_delete.php?id=<?= $d['id'] ?>"
                       onclick="return confirm('Xóa người hiến này?');">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'footer.php'; ?>
