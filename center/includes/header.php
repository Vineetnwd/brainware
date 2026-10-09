<?php
ob_start(); // Prevent headers already sent errors on redirects
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';

// Enforce Center authentication guard
require_center('/login.php');

$center_user = current_user();
$center_id = (int)($center_user['center_id'] ?? 0);

// Allow admin to switch center view via GET parameter
if (is_admin() && isset($_GET['view_center_id'])) {
    $sel_id = (int)$_GET['view_center_id'];
    if ($sel_id > 0) {
        $_SESSION['center_id'] = $sel_id;
        $center_id = $sel_id;
    }
}

// Fetch full center profile
$active_center = null;
if ($pdo && $center_id > 0) {
    $c_stmt = $pdo->prepare("SELECT * FROM center_details WHERE id = ? LIMIT 1");
    $c_stmt->execute([$center_id]);
    $active_center = $c_stmt->fetch();
}

// Fallback 1: Resolve by center_code matching username
if ($pdo && !$active_center && !empty($center_user['username'])) {
    $c_stmt = $pdo->prepare("SELECT * FROM center_details WHERE LOWER(TRIM(center_code)) = LOWER(TRIM(?)) LIMIT 1");
    $c_stmt->execute([$center_user['username']]);
    $active_center = $c_stmt->fetch();
    if ($active_center) {
        $center_id = (int)$active_center['id'];
        $_SESSION['center_id'] = $center_id;
        $_SESSION['center_name'] = $active_center['center_name'];
        $_SESSION['center_code'] = $active_center['center_code'];
    }
}

// Fallback 2: Admin viewing center portal defaults to first active center
if ($pdo && !$active_center && is_admin()) {
    $c_stmt = $pdo->query("SELECT * FROM center_details WHERE status = 'ACTIVE' ORDER BY id ASC LIMIT 1");
    $active_center = $c_stmt->fetch();
    if ($active_center) {
        $center_id = (int)$active_center['id'];
        $_SESSION['center_id'] = $center_id;
        $_SESSION['center_name'] = $active_center['center_name'];
        $_SESSION['center_code'] = $active_center['center_code'];
    }
}

$page_title = isset($page_title) ? $page_title . ' - Center Portal' : 'Authorized Center Portal - Brainware';
$active_menu = isset($active_menu) ? $active_menu : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  
  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Components & Center CSS (Separated with auto cache-busting) -->
  <link rel="stylesheet" href="<?= url('/assets/css/components.css') ?>?v=<?= @filemtime(__DIR__ . '/../../assets/css/components.css') ?: time() ?>">
  <link rel="stylesheet" href="<?= url('/center/assets/css/center.css') ?>?v=<?= @filemtime(__DIR__ . '/../assets/css/center.css') ?: time() ?>">
</head>
<body>
<div class="center-layout">
  <?php require_once __DIR__ . '/sidebar.php'; ?>
