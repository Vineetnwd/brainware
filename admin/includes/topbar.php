<?php
// Admin Top Navigation Bar
?>
<div class="admin-main">
  <header class="admin-topbar">
    <div class="topbar-left">
      <button type="button" class="sidebar-toggle-btn" aria-label="Toggle Sidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
      <span style="font-weight:700; font-size:0.85rem; color:var(--primary-blue-dark);">
        <i class="fa-solid fa-shield-halved" style="color:var(--primary-red);"></i> Central Administration
      </span>
    </div>

    <div class="topbar-right">
      <a href="<?= url('/') ?>" target="_blank" class="btn btn-sm btn-outline-primary" style="font-size:0.75rem; padding:0.25rem 0.55rem;">
        <i class="fa-solid fa-globe"></i> View Website
      </a>
      
      <div class="user-profile-badge">
        <div class="user-avatar">
          <?= strtoupper(substr($admin_user['username'] ?? 'A', 0, 1)) ?>
        </div>
        <div class="user-info">
          <span class="user-name"><?= htmlspecialchars($admin_user['username'] ?? 'Administrator') ?></span>
          <span class="user-role">SUPER ADMIN</span>
        </div>
      </div>

      <a href="<?= url('/logout.php') ?>" class="btn btn-sm btn-outline-danger" title="Logout" style="padding:0.25rem 0.5rem;">
        <i class="fa-solid fa-power-off"></i>
      </a>
    </div>
  </header>

  <main class="admin-content-body">
    <?php render_flash(); ?>
