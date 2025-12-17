<?php
// register_donor.php
require_once 'config.php';

if (getCurrentUser($pdo)) {
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $phone    = trim($_POST['phone'] ?? '');
    $dob      = $_POST['date_of_birth'] ?? '';
    $gender   = $_POST['gender'] ?? '';
    $blood    = $_POST['blood_type'] ?? '';
    $address  = trim($_POST['address'] ?? '');

    if (
        $name === '' || $username === '' || $email === '' ||
        $password === '' || $confirm === '' ||
        $phone === '' || $dob === '' || $gender === '' || $blood === ''
    ) {
        $errors[] = 'Vui lòng nhập đầy đủ các trường bắt buộc.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ.';
    } elseif ($password !== $confirm) {
        $errors[] = 'Mật khẩu xác nhận không khớp.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Tên đăng nhập hoặc email đã tồn tại.';
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $pdo->beginTransaction();

            // tạo user với role = donor
            $stmtUser = $pdo->prepare("
                INSERT INTO users (username, email, password_hash, name, role, is_active)
                VALUES (?, ?, ?, ?, 'donor', 1)
            ");
            $stmtUser->execute([$username, $email, $hash, $name]);
            $user_id = (int)$pdo->lastInsertId();

            // tạo donor và gắn user_id
            $code = 'HN' . date('Y') . substr(time(), -6);
            $stmtDonor = $pdo->prepare("
                INSERT INTO donors (code, full_name, phone, email,
                                    date_of_birth, gender, address, blood_type, user_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtDonor->execute([
                $code,
                $name,
                $phone,
                $email,
                $dob,
                $gender,
                $address ?: null,
                $blood,
                $user_id
            ]);

            $pdo->commit();
            header('Location: login_donor.php?registered=1');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Có lỗi khi tạo tài khoản, vui lòng thử lại.';
        }
    }
}

include 'header.php';
?>
<div class="card">
    <h2>Đăng ký tài khoản người hiến máu</h2>

    <?php if ($errors): ?>
        <div class="alert alert-error">
            <?= implode('<br>', array_map('htmlspecialchars', $errors)); ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Họ và tên *</label>
            <input type="text" name="name"
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Tên đăng nhập *</label>
            <input type="text" name="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Mật khẩu *</label>
            <input type="password" name="password">
        </div>
        <div class="form-group">
            <label>Nhập lại mật khẩu *</label>
            <input type="password" name="confirm">
        </div>
        <div class="form-group">
            <label>Số điện thoại *</label>
            <input type="text" name="phone"
                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Ngày sinh *</label>
            <input type="date" name="date_of_birth"
                   value="<?= htmlspecialchars($_POST['date_of_birth'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Giới tính *</label>
            <select name="gender">
                <option value="">-- Chọn --</option>
                <option value="male"   <?= (($_POST['gender'] ?? '')==='male')?'selected':'' ?>>Nam</option>
                <option value="female" <?= (($_POST['gender'] ?? '')==='female')?'selected':'' ?>>Nữ</option>
                <option value="other"  <?= (($_POST['gender'] ?? '')==='other')?'selected':'' ?>>Khác</option>
            </select>
        </div>
        <div class="form-group">
            <label>Nhóm máu *</label>
            <select name="blood_type">
                <option value="">-- Chọn --</option>
                <?php
                $bloodTypes = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
                $oldBlood = $_POST['blood_type'] ?? '';
                foreach ($bloodTypes as $bt):
                ?>
                    <option value="<?= $bt ?>" <?= $oldBlood===$bt?'selected':'' ?>>
                        <?= $bt ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Địa chỉ</label>
            <textarea name="address" rows="2"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
        </div>
        <button class="btn-primary" type="submit">Đăng ký</button>
    </form>
</div>
<?php include 'footer.php'; ?>
