<?php
$page_title = "Registration Verification";
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
            WHERE s.student_roll = :roll 
              AND s.date_of_birth = :dob 
              AND s.status NOT IN ('removed', 'EMPTY')
            LIMIT 1
        ");
        $stmt->execute([':roll' => $student_roll, ':dob' => $date_of_birth]);
        $student_row = $stmt->fetch();

        if (!$student_row) {
            $error_message = "No record found. Please verify your Roll Number and Date of Birth.";
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
      <li>Registration Verification</li>
    </ul>
    <h1 class="page-title">Registration Verification</h1>
    <p class="page-subtitle">Verify student enrollment authenticity, course registration, and branch details</p>
  </div>
</div>

<div class="container" style="padding-bottom: 4rem; max-width: 900px;">

  <!-- Search Card -->
  <div class="card" style="border-top:4px solid var(--primary-blue);">
    <div class="card-body" style="padding: 1.75rem;">
      <h3 style="font-size:1.15rem; font-weight:700; color:var(--primary-blue-dark); margin-bottom:0.4rem;">
        <i class="fa-solid fa-id-card-clip" style="color:var(--primary-blue);"></i> Verify Your Registration Here
      </h3>
      <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:1.25rem;">
        Please enter your registered Roll Number and Date of Birth as per admission record.
      </p>

      <form action="<?= url('/registration_verification.php') ?>" method="POST" style="display:flex; gap:1rem; align-items:flex-end; flex-wrap: nowrap;">
        <div class="form-group" style="margin-bottom:0; flex:1;">
          <label class="form-label" for="student_roll"><strong>Enter Roll No. *</strong></label>
          <input type="text" id="student_roll" name="student_roll" value="<?= htmlspecialchars($student_roll) ?>" class="form-control" placeholder="e.g. 101 or 501" required autofocus>
        </div>

        <div class="form-group" style="margin-bottom:0; flex:1;">
          <label class="form-label" for="date_of_birth"><strong>Date of Birth *</strong></label>
          <input type="date" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($date_of_birth) ?>" class="form-control" required>
        </div>

        <div style="flex-shrink: 0;">
          <button type="submit" class="btn btn-primary" style="padding:0.6rem 1.4rem;">
            <i class="fa-solid fa-magnifying-glass"></i> Submit
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
            <i class="fa-solid fa-circle-check"></i> REGISTRATION VERIFIED & ACTIVE
          </span>
          <div style="font-size:0.8rem; color:var(--text-muted); margin-top:0.35rem;">
            Official Record from Central Database
          </div>
        </div>
        <div>
          <a href="<?= url('/marksheet.php?student_id=' . $student_row['id']) ?>" class="btn btn-sm btn-outline-primary">
            <i class="fa-solid fa-file-lines"></i> View Marksheet
          </a>
          <button onclick="window.print()" class="btn btn-sm btn-danger" style="margin-left:0.4rem;">
            <i class="fa-solid fa-print"></i> Print
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
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Student Name</th>
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
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Course</th>
              <td style="font-weight:700; color:var(--primary-blue);"><?= htmlspecialchars($student_row['course_name'] ?: $student_row['course_code']) ?></td>
            </tr>
            <tr>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Course Duration</th>
              <td><?= htmlspecialchars($student_row['course_duration'] ?: 'N/A') ?></td>
              <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Total Semesters</th>
              <td><?= htmlspecialchars($student_row['no_of_semester'] ?: '1') ?> Semester(s)</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div style="text-align:center; margin-top:1.5rem;">
        <a href="<?= url('/registration_verification.php') ?>" class="btn btn-sm btn-outline-secondary">
          <i class="fa-solid fa-arrow-left"></i> Check Another Registration
        </a>
      </div>

    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
