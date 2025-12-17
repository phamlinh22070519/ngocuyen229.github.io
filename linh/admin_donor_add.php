<?php
// admin_donor_add.php
require_once 'config.php';
$user = requireRole($pdo, ['admin','staff']);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $dob       = $_POST['date_of_birth'] ?? '';
    $gender    = $_POST['gender'] ?? '';
    $address   = trim($_POST['address'] ?? '');
    $blood     = $_POST['blood_type'] ?? '';
    $weight    = $_POST['weight'] !== '' ? (float)$_POST['weight'] : null;

    if ($full_name === '' || $phone === '' || $dob === '' || $gender === '' || $blood === '') {
        $errors[] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
    }

    if (!$errors) {
        $code = 'HN' . date('Y') . substr(time(), -6);
        $stmt = $pdo->prepare("
            INSERT INTO donors
            (code, full_name, phone, email, date_of_birth, gender, address, blood_type, weight)
            VALUES (?,?,?,?,?,?,?,?,?)
        ");
        $stmt->execute([
            $code,
            $full_name,
            $phone,
            $email ?: null,
            $dob,
            $gender,
            $address ?: null,
            $blood,
            $weight
        ]);
        header('Location: admin_donors_list.php');
        exit;
    }
}

include 'header.php';
?>
<div class="card">
    <h2>Thêm người hiến máu</h2>
    <?php if ($errors): ?>
        <div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars',$errors)); ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group"><label>Họ tên *</label><input type="text" name="full_name"></div>
        <div class="form-group"><label>SĐT *</label><input type="text" name="phone"></div>
        <div class="form-group"><label>Email</label><input type="email" name="email"></div>
        <div class="form-group"><label>Ngày sinh *</label><input type="date" name="date_of_birth"></div>
        <div class="form-group">
            <label>Giới tính *</label>
            <select name="gender">
                <option value="">-- Chọn --</option>
                <option value="male">Nam</option>
                <option value="female">Nữ</option>
                <option value="other">Khác</option>
            </select>
        </div>
        <div class="form-group"><label>Địa chỉ</label><textarea name="address" rows="2"></textarea></div>
        <div class="form-group">
            <label>Nhóm máu *</label>
            <select name="blood_type">
                <option value="">-- Chọn --</option>
                <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt): ?>
                    <option value="<?= $bt ?>"><?= $bt ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Cân nặng (kg)</label><input type="number" step="0.1" name="weight"></div>
        <button class="btn-primary" type="submit">Lưu</button>
    </form>
</div>
<?php include 'footer.php'; ?>
