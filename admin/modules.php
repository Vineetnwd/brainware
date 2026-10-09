<?php
$page_title = "Syllabus Modules";
$active_menu = 'modules';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

// Handle Add Module
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['add_module'])) {
    $module_name = clean_input($_POST['module_name'] ?? '');
    $module_semester = (int)($_POST['module_semester'] ?? 1);
    $module_syllabus = clean_input($_POST['module_syllabus'] ?? '');
    $status = clean_input($_POST['status'] ?? 'ACTIVE');

    if (!empty($module_name) && !empty($module_syllabus) && $pdo) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO module (module_name, module_semester, module_syllabus, status, disp_order, created_by, created_at, updated_by, updated_at)
                VALUES (?, ?, ?, ?, 0, 1, NOW(), '1', NOW())
            ");
            $stmt->execute([$module_name, $module_semester, $module_syllabus, $status]);
            set_flash('success', "Module '$module_name' (Sem $module_semester) added successfully.");
        } catch (PDOException $e) {
            set_flash('danger', 'Failed to add module.');
        }
    }
    header('Location: ' . url('/admin/modules.php'));
    exit;
}

// Handle Delete Module
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $del_id = (int)$_GET['id'];
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM module WHERE id = ?");
            $stmt->execute([$del_id]);
            set_flash('success', 'Module deleted successfully.');
        } catch (PDOException $e) {
            set_flash('danger', 'Failed to delete module.');
        }
    }
    header('Location: ' . url('/admin/modules.php'));
    exit;
}

$modules = [];
$search = clean_input($_GET['search'] ?? '');
$sem_filter = (int)($_GET['semester'] ?? 0);
$per_page = (int)($_GET['per_page'] ?? 10);
if (!in_array($per_page, [10, 15, 25, 50])) {
    $per_page = 10;
}
$current_page = max(1, (int)($_GET['page'] ?? 1));
$pagination = paginate(0, 1, $per_page);

if ($pdo) {
    try {
        $whereSql = " WHERE status NOT IN ('removed', 'EMPTY')";
        $params = [];

        if (!empty($search)) {
            $whereSql .= " AND (module_name LIKE :q OR module_syllabus LIKE :q)";
            $params[':q'] = "%$search%";
        }

        if ($sem_filter > 0) {
            $whereSql .= " AND module_semester = :sem";
            $params[':sem'] = $sem_filter;
        }

        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM module $whereSql");
        $countStmt->execute($params);
        $total_records = (int)$countStmt->fetchColumn();

        $pagination = paginate($total_records, $current_page, $per_page);

        $stmt = $pdo->prepare("
            SELECT * FROM module 
            $whereSql 
            ORDER BY id ASC 
            LIMIT {$pagination['offset']}, {$pagination['per_page']}
        ");
        $stmt->execute($params);
        $modules = $stmt->fetchAll();
    } catch (PDOException $e) {
        $modules = [];
    }
}
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-cubes" style="color:var(--primary-red);"></i> Semester Modules & Syllabus</h1>
    <p class="admin-page-subtitle">Configure subject curriculum mapped into course semesters</p>
  </div>
</div>

<div style="display:grid; grid-template-columns: 340px 1fr; gap:1.5rem; align-items:start;">
  
  <!-- Add Module Form -->
  <div class="card" style="border-top:3px solid var(--primary-red);">
    <div class="card-header">
      <h3 class="card-title"><i class="fa-solid fa-plus"></i> Add New Syllabus Module</h3>
    </div>
    <div class="card-body" style="padding:1.25rem;">
      <form action="<?= url('/admin/modules.php') ?>" method="POST">
        <input type="hidden" name="add_module" value="1">
        
        <div class="form-group">
          <label class="form-label">Course / Module Title <span class="req">*</span></label>
          <input type="text" name="module_name" class="form-control" placeholder="e.g. DCA, ADCA, Python, Java" required>
        </div>

        <div class="form-group">
          <label class="form-label">Semester</label>
          <select name="module_semester" class="form-select">
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
            <option value="3">Semester 3</option>
            <option value="4">Semester 4</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Syllabus Details <span class="req">*</span></label>
          <textarea name="module_syllabus" class="form-control" rows="5" placeholder="1. Fundamentals of Computers, 2. MS Office, 3. HTML..." required></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="ACTIVE">ACTIVE</option>
            <option value="INACTIVE">INACTIVE</option>
          </select>
        </div>

        <button type="submit" class="btn btn-red btn-block">
          <i class="fa-solid fa-floppy-disk"></i> Save Module
        </button>
      </form>
    </div>
  </div>

  <!-- Modules List -->
  <div>
    <!-- Search & Filter Bar -->
    <div class="toolbar-card" style="margin-bottom:1rem;">
      <form action="<?= url('/admin/modules.php') ?>" method="GET" style="display:flex; gap:0.5rem; flex-wrap:wrap; flex:1; align-items:center;">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-sm" placeholder="Search Module Name, Topics..." style="max-width:200px;">
        
        <select name="semester" class="form-select form-control-sm" style="max-width:130px;">
          <option value="0">All Semesters</option>
          <option value="1" <?= ($sem_filter === 1) ? 'selected' : '' ?>>Semester 1</option>
          <option value="2" <?= ($sem_filter === 2) ? 'selected' : '' ?>>Semester 2</option>
          <option value="3" <?= ($sem_filter === 3) ? 'selected' : '' ?>>Semester 3</option>
          <option value="4" <?= ($sem_filter === 4) ? 'selected' : '' ?>>Semester 4</option>
        </select>

        <select name="per_page" class="form-select form-control-sm" style="max-width:100px;" onchange="this.form.submit()">
          <option value="10" <?= ($per_page == 10) ? 'selected' : '' ?>>10 / page</option>
          <option value="15" <?= ($per_page == 15) ? 'selected' : '' ?>>15 / page</option>
          <option value="25" <?= ($per_page == 25) ? 'selected' : '' ?>>25 / page</option>
          <option value="50" <?= ($per_page == 50) ? 'selected' : '' ?>>50 / page</option>
        </select>

        <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-filter"></i></button>
        <?php if (!empty($search) || $sem_filter > 0 || $per_page != 10): ?>
          <a href="<?= url('/admin/modules.php') ?>" class="btn btn-sm btn-outline-danger">Reset</a>
        <?php endif; ?>
      </form>
    </div>

    <div class="admin-table-card">
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Module Name</th>
              <th>Sem</th>
              <th>Syllabus Content</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($modules)): ?>
              <?php foreach ($modules as $m): ?>
                <tr>
                  <td><strong style="color:var(--primary-red);">#<?= $m['id'] ?></strong></td>
                  <td><strong><?= htmlspecialchars($m['module_name']) ?></strong></td>
                  <td><span class="badge badge-secondary">Sem <?= $m['module_semester'] ?></span></td>
                  <td><small style="color:var(--text-body); max-width:300px; display:inline-block;"><?= htmlspecialchars(substr($m['module_syllabus'], 0, 90)) ?>...</small></td>
                  <td>
                    <a href="<?= url('/admin/modules.php?action=delete&id=' . $m['id']) ?>" onclick="return confirmDelete()" class="btn btn-sm btn-outline-danger" style="padding:0.2rem 0.45rem;" title="Delete">
                      <i class="fa-solid fa-trash"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5" style="text-align:center; padding:1.5rem; color:var(--text-muted);">No syllabus modules found matching your criteria.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php render_pagination($pagination, url('/admin/modules.php')); ?>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
