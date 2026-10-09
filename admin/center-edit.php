<?php
$page_title = "Edit Study Center";
$active_menu = 'centers';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

$center_id = (int)($_GET['id'] ?? 0);
$error_msg = '';

if ($center_id <= 0) {
    header('Location: ' . url('/admin/centers.php'));
    exit;
}

// Fetch center
$center = null;
if ($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM center_details WHERE id = ? LIMIT 1");
    $stmt->execute([$center_id]);
    $center = $stmt->fetch();
}

if (!$center) {
    set_flash('danger', 'Center not found.');
    header('Location: ' . url('/admin/centers.php'));
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $center_code = strtoupper(trim(clean_input($_POST['center_code'] ?? '')));
    $center_name = trim(clean_input($_POST['center_name'] ?? ''));
    $center_director = trim(clean_input($_POST['center_director'] ?? ''));
    $center_father = trim(clean_input($_POST['center_father'] ?? ''));
    $center_mobile = trim(clean_input($_POST['center_mobile'] ?? ''));
    $center_email = trim(clean_input($_POST['center_email'] ?? ''));
    $center_address = trim(clean_input($_POST['center_address'] ?? ''));
    $center_dos = clean_input($_POST['center_dos'] ?? date('Y-m-d'));
    $status = clean_input($_POST['status'] ?? 'ACTIVE');

    // Handle Director Photograph Upload
    $director_photo = $center['director_photo'] ?: 'no_dir.jpg';
    if (isset($_FILES['director_photo']) && $_FILES['director_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_res = handle_file_upload($_FILES['director_photo'], __DIR__ . '/../uploads/centers');
        if ($upload_res['success']) {
            $director_photo = $upload_res['filename'];
        }
    }

    if (empty($center_code) || empty($center_name) || empty($center_director) || empty($center_mobile) || empty($center_address)) {
        $error_msg = "Please fill in all required fields (Center Code, Name, Director, Mobile, and Address).";
    } else {
        if ($pdo) {
            try {
                // Check if another center already uses this center_code
                $chk = $pdo->prepare("SELECT COUNT(*) FROM center_details WHERE LOWER(TRIM(center_code)) = LOWER(TRIM(?)) AND id != ?");
                $chk->execute([$center_code, $center_id]);
                if ($chk->fetchColumn() > 0) {
                    $error_msg = "Center Code '$center_code' is already in use by another study center.";
                } else {
                    $upd = $pdo->prepare("
                        UPDATE center_details SET
                        center_code = ?, center_name = ?, center_director = ?, center_father = ?,
                        center_mobile = ?, center_email = ?, center_address = ?, center_dos = ?,
                        status = ?, director_photo = ?, updated_at = NOW(), updated_by = ?
                        WHERE id = ?
                    ");
                    $upd->execute([
                        $center_code, $center_name, $center_director, $center_father,
                        $center_mobile, $center_email, $center_address, $center_dos,
                        $status, $director_photo, $admin_user['id'] ?? 1, $center_id
                    ]);
                    
                    set_flash('success', "Study Center details updated successfully.");
                    header('Location: ' . url('/admin/centers.php'));
                    exit;
                }
            } catch (PDOException $e) {
                $error_msg = "Update failed: " . $e->getMessage();
            }
        }
    }
}

$has_director_photo = (!empty($center['director_photo']) && !in_array($center['director_photo'], ['no_dir.jpg', 'no_photo.jpg']));
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-pen-to-square" style="color:var(--primary-blue);"></i> Edit Study Center</h1>
    <p class="admin-page-subtitle">Update center information for <strong><?= htmlspecialchars($center['center_name']) ?></strong> (<?= htmlspecialchars($center['center_code']) ?>)</p>
  </div>
  <div>
    <a href="<?= url('/admin/centers.php') ?>" class="btn btn-sm btn-outline-primary">
      <i class="fa-solid fa-arrow-left"></i> Back to Centers
    </a>
  </div>
</div>

<?php if (!empty($error_msg)): ?>
  <div class="alert alert-danger" style="margin-bottom:1.5rem;">
    <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error_msg) ?>
  </div>
<?php endif; ?>

<div class="student-form-card">
  <div class="student-form-accent"></div>
  <div class="student-form-body">
    <form action="<?= url('/admin/center-edit.php?id=' . $center['id']) ?>" method="POST" enctype="multipart/form-data" id="centerEditForm">
      
      <div class="form-grid-3col">
        
        <!-- ================= COLUMN 1: Center Identity ================= -->
        <div class="form-col-box">
          <div class="form-col-title">
            <i class="fa-solid fa-building-columns"></i> Center Identity
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Center Code <span class="req" style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="center_code" class="form-control" 
              value="<?= htmlspecialchars($center['center_code']) ?>" required 
              oninput="this.value = this.value.toUpperCase();" style="font-weight:600; text-transform:uppercase;">
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Center Name <span class="req" style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="center_name" class="form-control" 
              value="<?= htmlspecialchars($center['center_name']) ?>" required style="font-weight:600;">
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Establishment Date
            </label>
            <input type="date" name="center_dos" value="<?= htmlspecialchars($center['center_dos']) ?>" class="form-control">
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Center Status
            </label>
            <select name="status" class="form-select">
              <option value="ACTIVE" <?= ($center['status'] === 'ACTIVE') ? 'selected' : '' ?>>ACTIVE</option>
              <option value="INACTIVE" <?= ($center['status'] === 'INACTIVE') ? 'selected' : '' ?>>INACTIVE</option>
            </select>
          </div>
        </div>

        <!-- ================= COLUMN 2: Administration & Contact ================= -->
        <div class="form-col-box">
          <div class="form-col-title">
            <i class="fa-solid fa-user-tie"></i> Administration
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Director Name <span class="req" style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="center_director" class="form-control" 
              value="<?= htmlspecialchars($center['center_director']) ?>" required>
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Father's Name / Guardian
            </label>
            <input type="text" name="center_father" class="form-control" 
              value="<?= htmlspecialchars($center['center_father'] ?: '') ?>" placeholder="e.g. Ganga Prasad">
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Mobile Number <span class="req" style="color:#dc2626;">*</span>
            </label>
            <input type="tel" name="center_mobile" class="form-control" 
              value="<?= htmlspecialchars($center['center_mobile']) ?>" required maxlength="15">
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Email Address
            </label>
            <input type="email" name="center_email" class="form-control" 
              value="<?= htmlspecialchars($center['center_email'] ?: '') ?>" placeholder="center@gmail.com">
          </div>

          <!-- Upload Photograph (director_photo) -->
          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Upload Photograph
            </label>
            <div class="photo-upload-container">
              <div class="photo-preview-wrap">
                <img id="directorPhotoPreview" class="photo-preview-img" 
                  src="<?= $has_director_photo ? get_upload_image($center['director_photo'], 'center') : '' ?>" 
                  alt="Director Preview" style="<?= $has_director_photo ? 'display:block;' : 'display:none;' ?>">
                <i id="directorPhotoPlaceholder" class="fa-solid fa-user-tie photo-placeholder-icon" 
                  style="<?= $has_director_photo ? 'display:none;' : 'display:block;' ?>"></i>
              </div>
              <div class="photo-upload-btn-wrap">
                <label for="director_photo" class="custom-file-btn">
                  <i class="fa-solid fa-arrow-up-from-bracket"></i> <?= $has_director_photo ? 'Change Photo' : 'Choose Photo' ?>
                </label>
                <input type="file" name="director_photo" id="director_photo" accept="image/*" style="display:none;" onchange="previewDirectorPhoto(this)">
                <span id="directorFileNameLabel" class="file-name-label">
                  <?= $has_director_photo ? htmlspecialchars($center['director_photo']) : 'No photo chosen' ?>
                </span>
                <small class="form-hint" style="font-size:0.72rem; color:#94a3b8;">Max: 5MB • JPG, PNG, WEBP</small>
              </div>
            </div>
          </div>
        </div>

        <!-- ================= COLUMN 3: Location & Details ================= -->
        <div class="form-col-box">
          <div class="form-col-title">
            <i class="fa-solid fa-location-dot"></i> Center Location
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Center Full Address <span class="req" style="color:#dc2626;">*</span>
            </label>
            <textarea name="center_address" class="form-control" rows="5" required 
              placeholder="Full postal address with district and pin code"
              style="resize:vertical; min-height:140px;"><?= htmlspecialchars($center['center_address']) ?></textarea>
          </div>
        </div>

      </div>

      <!-- Action Buttons Bar -->
      <div style="display:flex; justify-content:center; align-items:center; gap:1.25rem; margin-top:2.25rem; padding-top:1.5rem; border-top:1px solid #e2e8f0; flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary" style="padding:0.75rem 2.75rem; font-size:1rem; font-weight:700; min-width:220px;">
          <i class="fa-solid fa-floppy-disk"></i> Update Changes
        </button>
        <a href="<?= url('/admin/centers.php') ?>" class="btn btn-outline-secondary" style="padding:0.75rem 2rem; font-size:1rem; font-weight:600;">
          <i class="fa-solid fa-xmark"></i> Cancel
        </a>
      </div>

    </form>
  </div>
</div>

<script>
function previewDirectorPhoto(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const previewImg = document.getElementById('directorPhotoPreview');
      const placeholder = document.getElementById('directorPhotoPlaceholder');
      const nameLabel = document.getElementById('directorFileNameLabel');
      if (previewImg) {
        previewImg.src = e.target.result;
        previewImg.style.display = 'block';
      }
      if (placeholder) {
        placeholder.style.display = 'none';
      }
      if (nameLabel) {
        nameLabel.textContent = input.files[0].name;
      }
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
