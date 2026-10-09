<?php
$page_title = "Student Profile";
$active_menu = 'students';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

$student_id = (int)($_GET['id'] ?? 0);
if ($student_id <= 0) {
    header('Location: ' . url('/admin/students.php'));
    exit;
}

$student = null;
$results = [];

if ($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT s.*, c.center_name, c.center_code, c.center_address, c.center_director,
                   co.course_name, co.course_code, co.course_duration, co.no_of_semester
            FROM student s
            LEFT JOIN center_details c ON s.center_id = c.id
            LEFT JOIN course_details co ON s.course_id = co.id
            WHERE s.id = ? LIMIT 1
        ");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch();

        if ($student) {
            $r_stmt = $pdo->prepare("SELECT * FROM result WHERE student_id = ? ORDER BY id ASC");
            $r_stmt->execute([$student_id]);
            $results = $r_stmt->fetchAll();
        }
    } catch (PDOException $e) {}
}

if (!$student) {
    set_flash('danger', 'Student record not found.');
    header('Location: ' . url('/admin/students.php'));
    exit;
}
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-id-card" style="color:var(--primary-blue);"></i> Student Master Profile</h1>
    <p class="admin-page-subtitle">Registration No: <strong style="color:var(--primary-red);"><?= htmlspecialchars($student['registration_no']) ?></strong></p>
  </div>
  <div style="display:flex; gap:0.5rem;">
    <a href="<?= url('/admin/student-edit.php?id=' . $student['id']) ?>" class="btn btn-sm btn-outline-primary">
      <i class="fa-solid fa-pen-to-square"></i> Edit Profile
    </a>
    <a href="<?= url('/admin/student-certificate.php?student_id=' . $student['id']) ?>" class="btn btn-sm btn-warning" style="color:#ffffff !important; background:linear-gradient(135deg, #d97706, #b45309);">
      <i class="fa-solid fa-award"></i> Certificate Entry
    </a>
    <a href="<?= url('/admin/marksheet-print.php?student_id=' . $student['id']) ?>" target="_blank" class="btn btn-sm btn-danger">
      <i class="fa-solid fa-print"></i> Print Marksheet
    </a>
    <a href="<?= url('/admin/certificate-print.php?student_id=' . $student['id']) ?>" target="_blank" class="btn btn-sm btn-primary">
      <i class="fa-solid fa-certificate"></i> Print Certificate
    </a>
  </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 2fr; gap:1.5rem; align-items:start;">
  
  <!-- Left: Photo & Identification Card -->
  <div class="card" style="border-top:3px solid var(--primary-red); text-align:center;">
    <div class="card-body" style="padding:1.5rem;">
      <div style="width:120px; height:135px; margin:0 auto 1rem; border-radius:6px; background:#e2e8f0; overflow:hidden; border:2px solid var(--primary-blue); display:flex; align-items:center; justify-content:center;">
        <?php if (!empty($student['student_photo']) && file_exists(__DIR__ . '/../uploads/students/' . $student['student_photo'])): ?>
          <img src="/uploads/students/<?= htmlspecialchars($student['student_photo']) ?>" alt="Photo" style="width:100%; height:100%; object-fit:cover;">
        <?php else: ?>
          <i class="fa-solid fa-user" style="font-size:3rem; color:#94a3b8;"></i>
        <?php endif; ?>
      </div>

      <h3 style="font-size:1.2rem; font-weight:800; color:var(--primary-blue-dark); margin-bottom:0.25rem;">
        <?= htmlspecialchars($student['student_name']) ?>
      </h3>
      <span class="badge badge-primary"><?= htmlspecialchars($student['course_code']) ?></span>
      <?php if (!empty($student['grade'])): ?>
      <span class="badge badge-active"><?= htmlspecialchars($student['grade']) ?> Grade</span>
      <?php endif; ?>

      <div style="margin-top:1.25rem; text-align:left; border-top:1px solid var(--border-color); padding-top:1rem; font-size:0.82rem; display:flex; flex-direction:column; gap:0.5rem;">
        <div><strong>Reg No:</strong> <span style="color:var(--primary-red); font-weight:700;"><?= htmlspecialchars($student['registration_no']) ?></span></div>
        <div><strong>Roll No:</strong> <?= htmlspecialchars($student['student_roll']) ?></div>
        <div><strong>Certificate No:</strong> <?= htmlspecialchars($student['certificate_no'] ?: 'Not Issued') ?></div>
        <div><strong>Issue Date:</strong> <?= (!empty($student['certificate_no']) && !empty($student['date_of_issue']) && $student['date_of_issue'] != '0000-00-00') ? format_display_date($student['date_of_issue']) : 'Not Issued' ?></div>
        <div><strong>Percentage:</strong> <?= (float)$student['finalpercentage'] > 0 ? number_format((float)$student['finalpercentage'], 2) . '%' : 'N/A' ?></div>
        <div><strong>Status:</strong> <span class="badge badge-active"><?= htmlspecialchars($student['status']) ?></span></div>
      </div>
    </div>
  </div>

  <!-- Right: Personal & Academic Details -->
  <div>
    <div class="card" style="border-top:3px solid var(--primary-blue); margin-bottom:1.5rem;">
      <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-circle-info"></i> Full Academic & Personal Information</h3>
      </div>
      <div class="card-body" style="padding:1.25rem;">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; font-size:0.86rem;">
          <div>
            <div style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; font-weight:700;">Father's Name</div>
            <div style="font-weight:700; color:var(--text-dark);"><?= htmlspecialchars($student['student_father'] ?: 'N/A') ?></div>
          </div>
          <div>
            <div style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; font-weight:700;">Mother's Name</div>
            <div style="font-weight:700; color:var(--text-dark);"><?= htmlspecialchars($student['student_mother'] ?: 'N/A') ?></div>
          </div>
          <div>
            <div style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; font-weight:700;">Mobile Number</div>
            <div style="font-weight:600;"><a href="tel:<?= htmlspecialchars($student['student_mobile']) ?>"><?= htmlspecialchars($student['student_mobile'] ?: 'N/A') ?></a></div>
          </div>
          <div>
            <div style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; font-weight:700;">Email Address</div>
            <div><?= htmlspecialchars($student['student_email'] ?: 'N/A') ?></div>
          </div>
          <div>
            <div style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; font-weight:700;">Date of Birth</div>
            <div><?= format_display_date($student['date_of_birth']) ?></div>
          </div>
          <div>
            <div style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; font-weight:700;">Gender / Blood Group</div>
            <div><?= htmlspecialchars($student['student_sex']) ?> / <?= htmlspecialchars($student['student_bloodgroup']) ?></div>
          </div>
          <div style="grid-column:1/-1;">
            <div style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; font-weight:700;">Authorized Center</div>
            <div style="font-weight:700; color:var(--primary-blue);"><?= htmlspecialchars($student['center_name']) ?> (<?= htmlspecialchars($student['center_code']) ?>)</div>
          </div>
          <div style="grid-column:1/-1;">
            <div style="color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; font-weight:700;">Residential Address</div>
            <div><?= htmlspecialchars($student['student_address'] ?: 'N/A') ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Examination & Marksheet History -->
    <div class="admin-table-card">
      <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-list-ol"></i> Examination & Marks Breakdown</h3>
        <a href="/admin/result-add.php?student_id=<?= $student['id'] ?>" class="btn btn-sm btn-primary">
          <i class="fa-solid fa-plus"></i> Add Semester Marks
        </a>
      </div>
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Exam Name</th>
              <th>Written</th>
              <th>Practical</th>
              <th>Assignment</th>
              <th>Viva</th>
              <th>Total</th>
              <th>Marksheet No</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($results)): ?>
              <?php foreach ($results as $res): ?>
                <?php $tot = $res['written'] + $res['practical'] + $res['assignment'] + $res['viva']; ?>
                <tr>
                  <td><strong><?= htmlspecialchars(strtoupper($res['exam_name'])) ?></strong></td>
                  <td><?= $res['written'] ?></td>
                  <td><?= $res['practical'] ?></td>
                  <td><?= $res['assignment'] ?></td>
                  <td><?= $res['viva'] ?></td>
                  <td><strong style="color:var(--primary-blue);"><?= $tot ?> / 400</strong></td>
                  <td><?= htmlspecialchars($res['ms_no']) ?></td>
                  <td><span class="badge badge-active"><?= htmlspecialchars($res['remarks'] ?: 'Pass') ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="8" style="text-align:center; padding:1rem; color:var(--text-muted);">No exam marks recorded yet. Click "Add Semester Marks" to enter scores.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
