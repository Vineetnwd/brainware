<?php
$page_title = "Center Profile";
$active_menu = 'profile';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $director_name = clean_input($_POST['center_director'] ?? '');
    $mobile = clean_input($_POST['center_mobile'] ?? '');
    $email = clean_input($_POST['center_email'] ?? '');
    $address = clean_input($_POST['center_address'] ?? '');
    $new_password = clean_input($_POST['new_password'] ?? '');

    if ($pdo && $center_id > 0) {
        try {
            // Update center_details
            $upd1 = $pdo->prepare("
                UPDATE center_details SET
                center_director = ?, center_mobile = ?, center_email = ?, center_address = ?, updated_at = NOW(), updated_by = ?
                WHERE id = ?
            ");
            $upd1->execute([$director_name, $mobile, $email, $address, $center_user['id'], $center_id]);

            // Update user password & mobile if provided
            if (!empty($new_password)) {
                $upd2 = $pdo->prepare("UPDATE user SET user_password = ?, user_mobile = ?, user_email = ?, updated_at = NOW() WHERE id = ?");
                $upd2->execute([$new_password, $mobile, $email, $center_user['id']]);
            } else {
                $upd2 = $pdo->prepare("UPDATE user SET user_mobile = ?, user_email = ?, updated_at = NOW() WHERE id = ?");
                $upd2->execute([$mobile, $email, $center_user['id']]);
            }

            set_flash('success', 'Center profile and login credentials updated successfully.');
            header('Location: ' . url('/center/profile.php'));
            exit;
        } catch (PDOException $e) {
            set_flash('danger', 'Failed to update profile: ' . $e->getMessage());
        }
    }
}
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-building-circle-check" style="color:var(--primary-blue);"></i> Center Profile & Account</h1>
    <p class="admin-page-subtitle">Manage branch contact details, director information, and login password</p>
  </div>
</div>

<div style="display:grid; grid-template-columns: 1.4fr 1fr; gap:1.5rem; max-width:950px;">
  
  <!-- Profile Edit Form -->
  <div class="card" style="border-top:3px solid var(--primary-blue);">
    <div class="card-header">
      <h3 class="card-title"><i class="fa-solid fa-pen-to-square"></i> Update Branch Information</h3>
    </div>
    <div class="card-body" style="padding:1.5rem;">
      <form action="<?= url('/center/profile.php') ?>" method="POST">
        
        <div style="display:grid; grid-template-columns:1fr 2fr; gap:1rem;">
          <div class="form-group">
            <label class="form-label">Center Code</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($active_center['center_code'] ?? '') ?>" disabled>
          </div>

          <div class="form-group">
            <label class="form-label">Center Name</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($active_center['center_name'] ?? '') ?>" disabled>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
          <div class="form-group">
            <label class="form-label">Director Name <span class="req">*</span></label>
            <input type="text" name="center_director" value="<?= htmlspecialchars($active_center['center_director'] ?? '') ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label">Contact Mobile <span class="req">*</span></label>
            <input type="tel" name="center_mobile" value="<?= htmlspecialchars($active_center['center_mobile'] ?? '') ?>" class="form-control" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="center_email" value="<?= htmlspecialchars($active_center['center_email'] ?? '') ?>" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label">Branch Address <span class="req">*</span></label>
          <textarea name="center_address" class="form-control" rows="2" required><?= htmlspecialchars($active_center['center_address'] ?? '') ?></textarea>
        </div>

        <div class="form-group" style="background:#f8fafc; border:1px dashed var(--border-color); padding:0.85rem; border-radius:var(--radius-sm);">
          <label class="form-label"><i class="fa-solid fa-key" style="color:var(--primary-red);"></i> Change Login Password</label>
          <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
        </div>

        <div style="display:flex; justify-content:flex-end;">
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Profile & Password</button>
        </div>

      </form>
    </div>
  </div>

  <!-- Center Status Card -->
  <div>
    <div class="card" style="border-top:3px solid var(--primary-red); margin-bottom:1rem;">
      <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-award"></i> Affiliation Badge</h3>
      </div>
      <div class="card-body" style="padding:1.25rem; font-size:0.85rem; line-height:1.7;">
        <div><strong>Status:</strong> <span class="badge badge-active"><?= htmlspecialchars($active_center['status'] ?? 'ACTIVE') ?></span></div>
        <div><strong>Affiliation Code:</strong> <code><?= htmlspecialchars($active_center['center_code'] ?? 'N/A') ?></code></div>
        <div><strong>Estd. Date:</strong> <?= format_display_date($active_center['center_dos'] ?? '') ?></div>
        <div><strong>Certification Standard:</strong> ISO 9001:2015</div>
      </div>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
