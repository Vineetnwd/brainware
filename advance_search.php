<?php
$page_title = "Advance Search - Student Directory";
$active_nav = 'student_zone';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$search_by = isset($_POST['search_by']) ? clean_input($_POST['search_by']) : (isset($_GET['search_by']) ? clean_input($_GET['search_by']) : 'student_name');
$search_value = isset($_POST['search_value']) ? clean_input($_POST['search_value']) : (isset($_GET['search_value']) ? clean_input($_GET['search_value']) : '');

$allowed_fields = [
    'student_name'    => 'Student Name',
    'student_roll'    => 'Roll Number',
    'registration_no' => 'Registration No.',
    'student_father'  => "Father's Name",
    'student_mobile'  => 'Mobile Number',
    'student_email'   => 'Email Address',
];

if (!array_key_exists($search_by, $allowed_fields)) {
    $search_by = 'student_name';
}

$results = [];
$error_message = '';
$is_searched = false;

if (!empty($search_value) && $pdo) {
    $is_searched = true;
    try {
        $sql = "
            SELECT s.*, c.center_name, c.center_code, co.course_name, co.course_code, co.course_duration, co.no_of_semester
            FROM student s
            LEFT JOIN center_details c ON s.center_id = c.id
            LEFT JOIN course_details co ON s.course_id = co.id
            WHERE s.{$search_by} LIKE :val
              AND s.status NOT IN ('removed', 'EMPTY')
            ORDER BY s.id DESC
            LIMIT 50
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':val' => '%' . $search_value . '%']);
        $results = $stmt->fetchAll();

        if (empty($results)) {
            $error_message = "No student records found matching \"$search_value\" in " . $allowed_fields[$search_by] . ".";
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
      <li>Advance Search</li>
    </ul>
    <h1 class="page-title">Advance Student Search</h1>
    <p class="page-subtitle">Search registered candidates by Name, Roll No, Registration No, Father's Name, or Mobile</p>
  </div>
</div>

<div class="container" style="padding-bottom: 4rem; max-width: 1000px;">

  <!-- Search Card -->
  <div class="card" style="border-top:4px solid var(--primary-red);">
    <div class="card-body" style="padding: 1.75rem;">
      <h3 style="font-size:1.15rem; font-weight:700; color:var(--primary-blue-dark); margin-bottom:0.4rem;">
        <i class="fa-solid fa-filter" style="color:var(--primary-red);"></i> Search Candidate Directory
      </h3>
      <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:1.25rem;">
        Select search criteria and enter the search term to look up verified student credentials.
      </p>

      <form action="<?= url('/advance_search.php') ?>" method="POST" style="display:grid; grid-template-columns: 220px 1fr auto; gap:1rem; align-items:flex-end;">
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" for="search_by"><strong>Search By Field</strong></label>
          <select id="search_by" name="search_by" class="form-control">
            <?php foreach ($allowed_fields as $key => $label): ?>
              <option value="<?= $key ?>" <?= ($search_by === $key) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" for="search_value"><strong>Enter Search Query *</strong></label>
          <input type="text" id="search_value" name="search_value" value="<?= htmlspecialchars($search_value) ?>" class="form-control" placeholder="e.g. Rahul, 101, or BR/CPR/01" required autofocus>
        </div>

        <div>
          <button type="submit" class="btn btn-red" style="padding:0.6rem 1.5rem;">
            <i class="fa-solid fa-magnifying-glass"></i> Search
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

  <?php if (!empty($results)): ?>
    <div style="margin-top:2rem;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h3 style="font-size:1.1rem; font-weight:700; color:var(--primary-blue-dark);">
          <i class="fa-solid fa-users" style="color:var(--primary-blue);"></i> Matching Candidates (<?= count($results) ?> Found)
        </h3>
        <span class="badge badge-primary"><?= htmlspecialchars($allowed_fields[$search_by]) ?>: <?= htmlspecialchars($search_value) ?></span>
      </div>

      <?php foreach ($results as $row): ?>
        <div class="verify-result-card" style="margin-top:1.25rem;">
          <div class="table-responsive">
            <table class="table table-bordered" style="background:#ffffff; font-size:0.88rem; margin-bottom:0;">
              <tbody>
                <tr>
                  <th style="width:22%; background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Registration No.</th>
                  <td style="font-weight:700; color:var(--primary-blue-dark);"><?= htmlspecialchars($row['registration_no'] ?: 'N/A') ?></td>
                  <td colspan="2" rowspan="3" style="width:130px; text-align:center; vertical-align:middle; background:var(--bg-page);">
                    <img src="<?= get_upload_image($row['student_photo'], 'student') ?>" 
                         alt="<?= htmlspecialchars($row['student_name']) ?>" 
                         style="width:105px; height:120px; object-fit:cover; border-radius:4px; border:2px solid var(--border-color); margin:0 auto; display:block;">
                  </td>
                </tr>
                <tr>
                  <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Name of Center</th>
                  <td style="font-weight:600;"><?= htmlspecialchars($row['center_name'] ?: 'Brainware Main Campus') ?> (<?= htmlspecialchars($row['center_code'] ?: '') ?>)</td>
                </tr>
                <tr>
                  <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Student Name</th>
                  <td style="font-weight:700; color:var(--primary-red); font-size:0.95rem;"><?= htmlspecialchars($row['student_name']) ?></td>
                </tr>
                <tr>
                  <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Father's Name</th>
                  <td><?= htmlspecialchars($row['student_father']) ?></td>
                  <th style="width:20%; background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Mother's Name</th>
                  <td><?= htmlspecialchars($row['student_mother']) ?></td>
                </tr>
                <tr>
                  <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Date of Birth</th>
                  <td><?= format_display_date($row['date_of_birth']) ?></td>
                  <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Gender</th>
                  <td><?= htmlspecialchars($row['student_sex']) ?></td>
                </tr>
                <tr>
                  <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Email ID</th>
                  <td><?= htmlspecialchars($row['student_email'] ?: 'N/A') ?></td>
                  <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Mobile No.</th>
                  <td><?= htmlspecialchars($row['student_mobile'] ?: 'N/A') ?></td>
                </tr>
                <tr>
                  <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Address</th>
                  <td><?= htmlspecialchars($row['student_address']) ?></td>
                  <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Course</th>
                  <td style="font-weight:700; color:var(--primary-blue);"><?= htmlspecialchars($row['course_name'] ?: $row['course_code']) ?></td>
                </tr>
                <tr>
                  <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Duration</th>
                  <td><?= htmlspecialchars($row['course_duration'] ?: 'N/A') ?></td>
                  <th style="background:var(--primary-blue-soft); color:var(--primary-blue-dark);">Semesters</th>
                  <td><?= htmlspecialchars($row['no_of_semester'] ?: '1') ?> Semester(s)</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div style="display:flex; justify-content:flex-end; gap:0.5rem; margin-top:0.75rem;">
            <a href="<?= url('/marksheet.php?student_id=' . $row['id']) ?>" class="btn btn-sm btn-outline-primary">
              <i class="fa-solid fa-file-invoice"></i> Marksheet
            </a>
            <a href="<?= url('/certificate.php?student_roll=' . urlencode($row['student_roll']) . '&date_of_birth=' . urlencode($row['date_of_birth'])) ?>" class="btn btn-sm btn-outline-danger">
              <i class="fa-solid fa-award"></i> Certificate
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
