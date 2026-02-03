<?php
require_once '../config.php';
if (!hasPermission(['admin', 'staff'])) {
    header('Location: ../login.php');
    exit;
}

$donor_id = $_GET['donor_id'] ?? 0;
if ($donor_id <= 0) {
    die("Invalid donor.");
}

// Lấy thông tin người hiến
$stmt = $pdo->prepare("SELECT id, full_name, blood_type FROM donors WHERE id = ?");
$stmt->execute([$donor_id]);
$donor = $stmt->fetch();

if (!$donor) {
    die("Donor not found.");
}

// Lấy danh sách kiểm tra sức khỏe ĐỦ ĐIỀU KIỆN gần nhất (5 lần mới nhất)
$stmt_hc = $pdo->prepare("
    SELECT id, check_date, hemoglobin, weight, is_normal 
    FROM health_checks 
    WHERE donor_id = ? AND is_normal = 1 
    ORDER BY check_date DESC 
    LIMIT 5
");
$stmt_hc->execute([$donor_id]);
$eligible_checks = $stmt_hc->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $health_check_id = $_POST['health_check_id'] ?? 0;
    $volume_ml = $_POST['volume_ml'] ?? 0;
    $notes = trim($_POST['notes'] ?? '');

    if ($health_check_id <= 0) {
        $error = "Please select a valid health check.";
    } elseif ($volume_ml <= 0) {
        $error = "Please select donation volume.";
    } else {
        try {
            $pdo->beginTransaction();

            // Ghi nhận lần hiến máu
            $donation_code = generateCode('DON');
            $don_stmt = $pdo->prepare("
                INSERT INTO donations 
                (donor_id, health_check_id, donation_code, donation_date, volume_ml, blood_type_collected, notes, staff_id, status) 
                VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, 'completed')
            ");
            $don_stmt->execute([
                $donor_id,
                $health_check_id,
                $donation_code,
                $volume_ml,
                $donor['blood_type'],
                $notes,
                $_SESSION['user_id']
            ]);
            $donation_id = $pdo->lastInsertId();

            // Tự động thêm túi máu vào kho
            $bag_code = generateCode('BAG');
            $expiry_date = date('Y-m-d', strtotime('+35 days'));
            $inv_stmt = $pdo->prepare("
                INSERT INTO blood_inventory 
                (donation_id, blood_bag_code, blood_type, volume_ml, collection_date, expiry_date, status) 
                VALUES (?, ?, ?, ?, CURDATE(), ?, 'available')
            ");
            $inv_stmt->execute([$donation_id, $bag_code, $donor['blood_type'], $volume_ml, $expiry_date]);

            // Cập nhật số lần hiến và ngày hiến cuối của người hiến
            $pdo->prepare("UPDATE donors SET total_donations = total_donations + 1, last_donation_date = CURDATE() WHERE id = ?")
                ->execute([$donor_id]);

            $pdo->commit();
            $success = "Donation recorded successfully! Blood bag <strong>$bag_code</strong> added to inventory (expires on " . date('M d, Y', strtotime($expiry_date)) . ").";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Record Donation - <?= htmlspecialchars($donor['full_name']) ?></title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .form-container {
            max-width: 900px;
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
        .donor-info {
            text-align: center;
            font-size: 22px;
            color: #d9534f;
            margin-bottom: 40px;
            font-weight: bold;
        }
        .info-box {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            font-size: 18px;
            color: #1565c0;
            margin-bottom: 30px;
            border-left: 6px solid #1976d2;
        }
        .warning-box {
            background: #fff3e0;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            font-size: 18px;
            color: #ef6c00;
            margin-bottom: 30px;
            border-left: 6px solid #ff9800;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #eee;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
        }
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #c31432;
            outline: none;
            box-shadow: 0 0 0 4px rgba(195, 20, 50, 0.2);
        }
        .form-group textarea {
            height: 140px;
            resize: vertical;
        }
        .submit-btn {
            display: block;
            width: 100%;
            padding: 20px;
            background: linear-gradient(45deg, #c31432, #d9534f);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 40px;
            transition: all 0.4s;
            box-shadow: 0 12px 35px rgba(195,20,50,0.5);
        }
        .submit-btn:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(195,20,50,0.7);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #c31432;
            font-weight: bold;
            font-size: 18px;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin: 20px 0;
            font-weight: bold;
            font-size: 18px;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin: 20px 0;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Record Donation</h1>
        <a href="donors.php">Back to Donors</a>
    </header>

    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Record Blood Donation</h2>
            <p class="donor-info">
                Donor: <?= htmlspecialchars($donor['full_name']) ?>
                <span style="color:#d9534f;"> | Blood Type: <?= $donor['blood_type'] ?></span>
            </p>

            <div class="info-box">
                <strong>Process:</strong> Select a recent eligible health check → Record donation → Blood bag automatically added to inventory (35-day expiry).
            </div>

            <?php if (empty($eligible_checks)): ?>
                <div class="warning-box">
                    <strong>No eligible health check found!</strong><br>
                    This donor has not passed a recent health check.<br><br>
                    <a href="add_health_check.php?donor_id=<?= $donor_id ?>" style="color:#d9534f; font-weight:bold;">
                        → Perform Health Check Now
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert-success"><?= $success ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert-error"><?= $error ?></div>
            <?php endif; ?>

            <?php if (!empty($eligible_checks)): ?>
                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Eligible Health Check *</label>
                            <select name="health_check_id" required>
                                <option value="">-- Select Recent Check --</option>
                                <?php foreach ($eligible_checks as $hc): ?>
                                    <option value="<?= $hc['id'] ?>">
                                        <?= date('M d, Y', strtotime($hc['check_date'])) ?> 
                                        (Hb: <?= $hc['hemoglobin'] ?> g/dL | Weight: <?= $hc['weight'] ?> kg)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Donated Volume (ml) *</label>
                            <select name="volume_ml" required>
                                <option value="250">250 ml</option>
                                <option value="350" selected>350 ml (Standard)</option>
                                <option value="450">450 ml</option>
                            </select>
                        </div>

                        <div class="form-group" style="grid-column: span 2;">
                            <label>Notes (optional)</label>
                            <textarea name="notes" placeholder="Any special observations about this donation..."></textarea>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        Record Donation & Add to Inventory
                    </button>
                </form>
            <?php endif; ?>

            <a href="donors.php" class="back-link">← Back to Donor List</a>
        </div>
    </div>
</body>
</html>