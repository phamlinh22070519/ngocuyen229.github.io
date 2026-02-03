<?php
require_once '../config.php';
if (!hasPermission(['admin', 'staff'])) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = trim($_POST['address']);
    $blood_type = $_POST['blood_type'];
    $weight = $_POST['weight'] ?? null;

    $code = generateCode('DON');

    // Kiểm tra trùng số điện thoại
    $check = $pdo->prepare("SELECT id FROM donors WHERE phone = ?");
    $check->execute([$phone]);
    if ($check->rowCount() > 0) {
        $error = "Phone number already exists!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO donors (code, full_name, phone, email, date_of_birth, gender, address, blood_type, weight) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$code, $full_name, $phone, $email, $date_of_birth, $gender, $address, $blood_type, $weight]);
        $success = "Donor added successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Donor</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        .form-title {
            font-size: 32px;
            color: #c31432;
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .form-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
            font-size: 18px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            color: #c31432;
            margin-bottom: 8px;
            font-size: 16px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #eee;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #c31432;
            outline: none;
            box-shadow: 0 0 0 4px rgba(195, 20, 50, 0.2);
        }
        .form-group textarea {
            height: 120px;
            resize: vertical;
        }
        .submit-btn {
            display: block;
            width: 100%;
            padding: 18px;
            background: linear-gradient(45deg, #c31432, #d9534f);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 30px;
            transition: all 0.4s;
            box-shadow: 0 10px 30px rgba(195,20,50,0.4);
        }
        .submit-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(195,20,50,0.6);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #c31432;
            font-weight: bold;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <h1>Add New Donor</h1>
        <a href="donors.php">Back to List</a>
    </header>

    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Register New Blood Donor</h2>
            <p class="form-subtitle">Please fill in all required information accurately</p>

            <?php if ($success): ?>
                <div class="alert-success"><?= $success ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert-error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" required>
                    </div>

                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="text" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email">
                    </div>

                    <div class="form-group">
                        <label>Date of Birth *</label>
                        <input type="date" name="date_of_birth" required>
                    </div>

                    <div class="form-group">
                        <label>Gender *</label>
                        <select name="gender" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Blood Type *</label>
                        <select name="blood_type" required>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Weight (kg)</label>
                        <input type="number" name="weight" step="0.1" placeholder="e.g. 65.5">
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>Address</label>
                        <textarea name="address" placeholder="Full home address (optional)"></textarea>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    Add Donor to System
                </button>
            </form>

            <a href="donors.php" class="back-link">← Back to Donor List</a>
        </div>
    </div>
</body>
</html>