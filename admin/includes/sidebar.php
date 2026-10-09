<?php
// Admin Sidebar Navigation
$enquiry_count = 0;
if ($pdo) {
    try {
        $enquiry_count = $pdo->query("SELECT COUNT(*) FROM enquiry WHERE status = 'ACTIVE'")->fetchColumn() ?: 0;
    } catch (PDOException $e) {}
}
?>
<aside class="admin-sidebar">
  <a href="<?= url('/admin/') ?>" class="sidebar-brand">
    <div class="sidebar-logo-icon">B</div>
    <div class="sidebar-brand-text">BRAIN<span>WARE</span></div>
  </a>

  <ul class="sidebar-menu">
    <li class="sidebar-heading">Core Navigation</li>
    <li>
      <a href="<?= url('/admin/') ?>" class="sidebar-link <?= ($active_menu === 'dashboard') ? 'active' : '' ?>">
        <i class="fa-solid fa-gauge-high"></i> Dashboard
      </a>
    </li>

    <li class="sidebar-heading">Academy Management</li>
    <li>
      <a href="<?= url('/admin/centers.php') ?>" class="sidebar-link <?= ($active_menu === 'centers') ? 'active' : '' ?>">
        <i class="fa-solid fa-building-columns"></i> Study Centers
      </a>
    </li>
    <li>
      <a href="<?= url('/admin/courses.php') ?>" class="sidebar-link <?= ($active_menu === 'courses') ? 'active' : '' ?>">
        <i class="fa-solid fa-book-open"></i> Courses & Semesters
      </a>
    </li>
    <li>
      <a href="<?= url('/admin/modules.php') ?>" class="sidebar-link <?= ($active_menu === 'modules') ? 'active' : '' ?>">
        <i class="fa-solid fa-cubes"></i> Syllabus Modules
      </a>
    </li>

    <li class="sidebar-heading">Students & Exams</li>
    <li>
      <a href="<?= url('/admin/students.php') ?>" class="sidebar-link <?= ($active_menu === 'students') ? 'active' : '' ?>">
        <i class="fa-solid fa-user-graduate"></i> Student Database
      </a>
    </li>
    <li>
      <a href="<?= url('/admin/student-add.php') ?>" class="sidebar-link <?= ($active_menu === 'student-add') ? 'active' : '' ?>">
        <i class="fa-solid fa-user-plus"></i> New Registration
      </a>
    </li>
    <li>
      <a href="<?= url('/admin/results.php') ?>" class="sidebar-link <?= ($active_menu === 'results') ? 'active' : '' ?>">
        <i class="fa-solid fa-file-invoice-dollar"></i> Results & Marks
      </a>
    </li>

    <li class="sidebar-heading">Portal & Public Web</li>
    <li>
      <a href="<?= url('/admin/enquiries.php') ?>" class="sidebar-link <?= ($active_menu === 'enquiries') ? 'active' : '' ?>">
        <i class="fa-solid fa-envelope-open-text"></i> Web Enquiries
        <?php if ($enquiry_count > 0): ?>
          <span class="sidebar-badge"><?= $enquiry_count ?></span>
        <?php endif; ?>
      </a>
    </li>
    <li>
      <a href="<?= url('/admin/gallery.php') ?>" class="sidebar-link <?= ($active_menu === 'gallery') ? 'active' : '' ?>">
        <i class="fa-solid fa-images"></i> Media Gallery
      </a>
    </li>

    <li class="sidebar-heading">Administration</li>
    <li>
      <a href="<?= url('/admin/users.php') ?>" class="sidebar-link <?= ($active_menu === 'users') ? 'active' : '' ?>">
        <i class="fa-solid fa-users-gear"></i> User Logins
      </a>
    </li>
    <li>
      <a href="<?= url('/admin/settings.php') ?>" class="sidebar-link <?= ($active_menu === 'settings') ? 'active' : '' ?>">
        <i class="fa-solid fa-sliders"></i> Settings
      </a>
    </li>
    <li>
      <a href="<?= url('/') ?>" target="_blank" class="sidebar-link">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Visit Website
      </a>
    </li>
    <li>
      <a href="<?= url('/logout.php') ?>" class="sidebar-link" style="color:#f87171;">
        <i class="fa-solid fa-right-from-bracket"></i> Sign Out
      </a>
    </li>
  </ul>
</aside>
