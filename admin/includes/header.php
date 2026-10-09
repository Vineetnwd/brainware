<?php
ob_start(); // Prevent headers already sent errors on redirects
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';

// Enforce admin security guard
require_admin('/login.php');

$admin_user = current_user();
$page_title = isset($page_title) ? $page_title . ' - Admin Panel' : 'Admin Control Panel - Brainware';
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
  
  <!-- FontAwesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Components & Admin CSS (Separated with auto cache-busting) -->
  <link rel="stylesheet" href="<?= url('/assets/css/components.css') ?>?v=<?= @filemtime(__DIR__ . '/../../assets/css/components.css') ?: time() ?>">
  <link rel="stylesheet" href="<?= url('/admin/assets/css/admin.css') ?>?v=<?= @filemtime(__DIR__ . '/../assets/css/admin.css') ?: time() ?>">
</head>
<body>
<div class="admin-layout">
  <?php require_once __DIR__ . '/sidebar.php'; ?>
