<?php
// Admin Footer
?>
  </main>

  <footer class="admin-footer">
    <div>
      &copy; <?= date('Y') ?> <strong><?= APP_FULL_NAME ?></strong>. Admin Management System v2.0
    </div>
    <div>
      <span style="color:var(--text-muted);">Database: Active</span> | 
      <span style="color:var(--primary-red); font-weight:700;">ISO 9001:2015</span>
    </div>
  </footer>
</div> <!-- /admin-main -->
</div> <!-- /admin-layout -->

<!-- Admin JS -->
<script src="<?= url('/admin/assets/js/admin.js') ?>"></script>
</body>
</html>
