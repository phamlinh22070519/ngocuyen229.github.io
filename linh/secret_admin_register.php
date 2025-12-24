<?php
// secret_admin_register.php - TRANG BÍ MẬT CHỈ BẠN BIẾT
// ĐỔI TÊN FILE THÀNH: x7k9p2m_register.php hoặc tên khó đoán khác
require_once 'config.php';

if (getCurrentUser($pdo)) {
    header('Location: admin_dashboard.php');
    exit;
}

// MÃ BẢO VỆ: ĐỔI THÀNH MÃ RIÊNG CỦA BẠN
$secret_code = 'ADMIN2025_BLOOD'; 
if (!isset($_GET['code']) || $_GET['code'] !== $secret_code) {
    http_response_code(403);
    die('❌ Access denied. Wrong or missing code.');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($name === '' || $username === '' || $email === '' || $password === '' || $confirm === '') {
        $errors[] = 'Vui lòng nhập đầy đủ thông tin.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ.';
    } elseif ($password !== $confirm) {
        $errors[] = 'Mật khẩu xác nhận không khớp.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
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
        $stmtIns = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, name, role, is_active)
            VALUES (?, ?, ?, ?, 'admin', 1)
        ");
        $stmtIns->execute([$username, $email, $hash, $name]);
        $success = true;
    }
}

include 'header.php';
?>
<style>
/* Ẩn khỏi Google */
<meta name="robots" content="noindex, nofollow">
body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.card { max-width: 500px; margin: 50px auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
h2 { color: #fff; text-align: center; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
.alert-success { background: #10b981; color: white; }
</style>

<div class="card">
    <h2>🔐 Tạo Admin - Bí mật</h2>
    <p style="color:#ddd;font-size:14px;text-align:center;">
        <strong>Link này chỉ bạn biết!</strong><br>
        Không chia sẻ với ai khác.
    </p>

    <?php if ($success): ?>
        <div class="alert alert-success">
            ✅ <strong>Tạo admin thành công!</strong><br><br>
            <strong>Username:</strong> <?= htmlspecialchars($username) ?><br>
            <strong>Password:</strong> (đã đặt)<br><br>
            <a href="login_admin.php" class="btn-primary" style="display:block;text-align:center;padding:12px;margin-top:10px;">
                → Đăng nhập Admin ngay ←
            </a>
        </div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="alert alert-error">
            <?= implode('<br>', array_map('htmlspecialchars', $errors)); ?>
        </div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="post">
        <div class="form-group">
            <label>Họ và tên *</label>
            <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Tên đăng nhập *</label>
            <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Mật khẩu * (≥6 ký tự)</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Nhập lại mật khẩu *</label>
            <input type="password" name="confirm" required>
        </div>
        <button class="btn-primary" type="submit" style="width:100%;padding:12px;font-size:16px;">
            Tạo tài khoản Admin
        </button>
    </form>
    <?php endif; ?>

    <div style="margin-top:20px;padding:15px;background:rgba(255,255,255,0.1);border-radius:8px;font-size:12px;color:#fff;">
        <strong>🔒 Bảo mật:</strong><br>
        • Đổi <code>$secret_code</code> thành mã riêng<br>
        • Đổi tên file thành <code>x7k9p2m_register.php</code><br>
        • <strong>XÓA FILE</strong> sau khi dùng xong
    </div>
</div>
<?php include 'footer.php'; ?>
