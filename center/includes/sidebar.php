<?php
// Center Sidebar Navigation
?>
<aside class="center-sidebar">
  <a href="<?= url('/center/') ?>" class="center-sidebar-brand">
    <div class="center-brand-badge">B</div>
    <div class="center-brand-text">BRAIN<span>WARE</span></div>
  </a>

  <ul class="center-sidebar-menu">
    <li class="center-sidebar-heading">Branch Operations</li>
    <li>
      <a href="<?= url('/center/') ?>" class="center-sidebar-link <?= ($active_menu === 'dashboard') ? 'active' : '' ?>">
        <i class="fa-solid fa-gauge-high"></i> Dashboard
      </a>
    </li>

    <li class="center-sidebar-heading">Student Admissions</li>
    <li>
      <a href="<?= url('/center/students.php') ?>" class="center-sidebar-link <?= ($active_menu === 'students') ? 'active' : '' ?>">
        <i class="fa-solid fa-user-graduate"></i> Enrolled Students
      </a>
    </li>
    <li>
      <a href="<?= url('/center/student-add.php') ?>" class="center-sidebar-link <?= ($active_menu === 'student-add') ? 'active' : '' ?>">
        <i class="fa-solid fa-user-plus"></i> New Admission
      </a>
    </li>

    <li class="center-sidebar-heading">Exams & Marksheets</li>
    <li>
      <a href="<?= url('/center/results.php') ?>" class="center-sidebar-link <?= ($active_menu === 'results') ? 'active' : '' ?>">
        <i class="fa-solid fa-file-invoice"></i> Student Marksheets
      </a>
    </li>

    <li class="center-sidebar-heading">Account & Branch</li>
    <li>
      <a href="<?= url('/center/profile.php') ?>" class="center-sidebar-link <?= ($active_menu === 'profile') ? 'active' : '' ?>">
        <i class="fa-solid fa-building-circle-check"></i> Center Profile & Password
      </a>
    </li>
    <li>
      <a href="<?= url('/') ?>" target="_blank" class="center-sidebar-link">
        <i class="fa-solid fa-globe"></i> Visit Website
      </a>
    </li>
    <li>
      <a href="<?= url('/logout.php') ?>" class="center-sidebar-link" style="color:#f87171;">
        <i class="fa-solid fa-right-from-bracket"></i> Sign Out
      </a>
    </li>
  </ul>
</aside>
