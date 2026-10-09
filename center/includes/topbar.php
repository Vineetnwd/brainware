<?php
// Center Topbar
?>
<div class="center-main">
  <header class="center-topbar">
    <div style="display:flex; align-items:center; gap:1rem;">
      <button type="button" class="center-sidebar-toggle" style="background:none; border:1px solid var(--border-color); padding:0.35rem 0.6rem; border-radius:4px; cursor:pointer;" aria-label="Toggle Sidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
      <div>
        <span class="badge badge-primary" style="font-size:0.75rem; padding:0.25rem 0.6rem;">
          <i class="fa-solid fa-hashtag"></i> <?= htmlspecialchars($active_center['center_code'] ?? $center_user['username']) ?>
        </span>
        <strong style="font-size:0.9rem; color:var(--primary-blue-dark); margin-left:0.4rem;">
          <?= htmlspecialchars($active_center['center_name'] ?? 'Authorized Study Center') ?>
        </strong>
      </div>
    </div>

    <div style="display:flex; align-items:center; gap:1.25rem;">
      <div style="display:flex; align-items:center; gap:0.5rem;">
        <div style="width:32px; height:32px; border-radius:50%; background:var(--primary-blue); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:0.8rem;">
          <i class="fa-solid fa-user-tie"></i>
        </div>
        <div style="display:flex; flex-direction:column;">
          <span style="font-size:0.8rem; font-weight:700; color:var(--text-dark);"><?= htmlspecialchars($active_center['center_director'] ?? $center_user['username']) ?></span>
          <span style="font-size:0.68rem; color:var(--primary-red); font-weight:700;">BRANCH DIRECTOR</span>
        </div>
      </div>

      <a href="<?= url('/logout.php') ?>" class="btn btn-sm btn-outline-danger" title="Logout" style="padding:0.25rem 0.55rem;">
        <i class="fa-solid fa-power-off"></i>
      </a>
    </div>
  </header>

  <main class="center-content-body">
    <?php render_flash(); ?>
