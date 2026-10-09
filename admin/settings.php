<?php
$page_title = "System Settings";
$active_menu = 'settings';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

$success = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $new_password = clean_input($_POST['new_password'] ?? '');
    $email = clean_input($_POST['user_email'] ?? '');
    $mobile = clean_input($_POST['user_mobile'] ?? '');

    if ($pdo) {
        try {
            if (!empty($new_password)) {
                $stmt = $pdo->prepare("UPDATE user SET user_password = ?, user_email = ?, user_mobile = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$new_password, $email, $mobile, $admin_user['id']]);
            } else {
                $stmt = $pdo->prepare("UPDATE user SET user_email = ?, user_mobile = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$email, $mobile, $admin_user['id']]);
            }
            set_flash('success', 'Admin account settings updated successfully.');
            header('Location: ' . url('/admin/settings.php'));
            exit;
        } catch (PDOException $e) {
            set_flash('danger', 'Failed to update settings: ' . $e->getMessage());
        }
    }
}
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-sliders" style="color:var(--primary-red);"></i> System & Admin Settings</h1>
    <p class="admin-page-subtitle">Configure system parameters, institute details, and update administrator credentials</p>
  </div>
</div>

<div style="display:grid; grid-template-columns:1.2fr 1fr; gap:1.5rem; max-width:900px;">
  
  <!-- Profile & Password -->
  <div class="card" style="border-top:3px solid var(--primary-red);">
    <div class="card-header">
      <h3 class="card-title"><i class="fa-solid fa-user-shield"></i> Admin Account Credentials</h3>
    </div>
    <div class="card-body" style="padding:1.5rem;">
      <form action="<?= url('/admin/settings.php') ?>" method="POST">
        <div class="form-group">
          <label class="form-label">Admin Username</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($admin_user['username']) ?>" disabled>
        </div>

        <div class="form-group">
          <label class="form-label">Admin Email</label>
          <input type="email" name="user_email" class="form-control" value="<?= htmlspecialchars($admin_user['user_email']) ?>">
        </div>

        <div class="form-group">
          <label class="form-label">Admin Mobile</label>
          <input type="tel" name="user_mobile" class="form-control" value="<?= htmlspecialchars($admin_user['user_mobile']) ?>">
        </div>

        <div class="form-group">
          <label class="form-label">New Password (Leave blank to keep unchanged)</label>
          <input type="password" name="new_password" class="form-control" placeholder="••••••••">
        </div>

        <button type="submit" class="btn btn-red"><i class="fa-solid fa-floppy-disk"></i> Save Profile Settings</button>
      </form>
    </div>
  </div>

  <!-- System Info -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><i class="fa-solid fa-server"></i> System Information</h3>
    </div>
    <div class="card-body" style="padding:1.25rem; font-size:0.85rem; line-height:1.7;">
      <div><strong>Application:</strong> <?= APP_FULL_NAME ?></div>
      <div><strong>Version:</strong> v2.0 Professional Enterprise Edition</div>
      <div><strong>Theme:</strong> High-Density Red & Blue UI</div>
      <div><strong>PHP Version:</strong> <?= phpversion() ?></div>
      <div><strong>Database:</strong> <?= DB_NAME ?> (MySQL/MariaDB)</div>
      <div><strong>Quality Standard:</strong> ISO 9001:2015</div>
      <div><strong>Timezone:</strong> <?= date_default_timezone_get() ?></div>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
