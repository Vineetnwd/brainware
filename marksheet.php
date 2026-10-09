<?php
$page_title = "Online Marksheet & Examination Result";
$active_nav = 'marksheet';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$query = isset($_GET['query']) ? clean_input($_GET['query']) : '';
$student_id = isset($_GET['student_id']) ? (int) $_GET['student_id'] : 0;
$student = null;
$results = [];
$error_msg = '';

if ($pdo && ($query !== '' || $student_id > 0)) {
  try {
    if ($student_id > 0) {
      $stmt = $pdo->prepare("
                SELECT s.*, c.center_name, c.center_code, c.center_address,
                       co.course_name, co.course_code, co.course_duration, co.no_of_semester
                FROM student s
                LEFT JOIN center_details c ON s.center_id = c.id
                LEFT JOIN course_details co ON s.course_id = co.id
                WHERE s.id = ? AND s.status = 'ACTIVE' LIMIT 1
            ");
      $stmt->execute([$student_id]);
    } else {
      $center_code = isset($_GET['center_code']) ? clean_input($_GET['center_code']) : '';
      if ($center_code !== '') {
        $full_reg_no = $center_code . '/' . ltrim($query, '/');
        $stmt = $pdo->prepare("
                  SELECT s.*, c.center_name, c.center_code, c.center_address,
                         co.course_name, co.course_code, co.course_duration, co.no_of_semester, co.course_syllabus
                  FROM student s
                  LEFT JOIN center_details c ON s.center_id = c.id
                  LEFT JOIN course_details co ON s.course_id = co.id
                  WHERE (s.registration_no = :q1 OR s.registration_no = :q2)
                    AND c.center_code = :center_code
                    AND s.status = 'ACTIVE' LIMIT 1
              ");
        $stmt->execute([':q1' => $full_reg_no, ':q2' => $query, ':center_code' => $center_code]);
      } else {
        $stmt = $pdo->prepare("
                  SELECT s.*, c.center_name, c.center_code, c.center_address,
                         co.course_name, co.course_code, co.course_duration, co.no_of_semester, co.course_syllabus
                  FROM student s
                  LEFT JOIN center_details c ON s.center_id = c.id
                  LEFT JOIN course_details co ON s.course_id = co.id
                  WHERE s.registration_no = :q1
                    AND s.status = 'ACTIVE' LIMIT 1
              ");
        $stmt->execute([':q1' => $query]);
      }
    }
    $student = $stmt->fetch();

    if ($student) {
      $selected_semester = isset($_GET['semester']) ? clean_input($_GET['semester']) : '';
      if (!empty($selected_semester)) {
          $r_stmt = $pdo->prepare("SELECT * FROM result WHERE student_id = ? AND exam_name = ? AND status = 'ACTIVE' ORDER BY exam_name ASC");
          $r_stmt->execute([$student['id'], $selected_semester]);
      } else {
          $r_stmt = $pdo->prepare("SELECT * FROM result WHERE student_id = ? AND status = 'ACTIVE' ORDER BY exam_name ASC");
          $r_stmt->execute([$student['id']]);
      }
      $all_results = $r_stmt->fetchAll();

      $sem_modules_map = [];
      try {
          $course_code = $student['course_code'];
          $mod_stmt = $pdo->prepare("SELECT module_semester, module_syllabus FROM module WHERE module_name = ?");
          $mod_stmt->execute([$course_code]);
          $modules = $mod_stmt->fetchAll();
          
          foreach ($modules as $mod) {
              $sem = $mod['module_semester'];
              $sem_modules_map[$sem] = $mod['module_syllabus'];
          }
      } catch (Exception $e) { }
    } else {
      $error_msg = "No student marksheet found for the provided search query.";
    }
  } catch (PDOException $e) {
    $error_msg = "Database lookup error. Please try again.";
  }
}
?>

<style>
  /* Print Styles for Marksheet */
  @media print {

    .top-strip,
    .site-header,
    .page-header,
    .site-footer,
    .no-print,
    .search-card-box {
      display: none !important;
    }

    body {
      background: #ffffff !important;
      font-size: 12pt;
    }

    .marksheet-print-wrapper {
      border: 3px double #1e3a8a !important;
      padding: 20px !important;
      margin: 0 !important;
      box-shadow: none !important;
    }
  }
</style>

<div class="page-header no-print">
  <div class="container">
    <ul class="breadcrumb">
      <li><a href="<?= url('/') ?>"><i class="fa-solid fa-house"></i> Home</a></li>
      <li><span>/</span></li>
      <li>Online Marksheet</li>
    </ul>
    <h1 class="page-title">Online Marksheet & Semester Result</h1>
    <p class="page-subtitle">Access complete semester examination marks, grades, and official scorecards</p>
  </div>
</div>

<div class="container" style="padding-bottom: 4rem; max-width: 960px;">

  <!-- Search Form -->
  <div class="card no-print search-card-box" style="border-top:4px solid var(--primary-blue);">
    <div class="card-header" style="background:#f8fafc; border-bottom:1px solid var(--border-color);">
      <h3 style="margin:0; font-size:1.25rem; color:var(--primary-blue); text-transform:uppercase; text-align:center;">
        Get Your Result Here</h3>
    </div>
    <div class="card-body" style="padding:1.5rem;">
      <form action="<?= url('/marksheet.php') ?>" method="GET">
        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:1.5rem;">

          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Enter You Reg. No.<span class="req">*</span></label>
            <input type="text" name="query" value="<?= htmlspecialchars($query ?? '') ?>" class="form-control"
              placeholder="Registration No." required>
          </div>

          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Center Code <span class="req">*</span></label>
            <select name="center_code" class="form-select" required>
              <option value="">---- Select One ----</option>
              <?php
              if ($pdo) {
                $centers = $pdo->query("SELECT center_code, center_name FROM center_details WHERE status = 'ACTIVE' ORDER BY center_code ASC")->fetchAll();
                foreach ($centers as $c) {
                  $selected = (isset($_GET['center_code']) && $_GET['center_code'] == $c['center_code']) ? 'selected' : '';
                  echo '<option value="' . htmlspecialchars($c['center_code']) . '" ' . $selected . '>' . htmlspecialchars($c['center_code']) . ' - ' . htmlspecialchars($c['center_name']) . '</option>';
                }
              }
              ?>
            </select>
          </div>
          
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Select Semester <span class="req">*</span></label>
            <select name="semester" class="form-select" required>
              <option value="">-----Select One-----</option>
              <?php
              $selected_sem = isset($_GET['semester']) ? clean_input($_GET['semester']) : '';
              for ($i = 1; $i <= 6; $i++) {
                  $sem_val = "semester $i";
                  $sel = (strtolower($selected_sem) == $sem_val) ? 'selected' : '';
                  echo "<option value=\"$sem_val\" $sel>Semester $i</option>";
              }
              ?>
            </select>
          </div>

        </div>

        <div style="text-align:center; margin-top:2rem;">
          <button type="submit" class="btn btn-red btn-lg" style="padding:0.6rem 2.5rem;">
            <i class="fa-solid fa-magnifying-glass"></i> Search Result
          </button>
        </div>
      </form>
    </div>
  </div>

  <?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger no-print">
      <i class="fa-solid fa-triangle-exclamation" style="margin-right:0.5rem;"></i>
      <?= htmlspecialchars($error_msg) ?>
    </div>
  <?php endif; ?>

  <?php if ($student): ?>

    <!-- Official Marksheet Layout -->
    <div class="card marksheet-print-wrapper"
      style="border:2px solid var(--primary-blue); border-radius:var(--radius-md); box-shadow:var(--shadow-md); background:#ffffff;">

      <!-- Marksheet Header -->
      <!-- Result Details Header -->
      <div style="background-color: var(--primary-blue); color: white; padding: 10px; text-align: center; font-weight: bold; font-size: 1.1rem; border-top-left-radius: 6px; border-top-right-radius: 6px;">
        Result Details
      </div>

      <!-- Student Details Table -->
      <div>
        <table style="width:100%; border-collapse:collapse; font-size:0.9rem; margin-bottom:0;">
          <tr>
            <td style="padding:10px; font-weight:600; color:#000; width:22%; border: 1px solid #dee2e6; background-color: #f8f9fa;">Registration No.</td>
            <td style="padding:10px; font-weight:700; color:#000; border: 1px solid #dee2e6;" colspan="3">
              <?= htmlspecialchars($student['registration_no']) ?>
            </td>
            <td rowspan="3" style="width:150px; border: 1px solid #dee2e6; text-align:center; vertical-align:middle; padding: 10px; background-color: #f8f9fa;">
              <img src="<?= get_upload_image($student['student_photo'], 'student') ?>" alt="Student Photo" style="width:115px; height:140px; object-fit:cover; border: 1px solid #ccc;">
            </td>
          </tr>
          <tr>
            <td style="padding:10px; font-weight:600; color:#000; border: 1px solid #dee2e6; background-color: #f8f9fa;">Name Of Center</td>
            <td style="padding:10px; font-weight:700; color:#000; border: 1px solid #dee2e6;" colspan="3">
              <?= htmlspecialchars($student['center_name']) ?>
            </td>
          </tr>
          <tr>
            <td style="padding:10px; font-weight:600; color:#000; border: 1px solid #dee2e6; background-color: #f8f9fa;">Name</td>
            <td style="padding:10px; font-weight:700; color:#000; border: 1px solid #dee2e6;" colspan="3">
              <?= htmlspecialchars($student['student_name']) ?>
            </td>
          </tr>
          <tr>
            <td style="padding:10px; font-weight:600; color:#000; border: 1px solid #dee2e6; background-color: #f8f9fa;">Father's Name</td>
            <td style="padding:10px; font-weight:600; color:#000; border: 1px solid #dee2e6;">
              <?= htmlspecialchars($student['student_father'] ?: 'N/A') ?>
            </td>
            <td style="padding:10px; font-weight:600; color:#000; border: 1px solid #dee2e6; background-color: #f8f9fa; width: 18%;">Mother's Name</td>
            <td style="padding:10px; font-weight:600; color:#000; border: 1px solid #dee2e6;" colspan="2">
              <?= htmlspecialchars($student['student_mother'] ?? 'N/A') ?>
            </td>
          </tr>
          <tr>
            <td style="padding:10px; font-weight:600; color:#000; border: 1px solid #dee2e6; background-color: #f8f9fa;">Date Of Birth</td>
            <td style="padding:10px; font-weight:600; color:#000; border: 1px solid #dee2e6;">
              <?= !empty($student['date_of_birth']) && $student['date_of_birth'] != '0000-00-00' ? date('d-M-Y', strtotime($student['date_of_birth'])) : 'N/A' ?>
            </td>
            <td style="padding:10px; font-weight:600; color:#000; border: 1px solid #dee2e6; background-color: #f8f9fa;">Gender</td>
            <td style="padding:10px; font-weight:600; color:#000; border: 1px solid #dee2e6; text-transform: uppercase;" colspan="2">
              <?= htmlspecialchars($student['student_sex'] ?? 'N/A') ?>
            </td>
          </tr>
          <tr>
            <td style="padding:10px; font-weight:600; color:#000; border: 1px solid #dee2e6; background-color: #f8f9fa;">Corse</td>
            <td style="padding:10px; font-weight:600; color:#000; border: 1px solid #dee2e6;">
              <?= htmlspecialchars($student['course_name']) ?> (<?= htmlspecialchars($student['course_code']) ?>)
            </td>
            <td style="padding:10px; font-weight:600; color:#000; border: 1px solid #dee2e6; background-color: #f8f9fa;">Course Code</td>
            <td style="padding:10px; font-weight:600; color:#000; border: 1px solid #dee2e6;" colspan="2">
              <?= htmlspecialchars($student['course_code']) ?>
            </td>
          </tr>
        </table>

        <?php if (!empty($all_results)): ?>
          <?php foreach ($all_results as $single_result): ?>
            <?php 
              $sem_num = 1;
              if (preg_match('/semester\s+(\d+)/i', $single_result['exam_name'], $matches)) {
                  $sem_num = $matches[1];
              }
              $modules_text = $sem_modules_map[$sem_num] ?? '';
            ?>
            <div style="margin-top: 1.5rem;">
              <!-- Module Covered Section -->
              <div style="padding: 8px; text-align: center; border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; background-color: #f8f9fa; font-weight: 600; color: #000;">Module Covered - <?= htmlspecialchars(ucwords($single_result['exam_name'])) ?></div>
              <div style="padding: 15px; font-size: 0.88rem; color: #000; border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; line-height: 1.5;">
                <i class="fa-solid fa-arrow-right"></i> <?= !empty($modules_text) ? $modules_text : 'N/A' ?>
              </div>

              <div style="background-color: var(--primary-blue); color: white; padding: 8px; text-align: center; font-weight: bold; font-size: 1.05rem;">
                Statement of Marks - <?= htmlspecialchars(ucwords($single_result['exam_name'])) ?>
              </div>
              
              <?php
              $written = (int) $single_result['written'];
              $practical = (int) $single_result['practical'];
              $assignment = (int) $single_result['assignment'];
              $viva = (int) $single_result['viva'];
              $total_obtained = $written + $practical + $assignment + $viva;
              $percentage = ($total_obtained / 400) * 100;
              $remarks = $single_result['remarks'] ?: ($total_obtained >= 160 ? 'Pass' : 'Fail');
              $issue_date = !empty($single_result['date_of_issue']) && $single_result['date_of_issue'] != '0000-00-00' ? date('d-M-Y', strtotime($single_result['date_of_issue'])) : date('d-M-Y');
              ?>
              <table style="width:100%; border-collapse:collapse; font-size:0.95rem; border:1px solid #dee2e6; border-top: none; text-align:center; margin-bottom:0;">
                <thead>
                  <tr style="background-color:var(--primary-red); color:white; font-weight:700;">
                    <th style="padding:10px; border:1px solid #dee2e6; text-align:left;">Subject</th>
                    <th style="padding:10px; border:1px solid #dee2e6;">Written</th>
                    <th style="padding:10px; border:1px solid #dee2e6;">Practical /Lab</th>
                    <th style="padding:10px; border:1px solid #dee2e6;">Assignment/Project</th>
                    <th style="padding:10px; border:1px solid #dee2e6;">Viva/Interview</th>
                    <th style="padding:10px; border:1px solid #dee2e6;">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td style="padding:10px; border:1px solid #dee2e6; text-align:left; font-weight:700;">Full Marks</td>
                    <td style="padding:10px; border:1px solid #dee2e6; color:#000;">100</td>
                    <td style="padding:10px; border:1px solid #dee2e6; color:#000;">100</td>
                    <td style="padding:10px; border:1px solid #dee2e6; color:#000;">100</td>
                    <td style="padding:10px; border:1px solid #dee2e6; color:#000;">100</td>
                    <td style="padding:10px; border:1px solid #dee2e6; font-weight:700; color:#000;">400</td>
                  </tr>
                  <tr>
                    <td style="padding:10px; border:1px solid #dee2e6; text-align:left; font-weight:700;">Pass Marks</td>
                    <td style="padding:10px; border:1px solid #dee2e6; color:#000;">40</td>
                    <td style="padding:10px; border:1px solid #dee2e6; color:#000;">40</td>
                    <td style="padding:10px; border:1px solid #dee2e6; color:#000;">40</td>
                    <td style="padding:10px; border:1px solid #dee2e6; color:#000;">40</td>
                    <td style="padding:10px; border:1px solid #dee2e6; font-weight:700; color:#000;">160</td>
                  </tr>
                  <tr>
                    <td style="padding:10px; border:1px solid #dee2e6; text-align:left; font-weight:700;">Marks Obtained</td>
                    <td style="padding:10px; border:1px solid #dee2e6; font-weight:600; color:#000;"><?= $written ?></td>
                    <td style="padding:10px; border:1px solid #dee2e6; font-weight:600; color:#000;"><?= $practical ?></td>
                    <td style="padding:10px; border:1px solid #dee2e6; font-weight:600; color:#000;"><?= $assignment ?></td>
                    <td style="padding:10px; border:1px solid #dee2e6; font-weight:600; color:#000;"><?= $viva ?></td>
                    <td style="padding:10px; border:1px solid #dee2e6; font-weight:700; color:#000;">
                      <?= $total_obtained ?>
                    </td>
                  </tr>
                </tbody>
              </table>

              <table style="width:100%; border-collapse:collapse; font-size:0.95rem; border:1px solid #dee2e6; border-top: none; text-align:center; margin-bottom:0;">
                <tr style="background-color: var(--primary-blue); color:white; font-weight:700;">
                  <td colspan="2" style="padding:10px; border:1px solid #dee2e6;">Issued On</td>
                  <td colspan="2" style="padding:10px; border:1px solid #dee2e6;">Semester Percentage (%)</td>
                  <td colspan="2" style="padding:10px; border:1px solid #dee2e6;">Semester Remarks</td>
                </tr>
                <tr style="font-weight:700; color:#000;">
                  <td colspan="2" style="padding:10px; border:1px solid #dee2e6;"><?= htmlspecialchars($issue_date) ?></td>
                  <td colspan="2" style="padding:10px; border:1px solid #dee2e6;"><?= number_format($percentage, 2) ?> %</td>
                  <td colspan="2" style="padding:10px; border:1px solid #dee2e6;"><?= htmlspecialchars($remarks) ?></td>
                </tr>
              </table>

              <?php if ($sem_num == $student['no_of_semester']): ?>
              <table style="width:100%; border-collapse:collapse; font-size:0.95rem; border:1px solid #dee2e6; border-top: none; text-align:center; margin-bottom:0;">
                <tr style="background-color: var(--primary-red); color:white; font-weight:700;">
                  <td colspan="2" style="padding:10px; border:1px solid #dee2e6;">Final Date Of Issue</td>
                  <td colspan="2" style="padding:10px; border:1px solid #dee2e6;">Overall Percentage (%)</td>
                  <td style="padding:10px; border:1px solid #dee2e6;">Final Grade</td>
                  <td style="padding:10px; border:1px solid #dee2e6;">Overall Remarks</td>
                </tr>
                <tr style="font-weight:700; color:#000;">
                  <td colspan="2" style="padding:10px; border:1px solid #dee2e6;">
                    <?= !empty($student['date_of_issue']) && $student['date_of_issue'] != '0000-00-00' ? date('d-M-Y', strtotime($student['date_of_issue'])) : 'N/A' ?>
                  </td>
                  <td colspan="2" style="padding:10px; border:1px solid #dee2e6;"><?= htmlspecialchars($student['finalpercentage']) ?> %</td>
                  <td style="padding:10px; border:1px solid #dee2e6;"><span class="badge" style="background-color: var(--primary-red); color: white; padding: 4px 8px; border-radius: 4px;"><?= htmlspecialchars($student['grade']) ?></span></td>
                  <td style="padding:10px; border:1px solid #dee2e6;"><?= htmlspecialchars($student['comment']) ?></td>
                </tr>
              </table>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div
            style="padding:2rem; text-align:center; color:var(--primary-red); border:1px solid var(--border-color); border-radius:var(--radius-sm); margin-top:2.5rem; background:#fef2f2;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size:2rem; margin-bottom:1rem;"></i>
            <h3 style="margin:0;">No Marks Found</h3>
            <p style="margin-top:0.5rem;">We couldn't find any results for this student.</p>
          </div>
        <?php endif; ?>

      </div>

    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>