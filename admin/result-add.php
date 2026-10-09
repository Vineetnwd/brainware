<?php
$page_title = "Enter Semester Marks";
$active_menu = 'results';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

$preset_student_id = (int)($_GET['student_id'] ?? 0);
$error_msg = '';

$students = [];
if ($pdo) {
    try {
        $students = $pdo->query("
            SELECT s.id, s.registration_no, s.student_name, co.course_code 
            FROM student s 
            LEFT JOIN course_details co ON s.course_id = co.id 
            WHERE s.status = 'ACTIVE' 
            ORDER BY s.id DESC
        ")->fetchAll();
    } catch (PDOException $e) {}
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $exam_name = clean_input($_POST['exam_name'] ?? 'semester 1');
    $written = (int)($_POST['written'] ?? 0);
    $practical = (int)($_POST['practical'] ?? 0);
    $assignment = (int)($_POST['assignment'] ?? 0);
    $viva = (int)($_POST['viva'] ?? 0);
    $ms_no = (int)($_POST['ms_no'] ?? rand(1000, 9999));
    $date_of_issue = clean_input($_POST['date_of_issue'] ?? date('Y-m-d'));
    $remarks = clean_input($_POST['remarks'] ?? 'Pass');
    $status = clean_input($_POST['status'] ?? 'ACTIVE');

    if ($student_id <= 0) {
        $error_msg = "Please select a valid student.";
    } else {
        if ($pdo) {
            try {
                // Check if ms_no already exists for another record
                $check_dup = $pdo->prepare("SELECT r.id, s.student_name, s.registration_no FROM result r JOIN student s ON r.student_id = s.id WHERE r.ms_no = ? AND (r.student_id != ? OR r.exam_name != ?) LIMIT 1");
                $check_dup->execute([$ms_no, $student_id, $exam_name]);
                $dup = $check_dup->fetch();
                
                if ($dup) {
                    $error_msg = 'MS No. "' . htmlspecialchars($ms_no) . '" is already assigned to ' . htmlspecialchars($dup['student_name']) . ' (' . htmlspecialchars($dup['registration_no']) . ').';
                } else {
                    // Upsert into result (student_id, exam_name is unique key in schema)
                    $stmt = $pdo->prepare("
                    INSERT INTO result 
                    (student_id, exam_name, written, practical, assignment, viva, ms_no, date_of_issue, remarks, status, created_at, created_by, updated_by, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE
                    written = VALUES(written), practical = VALUES(practical),
                    assignment = VALUES(assignment), viva = VALUES(viva),
                    ms_no = VALUES(ms_no), date_of_issue = VALUES(date_of_issue),
                    remarks = VALUES(remarks), status = VALUES(status), updated_at = NOW()
                ");
                $stmt->execute([
                    $student_id, $exam_name, $written, $practical, $assignment, $viva,
                    $ms_no, $date_of_issue, $remarks, $status, $admin_user['id'], $admin_user['id']
                ]);

                // Recalculate aggregate percentage for student
                $res_stmt = $pdo->prepare("SELECT written, practical, assignment, viva FROM result WHERE student_id = ? AND status = 'ACTIVE'");
                $res_stmt->execute([$student_id]);
                $all_results = $res_stmt->fetchAll();

                if (!empty($all_results)) {
                    $total_obtained = 0;
                    $total_max = count($all_results) * 400;
                    foreach ($all_results as $ar) {
                        $total_obtained += ($ar['written'] + $ar['practical'] + $ar['assignment'] + $ar['viva']);
                    }
                    $calc_percentage = round(($total_obtained / $total_max) * 100, 2);
                    $calc_grade = calculate_grade($calc_percentage);

                    $upd_student = $pdo->prepare("UPDATE student SET finalpercentage = ?, grade = ?, date_of_issue = ?, updated_at = NOW() WHERE id = ?");
                    $upd_student->execute([$calc_percentage, $calc_grade, $date_of_issue, $student_id]);
                }

                set_flash('success', "Marks for $exam_name saved successfully. Student percentage & grade updated.");
                header('Location: ' . url('/admin/results.php'));
                exit;
                } // End else
            } catch (PDOException $e) {
                $error_msg = "Failed to save marks: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-square-plus" style="color:var(--primary-red);"></i> Enter Examination Marks</h1>
    <p class="admin-page-subtitle">Submit semester marks (Theory, Practical, Assignment, Viva) and auto-compute grade</p>
  </div>
  <div>
    <a href="<?= url('/admin/results.php') ?>" class="btn btn-sm btn-outline-primary">
      <i class="fa-solid fa-arrow-left"></i> Back to Results
    </a>
  </div>
</div>

<?php if (!empty($error_msg)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
<?php endif; ?>

<div class="card" style="max-width:750px; border-top:3px solid var(--primary-red);">
  <div class="card-body" style="padding:1.5rem;">
    <form action="<?= url('/admin/result-add.php') ?>" method="POST">
      
      <div style="display:grid; grid-template-columns:2fr 1fr; gap:1rem;">
        <div class="form-group">
          <label class="form-label">Select Student <span class="req">*</span></label>
          <select name="student_id" id="resultStudentId" class="form-select" onchange="loadStudentCourseExams(this.value)" required autofocus>
            <option value="">-- Choose Enrolled Student --</option>
            <?php foreach ($students as $s): ?>
              <option value="<?= $s['id'] ?>" <?= ($preset_student_id == $s['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['registration_no']) ?> - <?= htmlspecialchars($s['student_name']) ?> (<?= htmlspecialchars($s['course_code']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Exam / Semester <span class="req">*</span></label>
          <select name="exam_name" id="resultExamSelect" class="form-select" onchange="onResultExamChange(this.value)" required>
            <option value="">Select Exam Name</option>
            <option value="semester 1">Semester 1</option>
            <option value="semester 2">Semester 2</option>
          </select>
        </div>
      </div>

      <!-- Marks Breakdown -->
      <div style="background:#f8fafc; border:1px solid var(--border-color); padding:1rem; border-radius:var(--radius-sm); margin-bottom:1.25rem;">
        <h4 style="font-size:0.85rem; font-weight:700; color:var(--primary-blue-dark); text-transform:uppercase; margin-bottom:0.75rem;">
          Marks Breakdown (Max 100 Marks Each)
        </h4>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:1rem;">
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Theory / Written</label>
            <input type="number" name="written" id="resWritten" min="0" max="100" value="80" class="form-control" required>
          </div>

          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Practical Lab</label>
            <input type="number" name="practical" id="resPractical" min="0" max="100" value="85" class="form-control" required>
          </div>

          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Assignment</label>
            <input type="number" name="assignment" id="resAssignment" min="0" max="100" value="85" class="form-control" required>
          </div>

          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Viva Voce</label>
            <input type="number" name="viva" id="resViva" min="0" max="100" value="80" class="form-control" required>
          </div>
        </div>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">
        <div class="form-group">
          <label class="form-label">Marksheet Serial No</label>
          <input type="text" name="ms_no" id="resMsNo" value="<?= rand(1650, 9999) ?>" class="form-control" required>
        </div>

        <div class="form-group">
          <label class="form-label">Date of Issue</label>
          <input type="date" name="date_of_issue" id="resDateOfIssue" value="<?= date('Y-m-d') ?>" class="form-control" required>
        </div>

        <div class="form-group">
          <label class="form-label">Remarks</label>
          <input type="text" name="remarks" id="resRemarks" value="Pass" class="form-control">
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:0.75rem;">
        <a href="<?= url('/admin/results.php') ?>" class="btn btn-outline-primary">Cancel</a>
        <button type="submit" class="btn btn-red"><i class="fa-solid fa-floppy-disk"></i> Save & Compute Grades</button>
      </div>

    </form>
  </div>
</div>

<script>
let studentResultCache = null;

function loadStudentCourseExams(studentId) {
  const examSelect = document.getElementById('resultExamSelect');
  if (!studentId) {
    examSelect.innerHTML = '<option value="">Select Exam Name</option>';
    return;
  }

  examSelect.innerHTML = '<option value="">Loading course exams...</option>';

  fetch('<?= url("/admin/ajax-marks.php") ?>?action=get_student_exams&student_id=' + studentId)
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        studentResultCache = data;
        let html = '<option value="">Select Exam Name</option>';
        data.semesters.forEach(s => {
          html += `<option value="${s.value}">${s.label}</option>`;
        });
        examSelect.innerHTML = html;

        // Auto-select first semester if available
        if (data.semesters.length > 0) {
          examSelect.value = data.semesters[0].value;
          onResultExamChange(data.semesters[0].value);
        }
      } else {
        examSelect.innerHTML = '<option value="">Error loading exams</option>';
      }
    })
    .catch(err => {
      console.error(err);
      examSelect.innerHTML = '<option value="">Network error</option>';
    });
}

function onResultExamChange(examVal) {
  const normKey = examVal ? examVal.toLowerCase().trim() : '';
  if (!normKey || !studentResultCache) return;

  const resultsMap = studentResultCache.results || {};
  if (resultsMap[normKey]) {
    const r = resultsMap[normKey];
    document.getElementById('resWritten').value = r.written;
    document.getElementById('resPractical').value = r.practical;
    document.getElementById('resAssignment').value = r.assignment;
    document.getElementById('resViva').value = r.viva;
    document.getElementById('resRemarks').value = r.remarks || 'Pass';
    document.getElementById('resMsNo').value = r.ms_no || '';
    document.getElementById('resDateOfIssue').value = r.date_of_issue || '<?= date("Y-m-d") ?>';
  } else {
    document.getElementById('resWritten').value = '80';
    document.getElementById('resPractical').value = '85';
    document.getElementById('resAssignment').value = '85';
    document.getElementById('resViva').value = '80';
    document.getElementById('resRemarks').value = 'Pass';
    document.getElementById('resMsNo').value = Math.floor(1000 + Math.random() * 9000);
    document.getElementById('resDateOfIssue').value = '<?= date("Y-m-d") ?>';
  }
}

// If student is preset on load, trigger fetch
document.addEventListener('DOMContentLoaded', function() {
  const curStudent = document.getElementById('resultStudentId').value;
  if (curStudent) {
    loadStudentCourseExams(curStudent);
  }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

