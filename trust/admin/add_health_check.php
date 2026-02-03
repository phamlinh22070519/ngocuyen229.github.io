<?php
require_once '../config.php';
if (!hasPermission(['admin', 'staff'])) {
    header('Location: ../login.php');
    exit;
}

$donor_id = $_GET['donor_id'] ?? 0;
$stmt = $pdo->prepare("SELECT id, full_name, blood_type FROM donors WHERE id = ?");
$stmt->execute([$donor_id]);
$donor = $stmt->fetch();

if (!$donor) {
    die("Donor not found.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $weight = $_POST['weight'] ?? 0;
    $blood_pressure = trim($_POST['blood_pressure'] ?? '');
    $heart_rate = $_POST['heart_rate'] ?? 0;
    $temperature = $_POST['temperature'] ?? 0;
    $hemoglobin = $_POST['hemoglobin'] ?? 0;
    $notes = trim($_POST['notes'] ?? '');

    // Tự động xác định đủ điều kiện
    $is_normal = ($hemoglobin >= 12.5 && $weight >= 45) ? 1 : 0;

    if ($weight <= 0 || $hemoglobin <= 0) {
        $error = "Please enter valid values for weight and hemoglobin.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO health_checks (donor_id, weight, blood_pressure, heart_rate, temperature, hemoglobin, is_normal, notes, staff_id, check_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$donor_id, $weight, $blood_pressure, $heart_rate, $temperature, $hemoglobin, $is_normal, $notes, $_SESSION['user_id']]);
        $success = "Health check saved successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Health Check - <?= htmlspecialchars($donor['full_name']) ?></title>
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
            font-size: 20px;
            color: #d9534f;
            margin-bottom: 40px;
            font-weight: bold;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #eee;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
        }
        .form-group input:focus,
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
        <h1>Health Check</h1>
        <a href="donors.php">Back to Donors</a>
    </header>

    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Pre-Donation Health Check</h2>
            <p class="donor-info">
                Donor: <?= htmlspecialchars($donor['full_name']) ?> 
                <span style="color:#d9534f;">(<?= $donor['blood_type'] ?>)</span>
            </p>

            <?php if ($success): ?>
                <div class="alert-success"><?= $success ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert-error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Weight (kg) *</label>
                        <input type="number" name="weight" step="0.1" placeholder="e.g. 65.5" required>
                    </div>

                    <div class="form-group">
                        <label>Blood Pressure *</label>
                        <input type="text" name="blood_pressure" placeholder="e.g. 120/80" required>
                    </div>

                    <div class="form-group">
                        <label>Heart Rate (bpm) *</label>
                        <input type="number" name="heart_rate" placeholder="e.g. 75" required>
                    </div>

                    <div class="form-group">
                        <label>Temperature (°C) *</label>
                        <input type="number" name="temperature" step="0.1" placeholder="e.g. 36.6" required>
                    </div>

                    <div class="form-group">
                        <label>Hemoglobin (g/dL) *</label>
                        <input type="number" name="hemoglobin" step="0.1" placeholder="≥12.5 for eligibility" required>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>Notes</label>
                        <textarea name="notes" placeholder="Any additional observations..."></textarea>
                    </div>
                </div>

                <p style="text-align:center; color:#666; margin:20px 0;">
                    <strong>Note:</strong> The system will automatically determine eligibility based on weight (≥45kg) and hemoglobin (≥12.5 g/dL).
                </p>

                <button type="submit" class="submit-btn">
                    Save Health Check Result
                </button>
            </form>

            <a href="donors.php" class="back-link">← Back to Donor List</a>
        </div>
    </div>
</body>
</html>