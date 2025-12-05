<?php
require_once 'connection.php';  // Kết nối với cơ sở dữ liệu

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy giá trị từ form
    $code = $_POST['code'];
    $name = $_POST['name'];
    $blood_type = $_POST['blood_type'];
    $phone_number = $_POST['phone_number'];
    $status = $_POST['status'];

    // Kiểm tra xem có người hiến máu nào đã có thông tin trùng lặp không
    $stmt = $pdo->prepare("SELECT * FROM donors WHERE phone_number = ? OR code = ?");
    $stmt->execute([$phone_number, $code]);
    $existing_donor = $stmt->fetch();

    if ($existing_donor) {
        // Nếu dữ liệu trùng, thông báo lỗi cho người dùng
        echo "A donor with this phone number or code already exists. Please check and try again.";
    } else {
        // Nếu không có trùng lặp, thêm người hiến máu mới vào cơ sở dữ liệu
        $stmt = $pdo->prepare("INSERT INTO donors (code, name, blood_type, phone_number, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$code, $name, $blood_type, $phone_number, $status]);

        // Chuyển hướng về trang index.php sau khi thêm thành công
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Donor</title>
    <style>
    /* Reset */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: url('donor.png') no-repeat center center fixed;
        background-size: cover;
        color: #333;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        backdrop-filter: brightness(0.85);
    }

    .form-container {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(8px);
        padding: 30px 40px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        width: 400px;
        text-align: center;
    }

    .form-container h1 {
        color: #333;
        font-size: 1.8em;
        margin-bottom: 25px;
        font-weight: 700;
    }

    label {
        display: block;
        margin-bottom: 5px;
        color: #444;
        font-weight: 600;
        text-align: left;
    }

    input[type="text"], input[type="password"], input[type="email"] {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 10px;
        box-sizing: border-box;
        font-size: 15px;
    }

    button {
        width: 100%;
        background-image: linear-gradient(315deg, #ff5f6d 0%, #ffc371 74%);
        border: none;
        padding: 12px;
        font-size: 16px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }

    button:hover {
        transform: scale(1.05);
        opacity: 0.9;
    }

    a.back {
        display: block;
        text-align: center;
        margin-top: 15px;
        text-decoration: none;
        color: #007BFF;
        font-weight: 600;
        transition: 0.3s;
    }

    a.back:hover {
        color: #ff5f6d;
        text-decoration: underline;
    }

    @media screen and (max-width: 500px) {
        .form-container {
            width: 90%;
            padding: 20px;
        }

        .form-container h1 {
            font-size: 1.5em;
        }

        button {
            padding: 10px;
            font-size: 15px;
        }
    }
</style>

</head>
<body>
<div class="form-container">
        <h1>Add Donor</h1>
        <form action="add_donor.php" method="POST">
            <label for="code">Code</label>
            <input type="text" name="code" required>

            <label for="name">Name</label>
            <input type="text" name="name" required>

            <label for="blood_type">Blood Type</label>
            <input type="text" name="blood_type" required>

            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" required>

            <label for="status">Status</label>
            <input type="text" name="status" required>

            <button type="submit">Add Donor</button>
        </form>
        <a href="index.php" class="back">← Back to list</a>
    </div>
</body>
</html>