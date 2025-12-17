<?php
// config.php
// Kết nối CSDL + hàm session, phân quyền, cookie

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'blood_donation';
$username = 'root';
$password = '040903'; // đổi thành mật khẩu MySQL/Laragon của bạn

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

/**
 * Lấy user hiện tại từ session hoặc cookie remember_token
 */
function getCurrentUser(PDO $pdo): ?array {
    if (!empty($_SESSION['user'])) {
        return $_SESSION['user'];
    }

    if (!empty($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];

        $stmt = $pdo->prepare(
            "SELECT id, username, email, name, role 
             FROM users 
             WHERE MD5(CONCAT(id, username, email)) = ? 
               AND is_active = 1"
        );
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $_SESSION['user'] = $user;
            return $user;
        }
    }
    return null;
}

/**
 * Đăng nhập user (set session + cookie nếu remember)
 */
function loginUser(PDO $pdo, array $user, bool $remember = false): void {
    $_SESSION['user'] = [
        'id'       => $user['id'],
        'username' => $user['username'],
        'name'     => $user['name'],
        'email'    => $user['email'],
        'role'     => $user['role'],
    ];

    if ($remember) {
        $token = md5($user['id'] . $user['username'] . $user['email']);
        setcookie('remember_token', $token, time() + 86400 * 7, "/");
    } else {
        setcookie('remember_token', '', time() - 3600, "/");
    }
}

/**
 * Bắt buộc đăng nhập, trả về thông tin user
 */
function requireLogin(PDO $pdo): array {
    $user = getCurrentUser($pdo);
    if (!$user) {
        header('Location: login_donor.php'); // mặc định đưa về trang login donor
        exit;
    }
    return $user;
}

/**
 * Bắt buộc role thuộc danh sách $roles
 * Ví dụ: requireRole($pdo, ['admin','staff'])
 */
function requireRole(PDO $pdo, array $roles): array {
    $user = requireLogin($pdo);
    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        die('Bạn không có quyền truy cập chức năng này.');
    }
    return $user;
}

/**
 * Gửi thông báo cho user
 */
function sendNotification(PDO $pdo, int $user_id, string $title, string $message, string $type = 'info'): void {
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, title, message, type)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $title, $message, $type]);
}
