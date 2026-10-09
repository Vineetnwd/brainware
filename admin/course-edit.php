<?php
$page_title = "Edit Course";
$active_menu = 'courses';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

$course_id = (int)($_GET['id'] ?? 0);
$error_msg = '';

if ($course_id <= 0) {
    header('Location: ' . url('/admin/courses.php'));
    exit;
}

$course = null;
if ($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM course_details WHERE id = ? LIMIT 1");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();
}

if (!$course) {
    set_flash('danger', 'Course record not found.');
    header('Location: ' . url('/admin/courses.php'));
    exit;
}

// Parse existing selected module IDs
$selected_module_ids = [];
if (!empty($course['course_syllabus'])) {
    $selected_module_ids = array_filter(array_map('intval', explode(',', $course['course_syllabus'])));
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $course_code = strtoupper(trim(clean_input($_POST['course_code'] ?? '')));
    $course_name = trim(clean_input($_POST['course_name'] ?? ''));
    $course_duration = trim(clean_input($_POST['course_duration'] ?? ''));
    $no_of_semester = max(1, (int)($_POST['no_of_semester'] ?? 1));
    
    // Process selected modules
    $modules_input = $_POST['modules'] ?? [];
    if (is_array($modules_input)) {
        $selected_module_ids = array_filter(array_map('intval', $modules_input));
        $course_syllabus = implode(',', $selected_module_ids);
    } else {
        $course_syllabus = clean_input($_POST['course_syllabus'] ?? '');
    }

    $status = clean_input($_POST['status'] ?? ($course['status'] ?? 'ACTIVE'));

    if (empty($course_code) || empty($course_name) || empty($course_duration)) {
        $error_msg = "Please fill in all mandatory fields (Course Code, Course Name, Course Duration).";
    } else {
        if ($pdo) {
            try {
                // Check if another course has the same code
                $chk = $pdo->prepare("SELECT COUNT(*) FROM course_details WHERE LOWER(TRIM(course_code)) = LOWER(TRIM(?)) AND id != ? AND status != 'removed'");
                $chk->execute([$course_code, $course_id]);
                if ($chk->fetchColumn() > 0) {
                    $error_msg = "Another course with Code '$course_code' already exists.";
                } else {
                    $upd = $pdo->prepare("
                        UPDATE course_details SET
                        course_code = ?, course_name = ?, course_duration = ?,
                        no_of_semester = ?, course_syllabus = ?, status = ?,
                        updated_at = NOW(), updated_by = ?
                        WHERE id = ?
                    ");
                    $upd->execute([$course_code, $course_name, $course_duration, $no_of_semester, $course_syllabus, $status, $admin_user['id'] ?? 1, $course_id]);
                    set_flash('success', "Course '$course_name' ($course_code) updated successfully.");
                    header('Location: ' . url('/admin/courses.php'));
                    exit;
                }
            } catch (PDOException $e) {
                $error_msg = "Failed to update course: " . $e->getMessage();
            }
        }
    }
}

// Fetch all active modules from module table
$all_modules = [];
if ($pdo) {
    try {
        $m_stmt = $pdo->query("
            SELECT id, module_name, module_semester 
            FROM module 
            WHERE status NOT IN ('removed', 'EMPTY') 
            ORDER BY module_name ASC, module_semester ASC
        ");
        $all_modules = $m_stmt->fetchAll();
    } catch (PDOException $e) {}
}
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-pen-to-square" style="color:var(--primary-blue);"></i> Edit Course</h1>
    <p class="admin-page-subtitle">Update course credentials, semester structure, and assign syllabus modules</p>
  </div>
  <div>
    <a href="<?= url('/admin/courses.php') ?>" class="btn btn-sm btn-outline-primary">
      <i class="fa-solid fa-arrow-left"></i> Back to Courses
    </a>
  </div>
</div>

<?php if (!empty($error_msg)): ?>
  <div class="alert alert-danger" style="margin-bottom:1.5rem; max-width:1080px;">
    <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error_msg) ?>
  </div>
<?php endif; ?>

<!-- Premium Responsive Form Card -->
<div class="student-form-card" style="max-width:1080px;">
  <div class="student-form-accent-blue"></div>
  
  <div class="student-form-body" style="padding:2rem;">
    <form action="<?= url('/admin/course-edit.php?id=' . $course['id']) ?>" method="POST" id="courseEditForm">
      
      <div class="form-grid-2col">
        
        <!-- ================= LEFT COLUMN: Course Identity & Structure ================= -->
        <div class="form-col-box">
          <div class="form-col-title">
            <i class="fa-solid fa-graduation-cap"></i> Course Details
          </div>

          <!-- Enter Course Code -->
          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.4rem;">
              Enter Course Code <span class="req" style="color:#dc2626;">*</span>
            </label>
            <div style="position:relative;">
              <input type="text" name="course_code" id="course_code" class="form-control" 
                value="<?= htmlspecialchars($_POST['course_code'] ?? $course['course_code']) ?>" required 
                oninput="this.value = this.value.toUpperCase();" 
                placeholder="e.g. DCA, ADCA, PGDCA"
                style="font-weight:700; letter-spacing:0.5px; text-transform:uppercase; font-size:0.95rem;">
            </div>
            <small style="color:#64748b; font-size:0.78rem; margin-top:0.3rem; display:block;">
              Unique identification abbreviation for this academic course.
            </small>
          </div>

          <!-- Enter Course Name /Title -->
          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.4rem;">
              Enter Course Name /Title <span class="req" style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="course_name" id="course_name" class="form-control" 
              value="<?= htmlspecialchars($_POST['course_name'] ?? $course['course_name']) ?>" required 
              placeholder="e.g. DIPLOMA IN COMPUTER APPLICATION"
              style="font-weight:600; font-size:0.92rem;">
          </div>

          <!-- 2-col nested grid for Duration & Semester -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.2rem; margin-bottom:1.25rem;">
            <!-- Course Duration -->
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.4rem;">
                Course Duration <span class="req" style="color:#dc2626;">*</span>
              </label>
              <input type="text" name="course_duration" id="course_duration" class="form-control" 
                placeholder="e.g. 12 Months" 
                value="<?= htmlspecialchars($_POST['course_duration'] ?? $course['course_duration']) ?>" required
                style="font-weight:500;">
            </div>

            <!-- No Of Semester -->
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.4rem;">
                No Of Semester <span class="req" style="color:#dc2626;">*</span>
              </label>
              <input type="number" min="1" max="10" name="no_of_semester" id="no_of_semester" class="form-control" 
                value="<?= htmlspecialchars($_POST['no_of_semester'] ?? $course['no_of_semester']) ?>" required
                style="font-weight:600;">
            </div>
          </div>

          <!-- Course Status -->
          <div class="form-group" style="margin-bottom:1rem;">
            <label class="form-label" style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.4rem;">
              Course Status
            </label>
            <select name="status" class="form-select" style="font-weight:600;">
              <option value="ACTIVE" <?= (($course['status'] ?? 'ACTIVE') === 'ACTIVE') ? 'selected' : '' ?>>ACTIVE (Available for admissions & exams)</option>
              <option value="INACTIVE" <?= (($course['status'] ?? 'ACTIVE') === 'INACTIVE') ? 'selected' : '' ?>>INACTIVE (Hidden from enrollments)</option>
            </select>
          </div>

        </div>

        <!-- ================= RIGHT COLUMN: Select Module * ================= -->
        <div class="form-col-box">
          <div class="form-col-title" style="display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:0.5rem;">
              <i class="fa-solid fa-cubes"></i> Select Module <span class="req" style="color:#dc2626;">*</span>
            </div>
            <span id="selectedCountBadge" class="badge badge-primary" style="font-size:0.75rem; font-weight:700; padding:0.25rem 0.6rem; border-radius:12px;">
              <?= count($selected_module_ids) ?> Selected
            </span>
          </div>

          <div class="module-checklist-wrapper">
            <!-- Search and Bulk Action Toolbar -->
            <div class="module-checklist-toolbar">
              <div class="module-checklist-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="moduleSearchInput" placeholder="Quick search module..." oninput="filterModules()">
              </div>
              <div style="display:flex; gap:0.35rem;">
                <button type="button" class="btn btn-sm btn-outline-primary" style="padding:0.35rem 0.65rem; font-size:0.78rem;" onclick="selectAllModules(true)" title="Select all modules">
                  All
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" style="padding:0.35rem 0.65rem; font-size:0.78rem;" onclick="selectAllModules(false)" title="Clear selection">
                  None
                </button>
              </div>
            </div>

            <!-- Scrollable Module Checkboxes -->
            <div class="module-checklist-body" id="moduleListContainer" style="height:310px; max-height:310px;">
              <?php if (!empty($all_modules)): ?>
                <?php foreach ($all_modules as $mod): ?>
                  <?php 
                    $is_checked = in_array((int)$mod['id'], $selected_module_ids);
                  ?>
                  <label class="module-checklist-item <?= $is_checked ? 'is-selected' : '' ?>" data-name="<?= strtolower(htmlspecialchars($mod['module_name'])) ?> sem <?= $mod['module_semester'] ?>">
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                      <input type="checkbox" name="modules[]" value="<?= $mod['id'] ?>" <?= $is_checked ? 'checked' : '' ?> 
                        class="module-chk"
                        style="width:17px; height:17px; accent-color:#1e3a8a; cursor:pointer;"
                        onchange="updateModuleItemState(this)">
                      <span style="font-weight:600; font-size:0.88rem; color:#1e293b;">
                        <?= htmlspecialchars($mod['module_name']) ?>
                      </span>
                    </div>
                    <span class="badge badge-secondary" style="font-size:0.75rem; font-weight:600; padding:0.25rem 0.55rem; border-radius:4px; background:#e2e8f0; color:#334155;">
                      Semester <?= htmlspecialchars($mod['module_semester']) ?>
                    </span>
                  </label>
                <?php endforeach; ?>
              <?php else: ?>
                <div style="padding:2.5rem 1.5rem; text-align:center; color:#94a3b8; font-size:0.88rem;">
                  <i class="fa-solid fa-box-open" style="font-size:1.75rem; margin-bottom:0.5rem; display:block;"></i>
                  No syllabus modules available in system database.
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <small style="color:#64748b; font-size:0.78rem; margin-top:0.45rem; display:block;">
            <i class="fa-solid fa-circle-info" style="color:#3b82f6;"></i> Check all syllabus modules covered under this course.
          </small>
        </div>

      </div>

      <!-- Action Footer -->
      <div style="display:flex; justify-content:space-between; align-items:center; margin-top:2rem; padding-top:1.5rem; border-top:1px solid #e2e8f0;">
        <a href="<?= url('/admin/courses.php') ?>" class="btn btn-outline-secondary" style="padding:0.65rem 1.5rem; font-weight:600;">
          <i class="fa-solid fa-arrow-left"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary" style="padding:0.75rem 2.25rem; font-size:0.95rem; font-weight:700; border-radius:8px; display:inline-flex; align-items:center; gap:0.5rem; background:linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); border:none; box-shadow:0 4px 14px rgba(37,99,235,0.25);">
          <i class="fa-regular fa-square-check"></i> SAVE NOW
        </button>
      </div>

    </form>
  </div>
