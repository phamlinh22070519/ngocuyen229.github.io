<?php
// Returns a PDO instance
$config = require __DIR__ . '/config.php';
$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
try {
$pdo = new PDO($dsn, $config['user'], $config['pass'], [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
} catch (PDOException $e) {
// In production, log errors and show friendly message
die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}