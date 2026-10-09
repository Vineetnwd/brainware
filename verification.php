<?php
$page_title = "Online Student & Certificate Verification";
$active_nav = 'verification';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$search_query = isset($_GET['reg_no']) ? clean_input($_GET['reg_no']) : '';
$student_record = null;
$center_record = null;
$course_record = null;
$results_records = [];
$error_message = '';

if (!empty($search_query) && $pdo) {
    try {
        // Search student by registration_no, certificate_no, or student_roll
        $stmt = $pdo->prepare("
            SELECT s.*, c.center_name, c.center_code, c.center_address, c.center_director,
                   co.course_name, co.course_code, co.course_duration, co.no_of_semester
            FROM student s
            LEFT JOIN center_details c ON s.center_id = c.id
            LEFT JOIN course_details co ON s.course_id = co.id
            WHERE (s.registration_no = :query OR s.certificate_no = :query OR s.student_roll = :query)
              AND s.status = 'ACTIVE'
            LIMIT 1
        ");
        $stmt->execute([':query' => $search_query]);
        $student_record = $stmt->fetch();

        if ($student_record) {
            // Fetch semester results if any
            $r_stmt = $pdo->prepare("SELECT * FROM result WHERE student_id = ? AND status = 'ACTIVE' ORDER BY id ASC");
            $r_stmt->execute([$student_record['id']]);
            $results_records = $r_stmt->fetchAll();
        } else {
            $error_message = "No certified student record found matching \"$search_query\". Please verify the Registration / Certificate No and try again.";
        }
    } catch (PDOException $e) {
        $error_message = "A database error occurred during verification. Please try again.";
    }
}
?>

<div class="page-header">
  <div class="container">
    <ul class="breadcrumb">
      <li><a href="<?= url('/') ?>"><i class="fa-solid fa-house"></i> Home</a></li>
      <li><span>/</span></li>
      <li>Verification</li>
    </ul>
    <h1 class="page-title">Online Student & Certificate Verification</h1>
    <p class="page-subtitle">Real-time instant credential verification for employers, institutions, and candidates</p>
  </div>
</div>

<div class="container" style="padding-bottom: 4rem; max-width: 960px;">
  
  <!-- Search Card -->
  <div class="card" style="border-top:4px solid var(--primary-red);">
    <div class="card-body" style="padding:1.75rem;">
      <h3 style="font-size:1.15rem; font-weight:700; color:var(--primary-blue-dark); margin-bottom:0.5rem;">
        <i class="fa-solid fa-shield-halved" style="color:var(--primary-red);"></i> Enter Verification Credentials
      </h3>
      <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:1.25rem;">
        Enter the student's <strong>Registration Number</strong> (e.g. <code>BR/CPR/01/251</code>), <strong>Certificate Number</strong> (e.g. <code>501</code>), or <strong>Roll Number</strong>.
      </p>

      <form action="<?= url('/verification.php') ?>" method="GET" style="display:flex; gap:0.75rem; flex-wrap:wrap;">
        <div style="flex:1; min-width:260px;">
          <input type="text" name="reg_no" value="<?= htmlspecialchars($search_query) ?>" class="form-control" placeholder="Registration No / Certificate No / Roll No" required autofocus>
        </div>
        <button type="submit" class="btn btn-red" style="padding:0.6rem 1.5rem;">
          <i class="fa-solid fa-magnifying-glass"></i> Verify Record
        </button>
      </form>
    </div>
  </div>

  <?php if (!empty($error_message)): ?>
    <div class="alert alert-danger" style="margin-top:1.5rem;">
      <i class="fa-solid fa-triangle-exclamation" style="margin-right:0.5rem;"></i>
      <?= htmlspecialchars($error_message) ?>
    </div>
  <?php endif; ?>

  <?php if ($student_record): ?>
    <!-- Verification Result Card -->
    <div class="verify-result-card">
      
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; border-bottom:1px solid var(--border-color); padding-bottom:1.25rem;">
        <div>
          <span class="verify-status-badge">
            <i class="fa-solid fa-circle-check"></i> OFFICIAL RECORD VERIFIED & VALID
          </span>
          <div style="font-size:0.8rem; color:var(--text-muted); margin-top:0.35rem;">
            Verified under ISO 9001:2015 Educational Standard Database
          </div>
        </div>
        <div>
          <a href="<?= url('/marksheet.php?student_id=' . $student_record['id']) ?>" class="btn btn-sm btn-primary">
            <i class="fa-solid fa-file-invoice"></i> View Official Marksheet
          </a>
        </div>
      </div>

      <!-- Student Master Data Grid -->
      <div class="verify-grid-info">
        <div class="verify-info-cell">
          <div class="label">Student Name</div>
          <div class="value" style="color:var(--primary-blue-dark);"><?= htmlspecialchars($student_record['student_name']) ?></div>
        </div>
        
        <div class="verify-info-cell">
          <div class="label">Father's / Guardian Name</div>
          <div class="value"><?= htmlspecialchars($student_record['student_father'] ?: 'N/A') ?></div>
        </div>

        <div class="verify-info-cell">
          <div class="label">Mother's Name</div>
          <div class="value"><?= htmlspecialchars($student_record['student_mother'] ?: 'N/A') ?></div>
        </div>

        <div class="verify-info-cell">
          <div class="label">Registration No.</div>
          <div class="value" style="color:var(--primary-red);"><?= htmlspecialchars($student_record['registration_no']) ?></div>
        </div>

        <div class="verify-info-cell">
          <div class="label">Roll Number</div>
          <div class="value"><?= htmlspecialchars($student_record['student_roll']) ?></div>
        </div>

        <div class="verify-info-cell">
          <div class="label">Certificate No.</div>
          <div class="value" style="color:var(--primary-blue);"><?= htmlspecialchars($student_record['certificate_no']) ?></div>
        </div>

        <div class="verify-info-cell" style="grid-column: 1 / -1;">
          <div class="label">Course Completed</div>
          <div class="value" style="color:var(--primary-blue-dark); font-size:1.05rem;">
            <?= htmlspecialchars($student_record['course_name']) ?> (<?= htmlspecialchars($student_record['course_code']) ?>)
          </div>
        </div>

        <div class="verify-info-cell">
          <div class="label">Final Percentage</div>
          <div class="value" style="font-size:1.1rem; color:var(--primary-blue);"><?= number_format((float)$student_record['finalpercentage'], 2) ?>%</div>
        </div>

        <div class="verify-info-cell">
          <div class="label">Awarded Grade</div>
          <div class="value">
            <span class="badge badge-active" style="font-size:0.85rem; padding:0.25rem 0.65rem;"><?= htmlspecialchars($student_record['grade']) ?></span>
          </div>
        </div>

        <div class="verify-info-cell">
          <div class="label">Date of Issue</div>
          <div class="value"><?= format_display_date($student_record['date_of_issue']) ?></div>
        </div>

        <div class="verify-info-cell" style="grid-column: 1 / -1;">
          <div class="label">Authorized Study Center</div>
          <div class="value">
            <?= htmlspecialchars($student_record['center_name']) ?> [Code: <?= htmlspecialchars($student_record['center_code']) ?>]
          </div>
        </div>
      </div>

      <!-- Semester Marks Breakdown if available -->
      <?php if (!empty($results_records)): ?>
        <div style="margin-top:2rem;">
          <h4 style="font-size:0.95rem; font-weight:700; color:var(--primary-blue-dark); margin-bottom:0.75rem;">
            <i class="fa-solid fa-list-ol"></i> Examination & Semester Marks Record:
          </h4>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Semester / Exam</th>
                  <th>Written (100)</th>
                  <th>Practical (100)</th>
                  <th>Assignment (100)</th>
                  <th>Viva (100)</th>
                  <th>Total (400)</th>
                  <th>Marksheet No</th>
                  <th>Remarks</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($results_records as $res): ?>
                  <?php $total_sem = $res['written'] + $res['practical'] + $res['assignment'] + $res['viva']; ?>
                  <tr>
                    <td><strong><?= htmlspecialchars(strtoupper($res['exam_name'])) ?></strong></td>
                    <td><?= $res['written'] ?></td>
                    <td><?= $res['practical'] ?></td>
                    <td><?= $res['assignment'] ?></td>
                    <td><?= $res['viva'] ?></td>
                    <td><strong style="color:var(--primary-blue);"><?= $total_sem ?></strong></td>
                    <td><?= htmlspecialchars($res['ms_no']) ?></td>
                    <td><span class="badge badge-active"><?= htmlspecialchars($res['remarks'] ?: 'Pass') ?></span></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>

      <!-- Verification Footer Note -->
      <div style="margin-top:1.5rem; padding:0.85rem; background:#f8fafc; border-radius:var(--radius-sm); font-size:0.78rem; color:var(--text-muted); text-align:center;">
        <i class="fa-solid fa-shield-halved" style="color:var(--success);"></i>
        This electronic verification document is system generated from Brainware Computer Academy central server. Authentic and legally valid for educational and employment background checks.
      </div>

    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
