<?php
$page_title = "Student Admission Card";
$active_menu = 'students';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

$student_id = (int)($_GET['id'] ?? 0);
if ($student_id <= 0) {
    header('Location: ' . url('/center/students.php'));
    exit;
}

$student = null;
$results = [];

if ($pdo) {
    $stmt = $pdo->prepare("
        SELECT s.*, co.course_name, co.course_code, co.course_duration, cd.center_name, cd.center_code as c_code
        FROM student s
        LEFT JOIN course_details co ON s.course_id = co.id
        LEFT JOIN center_details cd ON s.center_id = cd.id
        WHERE s.id = ? AND s.center_id = ? LIMIT 1
    ");
    $stmt->execute([$student_id, $center_id]);
    $student = $stmt->fetch();

    if ($student) {
        $r_stmt = $pdo->prepare("SELECT * FROM result WHERE student_id = ? ORDER BY id ASC");
        $r_stmt->execute([$student_id]);
        $results = $r_stmt->fetchAll();
    }
}

if (!$student) {
    set_flash('danger', 'Student record not found or access unauthorized.');
    header('Location: ' . url('/center/students.php'));
    exit;
}

$photo_url = get_upload_image($student['student_photo'] ?? '', 'student');

// Format clean course display without duplicate code
$course_display = htmlspecialchars($student['course_name'] ?? '');
if (!empty($student['course_code']) && strpos($course_display, '(' . $student['course_code'] . ')') === false) {
    $course_display .= ' (' . htmlspecialchars($student['course_code']) . ')';
}
?>

<div class="admin-page-header" style="margin-bottom:1.15rem;">
  <div>
    <h1 class="admin-page-title" style="font-size:1.35rem; display:flex; align-items:center; gap:0.5rem;">
      <i class="fa-solid fa-id-card" style="color:var(--primary-blue);"></i> Student Admission Card
    </h1>
    <p class="admin-page-subtitle" style="font-size:0.83rem;">
      Registration No: <strong style="color:var(--primary-red);"><?= htmlspecialchars($student['registration_no']) ?></strong>
    </p>
  </div>
  <div style="display:flex; justify-content:flex-end; gap:0.5rem; align-items:center; margin-left:auto;">
    <a href="<?= url('/center/student-edit.php?id=' . $student['id']) ?>" class="btn btn-sm btn-primary" style="padding:0.4rem 0.95rem; font-weight:700; font-size:0.82rem;">
      <i class="fa-solid fa-pen-to-square"></i> Edit Profile
    </a>
  </div>
</div>

<div style="display:grid; grid-template-columns: 270px 1fr; gap:1.25rem; align-items:start;">
  
  <!-- ================= LEFT COLUMN: Compact Profile Badge ================= -->
  <div class="card" style="border-radius:10px; border:1px solid #e2e8f0; box-shadow:0 2px 8px rgba(15,23,42,0.05); overflow:hidden; background:#ffffff;">
    <div style="height:3.5px; background:linear-gradient(90deg, #1e3a8a 0%, #2563eb 100%);"></div>
    
    <div class="card-body" style="padding:1.25rem 1rem; text-align:center;">
      <!-- Passport Photo Frame -->
      <div style="width:105px; height:128px; margin:0 auto 0.85rem; border-radius:8px; background:#f8fafc; overflow:hidden; border:2px solid #2563eb; display:flex; align-items:center; justify-content:center; box-shadow:0 3px 10px rgba(37,99,235,0.15);">
        <img src="<?= $photo_url ?>" alt="<?= htmlspecialchars($student['student_name']) ?>" 
          style="width:100%; height:100%; object-fit:cover;" 
          onerror="this.onerror=null; this.src='<?= url('/assets/images/logo.png') ?>';">
      </div>

      <h3 style="font-size:1.1rem; font-weight:800; color:#0f172a; margin-bottom:0.25rem; line-height:1.25;">
        <?= htmlspecialchars($student['student_name']) ?>
      </h3>
      
      <div style="margin-bottom:1rem;">
        <span class="badge badge-primary" style="font-size:0.78rem; padding:0.22rem 0.65rem; font-weight:700; border-radius:12px;">
          <?= htmlspecialchars($student['course_code']) ?>
        </span>
      </div>

      <!-- Quick Compact Detail Rows -->
      <div style="text-align:left; border-top:1px solid #f1f5f9; padding-top:0.85rem; font-size:0.82rem; display:flex; flex-direction:column; gap:0.45rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:0.3rem 0.5rem; background:#f8fafc; border-radius:5px;">
          <span style="color:#64748b; font-weight:600; font-size:0.78rem;">Reg No:</span>
          <strong style="color:var(--primary-red); font-size:0.84rem;"><?= htmlspecialchars($student['registration_no']) ?></strong>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; padding:0.15rem 0.5rem;">
          <span style="color:#64748b; font-size:0.78rem;">Roll No:</span>
          <strong style="color:#1e293b;"><?= htmlspecialchars($student['student_roll']) ?></strong>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; padding:0.15rem 0.5rem;">
          <span style="color:#64748b; font-size:0.78rem;">Certificate:</span>
          <strong style="color:#1e293b;"><?= htmlspecialchars($student['certificate_no'] ?: 'Under Process') ?></strong>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; padding:0.15rem 0.5rem;">
          <span style="color:#64748b; font-size:0.78rem;">Grade / %:</span>
          <span class="badge badge-active" style="font-size:0.75rem; font-weight:700; padding:0.18rem 0.5rem;">
            <?= htmlspecialchars($student['grade'] ?: 'A') ?> (<?= number_format((float)$student['finalpercentage'], 1) ?>%)
          </span>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; padding:0.15rem 0.5rem;">
          <span style="color:#64748b; font-size:0.78rem;">Center Code:</span>
          <strong style="color:#1e3a8a;"><?= htmlspecialchars($student['c_code'] ?? $active_center['center_code'] ?? 'BR/CPR/01') ?></strong>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; padding:0.15rem 0.5rem;">
          <span style="color:#64748b; font-size:0.78rem;">Admission:</span>
          <span style="font-weight:600; color:#334155;"><?= format_display_date($student['created_at'] ?? $student['student_dos'] ?? '') ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- ================= RIGHT COLUMN: Admission Details & Exam Evaluation ================= -->
  <div style="display:flex; flex-direction:column; gap:1.15rem;">
    
    <!-- Candidate Admission Details Card -->
    <div class="card" style="border-radius:10px; border:1px solid #e2e8f0; box-shadow:0 2px 8px rgba(15,23,42,0.05); overflow:hidden; background:#ffffff;">
      <div style="height:3px; background:linear-gradient(90deg, #1e3a8a 0%, #2563eb 100%);"></div>
      
      <div style="padding:0.75rem 1.25rem; background:#ffffff; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0; font-size:0.95rem; font-weight:700; color:#1e3a8a; display:flex; align-items:center; gap:0.45rem;">
          <i class="fa-solid fa-address-card" style="color:var(--primary-red);"></i> Candidate Admission Record
        </h3>
        <span style="font-size:0.75rem; color:#64748b; background:#f1f5f9; padding:0.18rem 0.55rem; border-radius:4px; font-weight:600;">
          Status: Active
        </span>
      </div>

      <div class="card-body" style="padding:1rem 1.25rem;">
        <!-- Compact 3-Column Info Grid -->
        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:0.85rem 1.25rem; font-size:0.84rem;">
          
          <div>
            <div style="color:#64748b; font-size:0.72rem; text-transform:uppercase; font-weight:700; margin-bottom:0.15rem;">Father's Name</div>
            <div style="font-weight:700; color:#0f172a;"><?= htmlspecialchars($student['student_father'] ?: 'N/A') ?></div>
          </div>

          <div>
            <div style="color:#64748b; font-size:0.72rem; text-transform:uppercase; font-weight:700; margin-bottom:0.15rem;">Mother's Name</div>
            <div style="font-weight:700; color:#0f172a;"><?= htmlspecialchars($student['student_mother'] ?: 'N/A') ?></div>
          </div>

          <div>
            <div style="color:#64748b; font-size:0.72rem; text-transform:uppercase; font-weight:700; margin-bottom:0.15rem;">Mobile Number</div>
            <div style="font-weight:700; color:#2563eb;">
              <?php if (!empty($student['student_mobile'])): ?>
                <a href="tel:<?= htmlspecialchars($student['student_mobile']) ?>" style="text-decoration:none; color:inherit; display:inline-flex; align-items:center; gap:0.3rem;">
                  <i class="fa-solid fa-phone" style="font-size:0.75rem;"></i> <?= htmlspecialchars($student['student_mobile']) ?>
                </a>
              <?php else: ?>
                <span style="color:#94a3b8;">N/A</span>
              <?php endif; ?>
            </div>
          </div>

          <div>
            <div style="color:#64748b; font-size:0.72rem; text-transform:uppercase; font-weight:700; margin-bottom:0.15rem;">Date of Birth</div>
            <div style="font-weight:600; color:#0f172a;">
              <i class="fa-regular fa-calendar" style="color:#64748b; font-size:0.78rem;"></i> <?= format_display_date($student['date_of_birth']) ?>
            </div>
          </div>

          <div>
            <div style="color:#64748b; font-size:0.72rem; text-transform:uppercase; font-weight:700; margin-bottom:0.15rem;">Gender</div>
            <div style="font-weight:600; color:#0f172a;"><?= htmlspecialchars(strtoupper($student['student_sex'] ?: 'Not Specified')) ?></div>
          </div>

          <div>
            <div style="color:#64748b; font-size:0.72rem; text-transform:uppercase; font-weight:700; margin-bottom:0.15rem;">Blood Group</div>
            <div style="font-weight:700; color:#dc2626;"><?= htmlspecialchars($student['student_bloodgroup'] ?: 'N/A') ?></div>
          </div>

          <div style="grid-column:1/-1; padding-top:0.4rem; border-top:1px solid #f1f5f9;">
            <div style="color:#64748b; font-size:0.72rem; text-transform:uppercase; font-weight:700; margin-bottom:0.15rem;">Course Enrolled</div>
            <div style="font-weight:700; color:#1e3a8a; font-size:0.9rem; display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
              <span><i class="fa-solid fa-graduation-cap" style="color:#1e3a8a;"></i> <?= $course_display ?></span>
              <?php if (!empty($student['course_duration'])): ?>
                <span class="badge badge-secondary" style="font-size:0.75rem; font-weight:600; background:#f1f5f9; color:#475569;">
                  <i class="fa-regular fa-clock"></i> <?= htmlspecialchars($student['course_duration']) ?>
                </span>
              <?php endif; ?>
            </div>
          </div>

          <div style="grid-column:1/-1; padding-top:0.2rem;">
            <div style="color:#64748b; font-size:0.72rem; text-transform:uppercase; font-weight:700; margin-bottom:0.15rem;">Residential Address</div>
            <div style="font-weight:500; color:#334155; line-height:1.4;">
              <i class="fa-solid fa-location-dot" style="color:#dc2626; font-size:0.8rem;"></i> <?= htmlspecialchars($student['student_address'] ?: 'No address provided') ?>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Exam Scores & Evaluation Table Card -->
    <div class="admin-table-card" style="margin-bottom:0; border-radius:10px; border:1px solid #e2e8f0; box-shadow:0 2px 8px rgba(15,23,42,0.05);">
      <div style="padding:0.75rem 1.25rem; background:#ffffff; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0; font-size:0.95rem; font-weight:700; color:#1e3a8a; display:flex; align-items:center; gap:0.45rem;">
          <i class="fa-solid fa-list-check" style="color:var(--primary-red);"></i> Exam Scores & Evaluation
        </h3>
        <span style="font-size:0.75rem; color:#64748b; font-weight:600; background:#f8fafc; padding:0.18rem 0.55rem; border-radius:4px; border:1px solid #e2e8f0;">
          <?= count($results) ?> Semester <?= count($results) === 1 ? 'Record' : 'Records' ?>
        </span>
      </div>

      <div class="table-responsive">
        <table class="admin-table" style="width:100%; border-collapse:collapse;">
          <thead>
            <tr style="background:#f8fafc;">
              <th style="padding:0.6rem 0.85rem; font-size:0.72rem; font-weight:700; color:#475569; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Semester</th>
              <th style="padding:0.6rem 0.85rem; font-size:0.72rem; font-weight:700; color:#475569; text-transform:uppercase; text-align:center; border-bottom:1px solid #e2e8f0;">Written</th>
              <th style="padding:0.6rem 0.85rem; font-size:0.72rem; font-weight:700; color:#475569; text-transform:uppercase; text-align:center; border-bottom:1px solid #e2e8f0;">Practical</th>
              <th style="padding:0.6rem 0.85rem; font-size:0.72rem; font-weight:700; color:#475569; text-transform:uppercase; text-align:center; border-bottom:1px solid #e2e8f0;">Assignment</th>
              <th style="padding:0.6rem 0.85rem; font-size:0.72rem; font-weight:700; color:#475569; text-transform:uppercase; text-align:center; border-bottom:1px solid #e2e8f0;">Viva</th>
              <th style="padding:0.6rem 0.85rem; font-size:0.72rem; font-weight:700; color:#475569; text-transform:uppercase; text-align:center; border-bottom:1px solid #e2e8f0;">Total</th>
              <th style="padding:0.6rem 0.85rem; font-size:0.72rem; font-weight:700; color:#475569; text-transform:uppercase; text-align:center; border-bottom:1px solid #e2e8f0;">Result</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($results)): ?>
              <?php foreach ($results as $res): ?>
                <?php $tot = $res['written'] + $res['practical'] + $res['assignment'] + $res['viva']; ?>
                <tr style="border-bottom:1px solid #f1f5f9; transition:background 0.15s ease;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                  <td style="padding:0.65rem 0.85rem; font-weight:700; color:#1e293b; font-size:0.84rem;">
                    <?= strtoupper(htmlspecialchars($res['exam_name'])) ?>
                  </td>
                  <td style="padding:0.65rem 0.85rem; text-align:center; font-weight:600; color:#334155; font-size:0.84rem;"><?= $res['written'] ?></td>
                  <td style="padding:0.65rem 0.85rem; text-align:center; font-weight:600; color:#334155; font-size:0.84rem;"><?= $res['practical'] ?></td>
                  <td style="padding:0.65rem 0.85rem; text-align:center; font-weight:600; color:#334155; font-size:0.84rem;"><?= $res['assignment'] ?></td>
                  <td style="padding:0.65rem 0.85rem; text-align:center; font-weight:600; color:#334155; font-size:0.84rem;"><?= $res['viva'] ?></td>
                  <td style="padding:0.65rem 0.85rem; text-align:center;">
                    <strong style="color:var(--primary-blue); font-size:0.9rem;"><?= $tot ?> / 400</strong>
                  </td>
                  <td style="padding:0.65rem 0.85rem; text-align:center;">
                    <span class="badge badge-active" style="padding:0.2rem 0.55rem; font-size:0.74rem; font-weight:700;">
                      <?= htmlspecialchars($res['remarks'] ?: 'PASS') ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" style="text-align:center; padding:1.5rem; color:var(--text-muted); font-size:0.84rem;">
                  No examination marks recorded yet for this candidate.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
