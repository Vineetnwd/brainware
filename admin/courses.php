<?php
$page_title = "Course Catalog";
$active_menu = 'courses';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

// Handle course delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $del_id = (int)$_GET['id'];
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM course_details WHERE id = ?");
            $stmt->execute([$del_id]);
            set_flash('success', 'Course deleted successfully.');
        } catch (PDOException $e) {
            set_flash('danger', 'Failed to delete course.');
        }
    }
    header('Location: ' . url('/admin/courses.php'));
    exit;
}

$courses = [];
$search = clean_input($_GET['search'] ?? '');
$status_filter = clean_input($_GET['status'] ?? '');
$sem_filter = (int)($_GET['semesters'] ?? 0);
$per_page = (int)($_GET['per_page'] ?? 15);
if (!in_array($per_page, [10, 15, 25, 50, 100])) {
    $per_page = 15;
}
$current_page = max(1, (int)($_GET['page'] ?? 1));
$pagination = paginate(0, 1, $per_page);

if ($pdo) {
    try {
        $whereSql = " WHERE status NOT IN ('removed', 'EMPTY')";
        $params = [];

        if (!empty($search)) {
            $whereSql .= " AND (course_code LIKE :q OR course_name LIKE :q OR course_duration LIKE :q)";
            $params[':q'] = "%$search%";
        }

        if (!empty($status_filter)) {
            $whereSql .= " AND status = :st";
            $params[':st'] = $status_filter;
        }

        if ($sem_filter > 0) {
            $whereSql .= " AND no_of_semester = :sem";
            $params[':sem'] = $sem_filter;
        }

        // Count
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM course_details $whereSql");
        $countStmt->execute($params);
        $total_records = (int)$countStmt->fetchColumn();

        $pagination = paginate($total_records, $current_page, $per_page);

        // Fetch
        $stmt = $pdo->prepare("
            SELECT * FROM course_details 
            $whereSql 
            ORDER BY id ASC 
            LIMIT {$pagination['offset']}, {$pagination['per_page']}
        ");
        $stmt->execute($params);
        $courses = $stmt->fetchAll();

        // Fetch module map for syllabus display
        $module_map = [];
        $m_stmt = $pdo->query("SELECT id, module_name, module_semester FROM module WHERE status NOT IN ('removed', 'EMPTY')");
        while ($mrow = $m_stmt->fetch()) {
            $module_map[$mrow['id']] = $mrow['module_name'] . ' (S' . $mrow['module_semester'] . ')';
        }
    } catch (PDOException $e) {
        $courses = [];
        $module_map = [];
    }
}
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-book-open" style="color:var(--primary-blue);"></i> Courses & Semesters Management</h1>
    <p class="admin-page-subtitle">Configure academic courses, duration, semester counts, and syllabus modules</p>
  </div>
  <div style="display:flex; gap:0.5rem;">
    <a href="<?= url('/admin/modules.php') ?>" class="btn btn-sm btn-outline-primary">
      <i class="fa-solid fa-cubes"></i> View Modules
    </a>
    <a href="<?= url('/admin/course-add.php') ?>" class="btn btn-sm btn-danger">
      <i class="fa-solid fa-plus"></i> Add New Course
    </a>
  </div>
</div>

<!-- Search & Filters Toolbar -->
<div class="toolbar-card">
  <form action="<?= url('/admin/courses.php') ?>" method="GET" style="display:flex; gap:0.6rem; flex-wrap:wrap; flex:1; align-items:center;">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-sm" placeholder="Search Code, Course Name, Duration..." style="max-width:280px;">
    
    <select name="semesters" class="form-select form-control-sm" style="max-width:140px;">
      <option value="0">All Semesters</option>
      <option value="1" <?= ($sem_filter === 1) ? 'selected' : '' ?>>1 Semester</option>
      <option value="2" <?= ($sem_filter === 2) ? 'selected' : '' ?>>2 Semesters</option>
      <option value="3" <?= ($sem_filter === 3) ? 'selected' : '' ?>>3 Semesters</option>
      <option value="4" <?= ($sem_filter === 4) ? 'selected' : '' ?>>4 Semesters</option>
    </select>

    <select name="status" class="form-select form-control-sm" style="max-width:130px;">
      <option value="">All Statuses</option>
      <option value="ACTIVE" <?= ($status_filter === 'ACTIVE') ? 'selected' : '' ?>>Active</option>
      <option value="INACTIVE" <?= ($status_filter === 'INACTIVE') ? 'selected' : '' ?>>Inactive</option>
    </select>

    <select name="per_page" class="form-select form-control-sm" style="max-width:110px;" onchange="this.form.submit()">
      <option value="10" <?= ($per_page == 10) ? 'selected' : '' ?>>10 / page</option>
      <option value="15" <?= ($per_page == 15) ? 'selected' : '' ?>>15 / page</option>
      <option value="25" <?= ($per_page == 25) ? 'selected' : '' ?>>25 / page</option>
      <option value="50" <?= ($per_page == 50) ? 'selected' : '' ?>>50 / page</option>
    </select>

    <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-filter"></i> Apply</button>
    <?php if (!empty($search) || !empty($status_filter) || $sem_filter > 0 || $per_page != 15): ?>
      <a href="<?= url('/admin/courses.php') ?>" class="btn btn-sm btn-outline-danger">Reset</a>
    <?php endif; ?>
  </form>
</div>

<div class="admin-table-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Code</th>
          <th>Course Name</th>
          <th>Duration</th>
          <th>Semesters</th>
          <th>Syllabus Modules</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($courses)): ?>
          <?php foreach ($courses as $c): ?>
            <tr>
              <td><?= $c['id'] ?></td>
              <td><span class="badge badge-primary"><?= htmlspecialchars($c['course_code']) ?></span></td>
              <td><strong><?= htmlspecialchars($c['course_name']) ?></strong></td>
              <td><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($c['course_duration']) ?></td>
              <td><span class="badge badge-secondary"><?= (int)$c['no_of_semester'] ?> Sem</span></td>
              <td>
                <?php 
                  $syl_ids = !empty($c['course_syllabus']) ? array_filter(array_map('trim', explode(',', $c['course_syllabus']))) : [];
                  if (!empty($syl_ids)) {
                      echo '<div style="display:flex; flex-wrap:wrap; gap:0.25rem; max-width:240px;">';
                      foreach ($syl_ids as $sid) {
                          $m_label = $module_map[$sid] ?? ('Mod #' . $sid);
                          echo '<span class="badge badge-secondary" style="font-size:0.75rem; font-weight:500;">' . htmlspecialchars($m_label) . '</span>';
                      }
                      echo '</div>';
                  } else {
                      echo '<span style="color:var(--text-muted); font-size:0.8rem;">None</span>';
                  }
                ?>
              </td>
              <td><span class="badge <?= ($c['status'] === 'ACTIVE') ? 'badge-active' : 'badge-danger' ?>"><?= htmlspecialchars($c['status']) ?></span></td>
              <td>
                <div style="display:flex; gap:0.35rem;">
                  <a href="<?= url('/admin/course-edit.php?id=' . $c['id']) ?>" class="btn btn-sm btn-outline-primary" style="padding:0.2rem 0.45rem;" title="Edit Course">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                  <a href="<?= url('/admin/courses.php?action=delete&id=' . $c['id']) ?>" onclick="return confirmDelete('Are you sure you want to delete this course?')" class="btn btn-sm btn-outline-danger" style="padding:0.2rem 0.45rem;" title="Delete Course">
                    <i class="fa-solid fa-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" style="text-align:center; padding:1.5rem; color:var(--text-muted);">No courses found matching your criteria.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php render_pagination($pagination, url('/admin/courses.php')); ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
