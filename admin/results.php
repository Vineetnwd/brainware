<?php
$page_title = "Results Management";
$active_menu = 'results';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

// Handle delete result
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $del_id = (int)$_GET['id'];
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM result WHERE id = ?");
            $stmt->execute([$del_id]);
            set_flash('success', 'Semester result deleted successfully.');
        } catch (PDOException $e) {
            set_flash('danger', 'Failed to delete result.');
        }
    }
    header('Location: ' . url('/admin/results.php'));
    exit;
}

$search = clean_input($_GET['search'] ?? '');
$center_filter = (int)($_GET['center_id'] ?? 0);
$course_filter = (int)($_GET['course_id'] ?? 0);
$exam_filter = clean_input($_GET['exam_name'] ?? '');
$per_page = (int)($_GET['per_page'] ?? 15);
if (!in_array($per_page, [10, 15, 25, 50, 100])) {
    $per_page = 15;
}
$current_page = max(1, (int)($_GET['page'] ?? 1));
$pagination = paginate(0, 1, $per_page);

$all_centers = [];
$all_courses = [];
$results = [];

if ($pdo) {
    try {
        $all_centers = $pdo->query("SELECT id, center_code, center_name FROM center_details WHERE status NOT IN ('removed', 'EMPTY') ORDER BY center_name ASC")->fetchAll();
        $all_courses = $pdo->query("SELECT id, course_code, course_name FROM course_details WHERE status NOT IN ('removed', 'EMPTY') ORDER BY course_code ASC")->fetchAll();

        $whereSql = " WHERE r.status NOT IN ('removed', 'EMPTY')";
        $params = [];

        if (!empty($search)) {
            $whereSql .= " AND (s.student_name LIKE :q OR s.registration_no LIKE :q OR s.student_roll LIKE :q OR r.ms_no LIKE :q)";
            $params[':q'] = "%$search%";
        }

        if ($center_filter > 0) {
            $whereSql .= " AND s.center_id = :cid";
            $params[':cid'] = $center_filter;
        }

        if ($course_filter > 0) {
            $whereSql .= " AND s.course_id = :coid";
            $params[':coid'] = $course_filter;
        }

        if (!empty($exam_filter)) {
            $whereSql .= " AND r.exam_name LIKE :ex";
            $params[':ex'] = "%$exam_filter%";
        }

        // Count total records
        $countStmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM result r
            LEFT JOIN student s ON r.student_id = s.id
            $whereSql
        ");
        $countStmt->execute($params);
        $total_records = (int)$countStmt->fetchColumn();

        $pagination = paginate($total_records, $current_page, $per_page);

        // Fetch paginated results
        $sql = "
            SELECT r.*, s.student_name, s.registration_no, s.student_roll, s.finalpercentage, s.grade,
                   co.course_code, c.center_code
            FROM result r
            LEFT JOIN student s ON r.student_id = s.id
            LEFT JOIN course_details co ON s.course_id = co.id
            LEFT JOIN center_details c ON s.center_id = c.id
            $whereSql
            ORDER BY r.id DESC 
            LIMIT {$pagination['offset']}, {$pagination['per_page']}
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
    } catch (PDOException $e) {
        $results = [];
    }
}
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-file-invoice" style="color:var(--primary-blue);"></i> Semester Results & Marks</h1>
    <p class="admin-page-subtitle">Manage theory written, practical lab, assignment, and viva examination marks</p>
  </div>
  <div>
    <a href="<?= url('/admin/result-add.php') ?>" class="btn btn-sm btn-danger">
      <i class="fa-solid fa-plus"></i> Enter Semester Marks
    </a>
  </div>
</div>

