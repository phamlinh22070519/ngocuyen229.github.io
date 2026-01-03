<?php
session_start();

// 1. Giả lập dữ liệu hệ thống
if (!isset($_SESSION['bookings'])) {
    $_SESSION['bookings'] = [
        1 => ['name' => 'Nguyen Van A', 'room' => '101', 'price' => '1.000.000 VND', 'secret_note' => 'Khách VIP, tặng thêm trái cây.'],
        2 => ['name' => 'Hacker Dangerous', 'room' => '666', 'price' => '0 VND', 'secret_note' => 'Đang bị theo dõi bởi an ninh.'],
        3 => ['name' => 'Tran Thi B', 'room' => '302', 'price' => '2.500.000 VND', 'secret_note' => 'Khách dị ứng với hải sản.']
    ];
}

if (!isset($_SESSION['reviews'])) {
    $_SESSION['reviews'] = [
        ['user' => 'Admin', 'msg' => 'Chào mừng quý khách đến với khách sạn PHP!']
    ];
}

// Xử lý gửi Review (Lỗi XSS ở đây)
if (isset($_POST['send_review'])) {
    // LỖI: Lưu trực tiếp không qua filter_var hay htmlspecialchars
    $_SESSION['reviews'][] = ['user' => $_POST['user'], 'msg' => $_POST['msg']];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hotel Security Lab - PHP Edition</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; display: flex; }
        .sidebar { width: 300px; background: #1c1e21; color: white; height: 100vh; padding: 20px; position: fixed; }
        .main { margin-left: 340px; padding: 20px; width: 100%; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px; }
        h2 { color: #1877f2; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        pre { background: #eee; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .danger { color: red; font-weight: bold; }
        input, button { padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #1877f2; color: white; cursor: pointer; border: none; }
        button:hover { background: #115cbf; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>🏨 Hotel Admin</h2>
    <p>Học phần: <b>Bảo mật Website</b></p>
    <hr>
    <ul>
        <li><a href="#sqli" style="color: white;">1. Lỗi SQL Injection</a></li>
        <li><a href="#xss" style="color: white;">2. Lỗi Stored XSS</a></li>
        <li><a href="#idor" style="color: white;">3. Lỗi IDOR</a></li>
    </ul>
    <p style="font-size: 0.8em; color: #888;">Lưu ý: Đây là môi trường giả lập để học tập. Tuyệt đối không sử dụng code này cho sản phẩm thực tế.</p>
</div>

<div class="main">

    <div id="sqli" class="card">
        <h2>1. Lab: Tìm kiếm khách hàng (SQL Injection)</h2>
        <p>Hệ thống tìm kiếm thông tin phòng theo tên khách hàng.</p>
        <form method="GET">
            <input type="text" name="search_name" placeholder="Nhập tên khách..." value="<?php echo $_GET['search_name'] ?? ''; ?>">
            <button type="submit">Tìm nhanh</button>
        </form>

        <?php
        if (isset($_GET['search_name'])) {
            $name = $_GET['search_name'];
            echo "<p>Câu lệnh SQL giả lập: <code>SELECT * FROM bookings WHERE name = '$name'</code></p>";
            
            // Mô phỏng lỗi Logic: Nếu thấy chuỗi tấn công ' OR '1'='1 thì trả về tất cả
            if (strpos($name, "' OR '1'='1") !== false) {
                $results = $_SESSION['bookings'];
                echo "<p class='danger'>Cảnh báo: Tấn công thành công! Bạn đã lấy được toàn bộ CSDL:</p>";
            } else {
                $results = array_filter($_SESSION['bookings'], function($b) use ($name) {
                    return strtolower($b['name']) == strtolower($name);
                });
            }
            echo "<pre>" . print_r($results, true) . "</pre>";
        }
        ?>
    </div>

    <div id="xss" class="card">
        <h2>2. Lab: Đánh giá khách sạn (Stored XSS)</h2>
        <div style="max-height: 200px; overflow-y: auto; background: #fafafa; padding: 10px; border: 1px solid #ddd;">
            <?php foreach ($_SESSION['reviews'] as $r): ?>
                <p><strong><?php echo $r['user']; ?>:</strong> <?php echo $r['msg']; // LỖI HIỂN THỊ TRỰC TIẾP ?></p>
            <?php endforeach; ?>
        </div>
        <hr>
        <form method="POST">
            <input type="text" name="user" placeholder="Tên của bạn" required> <br>
            <textarea name="msg" placeholder="Bình luận của bạn..." style="width:100%; height:80px;"></textarea> <br>
            <button type="submit" name="send_review">Gửi bình luận công khai</button>
        </form>
    </div>

    <div id="idor" class="card">
        <h2>3. Lab: Xem chi tiết đặt phòng (IDOR)</h2>
        <p>Bạn đang đăng nhập với tư cách: <b>Nguyen Van A (ID: 1)</b></p>
        <p><a href="?view_id=1">Bấm vào đây để xem chi tiết phòng của bạn</a></p>

        <?php
        if (isset($_GET['view_id'])) {
            $id = $_GET['view_id'];
            echo "<p>Đang truy vấn đơn hàng ID: <b>$id</b></p>";










            
            // LỖI: Không kiểm tra nếu $id khác 1 (id của người đang đăng nhập)
            if (isset($_SESSION['bookings'][$id])) {
                echo "<div style='border: 2px dashed red; padding: 10px;'>";
                echo "<h3>Dữ liệu nhạy cảm tìm thấy:</h3>";
                echo "Khách hàng: " . $_SESSION['bookings'][$id]['name'] . "<br>";
                echo "Số phòng: " . $_SESSION['bookings'][$id]['room'] . "<br>";
                echo "Ghi chú nội bộ: <span class='danger'>" . $_SESSION['bookings'][$id]['secret_note'] . "</span>";
                echo "</div>";
            } else {
                echo "Không tìm thấy đơn hàng.";
            }
        }
        ?>
    </div>

</div>

</body>
</html>






