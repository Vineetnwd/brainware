<?php
$page_title = "Study Centers - Authorized Network";
$active_nav = 'centers';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$centers = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM center_details WHERE status = 'ACTIVE' ORDER BY id ASC");
        $centers = $stmt->fetchAll();
    } catch (PDOException $e) {
        $centers = [];
    }
}
?>

<div class="page-header">
  <div class="container">
    <ul class="breadcrumb">
      <li><a href="<?= url('/') ?>"><i class="fa-solid fa-house"></i> Home</a></li>
      <li><span>/</span></li>
      <li>Study Centers</li>
    </ul>
    <h1 class="page-title">Authorized Study Centers Network</h1>
    <p class="page-subtitle">Find verified Brainware Computer Academy branches and learning facilities</p>
  </div>
</div>

<div class="container" style="padding-bottom: 4rem;">
  
  <div class="center-grid">
    <?php if (!empty($centers)): ?>
      <?php foreach ($centers as $center): ?>
        <div class="center-card">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.75rem;">
            <span class="center-code-pill"><i class="fa-solid fa-hashtag"></i> <?= htmlspecialchars($center['center_code']) ?></span>
            <span class="badge badge-active"><i class="fa-solid fa-check"></i> Authorized</span>
          </div>

          <div style="display:flex; gap:1rem; align-items:center; margin-bottom:1rem;">
            <img src="<?= get_upload_image($center['center_photo'], 'center') ?>" 
                 alt="<?= htmlspecialchars($center['center_name']) ?>" 
                 style="width:58px; height:58px; object-fit:cover; border-radius:var(--radius-sm); border:1px solid var(--border-color); flex-shrink:0;" 
                 onerror="this.onerror=null; this.src='<?= url('/assets/images/logo.png') ?>';">
            <h3 class="center-name" style="margin:0;"><?= htmlspecialchars($center['center_name']) ?></h3>
          </div>
          
          <ul class="center-info-list">
            <li>
              <strong><i class="fa-solid fa-location-dot" style="color:var(--primary-red);"></i> Address:</strong>
              <span><?= htmlspecialchars($center['center_address']) ?></span>
            </li>
            <?php if (!empty($center['center_dos']) && $center['center_dos'] !== '0000-00-00'): ?>
            <li>
              <strong><i class="fa-solid fa-calendar-check" style="color:var(--text-muted);"></i> Estd. Date:</strong>
              <span><?= format_display_date($center['center_dos']) ?></span>
            </li>
            <?php endif; ?>
          </ul>

          <div style="margin-top:1.25rem; padding-top:0.85rem; border-top:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
            <a href="<?= url('/verification.php?center=' . urlencode($center['center_code'])) ?>" class="btn btn-sm btn-outline-primary">
              <i class="fa-solid fa-user-check"></i> Verify Students
            </a>
            <a href="<?= url('/contact.php?center=' . urlencode($center['center_name'])) ?>" class="btn btn-sm btn-danger">
              <i class="fa-solid fa-envelope"></i> Contact Center
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="alert alert-info" style="grid-column:1/-1;">No active study centers found.</div>
    <?php endif; ?>
  </div>

  <!-- Franchise Affiliation Box -->
  <div class="card" style="margin-top: 3rem; background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); border:1px solid var(--primary-blue-border);">
    <div class="card-body" style="padding:2rem; display:grid; grid-template-columns:1.5fr 1fr; gap:2rem; align-items:center;">
      <div>
        <span class="badge badge-danger" style="margin-bottom:0.5rem;"><i class="fa-solid fa-star"></i> Franchise Partnership</span>
        <h2 style="font-size:1.4rem; font-weight:800; color:var(--primary-blue-dark); margin-bottom:0.5rem;">
          Start an Authorized Brainware Computer Academy in Your City
        </h2>
        <p style="font-size:0.9rem; color:var(--text-body); line-height:1.6;">
          Join our growing national franchise network with complete operational training, exam portal access, verified certification software, and promotional support.
        </p>
      </div>
      <div style="text-align:right;">
        <a href="<?= url('/contact.php?enquiry_type=franchise') ?>" class="btn btn-primary btn-lg">
          <i class="fa-solid fa-handshake"></i> Apply for Affiliation &rarr;
        </a>
      </div>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
