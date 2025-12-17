<?php
// admin_donor_delete.php
require_once 'config.php';
$user = requireRole($pdo, ['admin']); // chỉ admin được xóa

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM donors WHERE id = ?");
$stmt->execute([$id]);

header('Location: admin_donors_list.php');
exit;
