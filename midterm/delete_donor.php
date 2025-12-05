<?php
require_once 'connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM donors WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: index.php");
}
?>
