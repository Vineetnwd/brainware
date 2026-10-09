<?php
$page_title = "Edit Student";
$active_menu = 'students';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

$student_id = (int)($_GET['id'] ?? 0);
if ($student_id <= 0) {
    header('Location: ' . url('/center/students.php'));
    exit;
}

$student = null;
$courses = [];
$error_msg = '';

if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM student WHERE id = ? AND center_id = ? LIMIT 1");
        $stmt->execute([$student_id, $center_id]);
        $student = $stmt->fetch();

        $courses = $pdo->query("SELECT id, course_code, course_name FROM course_details WHERE status = 'ACTIVE' ORDER BY course_code ASC")->fetchAll();
    } catch (PDOException $e) {}
}

if (!$student) {
    set_flash('danger', 'Student not found in your center.');
    header('Location: ' . url('/center/students.php'));
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $course_id = (int)($_POST['course_id'] ?? $student['course_id']);
    $student_name = clean_input($_POST['student_name'] ?? $student['student_name']);
    $student_father = clean_input($_POST['student_father'] ?? '');
    $student_mother = clean_input($_POST['student_mother'] ?? '');
    $date_of_birth = clean_input($_POST['date_of_birth'] ?? $student['date_of_birth']);
    $student_sex = clean_input($_POST['student_sex'] ?? $student['student_sex']);
    $student_mobile = clean_input($_POST['student_mobile'] ?? '');
    $student_email = clean_input($_POST['student_email'] ?? '');
    $student_address = clean_input($_POST['student_address'] ?? '');
    $student_bloodgroup = clean_input($_POST['student_bloodgroup'] ?? 'NA');

    // Auto-derive roll from registration_no if not present
    $student_roll = $student['student_roll'];
    if (!empty($student['registration_no'])) {
        $parts = explode('/', $student['registration_no']);
        $last_part = trim(end($parts));
        if (!empty($last_part)) {
            $student_roll = $last_part;
        }
    }

    // Photo handling
    $student_photo = $student['student_photo'];
    if (isset($_FILES['student_photo']) && $_FILES['student_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_res = handle_file_upload($_FILES['student_photo'], __DIR__ . '/../uploads/students');
        if ($upload_res['success']) {
            $student_photo = $upload_res['filename'];
        }
    }

    if ($course_id <= 0 || empty($student_name)) {
        $error_msg = "Please fill in all mandatory fields (Course Name and Student Name).";
    } else {
        if ($pdo) {
            try {
                $upd = $pdo->prepare("
                    UPDATE student SET
                    course_id = ?, student_name = ?, student_mobile = ?,
                    student_roll = ?, student_mother = ?, student_father = ?,
                    student_address = ?, student_sex = ?, student_email = ?,
                    student_photo = ?, date_of_birth = ?, student_bloodgroup = ?,
                    updated_by = ?, updated_at = NOW()
                    WHERE id = ? AND center_id = ?
                ");
                $upd->execute([
                    $course_id, $student_name, $student_mobile,
                    $student_roll, $student_mother, $student_father,
                    $student_address, $student_sex, $student_email,
                    $student_photo, $date_of_birth, $student_bloodgroup,
                    $center_user['id'] ?? 0, $student_id, $center_id
                ]);

                set_flash('success', "Student profile for '$student_name' updated successfully.");
                header('Location: ' . url('/center/students.php'));
                exit;
            } catch (PDOException $e) {
                $error_msg = "Update failed: " . $e->getMessage();
            }
        }
    }
}

$cur_course_id = (int)($_POST['course_id'] ?? $student['course_id']);
$cur_student_name = $_POST['student_name'] ?? $student['student_name'];
$cur_student_father = $_POST['student_father'] ?? $student['student_father'];
$cur_student_mother = $_POST['student_mother'] ?? $student['student_mother'];
$cur_date_of_birth = $_POST['date_of_birth'] ?? $student['date_of_birth'];
$cur_student_sex = $_POST['student_sex'] ?? $student['student_sex'];
$cur_student_mobile = $_POST['student_mobile'] ?? $student['student_mobile'];
$cur_student_email = $_POST['student_email'] ?? $student['student_email'];
$cur_student_address = $_POST['student_address'] ?? $student['student_address'];
$cur_student_bloodgroup = $_POST['student_bloodgroup'] ?? $student['student_bloodgroup'];
$has_current_photo = !empty($student['student_photo']) && $student['student_photo'] !== 'no_photo.jpg';
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-pen-to-square" style="color:var(--primary-blue);"></i> Edit Student Details</h1>
    <p class="admin-page-subtitle">Update record for <strong><?= htmlspecialchars($student['student_name']) ?></strong> (<?= htmlspecialchars($student['registration_no']) ?>)</p>
  </div>
  <div style="display:flex; justify-content:flex-end; margin-left:auto;">
  </div>
</div>

<?php if (!empty($error_msg)): ?>
  <div class="alert alert-danger" style="margin-bottom:1.5rem; max-width:1180px;">
    <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error_msg) ?>
  </div>
<?php endif; ?>

<div class="student-form-card">
  <div class="student-form-accent"></div>
  <div class="student-form-body">
    <form action="<?= url('/center/student-edit.php?id=' . $student['id']) ?>" method="POST" enctype="multipart/form-data">
      
      <div class="form-grid-3col">
        
        <!-- ================= COLUMN 1 ================= -->
        <div class="form-col-box">
          <div class="form-col-title">
            <i class="fa-solid fa-graduation-cap"></i> Academic Details
          </div>

          <div class="form-group">
            <label class="form-label">Center Code</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($active_center['center_code'] ?? '') ?> - <?= htmlspecialchars($active_center['center_name'] ?? '') ?>" readonly style="background-color:#e2e8f0; cursor:not-allowed; font-weight:600;">
          </div>

          <div class="form-group">
            <label class="form-label" for="course_id">Select Course Name <span class="req">*</span></label>
            <select name="course_id" id="course_id" class="form-select" required>
              <option value="">---- Select One ----</option>
              <?php foreach ($courses as $co): ?>
                <option value="<?= $co['id'] ?>" <?= ($cur_course_id == $co['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($co['course_code']) ?> - <?= htmlspecialchars($co['course_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Reg. No.</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($student['registration_no']) ?>" readonly style="background-color:#e2e8f0; cursor:not-allowed; font-weight:600;">
            <small class="form-hint"><i class="fa-solid fa-lock"></i> Registration number is assigned upon admission</small>
          </div>

          <div class="form-group">
            <label class="form-label" for="student_name">Enter Student Name <span class="req">*</span></label>
            <input type="text" name="student_name" id="student_name" value="<?= htmlspecialchars($cur_student_name) ?>" class="form-control" placeholder="Student Name Here" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="student_father">Enter Father's Name</label>
            <input type="text" name="student_father" id="student_father" value="<?= htmlspecialchars($cur_student_father) ?>" class="form-control" placeholder="Father's Name">
          </div>
        </div>

        <!-- ================= COLUMN 2 ================= -->
        <div class="form-col-box">
          <div class="form-col-title">
            <i class="fa-solid fa-user"></i> Personal Details
          </div>

          <div class="form-group">
            <label class="form-label" for="student_mother">Enter Mother's Name</label>
            <input type="text" name="student_mother" id="student_mother" value="<?= htmlspecialchars($cur_student_mother) ?>" class="form-control" placeholder="Mother's Name">
          </div>

          <div class="form-group">
            <label class="form-label" for="date_of_birth">Date Of Birth</label>
            <input type="date" name="date_of_birth" id="date_of_birth" value="<?= htmlspecialchars($cur_date_of_birth) ?>" class="form-control" max="<?= date('Y-m-d') ?>">
          </div>

          <div class="form-group">
            <label class="form-label" for="student_sex">Select Sex</label>
            <select name="student_sex" id="student_sex" class="form-select">
              <option value="">Select A Value</option>
              <option value="MALE" <?= ($cur_student_sex === 'MALE') ? 'selected' : '' ?>>MALE</option>
              <option value="FEMALE" <?= ($cur_student_sex === 'FEMALE') ? 'selected' : '' ?>>FEMALE</option>
              <option value="OTHER" <?= ($cur_student_sex === 'OTHER') ? 'selected' : '' ?>>OTHER</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="student_mobile">Enter Mobile No.</label>
            <input type="tel" name="student_mobile" id="student_mobile" value="<?= htmlspecialchars($cur_student_mobile) ?>" class="form-control" placeholder="10-digit mobile number" maxlength="15">
          </div>

          <div class="form-group">
            <label class="form-label" for="student_email">Enter Email Id.</label>
            <input type="email" name="student_email" id="student_email" value="<?= htmlspecialchars($cur_student_email) ?>" class="form-control" placeholder="someone@email.com">
          </div>
        </div>

        <!-- ================= COLUMN 3 ================= -->
        <div class="form-col-box">
          <div class="form-col-title">
            <i class="fa-solid fa-address-card"></i> Contact & Photo
          </div>

          <div class="form-group">
            <label class="form-label" for="student_address">Address</label>
            <textarea name="student_address" id="student_address" class="form-control" rows="3" placeholder="Enter residential address"><?= htmlspecialchars($cur_student_address) ?></textarea>
          </div>

          <div class="form-group">
            <label class="form-label" for="student_bloodgroup">Select Blood Group</label>
            <select name="student_bloodgroup" id="student_bloodgroup" class="form-select">
              <option value="NA" <?= ($cur_student_bloodgroup === 'NA') ? 'selected' : '' ?>>Not Available</option>
              <option value="A+" <?= ($cur_student_bloodgroup === 'A+') ? 'selected' : '' ?>>A +</option>
              <option value="AB+" <?= ($cur_student_bloodgroup === 'AB+') ? 'selected' : '' ?>>AB +</option>
              <option value="B+" <?= ($cur_student_bloodgroup === 'B+') ? 'selected' : '' ?>>B +</option>
              <option value="O+" <?= ($cur_student_bloodgroup === 'O+') ? 'selected' : '' ?>>O +</option>
              <option value="A-" <?= ($cur_student_bloodgroup === 'A-') ? 'selected' : '' ?>>A -</option>
              <option value="AB-" <?= ($cur_student_bloodgroup === 'AB-') ? 'selected' : '' ?>>AB -</option>
              <option value="B-" <?= ($cur_student_bloodgroup === 'B-') ? 'selected' : '' ?>>B -</option>
              <option value="O-" <?= ($cur_student_bloodgroup === 'O-') ? 'selected' : '' ?>>O -</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Upload Photograph</label>
            <div class="photo-upload-container">
              <div class="photo-preview-wrap">
                <img id="photoPreview" class="photo-preview-img" src="<?= $has_current_photo ? get_upload_image($student['student_photo'], 'student') : '' ?>" alt="Preview" style="<?= $has_current_photo ? 'display:block;' : 'display:none;' ?>">
                <i id="photoPlaceholder" class="fa-solid fa-user-circle photo-placeholder-icon" style="<?= $has_current_photo ? 'display:none;' : 'display:block;' ?>"></i>
              </div>
              <div class="photo-upload-btn-wrap">
                <label for="student_photo" class="custom-file-btn">
                  <i class="fa-solid fa-arrow-up-from-bracket"></i> <?= $has_current_photo ? 'Change Photo' : 'Choose Photo' ?>
                </label>
                <input type="file" name="student_photo" id="student_photo" accept="image/*" style="display:none;" onchange="previewStudentPhoto(this)">
                <span id="fileNameLabel" class="file-name-label"><?= $has_current_photo ? htmlspecialchars($student['student_photo']) : 'No photo chosen' ?></span>
                <small class="form-hint" style="font-size:0.72rem; color:#94a3b8;">Max: 5MB • JPG, PNG, WEBP</small>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Footer Actions -->
      <div class="form-action-footer">
        <span style="font-size:0.83rem; color:var(--text-muted);">
          <i class="fa-solid fa-circle-info" style="color:var(--primary-blue);"></i> All fields marked with <span style="color:#dc2626; font-weight:bold;">*</span> are mandatory
        </span>
        <div style="display:flex; align-items:center; justify-content:flex-end; gap:0.75rem; margin-left:auto;">
          <a href="<?= url('/center/students.php') ?>" class="btn btn-outline-primary" style="padding:0.7rem 1.4rem;">
            Cancel
          </a>
          <button type="submit" class="btn-save-now">
            <i class="fa-solid fa-floppy-disk"></i> SAVE NOW
          </button>
        </div>
      </div>

    </form>
  </div>
</div>

<script>
function previewStudentPhoto(input) {
  const preview = document.getElementById('photoPreview');
  const placeholder = document.getElementById('photoPlaceholder');
  const label = document.getElementById('fileNameLabel');
  
  if (input.files && input.files[0]) {
    const file = input.files[0];
    label.textContent = file.name;
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
      placeholder.style.display = 'none';
    };
    reader.readAsDataURL(file);
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
