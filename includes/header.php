<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

$page_title = isset($page_title) ? $page_title . ' - ' . APP_FULL_NAME : APP_FULL_NAME . ' | ' . APP_TAGLINE;
$page_desc = isset($page_desc) ? $page_desc : 'Brainware Computer Academy - Premier ISO certified computer institute offering job oriented IT, programming, accounting & diploma courses.';
$active_nav = isset($active_nav) ? $active_nav : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  
  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- FontAwesome for standard icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Public Stylesheets (Separated with auto cache-busting) -->
  <link rel="stylesheet" href="<?= url('/assets/css/components.css') ?>?v=<?= @filemtime(__DIR__ . '/../assets/css/components.css') ?: time() ?>">
  <link rel="stylesheet" href="<?= url('/assets/css/style.css') ?>?v=<?= @filemtime(__DIR__ . '/../assets/css/style.css') ?: time() ?>">
</head>
<body>

  <!-- Top Announcement / Contact Strip -->
  <div class="top-strip">
    <div class="container">
      <ul class="top-contact-list">
        <li><a href="tel:<?= preg_replace('/[^0-9+]/', '', config_val('sc_contact1')) ?>"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars(config_val('sc_contact1')) ?></a></li>
        <li><a href="mailto:<?= htmlspecialchars(config_val('sc_email')) ?>"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars(config_val('sc_email')) ?></a></li>
        <li><span class="top-badge"><i class="fa-solid fa-certificate"></i> ISO 9001:2015 Certified</span></li>
      </ul>
      <div class="top-links">
        <a href="<?= url('/verification.php') ?>" style="color:#fca5a5; font-weight:600; font-size:0.8rem; margin-right:1rem;">
          <i class="fa-solid fa-shield-halved"></i> Online Verification
        </a>
        <?php if (is_logged_in()): ?>
          <?php if (is_admin()): ?>
            <a href="<?= url('/admin/') ?>" class="btn-portal-login" style="padding:0.2rem 0.6rem; font-size:0.75rem;">
              <i class="fa-solid fa-gauge-high"></i> Admin Dashboard
            </a>
          <?php else: ?>
            <a href="<?= url('/center/') ?>" class="btn-portal-login" style="padding:0.2rem 0.6rem; font-size:0.75rem;">
              <i class="fa-solid fa-building-columns"></i> Center Portal
            </a>
          <?php endif; ?>
        <?php else: ?>
          <a href="<?= url('/login.php') ?>" style="color:#ffffff; font-weight:600; font-size:0.8rem;">
            <i class="fa-solid fa-lock"></i> Portal Login
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
