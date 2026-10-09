<?php
$page_title = "Certificate Verification";
$active_nav = 'student_zone';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$student_roll = isset($_POST['student_roll']) ? clean_input($_POST['student_roll']) : (isset($_GET['student_roll']) ? clean_input($_GET['student_roll']) : '');
$date_of_birth = isset($_POST['date_of_birth']) ? clean_input($_POST['date_of_birth']) : (isset($_GET['date_of_birth']) ? clean_input($_GET['date_of_birth']) : '');

$student_row = null;
$error_message = '';
$is_searched = false;

if ((!empty($student_roll) && !empty($date_of_birth)) && $pdo) {
    $is_searched = true;
    try {
        $stmt = $pdo->prepare("
            SELECT s.*, c.center_name, c.center_code, c.center_address, c.center_director,
                   co.course_name, co.course_code, co.course_duration, co.no_of_semester
            FROM student s
            LEFT JOIN center_details c ON s.center_id = c.id
            LEFT JOIN course_details co ON s.course_id = co.id
            WHERE (s.student_roll = :roll1 OR s.certificate_no = :roll2)
              AND s.date_of_birth = :dob 
              AND s.status NOT IN ('removed', 'EMPTY')
            LIMIT 1
        ");
        $stmt->execute([':roll1' => $student_roll, ':roll2' => $student_roll, ':dob' => $date_of_birth]);
        $student_row = $stmt->fetch();

        if (!$student_row) {
            $error_message = "No certified record found. Please check your Roll Number / Certificate No. and Date of Birth.";
        }
    } catch (PDOException $e) {
        $error_message = "A database query error occurred. Please try again.";
    }
}
?>

<div class="page-header">
  <div class="container">
    <ul class="breadcrumb">
      <li><a href="<?= url('/') ?>"><i class="fa-solid fa-house"></i> Home</a></li>
      <li><span>/</span></li>
      <li><a href="<?= url('/verification.php') ?>">Student Zone</a></li>
      <li><span>/</span></li>
      <li>Certificate Verification</li>
    </ul>
    <h1 class="page-title">Certificate Verification</h1>
    <p class="page-subtitle">Verify course completion credentials, certificate numbers, and issued grades</p>
  </div>
</div>

<div class="container" style="padding-bottom: 4rem; max-width: 900px;">

  <!-- Search Card -->
  <div class="card no-print search-card-box" style="border-top:4px solid var(--primary-red); max-width:800px; margin:0 auto;">
    <div class="card-header" style="background:#f8fafc; border-bottom:1px solid var(--border-color);">
      <h3 style="margin:0; font-size:1.25rem; color:var(--primary-red); text-transform:uppercase; text-align:center;">Get Your Certificate Here</h3>
    </div>
    <div class="card-body" style="padding:1.5rem;">
      <form action="<?= url('/certificate.php') ?>" method="POST">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem;">
          
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Enter Your Roll. No.<span class="req">*</span></label>
            <input type="text" name="student_roll" value="<?= htmlspecialchars($student_roll) ?>" class="form-control" placeholder="Roll No." required>
          </div>
          
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Date of Birth <span class="req">*</span></label>
            <input type="date" name="date_of_birth" value="<?= htmlspecialchars($date_of_birth) ?>" class="form-control" required>
          </div>
        </div>

        <div style="text-align:center; margin-top:2rem;">
          <button type="submit" class="btn btn-red btn-lg" style="padding:0.6rem 2.5rem;">
            <i class="fa-solid fa-magnifying-glass"></i> Search Certificate
          </button>
        </div>
      </form>
    </div>
  </div>

  <?php if (!empty($error_message)): ?>
    <div class="alert alert-danger" style="margin-top:1.5rem;">
      <i class="fa-solid fa-circle-exclamation" style="margin-right:0.5rem;"></i>
      <?= htmlspecialchars($error_message) ?>
    </div>
  <?php endif; ?>

  <?php if ($student_row): ?>
    <div class="verify-result-card" style="margin-top:2rem;">
      
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; border-bottom:1px solid var(--border-color); padding-bottom:1.25rem;">
        <div>
          <span class="verify-status-badge">
            <i class="fa-solid fa-circle-check"></i> CERTIFICATE VERIFIED & VALID
          </span>
          <div style="font-size:0.8rem; color:var(--text-muted); margin-top:0.35rem;">
            ISO 9001:2015 Educational Standard Verified Credential
          </div>
        </div>
        <div>
          <a href="<?= url('/admin/certificate_print.php?id=' . $student_row['id']) ?>" target="_blank" class="btn btn-sm btn-outline-danger">
            <i class="fa-solid fa-file-pdf"></i> Certificate View
          </a>
          <button onclick="window.print()" class="btn btn-sm btn-primary" style="margin-left:0.4rem;">
            <i class="fa-solid fa-print"></i> Print Details
          </button>
        </div>
      </div>

      <!-- Detail Table Layout matching old system design with modern aesthetic -->
      <div class="table-responsive" style="margin-top:1.5rem;">
        <table class="table table-bordered" style="background:#ffffff; font-size:0.88rem;">
          <tbody>
            <tr>
              <th style="width:25%; background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Registration No.</th>
              <td style="font-weight:700; color:var(--primary-blue-dark);"><?= htmlspecialchars($student_row['registration_no'] ?: 'N/A') ?></td>
              <td colspan="2" rowspan="3" style="width:140px; text-align:center; vertical-align:middle; background:var(--bg-page);">
                <img src="<?= get_upload_image($student_row['student_photo'], 'student') ?>" 
                     alt="<?= htmlspecialchars($student_row['student_name']) ?>" 
                     style="width:110px; height:125px; object-fit:cover; border-radius:4px; border:2px solid var(--border-color); margin:0 auto; display:block;">
              </td>
            </tr>
            <tr>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Name of Center</th>
              <td style="font-weight:600;"><?= htmlspecialchars($student_row['center_name'] ?: 'Brainware Main Campus') ?> (<?= htmlspecialchars($student_row['center_code'] ?: '') ?>)</td>
            </tr>
            <tr>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Candidate Name</th>
              <td style="font-weight:700; color:var(--primary-red); font-size:0.95rem;"><?= htmlspecialchars($student_row['student_name']) ?></td>
            </tr>
            <tr>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Father's Name</th>
              <td><?= htmlspecialchars($student_row['student_father']) ?></td>
              <th style="width:20%; background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Mother's Name</th>
              <td><?= htmlspecialchars($student_row['student_mother']) ?></td>
            </tr>
            <tr>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Date of Birth</th>
              <td><?= format_display_date($student_row['date_of_birth']) ?></td>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Gender</th>
              <td><?= htmlspecialchars($student_row['student_sex']) ?></td>
            </tr>
            <tr>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Email ID</th>
              <td><?= htmlspecialchars($student_row['student_email'] ?: 'N/A') ?></td>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Mobile No.</th>
              <td><?= htmlspecialchars($student_row['student_mobile'] ?: 'N/A') ?></td>
            </tr>
            <tr>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Address</th>
              <td><?= htmlspecialchars($student_row['student_address']) ?></td>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Course Completed</th>
              <td style="font-weight:700; color:var(--primary-blue);"><?= htmlspecialchars($student_row['course_name'] ?: $student_row['course_code']) ?></td>
            </tr>
            <tr>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Duration</th>
              <td><?= htmlspecialchars($student_row['no_of_semester']) ?> Semester(s)</td>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Certificate No.</th>
              <td style="font-weight:700; color:var(--primary-red);"><?= htmlspecialchars($student_row['certificate_no']) ?></td>
            </tr>
            <tr>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Date Of Issue</th>
              <td><?= format_display_date($student_row['date_of_issue']) ?></td>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Final Percentage</th>
              <td style="font-weight:700; color:var(--primary-blue-dark);"><?= htmlspecialchars($student_row['finalpercentage']) ?> %</td>
            </tr>
            <tr>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Grade</th>
              <td><span class="badge badge-active"><?= htmlspecialchars($student_row['grade']) ?></span></td>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Remarks</th>
              <td><?= htmlspecialchars($student_row['comment']) ?></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div style="text-align:center; margin-top:1.5rem;">
        <a href="<?= url('/certificate.php') ?>" class="btn btn-sm btn-outline-secondary">
          <i class="fa-solid fa-arrow-left"></i> Verify Another Certificate
        </a>
      </div>

    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
