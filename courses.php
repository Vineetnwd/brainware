<?php
$page_title = "Courses & Syllabus - Brainware Computer Academy";
$active_nav = 'courses';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Fetch all active courses
$courses = [];
$modules_map = [];

if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM course_details WHERE status = 'ACTIVE' ORDER BY id ASC");
        $courses = $stmt->fetchAll();

        // Fetch all modules to map syllabus details
        $m_stmt = $pdo->query("SELECT * FROM module WHERE status = 'ACTIVE'");
        $all_modules = $m_stmt->fetchAll();
        foreach ($all_modules as $mod) {
            $modules_map[$mod['id']] = $mod;
        }
    } catch (PDOException $e) {
        $courses = [];
    }
}
?>

<div class="page-header">
  <div class="container">
    <ul class="breadcrumb">
      <li><a href="<?= url('/') ?>"><i class="fa-solid fa-house"></i> Home</a></li>
      <li><span>/</span></li>
      <li>Courses & Syllabus</li>
    </ul>
    <h1 class="page-title">Career Courses & Curriculum</h1>
    <p class="page-subtitle">Industry-aligned diploma and certificate programs with detailed semester modules</p>
  </div>
</div>

<div class="container" style="padding-bottom: 4rem;">
  
  <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap:1.75rem;">
    <?php if (!empty($courses)): ?>
      <?php foreach ($courses as $c): ?>
        <?php 
          // Parse course syllabus module IDs if comma-separated
          $syllabus_ids = explode(',', $c['course_syllabus']);
        ?>
        <div class="card" id="course-<?= $c['id'] ?>" style="display:flex; flex-direction:column; height:100%; border-top:3px solid var(--primary-blue);">
          <div class="card-header" style="background:var(--primary-blue-soft); flex-direction:column; align-items:flex-start; gap:0.25rem;">
            <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
              <span class="badge badge-primary" style="font-size:0.75rem;"><i class="fa-solid fa-code"></i> <?= htmlspecialchars($c['course_code']) ?></span>
              <span class="badge badge-danger"><?= $c['no_of_semester'] ?> <?= ($c['no_of_semester'] > 1) ? 'Semesters' : 'Semester' ?></span>
            </div>
            <h3 style="font-size:1.15rem; font-weight:700; color:var(--primary-blue-dark); margin-top:0.35rem;">
              <?= htmlspecialchars($c['course_name']) ?>
            </h3>
          </div>
          
          <div class="card-body" style="flex:1; display:flex; flex-direction:column;">
            <div style="display:flex; align-items:center; gap:0.5rem; font-size:0.88rem; color:var(--text-muted); margin-bottom:1rem; padding-bottom:0.75rem; border-bottom:1px solid var(--border-color);">
              <i class="fa-regular fa-clock" style="color:var(--primary-red);"></i>
              <strong>Duration:</strong> <?= htmlspecialchars($c['course_duration']) ?>
            </div>

            <h4 style="font-size:0.85rem; font-weight:700; color:var(--text-dark); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.6rem;">
              <i class="fa-solid fa-list-check" style="color:var(--primary-blue);"></i> Course Syllabus & Modules:
            </h4>

            <div style="background:var(--bg-page); border:1px solid var(--border-color); border-radius:var(--radius-sm); padding:0.85rem; font-size:0.84rem; line-height:1.55; color:var(--text-body); flex:1;">
              <?php 
                $found_module = false;
                foreach ($syllabus_ids as $sid) {
                  $sid = trim($sid);
                  if (isset($modules_map[$sid])) {
                    $found_module = true;
                    $mod = $modules_map[$sid];
                    echo '<div style="margin-bottom:0.6rem;">';
                    echo '<strong style="color:var(--primary-blue); font-size:0.82rem;">Semester ' . $mod['module_semester'] . ' (' . htmlspecialchars($mod['module_name']) . '):</strong><br>';
                    echo '<span style="color:var(--text-body);">' . nl2br(htmlspecialchars($mod['module_syllabus'])) . '</span>';
                    echo '</div>';
                  }
                }
                if (!$found_module) {
                  echo '<span>Comprehensive practical training in ' . htmlspecialchars($c['course_name']) . ', lab assignments, and project work.</span>';
                }
              ?>
            </div>
          </div>

          <div class="card-footer" style="display:flex; justify-content:space-between; align-items:center;">
            <a href="<?= url('/verification.php') ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-shield-halved"></i> Verify Syllabus</a>
            <a href="<?= url('/contact.php?course=' . urlencode($c['course_name'])) ?>" class="btn btn-sm btn-danger"><i class="fa-solid fa-paper-plane"></i> Apply / Enquire</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="alert alert-info" style="grid-column:1/-1;">No course details found.</div>
    <?php endif; ?>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
