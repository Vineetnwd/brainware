<?php
require_once __DIR__ . '/includes/header.php';
$stmt = $pdo->query("SELECT * FROM module LIMIT 5");
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($modules);
