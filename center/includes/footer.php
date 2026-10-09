<?php
// Center Footer
?>
  </main>

  <footer class="center-footer">
    <div>
      &copy; <?= date('Y') ?> <strong><?= APP_FULL_NAME ?></strong>. Authorized Center Portal
    </div>
    <div>
      Branch: <strong><?= htmlspecialchars($active_center['center_code'] ?? 'Active') ?></strong> | Central System Synchronized
    </div>
  </footer>
</div> <!-- /center-main -->
</div> <!-- /center-layout -->

<!-- Center JS -->
<script src="<?= url('/center/assets/js/center.js') ?>"></script>
</body>
</html>
