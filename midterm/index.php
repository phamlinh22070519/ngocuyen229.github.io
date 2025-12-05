<?php
require_once 'connection.php';

// Lấy danh sách người hiến máu
$stmt = $pdo->query("SELECT * FROM donors");
$donors = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$donors) $donors = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Management</title>
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
            min-height: 100vh;
            backdrop-filter: brightness(0.85);
        }

        header {
            width: 100%;
            text-align: center;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(6px);
            padding: 20px 0;
            position: relative;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        header h1 {
            color: #fff;
            font-size: 2.2em;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.4);
        }

        header button {
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            background-color: #ff5f6d;
            background-image: linear-gradient(315deg, #ff5f6d 0%, #ffc371 74%);
            border: none;
            padding: 10px 18px;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        header button:hover {
            transform: translateY(-50%) scale(1.05);
            opacity: 0.9;
        }

        table {
            width: 85%;
            margin-top: 40px;
            border-collapse: collapse;
            background: rgba(255,255,255,0.85);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }

        th, td {
            padding: 14px 18px;
            text-align: center;
        }

        th {
            background: rgba(255, 100, 100, 0.9);
            color: white;
            font-size: 16px;
            letter-spacing: 0.5px;
        }

        td {
            font-size: 15px;
            background-color: rgba(255,255,255,0.9);
        }

        tr:nth-child(even) td {
            background-color: rgba(250,250,250,0.9);
        }

        a {
            text-decoration: none;
            margin: 0 5px;
            color: #007BFF;
            font-weight: 600;
            transition: 0.3s;
        }

        a:hover {
            color: #ff5f6d;
        }
@media screen and (max-width: 768px) {
            table {
                width: 95%;
                font-size: 14px;
            }

            header h1 {
                font-size: 1.6em;
            }

            header button {
                right: 15px;
                padding: 8px 14px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Donor Management</h1>
        <button onclick="window.location.href='add_donor.php'">+ Add Donor</button>
    </header>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Code</th>
                <th>Name</th>
                <th>Blood Type</th>
                <th>Phone Number</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($donors)): ?>
                <tr><td colspan="7">No donor data available.</td></tr>
            <?php else: ?>
                <?php foreach ($donors as $index => $donor): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($donor['code']) ?></td>
                        <td><?= htmlspecialchars($donor['name']) ?></td>
                        <td><?= htmlspecialchars($donor['blood_type']) ?></td>
                        <td><?= htmlspecialchars($donor['phone_number']) ?></td>
                        <td><?= htmlspecialchars($donor['status']) ?></td>
                        <td>
                            <a href="edit_donor.php?id=<?= $donor['id'] ?>">Edit</a> |
                            <a href="delete_donor.php?id=<?= $donor['id'] ?>" onclick="return confirm('Are you sure to delete this donor?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>