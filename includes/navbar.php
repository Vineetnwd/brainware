<?php
// Main Public Navbar
?>
<header class="site-header">
  <div class="container">
    <div class="site-nav">
      
      <!-- Brand Logo -->
      <a href="<?= url('/') ?>" class="brand-logo">
        <div class="brand-icon">
          <span>B</span>
        </div>
        <div class="brand-text">
          <span class="brand-title">BRAIN<span>WARE</span></span>
          <span class="brand-subtitle">COMPUTER ACADEMY</span>
        </div>
      </a>

      <!-- Desktop Navigation Menu & Mobile Drawer -->
      <ul class="nav-menu" id="mainNavMenu">
        <li><a href="<?= url('/') ?>" class="nav-link <?= ($active_nav === 'home') ? 'active' : '' ?>"><i class="fa-solid fa-house"></i> Home</a></li>
        <li><a href="<?= url('/about.php') ?>" class="nav-link <?= ($active_nav === 'about') ? 'active' : '' ?>"><i class="fa-solid fa-circle-info"></i> About</a></li>
        
        <!-- Student Zone Multi-level Dropdown -->
        <li class="nav-item-dropdown">
          <a href="javascript:void(0)" class="nav-link nav-dropdown-toggle <?= in_array($active_nav ?? '', ['student_zone', 'verification', 'marksheet', 'admission']) ? 'active' : '' ?>">
            <i class="fa-solid fa-user-graduate"></i> Student Zone <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
          </a>
          <ul class="dropdown-menu">
            <li class="dropdown-submenu-parent">
              <a href="<?= url('/registration_verification.php') ?>" class="dropdown-item">
                <span class="dropdown-item-text"><i class="fa-solid fa-angle-right item-arrow"></i> Admission</span>
                <i class="fa-solid fa-chevron-right submenu-arrow"></i>
              </a>
              <ul class="dropdown-submenu">
                <li><a href="<?= url('/registration_verification.php') ?>" class="dropdown-item"><span class="dropdown-item-text"><i class="fa-solid fa-angle-right item-arrow"></i> Registration Verification</span></a></li>
                <li><a href="<?= url('/advance_search.php') ?>" class="dropdown-item"><span class="dropdown-item-text"><i class="fa-solid fa-angle-right item-arrow"></i> Advance Search</span></a></li>
              </ul>
            </li>
            <li>
              <a href="<?= url('/result.php') ?>" class="dropdown-item <?= (($active_nav ?? '') === 'marksheet') ? 'active' : '' ?>">
                <span class="dropdown-item-text"><i class="fa-solid fa-angle-right item-arrow"></i> Result</span>
              </a>
            </li>
            <li>
              <a href="<?= url('/certificate.php') ?>" class="dropdown-item <?= (($active_nav ?? '') === 'verification') ? 'active' : '' ?>">
                <span class="dropdown-item-text"><i class="fa-solid fa-angle-right item-arrow"></i> Certificate</span>
              </a>
            </li>
          </ul>
        </li>

        <li><a href="<?= url('/centers.php') ?>" class="nav-link <?= ($active_nav === 'centers') ? 'active' : '' ?>"><i class="fa-solid fa-network-wired"></i> Study Center</a></li>
        <li><a href="<?= url('/courses.php') ?>" class="nav-link <?= ($active_nav === 'courses') ? 'active' : '' ?>"><i class="fa-solid fa-book-open"></i> Courses</a></li>
        <li><a href="<?= url('/gallery.php') ?>" class="nav-link <?= ($active_nav === 'gallery') ? 'active' : '' ?>"><i class="fa-solid fa-images"></i> Gallery</a></li>
        <li><a href="<?= url('/contact.php') ?>" class="nav-link <?= ($active_nav === 'contact') ? 'active' : '' ?>"><i class="fa-solid fa-headset"></i> Contact</a></li>

        <!-- Quick actions inside mobile drawer menu -->
        <li class="nav-mobile-action-item">
          <a href="<?= url('/verification.php') ?>" class="nav-mobile-btn btn-danger">
            <i class="fa-solid fa-qrcode"></i> <span>Online Verification</span>
          </a>
        </li>
        <li class="nav-mobile-action-item">
          <a href="<?= url('/login.php') ?>" class="nav-mobile-btn btn-primary">
            <i class="fa-solid fa-right-to-bracket"></i> <span>Portal Login</span>
          </a>
        </li>
      </ul>

      <!-- Action Buttons & Mobile Hamburger -->
      <div class="nav-actions">
        <a href="<?= url('/verification.php') ?>" class="btn-verify-quick" title="Online Verification">
          <i class="fa-solid fa-qrcode"></i> <span class="nav-btn-text">Verify</span>
        </a>
        <a href="<?= url('/login.php') ?>" class="btn-portal-login" title="Portal Login">
          <i class="fa-solid fa-right-to-bracket"></i> <span class="nav-btn-text">Login</span>
        </a>
        <button type="button" class="mobile-toggle" aria-label="Toggle navigation" id="navMobileToggle">
          <i class="fa-solid fa-bars"></i>
        </button>
      </div>

    </div>
  </div>
</header>
