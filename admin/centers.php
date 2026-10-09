<?php
$page_title = "Study Centers Management";
$active_menu = 'centers';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $del_id = (int)$_GET['id'];
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM center_details WHERE id = ?");
            $stmt->execute([$del_id]);
            set_flash('success', 'Study center record deleted successfully.');
        } catch (PDOException $e) {
            set_flash('danger', 'Failed to delete center. Check if students are linked to this center.');
        }
    }
    header('Location: ' . url('/admin/centers.php'));
    exit;
}

// Handle status toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle_status' && isset($_GET['id'])) {
    $t_id = (int)$_GET['id'];
    $cur_status = clean_input($_GET['status'] ?? 'ACTIVE');
    $new_status = ($cur_status === 'ACTIVE') ? 'INACTIVE' : 'ACTIVE';
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE center_details SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $t_id]);
            set_flash('success', "Center status updated to $new_status.");
        } catch (PDOException $e) {
            set_flash('danger', 'Failed to update center status.');
        }
    }
    header('Location: ' . url('/admin/centers.php'));
    exit;
}

$centers = [];
$search = clean_input($_GET['search'] ?? '');
$status_filter = clean_input($_GET['status'] ?? '');
$per_page = (int)($_GET['per_page'] ?? 15);
if (!in_array($per_page, [10, 15, 25, 50, 100])) {
    $per_page = 15;
}
$current_page = max(1, (int)($_GET['page'] ?? 1));
$pagination = paginate(0, 1, $per_page);

if ($pdo) {
    try {
        $whereSql = " WHERE c.status NOT IN ('removed', 'EMPTY')";
        $params = [];

        if (!empty($search)) {
            $whereSql .= " AND (c.center_code LIKE :q OR c.center_name LIKE :q OR c.center_director LIKE :q OR c.center_mobile LIKE :q OR c.center_address LIKE :q)";
            $params[':q'] = "%$search%";
        }

        if (!empty($status_filter)) {
            $whereSql .= " AND c.status = :st";
            $params[':st'] = $status_filter;
        }

        // Count total records
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM center_details c $whereSql");
        $countStmt->execute($params);
        $total_records = (int)$countStmt->fetchColumn();

        $pagination = paginate($total_records, $current_page, $per_page);

        // Fetch paginated records
        $stmt = $pdo->prepare("
            SELECT c.*, (SELECT COUNT(*) FROM student s WHERE s.center_id = c.id AND s.status NOT IN ('removed', 'EMPTY')) AS student_count
            FROM center_details c
            $whereSql
            ORDER BY c.id DESC
            LIMIT {$pagination['offset']}, {$pagination['per_page']}
        ");
        $stmt->execute($params);
        $centers = $stmt->fetchAll();
    } catch (PDOException $e) {
        $centers = [];
    }
}
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-building-columns" style="color:var(--primary-red);"></i> Study Centers Directory</h1>
    <p class="admin-page-subtitle">Manage authorized study centers, branch codes, director details, and status</p>
  </div>
  <div>
    <a href="<?= url('/admin/center-add.php') ?>" class="btn btn-sm btn-danger">
      <i class="fa-solid fa-plus"></i> Add New Center
    </a>
  </div>
</div>

<!-- Search Toolbar -->
<div class="toolbar-card">
  <form action="<?= url('/admin/centers.php') ?>" method="GET" style="display:flex; gap:0.6rem; flex-wrap:wrap; flex:1; align-items:center;">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-sm" placeholder="Search Code, Center Name, Director, City..." style="max-width:280px;">
    
    <select name="status" class="form-select form-control-sm" style="max-width:140px;">
      <option value="">All Statuses</option>
      <option value="ACTIVE" <?= ($status_filter === 'ACTIVE') ? 'selected' : '' ?>>Active</option>
      <option value="INACTIVE" <?= ($status_filter === 'INACTIVE') ? 'selected' : '' ?>>Inactive</option>
    </select>

    <select name="per_page" class="form-select form-control-sm" style="max-width:110px;" onchange="this.form.submit()">
      <option value="10" <?= ($per_page == 10) ? 'selected' : '' ?>>10 / page</option>
      <option value="15" <?= ($per_page == 15) ? 'selected' : '' ?>>15 / page</option>
      <option value="25" <?= ($per_page == 25) ? 'selected' : '' ?>>25 / page</option>
      <option value="50" <?= ($per_page == 50) ? 'selected' : '' ?>>50 / page</option>
      <option value="100" <?= ($per_page == 100) ? 'selected' : '' ?>>100 / page</option>
    </select>

    <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-filter"></i> Apply</button>
    <?php if (!empty($search) || !empty($status_filter) || $per_page != 15): ?>
      <a href="<?= url('/admin/centers.php') ?>" class="btn btn-sm btn-outline-danger">Reset</a>
    <?php endif; ?>
  </form>
</div>

<!-- Centers Table -->
<div class="admin-table-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Center Code</th>
          <th>Center Name</th>
          <th>Director Name</th>
          <th>Mobile / Contact</th>
          <th>Address</th>
          <th>Students</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($centers)): ?>
          <?php foreach ($centers as $c): ?>
            <tr>
              <td><?= $c['id'] ?></td>
              <td><strong style="color:var(--primary-red);"><?= htmlspecialchars($c['center_code']) ?></strong></td>
              <td>
                <div style="display:flex; align-items:center; gap:0.5rem;">
                  <img src="<?= get_upload_image($c['center_photo'], 'center') ?>" alt="" style="width:28px; height:28px; border-radius:4px; object-fit:cover; border:1px solid var(--border-color); flex-shrink:0;">
                  <strong><?= htmlspecialchars($c['center_name']) ?></strong>
                </div>
              </td>
              <td>
                <div style="display:flex; align-items:center; gap:0.45rem;">
                  <img src="<?= get_upload_image($c['director_photo'] ?? '', 'center') ?>" alt="" style="width:26px; height:26px; border-radius:50%; object-fit:cover; border:1px solid var(--border-color); flex-shrink:0;">
                  <span><?= htmlspecialchars($c['center_director']) ?></span>
                </div>
              </td>
              <td><a href="tel:<?= htmlspecialchars($c['center_mobile']) ?>"><?= htmlspecialchars($c['center_mobile']) ?></a></td>
              <td><small><?= htmlspecialchars($c['center_address']) ?></small></td>
              <td><span class="badge badge-primary"><?= (int)$c['student_count'] ?> Enrolled</span></td>
              <td>
                <a href="<?= url('/admin/centers.php?action=toggle_status&id=' . $c['id'] . '&status=' . $c['status']) ?>" title="Click to toggle status">
                  <span class="badge <?= ($c['status'] === 'ACTIVE') ? 'badge-active' : 'badge-danger' ?>">
                    <?= htmlspecialchars($c['status']) ?>
                  </span>
                </a>
              </td>
              <td>
                <div style="display:flex; gap:0.35rem;">
                  <a href="<?= url('/admin/center-edit.php?id=' . $c['id']) ?>" class="btn btn-sm btn-outline-primary" style="padding:0.2rem 0.45rem;" title="Edit Center">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                  <a href="<?= url('/admin/centers.php?action=delete&id=' . $c['id']) ?>" onclick="return confirmDelete('Are you sure you want to delete this study center?')" class="btn btn-sm btn-outline-danger" style="padding:0.2rem 0.45rem;" title="Delete Center">
                    <i class="fa-solid fa-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="9" style="text-align:center; padding:1.5rem; color:var(--text-muted);">No study centers found matching your criteria.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php render_pagination($pagination, url('/admin/centers.php')); ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
