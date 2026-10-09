<?php
$page_title = "Website Enquiries";
$active_menu = 'enquiries';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

// Handle delete enquiry
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $del_id = (int)$_GET['id'];
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM enquiry WHERE id = ?");
            $stmt->execute([$del_id]);
            set_flash('success', 'Enquiry record deleted.');
        } catch (PDOException $e) {
            set_flash('danger', 'Failed to delete enquiry.');
        }
    }
    header('Location: ' . url('/admin/enquiries.php'));
    exit;
}

$enquiries = [];
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
        $whereSql = " WHERE status NOT IN ('removed', 'EMPTY')";
        $params = [];

        if (!empty($search)) {
            $whereSql .= " AND (e_name LIKE :q OR e_mobile LIKE :q OR e_email LIKE :q OR e_question LIKE :q)";
            $params[':q'] = "%$search%";
        }

        if (!empty($status_filter)) {
            $whereSql .= " AND status = :st";
            $params[':st'] = $status_filter;
        }

        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM enquiry $whereSql");
        $countStmt->execute($params);
        $total_records = (int)$countStmt->fetchColumn();

        $pagination = paginate($total_records, $current_page, $per_page);

        $stmt = $pdo->prepare("
            SELECT * FROM enquiry 
            $whereSql 
            ORDER BY id DESC 
            LIMIT {$pagination['offset']}, {$pagination['per_page']}
        ");
        $stmt->execute($params);
        $enquiries = $stmt->fetchAll();
    } catch (PDOException $e) {
        $enquiries = [];
    }
}
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-envelope-open-text" style="color:var(--primary-red);"></i> Website Enquiries & Leads</h1>
    <p class="admin-page-subtitle">Track incoming student inquiries, franchise requests, and contact submissions</p>
  </div>
</div>

<!-- Search Toolbar -->
<div class="toolbar-card">
  <form action="<?= url('/admin/enquiries.php') ?>" method="GET" style="display:flex; gap:0.6rem; flex-wrap:wrap; flex:1; align-items:center;">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-sm" placeholder="Search Lead Name, Phone, Email, Query..." style="max-width:280px;">
    
    <select name="status" class="form-select form-control-sm" style="max-width:140px;">
      <option value="">All Statuses</option>
      <option value="ACTIVE" <?= ($status_filter === 'ACTIVE') ? 'selected' : '' ?>>Active</option>
      <option value="NEW" <?= ($status_filter === 'NEW') ? 'selected' : '' ?>>New</option>
      <option value="CLOSED" <?= ($status_filter === 'CLOSED') ? 'selected' : '' ?>>Closed</option>
    </select>

    <select name="per_page" class="form-select form-control-sm" style="max-width:110px;" onchange="this.form.submit()">
      <option value="10" <?= ($per_page == 10) ? 'selected' : '' ?>>10 / page</option>
      <option value="15" <?= ($per_page == 15) ? 'selected' : '' ?>>15 / page</option>
      <option value="25" <?= ($per_page == 25) ? 'selected' : '' ?>>25 / page</option>
      <option value="50" <?= ($per_page == 50) ? 'selected' : '' ?>>50 / page</option>
    </select>

    <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-filter"></i> Apply</button>
    <?php if (!empty($search) || !empty($status_filter) || $per_page != 15): ?>
      <a href="<?= url('/admin/enquiries.php') ?>" class="btn btn-sm btn-outline-danger">Reset</a>
    <?php endif; ?>
  </form>
</div>

<div class="admin-table-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Date / Time</th>
          <th>Lead Name</th>
          <th>Mobile Contact</th>
          <th>Email Address</th>
          <th>Query / Message</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($enquiries)): ?>
          <?php foreach ($enquiries as $eq): ?>
            <tr>
              <td>#<?= $eq['id'] ?></td>
              <td><small><?= format_display_date($eq['e_datetime'], 'd M Y, h:i A') ?></small></td>
              <td><strong><?= htmlspecialchars($eq['e_name']) ?></strong></td>
              <td><a href="tel:<?= htmlspecialchars($eq['e_mobile']) ?>"><i class="fa-solid fa-phone" style="font-size:0.75rem;"></i> <?= htmlspecialchars($eq['e_mobile']) ?></a></td>
              <td><small><?= htmlspecialchars($eq['e_email'] ?: '—') ?></small></td>
              <td style="max-width:320px; line-height:1.4;">
                <span style="font-size:0.8rem; color:var(--text-body);"><?= htmlspecialchars($eq['e_question']) ?></span>
              </td>
              <td><span class="badge badge-active"><?= htmlspecialchars($eq['status'] ?: 'NEW') ?></span></td>
              <td>
                <a href="<?= url('/admin/enquiries.php?action=delete&id=' . $eq['id']) ?>" onclick="return confirmDelete()" class="btn btn-sm btn-outline-danger" style="padding:0.2rem 0.45rem;" title="Delete">
                  <i class="fa-solid fa-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" style="text-align:center; padding:1.5rem; color:var(--text-muted);">No website enquiries matching your criteria.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php render_pagination($pagination, url('/admin/enquiries.php')); ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
