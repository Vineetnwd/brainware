<?php
$page_title = "Photo & Video Gallery";
$active_nav = 'gallery';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$gallery_items = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM gallery WHERE status = 'ACTIVE' ORDER BY id DESC");
        $gallery_items = $stmt->fetchAll();
    } catch (PDOException $e) {
        $gallery_items = [];
    }
}
?>

<div class="page-header">
  <div class="container">
    <ul class="breadcrumb">
      <li><a href="<?= url('/') ?>"><i class="fa-solid fa-house"></i> Home</a></li>
      <li><span>/</span></li>
      <li>Gallery</li>
    </ul>
    <h1 class="page-title">Life & Activities at Brainware</h1>
    <p class="page-subtitle">Snapshots of classroom sessions, computer laboratories, events, and award ceremonies</p>
  </div>
</div>

<div class="container" style="padding-bottom: 4rem;">
  
  <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:1.5rem;">
    <?php if (!empty($gallery_items)): ?>
      <?php foreach ($gallery_items as $item): ?>
        <div class="card" style="overflow:hidden;">
          <div style="height:210px; background:#e2e8f0; display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden;">
            <?php $img_src = get_upload_image($item['image_url'], 'gallery'); ?>
            <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($item['image_title'] ?: 'Brainware Event') ?>" style="width:100%; height:100%; object-fit:cover;" onerror="this.onerror=null; this.src='<?= url('/assets/images/logo.png') ?>';">
          </div>
          <div class="card-body" style="padding:1rem;">
            <h4 style="font-size:0.95rem; font-weight:700; color:var(--primary-blue-dark); margin-bottom:0.25rem;">
              <?= htmlspecialchars($item['image_title'] ?: 'Campus Activity') ?>
            </h4>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.5rem;">
              <i class="fa-regular fa-calendar"></i> <?= format_display_date($item['image_date']) ?>
            </div>
            <?php if (!empty($item['image_description'])): ?>
              <p style="font-size:0.82rem; color:var(--text-body); margin:0;">
                <?= htmlspecialchars($item['image_description']) ?>
              </p>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <!-- Sample gallery showcase when table has minimal data -->
      <div class="card" style="grid-column:1/-1; text-align:center; padding:3rem 1rem;">
        <i class="fa-solid fa-images" style="font-size:3rem; color:var(--primary-blue); margin-bottom:1rem;"></i>
        <h3 style="font-size:1.2rem; font-weight:700; color:var(--primary-blue-dark);">Explore Campus Memories</h3>
        <p style="color:var(--text-muted); font-size:0.9rem; max-width:500px; margin:0.5rem auto 1.5rem;">
          Our photo gallery is continuously updated with recent batch convocation pictures, student seminars, and practical workshop moments.
        </p>
        <a href="<?= url('/courses.php') ?>" class="btn btn-primary"><i class="fa-solid fa-graduation-cap"></i> Explore Programs</a>
      </div>
    <?php endif; ?>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
