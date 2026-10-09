<?php
$page_title = "Add New Student";
$active_menu = 'student-add';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

$error_msg = '';
$centers = [];
$courses = [];

if ($pdo) {
    try {
        $centers = $pdo->query("SELECT id, center_code, center_name FROM center_details WHERE status = 'ACTIVE' ORDER BY center_code ASC")->fetchAll();
        $courses = $pdo->query("SELECT id, course_code, course_name FROM course_details WHERE status = 'ACTIVE' ORDER BY course_code ASC")->fetchAll();
    } catch (PDOException $e) {}
}

// Retain values on validation failure
$val_center_id = 0;
$val_course_id = 0;
$val_registration_no = '';
$val_student_name = '';
$val_student_father = '';
$val_student_mother = '';
$val_date_of_birth = '2002-01-01';
$val_student_sex = 'MALE';
$val_student_mobile = '';
$val_student_email = '';
$val_student_address = '';
$val_student_bloodgroup = 'NA';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $val_center_id = (int)($_POST['center_id'] ?? 0);
    $val_course_id = (int)($_POST['course_id'] ?? 0);
    $val_registration_no = clean_input($_POST['registration_no'] ?? '');
    $val_student_name = clean_input($_POST['student_name'] ?? '');
    $val_student_father = clean_input($_POST['student_father'] ?? '');
    $val_student_mother = clean_input($_POST['student_mother'] ?? '');
    $val_date_of_birth = clean_input($_POST['date_of_birth'] ?? '2002-01-01');
    $val_student_sex = clean_input($_POST['student_sex'] ?? 'MALE');
    $val_student_mobile = clean_input($_POST['student_mobile'] ?? '');
    $val_student_email = clean_input($_POST['student_email'] ?? '');
    $val_student_address = clean_input($_POST['student_address'] ?? '');
    $val_student_bloodgroup = clean_input($_POST['student_bloodgroup'] ?? 'NA');

    // Auto-derive roll number from registration_no (e.g. BR/CPR/01/650 -> 650)
    $student_roll = clean_input($_POST['student_roll'] ?? '');
    if (empty($student_roll) && !empty($val_registration_no)) {
        $parts = explode('/', $val_registration_no);
        $student_roll = trim(end($parts));
        if (empty($student_roll)) {
            $student_roll = $val_registration_no;
        }
    }

    // Default values for fields not on this simplified admission form
    $certificate_no = '';
    $grade = 'A';
    $date_of_issue = date('Y-m-d');
    $finalpercentage = 0.00;
    $comment = 'Enrolled';
    $status = 'ACTIVE';

    // Photo handling
    $student_photo = 'no_photo.jpg';
    if (isset($_FILES['student_photo']) && $_FILES['student_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_res = handle_file_upload($_FILES['student_photo'], __DIR__ . '/../uploads/students');
        if ($upload_res['success']) {
            $student_photo = $upload_res['filename'];
        }
    }

    if ($val_center_id <= 0 || $val_course_id <= 0 || empty($val_registration_no) || empty($val_student_name)) {
        $error_msg = "Please fill in all mandatory fields (Center Code, Course Name, Reg. No., and Student Name).";
    } else {
        if ($pdo) {
            try {
                // Check duplicate registration_no
                $chk = $pdo->prepare("SELECT COUNT(*) FROM student WHERE registration_no = ?");
                $chk->execute([$val_registration_no]);
                if ($chk->fetchColumn() > 0) {
                    $error_msg = "A student with Registration No '$val_registration_no' already exists.";
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO student 
                        (center_id, course_id, registration_no, student_name, student_mobile, certificate_no, grade, student_roll, student_mother, student_father, student_address, student_sex, student_email, student_photo, date_of_birth, student_bloodgroup, date_of_issue, finalpercentage, status, comment, created_by, created_at, updated_by, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())
                    ");
                    $stmt->execute([
                        $val_center_id, $val_course_id, $val_registration_no, $val_student_name, $val_student_mobile,
                        $certificate_no, $grade, $student_roll, $val_student_mother, $val_student_father,
                        $val_student_address, $val_student_sex, $val_student_email, $student_photo,
                        $val_date_of_birth, $val_student_bloodgroup, $date_of_issue, $finalpercentage,
                        $status, $comment, $admin_user['id'] ?? 0, $admin_user['id'] ?? 0
                    ]);

                    set_flash('success', "Student '$val_student_name' (Reg No: $val_registration_no) registered successfully.");
                    header('Location: ' . url('/admin/students.php'));
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
    <h1 class="admin-page-title"><i class="fa-solid fa-user-plus" style="color:var(--primary-red);"></i> Register New Student</h1>
    <p class="admin-page-subtitle">Enroll a new student under an authorized center and assign course details</p>
  </div>
  <div>
    <a href="<?= url('/admin/students.php') ?>" class="btn btn-sm btn-outline-primary">
      <i class="fa-solid fa-arrow-left"></i> Back to Students
    </a>
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
    <form action="<?= url('/admin/student-add.php') ?>" method="POST" enctype="multipart/form-data">
      
      <div class="form-grid-3col">
        
        <!-- ================= COLUMN 1 ================= -->
        <div class="form-col-box">
          <div class="form-col-title">
            <i class="fa-solid fa-graduation-cap"></i> Academic Details
          </div>

          <div class="form-group">
            <label class="form-label" for="center_id">Select Center Code <span class="req">*</span></label>
            <select name="center_id" id="center_id" class="form-select" required onchange="handleCenterChange()">
              <option value="">---- Select One ----</option>
              <?php foreach ($centers as $c): ?>
                <option value="<?= $c['id'] ?>" data-code="<?= htmlspecialchars($c['center_code']) ?>" <?= ($val_center_id == $c['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($c['center_code']) ?> - <?= htmlspecialchars($c['center_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="course_id">Select Course Name <span class="req">*</span></label>
            <select name="course_id" id="course_id" class="form-select" required>
              <option value="">---- Select One ----</option>
              <?php foreach ($courses as $co): ?>
                <option value="<?= $co['id'] ?>" <?= ($val_course_id == $co['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($co['course_code']) ?> - <?= htmlspecialchars($co['course_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="registration_no">Reg. No. <span class="req">*</span></label>
            <input type="text" name="registration_no" id="registration_no" value="<?= htmlspecialchars($val_registration_no) ?>" class="form-control" placeholder="Reg No Here" required>
            <small id="regHint" class="form-hint" style="display:none; color:var(--primary-blue); font-weight:600; margin-top:4px;">
              <i class="fa-solid fa-circle-info"></i> Center Prefix: <span id="regHintPrefix"></span>
            </small>
          </div>

          <div class="form-group">
            <label class="form-label" for="student_name">Enter Student Name <span class="req">*</span></label>
            <input type="text" name="student_name" id="student_name" value="<?= htmlspecialchars($val_student_name) ?>" class="form-control" placeholder="Student Name Here" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="student_father">Enter Father's Name</label>
            <input type="text" name="student_father" id="student_father" value="<?= htmlspecialchars($val_student_father) ?>" class="form-control" placeholder="Father's Name">
          </div>
        </div>

        <!-- ================= COLUMN 2 ================= -->
        <div class="form-col-box">
          <div class="form-col-title">
            <i class="fa-solid fa-user"></i> Personal Details
          </div>

          <div class="form-group">
            <label class="form-label" for="student_mother">Enter Mother's Name</label>
            <input type="text" name="student_mother" id="student_mother" value="<?= htmlspecialchars($val_student_mother) ?>" class="form-control" placeholder="Mother's Name">
          </div>

          <div class="form-group">
            <label class="form-label" for="date_of_birth">Date Of Birth</label>
            <input type="date" name="date_of_birth" id="date_of_birth" value="<?= htmlspecialchars($val_date_of_birth) ?>" class="form-control" max="<?= date('Y-m-d') ?>">
          </div>

          <div class="form-group">
            <label class="form-label" for="student_sex">Select Sex</label>
            <select name="student_sex" id="student_sex" class="form-select">
              <option value="">Select A Value</option>
              <option value="MALE" <?= ($val_student_sex === 'MALE') ? 'selected' : '' ?>>MALE</option>
              <option value="FEMALE" <?= ($val_student_sex === 'FEMALE') ? 'selected' : '' ?>>FEMALE</option>
              <option value="OTHER" <?= ($val_student_sex === 'OTHER') ? 'selected' : '' ?>>OTHER</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="student_mobile">Enter Mobile No.</label>
            <input type="tel" name="student_mobile" id="student_mobile" value="<?= htmlspecialchars($val_student_mobile) ?>" class="form-control" placeholder="10-digit mobile number" maxlength="15">
          </div>

          <div class="form-group">
            <label class="form-label" for="student_email">Enter Email Id.</label>
            <input type="email" name="student_email" id="student_email" value="<?= htmlspecialchars($val_student_email) ?>" class="form-control" placeholder="someone@email.com">
          </div>
        </div>

        <!-- ================= COLUMN 3 ================= -->
        <div class="form-col-box">
          <div class="form-col-title">
            <i class="fa-solid fa-address-card"></i> Contact & Photo
          </div>

          <div class="form-group">
            <label class="form-label" for="student_address">Address</label>
            <textarea name="student_address" id="student_address" class="form-control" rows="3" placeholder="Enter residential address"><?= htmlspecialchars($val_student_address) ?></textarea>
          </div>

          <div class="form-group">
            <label class="form-label" for="student_bloodgroup">Select Blood Group</label>
            <select name="student_bloodgroup" id="student_bloodgroup" class="form-select">
              <option value="NA" <?= ($val_student_bloodgroup === 'NA') ? 'selected' : '' ?>>Not Available</option>
              <option value="A+" <?= ($val_student_bloodgroup === 'A+') ? 'selected' : '' ?>>A +</option>
              <option value="AB+" <?= ($val_student_bloodgroup === 'AB+') ? 'selected' : '' ?>>AB +</option>
              <option value="B+" <?= ($val_student_bloodgroup === 'B+') ? 'selected' : '' ?>>B +</option>
              <option value="O+" <?= ($val_student_bloodgroup === 'O+') ? 'selected' : '' ?>>O +</option>
              <option value="A-" <?= ($val_student_bloodgroup === 'A-') ? 'selected' : '' ?>>A -</option>
              <option value="AB-" <?= ($val_student_bloodgroup === 'AB-') ? 'selected' : '' ?>>AB -</option>
              <option value="B-" <?= ($val_student_bloodgroup === 'B-') ? 'selected' : '' ?>>B -</option>
              <option value="O-" <?= ($val_student_bloodgroup === 'O-') ? 'selected' : '' ?>>O -</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Upload Photograph</label>
            <div class="photo-upload-container">
              <div class="photo-preview-wrap">
                <img id="photoPreview" class="photo-preview-img" alt="Preview">
                <i id="photoPlaceholder" class="fa-solid fa-user-circle photo-placeholder-icon"></i>
              </div>
              <div class="photo-upload-btn-wrap">
                <label for="student_photo" class="custom-file-btn">
                  <i class="fa-solid fa-arrow-up-from-bracket"></i> Choose Photo
                </label>
                <input type="file" name="student_photo" id="student_photo" accept="image/*" style="display:none;" onchange="previewStudentPhoto(this)">
                <span id="fileNameLabel" class="file-name-label">No photo chosen</span>
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
        <div style="display:flex; align-items:center; gap:0.75rem;">
          <a href="<?= url('/admin/students.php') ?>" class="btn btn-outline-primary" style="padding:0.7rem 1.4rem;">
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
// Photo Live Preview
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
  } else {
    preview.src = '';
    preview.style.display = 'none';
    placeholder.style.display = 'block';
    label.textContent = 'No photo chosen';
  }
}

// Interactive Center Code Helper
function handleCenterChange() {
  const centerSelect = document.getElementById('center_id');
  const regInput = document.getElementById('registration_no');
  const regHint = document.getElementById('regHint');
  const regHintPrefix = document.getElementById('regHintPrefix');
  
  const selectedOption = centerSelect.options[centerSelect.selectedIndex];
  const centerCode = selectedOption ? selectedOption.getAttribute('data-code') : '';
  
  if (centerCode) {
    regHintPrefix.textContent = centerCode + '/';
    regHint.style.display = 'block';
    
    // If registration input is empty or has previous prefix, offer helper
    if (!regInput.value.trim()) {
      regInput.value = centerCode + '/';
      regInput.focus();
    }
  } else {
    regHint.style.display = 'none';
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
