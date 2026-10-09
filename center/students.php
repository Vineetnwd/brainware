<?php
$page_title = "Center Enrolled Students";
$active_menu = 'students';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

$search = clean_input($_GET['search'] ?? '');
$course_filter = (int)($_GET['course_id'] ?? 0);
$status_filter = clean_input($_GET['status'] ?? '');
$per_page = (int)($_GET['per_page'] ?? 15);
$page = (int)($_GET['page'] ?? 1);

$students = [];
$courses = [];
$pagination = null;

if ($pdo && $center_id > 0) {
    try {
        $courses = $pdo->query("SELECT id, course_code, course_name FROM course_details WHERE status = 'ACTIVE' ORDER BY course_code ASC")->fetchAll();

        $where = ["s.center_id = :cid"];
        $params = [':cid' => $center_id];

        if (!empty($status_filter)) {
            $where[] = "s.status = :st";
            $params[':st'] = $status_filter;
        } else {
            $where[] = "s.status NOT IN ('removed', 'EMPTY')";
        }

        if (!empty($search)) {
            $where[] = "(s.student_name LIKE :q OR s.registration_no LIKE :q OR s.student_roll LIKE :q OR s.student_mobile LIKE :q OR s.student_father LIKE :q)";
            $params[':q'] = "%$search%";
        }

        if ($course_filter > 0) {
            $where[] = "s.course_id = :coid";
            $params[':coid'] = $course_filter;
        }

        $where_sql = implode(" AND ", $where);

        // Total count
        $cnt_stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM student s 
            LEFT JOIN course_details co ON s.course_id = co.id 
            WHERE $where_sql
        ");
        $cnt_stmt->execute($params);
        $total_records = (int)$cnt_stmt->fetchColumn();

        $pagination = paginate($total_records, $page, $per_page);

        // Fetch paginated
        $stmt = $pdo->prepare("
            SELECT s.*, co.course_code, co.course_name
            FROM student s
            LEFT JOIN course_details co ON s.course_id = co.id
            WHERE $where_sql
            ORDER BY s.id DESC
            LIMIT :offset, :limit
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':offset', $pagination['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pagination['per_page'], PDO::PARAM_INT);
        $stmt->execute();
        $students = $stmt->fetchAll();
    } catch (PDOException $e) {}
}
?>

<style>
.center-table-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(15, 23, 42, 0.05);
  overflow: hidden;
  margin-bottom: 1.5rem;
}
.center-data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.86rem;
}
.center-data-table th {
  background: #f8fafc;
  color: #475569;
  font-weight: 700;
  text-transform: uppercase;
  font-size: 0.72rem;
  letter-spacing: 0.5px;
  padding: 0.75rem 1rem;
  border-bottom: 1.5px solid #e2e8f0;
  white-space: nowrap;
}
.center-data-table td {
  padding: 0.7rem 1rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  color: #1e293b;
}
.center-data-table tbody tr:hover {
  background-color: #f8fafc;
}
.center-data-table tbody tr:last-child td {
  border-bottom: none;
}
.center-action-btn {
  width: 32px;
  height: 32px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  font-size: 0.85rem;
  border: 1.5px solid #cbd5e1;
  background: #ffffff;
  color: #2563eb;
  transition: all 0.15s ease;
  cursor: pointer;
  text-decoration: none;
}
.center-action-btn:hover {
  border-color: #2563eb;
  background: #eff6ff;
  color: #1e40af;
  transform: translateY(-1px);
}
</style>

<div class="admin-page-header" style="margin-bottom:1.15rem;">
  <div>
    <h1 class="admin-page-title" style="font-size:1.35rem; display:flex; align-items:center; gap:0.5rem;">
      <i class="fa-solid fa-user-graduate" style="color:var(--primary-blue);"></i> Center Student Admissions
    </h1>
    <p class="admin-page-subtitle" style="font-size:0.83rem;">
      Manage enrolled students registered under <strong><?= htmlspecialchars($active_center['center_code'] ?? 'BR/CPR/01') ?></strong>
    </p>
  </div>
  <div style="display:flex; justify-content:flex-end; margin-left:auto;">
    <a href="<?= url('/center/student-add.php') ?>" class="btn btn-sm btn-danger" style="font-weight:700; padding:0.5rem 1.1rem; border-radius:6px; display:inline-flex; align-items:center; gap:0.4rem; box-shadow:0 2px 6px rgba(220,38,38,0.25);">
      <i class="fa-solid fa-user-plus"></i> New Student Admission
    </a>
  </div>
</div>

