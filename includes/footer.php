<?php
// Main Public Footer
?>
<footer class="site-footer">
  <div class="footer-main">
    <div class="container">
      <div class="footer-grid">
        
        <!-- About Column -->
        <div class="footer-col">
          <div class="brand-logo" style="margin-bottom: 1rem;">
            <div class="brand-icon" style="background:#dc2626;">
              <span>B</span>
            </div>
            <div class="brand-text">
              <span class="brand-title" style="color:#ffffff;">BRAIN<span style="color:#ef4444;">WARE</span></span>
              <span class="brand-subtitle" style="color:#94a3b8;">COMPUTER ACADEMY</span>
            </div>
          </div>
          <p class="footer-about-text">
            Brainware Computer Academy is an autonomous premier computer education institution dedicated to empowering youth with industry-standard IT training, diplomas, professional programming, and vocational skills.
          </p>
          <div style="display:flex; gap:0.6rem;">
            <a href="#" class="btn btn-sm btn-primary" style="padding:0.4rem 0.6rem; border-radius:4px;"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" class="btn btn-sm btn-primary" style="padding:0.4rem 0.6rem; border-radius:4px;"><i class="fa-brands fa-youtube"></i></a>
            <a href="#" class="btn btn-sm btn-primary" style="padding:0.4rem 0.6rem; border-radius:4px;"><i class="fa-brands fa-whatsapp"></i></a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-col">
          <h4>Quick Links</h4>
          <ul class="footer-links">
            <li><a href="<?= url('/') ?>"><i class="fa-solid fa-chevron-right" style="font-size:0.7rem; color:var(--primary-red);"></i> Home</a></li>
            <li><a href="<?= url('/about.php') ?>"><i class="fa-solid fa-chevron-right" style="font-size:0.7rem; color:var(--primary-red);"></i> About Academy</a></li>
            <li><a href="<?= url('/courses.php') ?>"><i class="fa-solid fa-chevron-right" style="font-size:0.7rem; color:var(--primary-red);"></i> Courses Offered</a></li>
            <li><a href="<?= url('/centers.php') ?>"><i class="fa-solid fa-chevron-right" style="font-size:0.7rem; color:var(--primary-red);"></i> Study Center Network</a></li>
            <li><a href="<?= url('/gallery.php') ?>"><i class="fa-solid fa-chevron-right" style="font-size:0.7rem; color:var(--primary-red);"></i> Photo Gallery</a></li>
            <li><a href="<?= url('/contact.php') ?>"><i class="fa-solid fa-chevron-right" style="font-size:0.7rem; color:var(--primary-red);"></i> Contact & Enquiries</a></li>
          </ul>
        </div>

        <!-- Student Zone -->
        <div class="footer-col">
          <h4>Student Zone</h4>
          <ul class="footer-links">
            <li><a href="<?= url('/verification.php') ?>"><i class="fa-solid fa-chevron-right" style="font-size:0.7rem; color:var(--primary-red);"></i> Student Verification</a></li>
            <li><a href="<?= url('/verification.php') ?>"><i class="fa-solid fa-chevron-right" style="font-size:0.7rem; color:var(--primary-red);"></i> Certificate Verification</a></li>
            <li><a href="<?= url('/marksheet.php') ?>"><i class="fa-solid fa-chevron-right" style="font-size:0.7rem; color:var(--primary-red);"></i> Check Semester Result</a></li>
            <li><a href="<?= url('/login.php') ?>"><i class="fa-solid fa-chevron-right" style="font-size:0.7rem; color:var(--primary-red);"></i> Authorized Center Login</a></li>
            <li><a href="<?= url('/login.php') ?>"><i class="fa-solid fa-chevron-right" style="font-size:0.7rem; color:var(--primary-red);"></i> Admin Portal Login</a></li>
          </ul>
        </div>

        <!-- Contact & Head Office -->
        <div class="footer-col">
          <h4>Head Office</h4>
          <ul class="footer-contact-list">
            <li>
              <i class="fa-solid fa-location-dot" style="color:var(--primary-red); margin-top:0.25rem;"></i>
              <span><?= htmlspecialchars(config_val('sc_address1')) ?>, <?= htmlspecialchars(config_val('sc_district')) ?>, <?= htmlspecialchars(config_val('sc_state')) ?> - <?= htmlspecialchars(config_val('sc_pin')) ?></span>
            </li>
            <li>
              <i class="fa-solid fa-phone" style="color:var(--primary-red);"></i>
              <span><?= htmlspecialchars(config_val('sc_contact1')) ?> / <?= htmlspecialchars(config_val('sc_contact2')) ?></span>
            </li>
            <li>
              <i class="fa-solid fa-envelope" style="color:var(--primary-red);"></i>
              <span><?= htmlspecialchars(config_val('sc_email')) ?></span>
            </li>
            <li>
              <i class="fa-solid fa-clock" style="color:var(--primary-red);"></i>
              <span>Mon - Sat: 08:00 AM - 06:00 PM</span>
            </li>
          </ul>
        </div>

      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="container">
      <div>
        &copy; <?= date('Y') ?> <strong><?= APP_FULL_NAME ?></strong>. All Rights Reserved.
      </div>
      <div>
        Designed for Excellence | ISO 9001:2015 Certified Institution
      </div>
    </div>
  </div>
</footer>

<!-- Public JS with auto cache-busting -->
<script src="<?= url('/assets/js/main.js') ?>?v=<?= @filemtime(__DIR__ . '/../assets/js/main.js') ?: time() ?>"></script>
</body>
</html>
