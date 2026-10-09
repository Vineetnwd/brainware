<?php
$page_title = "Center Student Marksheets";
$active_menu = 'results';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

$search = clean_input($_GET['search'] ?? '');
$course_filter = (int)($_GET['course_id'] ?? 0);
$exam_filter = clean_input($_GET['exam_name'] ?? '');
$per_page = (int)($_GET['per_page'] ?? 15);
$page = (int)($_GET['page'] ?? 1);

$results = [];
$courses = [];
$exam_names = [];
$pagination = null;

if ($pdo && $center_id > 0) {
    try {
        $courses = $pdo->query("SELECT id, course_code, course_name FROM course_details WHERE status = 'ACTIVE' ORDER BY course_code ASC")->fetchAll();
        $exam_names = $pdo->query("SELECT DISTINCT exam_name FROM result WHERE exam_name != '' ORDER BY exam_name ASC")->fetchAll(PDO::FETCH_COLUMN);

        $where = ["s.center_id = :cid"];
        $params = [':cid' => $center_id];

        if (!empty($search)) {
            $where[] = "(s.student_name LIKE :q OR s.registration_no LIKE :q OR s.student_roll LIKE :q OR r.ms_no LIKE :q)";
            $params[':q'] = "%$search%";
        }

        if ($course_filter > 0) {
            $where[] = "s.course_id = :coid";
            $params[':coid'] = $course_filter;
        }

        if (!empty($exam_filter)) {
            $where[] = "r.exam_name = :ex";
            $params[':ex'] = $exam_filter;
        }

        $where_sql = implode(" AND ", $where);

        // Count total
        $cnt_stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM result r
            JOIN student s ON r.student_id = s.id
            LEFT JOIN course_details co ON s.course_id = co.id
            WHERE $where_sql
        ");
        $cnt_stmt->execute($params);
        $total_records = (int)$cnt_stmt->fetchColumn();

        $pagination = paginate($total_records, $page, $per_page);

        // Fetch paginated
        $stmt = $pdo->prepare("
            SELECT r.*, s.student_name, s.registration_no, s.student_roll, s.finalpercentage, s.grade, co.course_code
            FROM result r
            JOIN student s ON r.student_id = s.id
            LEFT JOIN course_details co ON s.course_id = co.id
            WHERE $where_sql
            ORDER BY r.id DESC
            LIMIT :offset, :limit
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':offset', $pagination['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pagination['per_page'], PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
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
.center-view-badge-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.3rem 0.75rem;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  border: 1.5px solid #2563eb;
  background: #eff6ff;
  color: #1e40af;
  text-decoration: none;
  transition: all 0.15s ease;
}
.center-view-badge-btn:hover {
  background: #2563eb;
  color: #ffffff;
  transform: translateY(-1px);
}
</style>

<div class="admin-page-header" style="margin-bottom:1.15rem;">
  <div>
    <h1 class="admin-page-title" style="font-size:1.35rem; display:flex; align-items:center; gap:0.5rem;">
      <i class="fa-solid fa-file-invoice" style="color:var(--primary-blue);"></i> Center Student Marksheets
    </h1>
    <p class="admin-page-subtitle" style="font-size:0.83rem;">
      Access student evaluation records and semester academic scores for <strong><?= htmlspecialchars($active_center['center_code'] ?? 'BR/CPR/01') ?></strong>
    </p>
  </div>
</div>

<!-- Search & Filter Toolbar -->
<div class="toolbar-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:0.85rem 1.15rem; margin-bottom:1.25rem; box-shadow:0 2px 6px rgba(15,23,42,0.03); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.75rem;">
  <form action="<?= url('/center/results.php') ?>" method="GET" style="display:flex; gap:0.6rem; flex-wrap:wrap; flex:1; align-items:center;">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-sm" placeholder="Search Student, Reg No, MS No..." style="min-width:240px; padding:0.45rem 0.75rem; border-radius:6px; border:1px solid #cbd5e1; font-size:0.84rem;">

    <select name="course_id" class="form-select form-control-sm" style="max-width:180px; padding:0.45rem 0.75rem; border-radius:6px; border:1px solid #cbd5e1; font-size:0.84rem;">
      <option value="0">All Courses</option>
      <?php foreach ($courses as $c): ?>
        <option value="<?= $c['id'] ?>" <?= ($course_filter == $c['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($c['course_code']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <?php if (!empty($exam_names)): ?>
      <select name="exam_name" class="form-select form-control-sm" style="max-width:160px; padding:0.45rem 0.75rem; border-radius:6px; border:1px solid #cbd5e1; font-size:0.84rem;">
        <option value="">All Exams</option>
        <?php foreach ($exam_names as $ex): ?>
          <option value="<?= htmlspecialchars($ex) ?>" <?= ($exam_filter === $ex) ? 'selected' : '' ?>>
            <?= htmlspecialchars(strtoupper($ex)) ?>
          </option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>

    <select name="per_page" class="form-select form-control-sm" style="max-width:95px; padding:0.45rem 0.75rem; border-radius:6px; border:1px solid #cbd5e1; font-size:0.84rem;" onchange="this.form.submit()">
      <?php foreach ([10, 15, 25, 50, 100] as $opt): ?>
        <option value="<?= $opt ?>" <?= ($per_page == $opt) ? 'selected' : '' ?>><?= $opt ?>/pg</option>
      <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-sm btn-primary" style="padding:0.45rem 1rem; border-radius:6px; font-weight:600; font-size:0.84rem;">
      <i class="fa-solid fa-filter"></i> Filter
    </button>
    <?php if (!empty($search) || $course_filter > 0 || !empty($exam_filter)): ?>
      <a href="<?= url('/center/results.php') ?>" class="btn btn-sm btn-outline-danger" style="padding:0.45rem 0.85rem; border-radius:6px; font-weight:600; font-size:0.84rem;" title="Reset Filters">
        <i class="fa-solid fa-rotate-left"></i> Reset
      </a>
    <?php endif; ?>
  </form>
  <div>
    <span style="font-size:0.82rem; color:#64748b; font-weight:600;">
      Showing <strong><?= count($results) ?></strong> of <strong><?= $pagination['total_records'] ?? count($results) ?></strong> marksheets
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
          <th>Course</th>
          <th>Exam</th>
          <th style="text-align:center;">Written</th>
          <th style="text-align:center;">Practical</th>
          <th style="text-align:center;">Assignment</th>
          <th style="text-align:center;">Viva</th>
          <th style="text-align:center;">Total</th>
          <th>MS No</th>
          <th style="text-align:center; width:95px;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($results)): ?>
          <?php foreach ($results as $res): ?>
            <?php $total = $res['written'] + $res['practical'] + $res['assignment'] + $res['viva']; ?>
            <tr>
              <td><strong style="color:var(--primary-red); font-size:0.88rem;"><?= htmlspecialchars($res['registration_no']) ?></strong></td>
              <td><strong style="color:#0f172a; font-size:0.88rem;"><?= htmlspecialchars($res['student_name']) ?></strong></td>
              <td><span class="badge badge-primary" style="font-size:0.75rem; padding:0.25rem 0.6rem; font-weight:700; border-radius:12px;"><?= htmlspecialchars($res['course_code']) ?></span></td>
              <td><strong style="color:#334155;"><?= strtoupper(htmlspecialchars($res['exam_name'])) ?></strong></td>
              <td style="text-align:center; font-weight:600; color:#334155;"><?= $res['written'] ?></td>
              <td style="text-align:center; font-weight:600; color:#334155;"><?= $res['practical'] ?></td>
              <td style="text-align:center; font-weight:600; color:#334155;"><?= $res['assignment'] ?></td>
              <td style="text-align:center; font-weight:600; color:#334155;"><?= $res['viva'] ?></td>
              <td style="text-align:center;"><strong style="color:var(--primary-blue); font-size:0.9rem;"><?= $total ?> / 400</strong></td>
              <td><strong style="color:#475569; font-size:0.85rem;"><?= htmlspecialchars($res['ms_no'] ?: '—') ?></strong></td>
              <td style="text-align:center;">
                <a href="<?= url('/center/student-view.php?id=' . $res['student_id']) ?>" class="center-view-badge-btn" title="View Student Admission Card">
                  <i class="fa-solid fa-eye"></i> View
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="11" style="text-align:center; padding:2rem; color:#94a3b8; font-size:0.9rem;">No student marksheets match the filter criteria.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?= render_pagination($pagination, url('/center/results.php')) ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
