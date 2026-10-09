<?php
$page_title = "Admin Dashboard";
$active_menu = 'dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

// Dashboard Metrics
$kpi_students = 0;
$kpi_centers = 0;
$kpi_courses = 0;
$kpi_results = 0;
$kpi_enquiries = 0;
$recent_students = [];
$recent_centers = [];

if ($pdo) {
    try {
        $kpi_students = $pdo->query("SELECT COUNT(*) FROM student WHERE status NOT IN ('removed', 'EMPTY')")->fetchColumn() ?: 0;
        $kpi_centers = $pdo->query("SELECT COUNT(*) FROM center_details WHERE status NOT IN ('removed', 'EMPTY')")->fetchColumn() ?: 0;
        $kpi_courses = $pdo->query("SELECT COUNT(*) FROM course_details WHERE status NOT IN ('removed', 'EMPTY')")->fetchColumn() ?: 0;
        $kpi_results = $pdo->query("SELECT COUNT(*) FROM result WHERE status NOT IN ('removed', 'EMPTY')")->fetchColumn() ?: 0;
        $kpi_enquiries = $pdo->query("SELECT COUNT(*) FROM enquiry WHERE status NOT IN ('removed', 'EMPTY')")->fetchColumn() ?: 0;

        // Recent 6 student registrations
        $s_stmt = $pdo->query("
            SELECT s.*, c.center_name, c.center_code, co.course_code 
            FROM student s
            LEFT JOIN center_details c ON s.center_id = c.id
            LEFT JOIN course_details co ON s.course_id = co.id
            WHERE s.status NOT IN ('removed', 'EMPTY')
            ORDER BY s.id DESC LIMIT 6
        ");
        $recent_students = $s_stmt->fetchAll();

        // Recent Centers
        $c_stmt = $pdo->query("SELECT * FROM center_details WHERE status NOT IN ('removed', 'EMPTY') ORDER BY id DESC LIMIT 5");
        $recent_centers = $c_stmt->fetchAll();
    } catch (PDOException $e) {}
}
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title">Administration Dashboard</h1>
    <p class="admin-page-subtitle">Welcome back, <?= htmlspecialchars($admin_user['username']) ?>. Here is the operational overview.</p>
  </div>
  <div style="display:flex; gap:0.5rem;">
    <a href="<?= url('/admin/student-add.php') ?>" class="btn btn-sm btn-danger">
      <i class="fa-solid fa-user-plus"></i> New Student Admission
    </a>
    <a href="<?= url('/admin/center-add.php') ?>" class="btn btn-sm btn-primary">
      <i class="fa-solid fa-building-circle-check"></i> Add Center
    </a>
  </div>
</div>

<!-- KPI Summary Cards -->
<div class="kpi-grid">
  <div class="kpi-card blue">
    <div class="kpi-data">
      <div class="kpi-label">Total Enrolled Students</div>
      <div class="kpi-value"><?= number_format($kpi_students) ?></div>
    </div>
    <div class="kpi-icon blue"><i class="fa-solid fa-user-graduate"></i></div>
  </div>

  <div class="kpi-card red">
    <div class="kpi-data">
      <div class="kpi-label">Authorized Centers</div>
      <div class="kpi-value"><?= $kpi_centers ?></div>
    </div>
    <div class="kpi-icon red"><i class="fa-solid fa-building-columns"></i></div>
  </div>

  <div class="kpi-card green">
    <div class="kpi-data">
      <div class="kpi-label">Active Courses</div>
      <div class="kpi-value"><?= $kpi_courses ?></div>
    </div>
    <div class="kpi-icon green"><i class="fa-solid fa-book-open"></i></div>
  </div>

  <div class="kpi-card amber">
    <div class="kpi-data">
      <div class="kpi-label">Total Results Processed</div>
      <div class="kpi-value"><?= number_format($kpi_results) ?></div>
    </div>
    <div class="kpi-icon amber"><i class="fa-solid fa-award"></i></div>
  </div>
</div>

<!-- Quick Actions & Recent Tables -->
<div style="display:grid; grid-template-columns: 1.4fr 0.9fr; gap:1.5rem; margin-bottom:1.5rem;">
  
  <!-- Left: Recent Registrations -->
  <div class="admin-table-card">
    <div class="card-header">
      <h3 class="card-title"><i class="fa-solid fa-users" style="color:var(--primary-blue);"></i> Recent Admissions</h3>
      <a href="<?= url('/admin/students.php') ?>" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Reg No</th>
            <th>Student Name</th>
            <th>Course</th>
            <th>Center</th>
            <th>Grade</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($recent_students)): ?>
            <?php foreach ($recent_students as $st): ?>
              <tr>
                <td><strong style="color:var(--primary-red);"><?= htmlspecialchars($st['registration_no']) ?></strong></td>
                <td><strong><?= htmlspecialchars($st['student_name']) ?></strong></td>
                <td><span class="badge badge-primary"><?= htmlspecialchars($st['course_code']) ?></span></td>
                <td><small><?= htmlspecialchars($st['center_code'] ?: 'Center #'.$st['center_id']) ?></small></td>
                <td><span class="badge badge-active"><?= htmlspecialchars($st['grade'] ?: 'A') ?></span></td>
                <td>
                  <a href="/admin/student-view.php?id=<?= $st['id'] ?>" class="btn btn-sm btn-outline-primary" title="View" style="padding:0.2rem 0.45rem;">
                    <i class="fa-solid fa-eye"></i>
                  </a>
                  <a href="/admin/marksheet-print.php?student_id=<?= $st['id'] ?>" target="_blank" class="btn btn-sm btn-outline-danger" title="Print Marksheet" style="padding:0.2rem 0.45rem;">
                    <i class="fa-solid fa-print"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" style="text-align:center; padding:1.5rem; color:var(--text-muted);">No student records found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Right: Centers List & Quick Links -->
  <div>
    <div class="admin-table-card" style="margin-bottom:1.25rem;">
      <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-network-wired" style="color:var(--primary-red);"></i> Active Study Centers</h3>
        <a href="<?= url('/admin/centers.php') ?>" class="btn btn-sm btn-outline-primary">Manage</a>
      </div>
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Code</th>
              <th>Center / Director</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($recent_centers)): ?>
              <?php foreach ($recent_centers as $rc): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($rc['center_code']) ?></strong></td>
                  <td>
                    <div><strong><?= htmlspecialchars($rc['center_name']) ?></strong></div>
                    <small style="color:var(--text-muted);"><i class="fa-solid fa-user-tie"></i> <?= htmlspecialchars($rc['center_director']) ?></small>
                  </td>
                  <td><span class="badge badge-active"><?= htmlspecialchars($rc['status']) ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Quick Tool Links -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-bolt" style="color:var(--warning);"></i> Quick Operations</h3>
      </div>
      <div class="card-body" style="padding:0.85rem; display:grid; grid-template-columns:1fr 1fr; gap:0.5rem;">
        <a href="<?= url('/admin/results.php') ?>" class="btn btn-sm btn-outline-primary" style="justify-content:flex-start;">
          <i class="fa-solid fa-square-plus"></i> Enter Marks
        </a>
        <a href="<?= url('/admin/enquiries.php') ?>" class="btn btn-sm btn-outline-primary" style="justify-content:flex-start;">
          <i class="fa-solid fa-inbox"></i> Inquiries (<?= $kpi_enquiries ?>)
        </a>
        <a href="<?= url('/admin/courses.php') ?>" class="btn btn-sm btn-outline-primary" style="justify-content:flex-start;">
          <i class="fa-solid fa-book"></i> Course Catalog
        </a>
        <a href="<?= url('/admin/users.php') ?>" class="btn btn-sm btn-outline-primary" style="justify-content:flex-start;">
          <i class="fa-solid fa-key"></i> Passwords
        </a>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