</div>

<script>
function updateModuleItemState(chk) {
  const item = chk.closest('.module-checklist-item');
  if (chk.checked) {
    item.classList.add('is-selected');
  } else {
    item.classList.remove('is-selected');
  }
  refreshSelectedCounter();
}

function refreshSelectedCounter() {
  const allChecked = document.querySelectorAll('.module-chk:checked').length;
  const badge = document.getElementById('selectedCountBadge');
  if (badge) {
    badge.textContent = allChecked + ' Selected';
  }
}

function filterModules() {
  const query = document.getElementById('moduleSearchInput').value.toLowerCase().trim();
  const items = document.querySelectorAll('.module-checklist-item');
  items.forEach(function(item) {
    const text = item.getAttribute('data-name') || '';
    if (!query || text.includes(query)) {
      item.style.display = 'flex';
    } else {
      item.style.display = 'none';
    }
  });
}

function selectAllModules(select) {
  const visibleCheckboxes = document.querySelectorAll('.module-checklist-item');
  visibleCheckboxes.forEach(function(item) {
    if (item.style.display !== 'none') {
      const chk = item.querySelector('.module-chk');
      if (chk) {
        chk.checked = select;
        if (select) {
          item.classList.add('is-selected');
        } else {
          item.classList.remove('is-selected');
        }
      }
    }
  });
  refreshSelectedCounter();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
