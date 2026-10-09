<?php
$page_title = "Add Study Center";
$active_menu = 'centers';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

$error_msg = '';

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
    
    $create_login = isset($_POST['create_login']);
    $login_password = clean_input($_POST['login_password'] ?? '');

    // Handle Director Photograph Upload
    $director_photo = 'no_dir.jpg';
    if (isset($_FILES['director_photo']) && $_FILES['director_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_res = handle_file_upload($_FILES['director_photo'], __DIR__ . '/../uploads/centers');
        if ($upload_res['success']) {
            $director_photo = $upload_res['filename'];
        }
    }

    if (empty($center_code) || empty($center_name) || empty($center_director) || empty($center_mobile) || empty($center_address)) {
        $error_msg = "Please fill in all mandatory fields (Center Code, Name, Director, Mobile, and Address).";
    } else {
        if ($pdo) {
            try {
                $chk = $pdo->prepare("SELECT COUNT(*) FROM center_details WHERE LOWER(TRIM(center_code)) = LOWER(TRIM(?))");
                $chk->execute([$center_code]);
                if ($chk->fetchColumn() > 0) {
                    $error_msg = "A study center with Code '$center_code' already exists.";
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO center_details 
                        (center_code, center_name, center_director, center_father, center_mobile, center_email, center_address, center_dos, status, center_photo, director_photo, created_at, created_by, updated_at, updated_by)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'no_center.jpg', ?, NOW(), '1', NOW(), 1)
                    ");
                    $stmt->execute([$center_code, $center_name, $center_director, $center_father, $center_mobile, $center_email, $center_address, $center_dos, $status, $director_photo]);
                    $new_center_id = $pdo->lastInsertId();

                    // If create login checked
                    if ($create_login && !empty($login_password)) {
                        $chk_u = $pdo->prepare("SELECT id FROM user WHERE LOWER(TRIM(user_name)) = LOWER(TRIM(?)) LIMIT 1");
                        $chk_u->execute([$center_code]);
                        $u_exist = $chk_u->fetch();

                        if ($u_exist) {
                            $u_stmt = $pdo->prepare("
                                UPDATE user 
                                SET user_mobile = ?, user_password = ?, user_email = ?, doc = ?, center_id = ?, status = 'ACTIVE', updated_at = NOW(), updated_by = '1'
                                WHERE id = ?
                            ");
                            $u_stmt->execute([$center_mobile, $login_password, $center_email, $center_dos, $new_center_id, $u_exist['id']]);
                        } else {
                            $u_stmt = $pdo->prepare("
                                INSERT INTO user (user_name, user_mobile, user_password, user_type, status, user_email, doc, center_id, my_api, created_by, created_at, updated_by, updated_at)
                                VALUES (?, ?, ?, 'CLIENT', 'ACTIVE', ?, ?, ?, '', '1', NOW(), '1', NOW())
                            ");
                            $u_stmt->execute([$center_code, $center_mobile, $login_password, $center_email, $center_dos, $new_center_id]);
                        }
                    }

                    set_flash('success', "Study Center '$center_name' ($center_code) created successfully.");
                    header('Location: ' . url('/admin/centers.php'));
                    exit;
                }
            } catch (PDOException $e) {
                $error_msg = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-building-circle-check" style="color:var(--primary-red);"></i> Add New Study Center</h1>
    <p class="admin-page-subtitle">Register a new authorized franchise branch and optionally configure portal login credentials</p>
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
    <form action="<?= url('/admin/center-add.php') ?>" method="POST" enctype="multipart/form-data" id="centerAddForm">
      
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
            <input type="text" name="center_code" id="centerCodeInput" class="form-control" 
              placeholder="e.g. BR/CPR/06" value="<?= htmlspecialchars($_POST['center_code'] ?? '') ?>" 
              required autofocus oninput="this.value = this.value.toUpperCase(); updateLoginUsername(this.value);" 
              style="font-weight:600; text-transform:uppercase;">
            <small class="form-hint" style="font-size:0.78rem; color:var(--text-muted); margin-top:0.25rem;">
              Official franchise center identification code.
            </small>
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Center Name <span class="req" style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="center_name" class="form-control" 
              placeholder="e.g. BRAINWARE COMPUTER ACADEMY" value="<?= htmlspecialchars($_POST['center_name'] ?? '') ?>" 
              required style="font-weight:600;">
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Establishment Date
            </label>
            <input type="date" name="center_dos" value="<?= htmlspecialchars($_POST['center_dos'] ?? date('Y-m-d')) ?>" class="form-control">
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Center Status
            </label>
            <select name="status" class="form-select">
              <option value="ACTIVE" <?= (($_POST['status'] ?? 'ACTIVE') === 'ACTIVE') ? 'selected' : '' ?>>ACTIVE</option>
              <option value="INACTIVE" <?= (($_POST['status'] ?? '') === 'INACTIVE') ? 'selected' : '' ?>>INACTIVE</option>
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
              placeholder="e.g. Er. Ajit Kumar" value="<?= htmlspecialchars($_POST['center_director'] ?? '') ?>" required>
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Father's Name / Guardian
            </label>
            <input type="text" name="center_father" class="form-control" 
              placeholder="e.g. Ganga Prasad" value="<?= htmlspecialchars($_POST['center_father'] ?? '') ?>">
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Mobile Number <span class="req" style="color:#dc2626;">*</span>
            </label>
            <input type="tel" name="center_mobile" class="form-control" 
              placeholder="10-digit mobile number" maxlength="15" value="<?= htmlspecialchars($_POST['center_mobile'] ?? '') ?>" required>
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Email Address
            </label>
            <input type="email" name="center_email" class="form-control" 
              placeholder="e.g. center@gmail.com" value="<?= htmlspecialchars($_POST['center_email'] ?? '') ?>">
          </div>

          <!-- Upload Photograph (director_photo) -->
          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Upload Photograph
            </label>
            <div class="photo-upload-container">
              <div class="photo-preview-wrap">
                <img id="directorPhotoPreview" class="photo-preview-img" src="" alt="Director Preview" style="display:none;">
                <i id="directorPhotoPlaceholder" class="fa-solid fa-user-tie photo-placeholder-icon" style="display:block;"></i>
              </div>
              <div class="photo-upload-btn-wrap">
                <label for="director_photo" class="custom-file-btn">
                  <i class="fa-solid fa-arrow-up-from-bracket"></i> Choose Photo
                </label>
                <input type="file" name="director_photo" id="director_photo" accept="image/*" style="display:none;" onchange="previewDirectorPhoto(this)">
                <span id="directorFileNameLabel" class="file-name-label">No photo chosen</span>
                <small class="form-hint" style="font-size:0.72rem; color:#94a3b8;">Max: 5MB • JPG, PNG, WEBP</small>
              </div>
            </div>
          </div>
        </div>

        <!-- ================= COLUMN 3: Location & Portal Access ================= -->
        <div class="form-col-box">
          <div class="form-col-title">
            <i class="fa-solid fa-location-dot"></i> Location & Access
          </div>

          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
              Center Full Address <span class="req" style="color:#dc2626;">*</span>
            </label>
            <textarea name="center_address" class="form-control" rows="4" 
              placeholder="Full postal address with district, state and pin code" required 
              style="resize:vertical; min-height:100px;"><?= htmlspecialchars($_POST['center_address'] ?? '') ?></textarea>
          </div>

          <!-- Portal User Login Box -->
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:1rem; margin-top:0.75rem;">
            <label style="display:flex; align-items:center; gap:0.5rem; font-weight:700; font-size:0.88rem; color:#1e3a8a; cursor:pointer; user-select:none; margin-bottom:0.75rem;">
              <input type="checkbox" name="create_login" value="1" checked id="createLoginCheckbox" onchange="toggleLoginFields(this.checked)" style="width:17px; height:17px; accent-color:#1e3a8a;">
              <span>Create Center Portal Login</span>
            </label>

            <div id="loginPassContainer">
              <div class="form-group" style="margin-bottom:0.5rem;">
                <label class="form-label" style="font-size:0.8rem; font-weight:700; color:#475569; margin-bottom:0.25rem;">
                  Portal Password
                </label>
                <div style="display:flex; gap:0.5rem;">
                  <input type="text" name="login_password" id="loginPasswordInput" value="center124" class="form-control" 
                    placeholder="Password" style="font-weight:600; font-family:'Courier New', monospace;">
                  <button type="button" class="btn btn-sm btn-outline-secondary" onclick="generateRandomPassword()" title="Generate random password" style="padding:0.25rem 0.6rem; font-size:0.8rem;">
                    <i class="fa-solid fa-arrows-rotate"></i>
                  </button>
                </div>
              </div>
              <small style="display:block; font-size:0.76rem; color:#64748b; line-height:1.4;">
                Username defaults to: <strong id="previewUsername" style="color:#1e3a8a;">[Center Code]</strong>
              </small>
            </div>
          </div>

        </div>

      </div>

      <!-- Action Buttons Bar -->
      <div style="display:flex; justify-content:center; align-items:center; gap:1.25rem; margin-top:2.25rem; padding-top:1.5rem; border-top:1px solid #e2e8f0; flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary" style="padding:0.75rem 2.75rem; font-size:1rem; font-weight:700; min-width:220px;">
          <i class="fa-solid fa-floppy-disk"></i> Save Study Center
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

function toggleLoginFields(isChecked) {
  const container = document.getElementById('loginPassContainer');
  if (container) {
    container.style.display = isChecked ? 'block' : 'none';
  }
}

function updateLoginUsername(val) {
  const preview = document.getElementById('previewUsername');
  if (preview) {
    preview.textContent = val ? val.trim() : '[Center Code]';
  }
}

function generateRandomPassword() {
  const chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789@#';
  let pwd = '';
  for (let i = 0; i < 8; i++) {
    pwd += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  document.getElementById('loginPasswordInput').value = pwd;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  const codeVal = document.getElementById('centerCodeInput').value;
  updateLoginUsername(codeVal);
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
