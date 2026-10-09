<?php
require_once __DIR__ . '/config/db.php';
$stmt = $pdo->query("SELECT * FROM gallery");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT);
