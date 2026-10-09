<?php
/**
 * Certificate Details & Entry Page
 * Brainware Computer Academy Management System
 */

$page_title = "Certificate Entry & Details";
$active_menu = 'students';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

$student_id = (int)($_GET['student_id'] ?? $_GET['id'] ?? 0);
if ($student_id <= 0) {
    set_flash('danger', 'Invalid or missing student ID.');
    header('Location: ' . url('/admin/students.php'));
    exit;
}

$student = null;
$results = [];

if ($pdo) {
    try {
        // Fetch student with center & course details
        $stmt = $pdo->prepare("
            SELECT s.*, 
                   c.center_name, c.center_code, c.center_address, c.center_director,
                   co.course_name, co.course_code, co.course_duration, co.no_of_semester
            FROM student s
            LEFT JOIN center_details c ON s.center_id = c.id
            LEFT JOIN course_details co ON s.course_id = co.id
            WHERE s.id = ? 
            LIMIT 1
        ");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch();

        if ($student) {
            // Fetch recorded results for this student
            $r_stmt = $pdo->prepare("
                SELECT * FROM result 
                WHERE student_id = ? AND (status != 'removed' OR status IS NULL)
                ORDER BY id ASC
            ");
            $r_stmt->execute([$student_id]);
            $results = $r_stmt->fetchAll();
        }
    } catch (PDOException $e) {
        set_flash('danger', 'Database query error: ' . $e->getMessage());
    }
}

if (!$student) {
    set_flash('danger', 'Student record not found.');
    header('Location: ' . url('/admin/students.php'));
    exit;
}

// Calculate totals from results table
$tw = 0; // Total written
$tl = 0; // Total lab/practical
$ta = 0; // Total assignment
$tv = 0; // Total viva
$grand_total = 0;

foreach ($results as $res) {
    $w = (int)$res['written'];
    $l = (int)$res['practical'];
    $a = (int)$res['assignment'];
    $v = (int)$res['viva'];
    $row_total = $w + $l + $a + $v;

    $tw += $w;
    $tl += $l;
    $ta += $a;
    $tv += $v;
    $grand_total += $row_total;
}

// Calculate aggregate percentage & grade
$semester_count = max(1, (int)($student['no_of_semester'] ?: count($results) ?: 1));
$calculated_pct = !empty($results) ? round(($grand_total / ($semester_count * 400)) * 100, 2) : 0.00;
$calculated_grade = calculate_grade($calculated_pct);

$error_msg = '';

// Handle Certificate Details Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_certificate'])) {
    $certificate_no  = clean_input($_POST['certificate_no'] ?? '');
    $grade           = strtoupper(clean_input($_POST['grade'] ?? ''));
    $date_of_issue   = clean_input($_POST['date_of_issue'] ?? date('Y-m-d'));
    $finalpercentage = (float)($_POST['finalpercentage'] ?? 0);
    $comment         = clean_input($_POST['comment'] ?? 'Pass');

    if (empty($certificate_no)) {
        $error_msg = 'Certificate Number is required.';
    } else {
        try {
            // Check for duplicate certificate_no on other students
            $dup_stmt = $pdo->prepare("SELECT id, student_name, registration_no FROM student WHERE certificate_no = ? AND id != ? LIMIT 1");
            $dup_stmt->execute([$certificate_no, $student_id]);
            $dup = $dup_stmt->fetch();

            if ($dup) {
                $error_msg = 'Certificate No. "' . htmlspecialchars($certificate_no) . '" is already assigned to ' . htmlspecialchars($dup['student_name']) . ' (' . htmlspecialchars($dup['registration_no']) . ').';
            } else {
                $upd_stmt = $pdo->prepare("
                    UPDATE student SET 
                        certificate_no = ?,
                        grade = ?,
                        date_of_issue = ?,
                        finalpercentage = ?,
                        comment = ?,
                        updated_by = ?,
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $upd_stmt->execute([
                    $certificate_no,
                    $grade ?: $calculated_grade,
                    $date_of_issue ?: date('Y-m-d'),
                    $finalpercentage > 0 ? $finalpercentage : $calculated_pct,
                    $comment ?: 'Pass',
                    $admin_user['id'] ?? 1,
                    $student_id
                ]);

                set_flash('success', 'Certificate details saved successfully for ' . htmlspecialchars($student['student_name']) . '!');
                header('Location: ' . url('/admin/students.php'));
                exit;
            }
        } catch (PDOException $e) {
            $error_msg = 'Failed to update certificate: ' . $e->getMessage();
        }
    }
}
?>

<div class="breadcrumb-container" style="display:flex; align-items:center; gap:0.5rem; font-size:0.85rem; color:var(--text-muted); margin-bottom:1.5rem;">
  <a href="<?= url('/admin/dashboard.php') ?>" style="color:var(--text-muted); text-decoration:none;"><i class="fa-solid fa-house"></i> Home</a>
  <span>/</span>
  <a href="<?= url('/admin/students.php') ?>" style="color:var(--text-muted); text-decoration:none;">Students</a>
  <span>/</span>
  <span style="color:var(--primary-blue); font-weight:600;">Certificate Entry</span>
</div>

<?php if (!empty($error_msg)): ?>
  <div class="alert alert-danger" style="margin-bottom:1.5rem; border-radius:8px;">
    <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error_msg) ?>
  </div>
<?php endif; ?>

<div class="card" style="background:#ffffff; border-radius:12px; border:1px solid var(--border-color); box-shadow:0 4px 20px rgba(0,0,0,0.06); overflow:hidden; margin-bottom:2.5rem;">
  <!-- Card Header -->
  <div style="padding: 1.5rem 2rem; border-bottom: 1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; background:#ffffff;">
    <div>
      <h2 style="font-size:1.35rem; font-weight:700; color:#1e293b; margin:0;">
        Certificate Details Of <span style="color:#d97706;"><?= htmlspecialchars($student['student_name']) ?></span>
      </h2>
      <p style="font-size:0.85rem; color:var(--text-muted); margin:0.35rem 0 0;">
        Registration: <strong style="color:var(--primary-red);"><?= htmlspecialchars($student['registration_no']) ?></strong> &nbsp;|&nbsp; 
        Course: <strong><?= htmlspecialchars($student['course_code'] . ($student['course_name'] ? ' - ' . $student['course_name'] : '')) ?></strong> &nbsp;|&nbsp;
        Roll No: <strong><?= htmlspecialchars($student['student_roll'] ?: '—') ?></strong>
      </p>
    </div>
    <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
      <a href="<?= url('/admin/certificate-print.php?student_id=' . $student['id']) ?>" target="_blank" class="btn btn-sm btn-danger" style="box-shadow: 0 2px 6px rgba(220,38,38,0.25);">
        <i class="fa-solid fa-certificate"></i> Print Certificate
      </a>
      <a href="<?= url('/admin/marksheet-print.php?student_id=' . $student['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
        <i class="fa-solid fa-file-invoice"></i> Print Marksheet
      </a>
      <a href="<?= url('/admin/students.php') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back to Directory
      </a>
    </div>
  </div>

  <div style="padding: 2rem;">
    <!-- Semester Marks Summary Table -->
    <div class="table-responsive" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 2.25rem;">
      <table class="table" style="width:100%; margin:0; border-collapse: collapse; text-align:center;">
        <thead style="background:#f8fafc; border-bottom: 2px solid #e2e8f0;">
          <tr>
            <th style="padding:0.85rem 1rem; font-weight:700; color:#1e293b; text-align:left;">Exam</th>
            <th style="padding:0.85rem 1rem; font-weight:700; color:#1e293b;">Written</th>
            <th style="padding:0.85rem 1rem; font-weight:700; color:#1e293b;">Lab</th>
            <th style="padding:0.85rem 1rem; font-weight:700; color:#1e293b;">Assignment</th>
            <th style="padding:0.85rem 1rem; font-weight:700; color:#1e293b;">Viva</th>
            <th style="padding:0.85rem 1rem; font-weight:700; color:#1e293b;">Total</th>
            <th style="padding:0.85rem 1rem; font-weight:700; color:#1e293b;">Percentage (%)</th>
            <th style="padding:0.85rem 1rem; font-weight:700; color:#1e293b;">M.S. No.</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($results)): ?>
            <?php foreach ($results as $res): ?>
              <?php 
                $rw = (int)$res['written'];
                $rl = (int)$res['practical'];
                $ra = (int)$res['assignment'];
                $rv = (int)$res['viva'];
                $rt = $rw + $rl + $ra + $rv;
                $rp = round($rt / 4, 2);
              ?>
              <tr style="border-bottom:1px solid #f1f5f9; transition: background 0.15s ease;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                <td style="padding:0.75rem 1rem; text-align:left; font-weight:600; color:#334155; text-transform:capitalize;"><?= htmlspecialchars($res['exam_name']) ?></td>
                <td style="padding:0.75rem 1rem; color:#475569;"><?= $rw ?></td>
                <td style="padding:0.75rem 1rem; color:#475569;"><?= $rl ?></td>
                <td style="padding:0.75rem 1rem; color:#475569;"><?= $ra ?></td>
                <td style="padding:0.75rem 1rem; color:#475569;"><?= $rv ?></td>
                <td style="padding:0.75rem 1rem; font-weight:600; color:#1e293b;"><?= $rt ?></td>
                <td style="padding:0.75rem 1rem; color:#475569;"><?= $rp ?>%</td>
                <td style="padding:0.75rem 1rem; color:#475569;"><?= htmlspecialchars($res['ms_no'] ?: '—') ?></td>
              </tr>
            <?php endforeach; ?>
            <!-- Grand Total Row -->
            <tr style="background:#f1f5f9; font-weight:700; border-top: 2px solid #cbd5e1;">
              <td style="padding:0.85rem 1rem; text-align:left; color:#0f172a;">Total</td>
              <td style="padding:0.85rem 1rem; color:#0f172a;"><?= $tw ?></td>
              <td style="padding:0.85rem 1rem; color:#0f172a;"><?= $tl ?></td>
              <td style="padding:0.85rem 1rem; color:#0f172a;"><?= $ta ?></td>
              <td style="padding:0.85rem 1rem; color:#0f172a;"><?= $tv ?></td>
              <td style="padding:0.85rem 1rem; color:#0f172a;"><?= $grand_total ?></td>
              <td style="padding:0.85rem 1rem; color:#dc2626; font-size:1.05rem; font-weight:800;"><?= $calculated_pct ?>%</td>
              <td style="padding:0.85rem 1rem; color:#dc2626; font-size:1.05rem; font-weight:800;">Grade : <?= htmlspecialchars($calculated_grade) ?></td>
            </tr>
          <?php else: ?>
            <tr>
              <td colspan="8" style="padding:2rem; color:var(--text-muted); font-size:0.95rem;">
                <i class="fa-solid fa-circle-info" style="color:var(--primary-blue); margin-right:0.5rem;"></i>
                No exam marks found for this student. You can enter semester marks using the <strong>Marks</strong> button in the Student Directory.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Section Heading -->
    <div style="text-align:center; margin-bottom: 2rem;">
      <h3 style="font-size:1.15rem; font-weight:700; color:#d97706; margin:0; text-transform:uppercase; letter-spacing:0.5px;">
        Enter Certificate Details
      </h3>
      <div style="width:60px; height:3px; background:#f59e0b; margin:0.5rem auto 0; border-radius:2px;"></div>
    </div>

    <!-- 3-Column Certificate Entry Form -->
    <form method="POST" action="" id="certificateForm">
      <div class="form-grid-3col" style="margin-bottom:2.25rem;">
        
        <!-- Column 1: Readonly Student & Center info -->
        <div class="form-col-box">
          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:600; color:#475569; font-size:0.88rem; margin-bottom:0.4rem; display:block;">Student's Name</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($student['student_name']) ?>" disabled style="background:#f8fafc; color:#64748b; cursor:not-allowed;">
          </div>
          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:600; color:#475569; font-size:0.88rem; margin-bottom:0.4rem; display:block;">Registration No.</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($student['registration_no']) ?>" disabled style="background:#f8fafc; color:#64748b; cursor:not-allowed;">
          </div>
          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:600; color:#475569; font-size:0.88rem; margin-bottom:0.4rem; display:block;">Study Center</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($student['center_name'] ?: 'BRAINWARE COMPUTER ACADEMY') ?>" disabled style="background:#f8fafc; color:#64748b; cursor:not-allowed;">
          </div>
        </div>

        <!-- Column 2: Course & Certificate Details -->
        <div class="form-col-box">
          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:600; color:#475569; font-size:0.88rem; margin-bottom:0.4rem; display:block;">Course</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($student['course_code'] . ($student['course_name'] ? ' (' . $student['course_name'] . ')' : '')) ?>" disabled style="background:#f8fafc; color:#64748b; cursor:not-allowed;">
          </div>
          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; color:#1e3a8a; font-size:0.88rem; margin-bottom:0.4rem; display:block;">
              Enter Certificate No <span style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="certificate_no" id="certificate_no" class="form-control" value="<?= htmlspecialchars($student['certificate_no'] ?: '') ?>" required placeholder="e.g. 877" autofocus style="border:1.5px solid #3b82f6; font-weight:600;">
          </div>
          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; color:#1e3a8a; font-size:0.88rem; margin-bottom:0.4rem; display:block;">
              Enter Grade <span style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="grade" id="grade" class="form-control" value="<?= htmlspecialchars($student['grade'] ?: $calculated_grade) ?>" required placeholder="e.g. A, A+, B">
          </div>
        </div>

        <!-- Column 3: Issue Date, Percentage, Comment -->
        <div class="form-col-box">
          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; color:#1e3a8a; font-size:0.88rem; margin-bottom:0.4rem; display:block;">
              Date Of Issue <span style="color:#dc2626;">*</span>
            </label>
            <?php 
              $doi_val = (!empty($student['date_of_issue']) && $student['date_of_issue'] != '0000-00-00') ? $student['date_of_issue'] : date('Y-m-d');
            ?>
            <input type="date" name="date_of_issue" id="date_of_issue" class="form-control" value="<?= htmlspecialchars($doi_val) ?>" required>
          </div>
          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; color:#1e3a8a; font-size:0.88rem; margin-bottom:0.4rem; display:block;">
              Percentage (%) <span style="color:#dc2626;">*</span>
            </label>
            <?php 
              $pct_val = (float)$student['finalpercentage'] > 0 ? $student['finalpercentage'] : $calculated_pct;
            ?>
            <input type="number" step="0.01" min="0" max="100" name="finalpercentage" id="finalpercentage" class="form-control" value="<?= htmlspecialchars($pct_val) ?>" required>
          </div>
          <div class="form-group" style="margin-bottom:1.25rem;">
            <label class="form-label" style="font-weight:700; color:#1e3a8a; font-size:0.88rem; margin-bottom:0.4rem; display:block;">
              Comment (Pass/ Fail) <span style="color:#dc2626;">*</span>
            </label>
            <input type="text" name="comment" id="comment" class="form-control" value="<?= htmlspecialchars($student['comment'] ?: 'Pass') ?>" required placeholder="Pass">
          </div>
        </div>

      </div>

      <!-- Action Buttons -->
      <div style="display:flex; justify-content:center; align-items:center; gap:1.25rem; padding-top:1.5rem; border-top:1px solid #e2e8f0; flex-wrap:wrap;">
        <button type="submit" name="save_certificate" class="btn btn-primary" style="padding:0.75rem 2.75rem; font-size:1rem; font-weight:700; min-width:200px;">
          <i class="fa-solid fa-floppy-disk"></i> SAVE NOW
        </button>
        <a href="<?= url('/admin/certificate-print.php?student_id=' . $student['id']) ?>" target="_blank" class="btn btn-danger" style="padding:0.75rem 2.25rem; font-size:1rem; font-weight:700;">
          <i class="fa-solid fa-print"></i> PRINT CERTIFICATE
        </a>
      </div>
    </form>
  </div>
</div>

<script>
// Automatically update Grade suggestion when Percentage is modified
document.getElementById('finalpercentage').addEventListener('input', function() {
  const pct = parseFloat(this.value);
  if (!isNaN(pct)) {
    let grade = 'FAIL';
    if (pct >= 85) grade = 'A+';
    else if (pct >= 70) grade = 'A';
    else if (pct >= 60) grade = 'B';
    else if (pct >= 50) grade = 'C';
    else if (pct >= 40) grade = 'D';
    document.getElementById('grade').value = grade;
  }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