<!-- Search & Filter Toolbar -->
<div class="toolbar-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:0.85rem 1.15rem; margin-bottom:1.25rem; box-shadow:0 2px 6px rgba(15,23,42,0.03); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.75rem;">
  <form action="<?= url('/center/students.php') ?>" method="GET" style="display:flex; gap:0.6rem; flex-wrap:wrap; flex:1; align-items:center;">
    <div style="position:relative;">
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-sm" placeholder="Search Name, Reg No, Roll, Mobile..." style="min-width:240px; padding:0.45rem 0.75rem; border-radius:6px; border:1px solid #cbd5e1; font-size:0.84rem;">
    </div>

    <select name="course_id" class="form-select form-control-sm" style="max-width:180px; padding:0.45rem 0.75rem; border-radius:6px; border:1px solid #cbd5e1; font-size:0.84rem;">
      <option value="0">All Courses</option>
      <?php foreach ($courses as $c): ?>
        <option value="<?= $c['id'] ?>" <?= ($course_filter == $c['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($c['course_code']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="status" class="form-select form-control-sm" style="max-width:130px; padding:0.45rem 0.75rem; border-radius:6px; border:1px solid #cbd5e1; font-size:0.84rem;">
      <option value="">All Status</option>
      <option value="ACTIVE" <?= ($status_filter === 'ACTIVE') ? 'selected' : '' ?>>ACTIVE</option>
      <option value="PENDING" <?= ($status_filter === 'PENDING') ? 'selected' : '' ?>>PENDING</option>
      <option value="CONFIRMED" <?= ($status_filter === 'CONFIRMED') ? 'selected' : '' ?>>CONFIRMED</option>
    </select>

    <select name="per_page" class="form-select form-control-sm" style="max-width:95px; padding:0.45rem 0.75rem; border-radius:6px; border:1px solid #cbd5e1; font-size:0.84rem;" onchange="this.form.submit()">
      <?php foreach ([10, 15, 25, 50, 100] as $opt): ?>
        <option value="<?= $opt ?>" <?= ($per_page == $opt) ? 'selected' : '' ?>><?= $opt ?>/pg</option>
      <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-sm btn-primary" style="padding:0.45rem 1rem; border-radius:6px; font-weight:600; font-size:0.84rem;">
      <i class="fa-solid fa-filter"></i> Filter
    </button>
    <?php if (!empty($search) || $course_filter > 0 || !empty($status_filter)): ?>
      <a href="<?= url('/center/students.php') ?>" class="btn btn-sm btn-outline-danger" style="padding:0.45rem 0.85rem; border-radius:6px; font-weight:600; font-size:0.84rem;" title="Reset Filters">
        <i class="fa-solid fa-rotate-left"></i> Reset
      </a>
    <?php endif; ?>
  </form>
  <div>
    <span style="font-size:0.82rem; color:#64748b; font-weight:600;">
      Showing <strong><?= count($students) ?></strong> of <strong><?= $pagination['total_records'] ?? count($students) ?></strong> students
    </span>
  </div>
</div>

<div class="center-table-card">
  <div class="table-responsive">
    <table class="center-data-table">
      <thead>
        <tr>
          <th>Reg No</th>
          <th>Student Name</th>
          <th>Father's Name</th>
          <th>Course</th>
          <th style="text-align:center;">Roll No</th>
          <th style="text-align:center;">Certificate No</th>
          <th style="text-align:center;">Grade / %</th>
          <th style="text-align:center; width:95px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($students)): ?>
          <?php foreach ($students as $st): ?>
            <tr>
              <td>
                <strong style="color:var(--primary-red); font-size:0.88rem; letter-spacing:0.3px;">
                  <?= htmlspecialchars($st['registration_no']) ?>
                </strong>
              </td>
              <td>
                <strong style="color:#0f172a; font-size:0.88rem;">
                  <?= htmlspecialchars($st['student_name']) ?>
                </strong>
              </td>
              <td style="color:#475569;"><?= htmlspecialchars($st['student_father'] ?: 'N/A') ?></td>
              <td>
                <span class="badge badge-primary" style="font-size:0.75rem; font-weight:700; padding:0.25rem 0.6rem; border-radius:12px;">
                  <?= htmlspecialchars($st['course_code']) ?>
                </span>
              </td>
              <td style="text-align:center; font-weight:600; color:#334155;"><?= htmlspecialchars($st['student_roll']) ?></td>
              <td style="text-align:center; font-weight:700; color:#0f172a;"><?= htmlspecialchars($st['certificate_no'] ?: '—') ?></td>
              <td style="text-align:center;">
                <span class="badge badge-active" style="font-size:0.75rem; font-weight:700; padding:0.22rem 0.55rem; border-radius:4px;">
                  <?= htmlspecialchars($st['grade'] ?: 'A') ?> (<?= number_format((float)$st['finalpercentage'], 1) ?>%)
                </span>
              </td>
              <td>
                <div style="display:flex; gap:0.45rem; justify-content:center; align-items:center;">
                  <!-- View Profile Button -->
                  <a href="<?= url('/center/student-view.php?id=' . $st['id']) ?>" class="center-action-btn" title="View Student Admission Card">
                    <i class="fa-solid fa-eye"></i>
                  </a>
                  <!-- Edit Student Button -->
                  <a href="<?= url('/center/student-edit.php?id=' . $st['id']) ?>" class="center-action-btn" title="Edit Student Record">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" style="text-align:center; padding:2rem; color:#94a3b8; font-size:0.9rem;">No student records found under this center.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?= render_pagination($pagination, url('/center/students.php')) ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