<div class="toolbar-card">
  <form action="<?= url('/admin/results.php') ?>" method="GET" style="display:flex; gap:0.6rem; flex-wrap:wrap; flex:1; align-items:center;">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-sm" placeholder="Search Student Name, Reg No, Roll, MS No..." style="max-width:240px;">
    
    <select name="center_id" class="form-select form-control-sm" style="max-width:180px;">
      <option value="0">All Centers</option>
      <?php foreach ($all_centers as $ac): ?>
        <option value="<?= $ac['id'] ?>" <?= ($center_filter == $ac['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($ac['center_code']) ?> - <?= htmlspecialchars($ac['center_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="course_id" class="form-select form-control-sm" style="max-width:150px;">
      <option value="0">All Courses</option>
      <?php foreach ($all_courses as $ac): ?>
        <option value="<?= $ac['id'] ?>" <?= ($course_filter == $ac['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($ac['course_code']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="exam_name" class="form-select form-control-sm" style="max-width:140px;">
      <option value="">All Exams</option>
      <option value="semester 1" <?= (stripos($exam_filter, '1') !== false) ? 'selected' : '' ?>>Semester 1</option>
      <option value="semester 2" <?= (stripos($exam_filter, '2') !== false) ? 'selected' : '' ?>>Semester 2</option>
      <option value="semester 3" <?= (stripos($exam_filter, '3') !== false) ? 'selected' : '' ?>>Semester 3</option>
      <option value="semester 4" <?= (stripos($exam_filter, '4') !== false) ? 'selected' : '' ?>>Semester 4</option>
    </select>

    <select name="per_page" class="form-select form-control-sm" style="max-width:110px;" onchange="this.form.submit()">
      <option value="10" <?= ($per_page == 10) ? 'selected' : '' ?>>10 / page</option>
      <option value="15" <?= ($per_page == 15) ? 'selected' : '' ?>>15 / page</option>
      <option value="25" <?= ($per_page == 25) ? 'selected' : '' ?>>25 / page</option>
      <option value="50" <?= ($per_page == 50) ? 'selected' : '' ?>>50 / page</option>
      <option value="100" <?= ($per_page == 100) ? 'selected' : '' ?>>100 / page</option>
    </select>

    <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-filter"></i> Apply</button>
    <?php if (!empty($search) || $center_filter > 0 || $course_filter > 0 || !empty($exam_filter) || $per_page != 15): ?>
      <a href="<?= url('/admin/results.php') ?>" class="btn btn-sm btn-outline-danger">Reset</a>
    <?php endif; ?>
  </form>
</div>

<div class="admin-table-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Reg No</th>
          <th>Student Name</th>
          <th>Course</th>
          <th>Exam</th>
          <th>Written</th>
          <th>Practical</th>
          <th>Assignment</th>
          <th>Viva</th>
          <th>Total</th>
          <th>MS No</th>
          <th>Issue Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($results)): ?>
          <?php foreach ($results as $res): ?>
            <?php $total = $res['written'] + $res['practical'] + $res['assignment'] + $res['viva']; ?>
            <tr>
              <td><strong style="color:var(--primary-red);"><?= htmlspecialchars($res['registration_no'] ?? 'ID: '.$res['student_id']) ?></strong></td>
              <td><strong><?= htmlspecialchars($res['student_name'] ?? 'Student #'.$res['student_id']) ?></strong></td>
              <td><span class="badge badge-primary"><?= htmlspecialchars($res['course_code'] ?? '') ?></span></td>
              <td><strong><?= htmlspecialchars(strtoupper($res['exam_name'])) ?></strong></td>
              <td><?= $res['written'] ?></td>
              <td><?= $res['practical'] ?></td>
              <td><?= $res['assignment'] ?></td>
              <td><?= $res['viva'] ?></td>
              <td><strong style="color:var(--primary-blue);"><?= $total ?> / 400</strong></td>
              <td><strong><?= htmlspecialchars($res['ms_no']) ?></strong></td>
              <td><small><?= format_display_date($res['date_of_issue']) ?></small></td>
              <td>
                <div style="display:flex; gap:0.3rem;">
                  <a href="<?= url('/admin/result-add.php?student_id=' . $res['student_id']) ?>" class="btn btn-sm btn-outline-primary" style="padding:0.2rem 0.45rem;" title="Edit / Update Marks">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                  <a href="<?= url('/admin/marksheet-print.php?student_id=' . $res['student_id']) ?>" target="_blank" class="btn btn-sm btn-outline-danger" style="padding:0.2rem 0.45rem;" title="Print Marksheet">
                    <i class="fa-solid fa-print"></i>
                  </a>
                  <a href="<?= url('/admin/results.php?action=delete&id=' . $res['id']) ?>" onclick="return confirmDelete()" class="btn btn-sm btn-outline-danger" style="padding:0.2rem 0.45rem;" title="Delete">
                    <i class="fa-solid fa-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="12" style="text-align:center; padding:1.5rem; color:var(--text-muted);">No result marks found matching your criteria.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php render_pagination($pagination, url('/admin/results.php')); ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
