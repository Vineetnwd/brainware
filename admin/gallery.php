<?php
$page_title = "Gallery Management";
$active_menu = 'gallery';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

// Handle Add Gallery Image
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['add_photo'])) {
    $image_title = clean_input($_POST['image_title'] ?? '');
    $image_description = clean_input($_POST['image_description'] ?? '');
    $image_date = clean_input($_POST['image_date'] ?? date('Y-m-d'));
    $video_url = clean_input($_POST['video_url'] ?? '');
    $status = clean_input($_POST['status'] ?? 'ACTIVE');

    $image_url = '';
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $res = handle_file_upload($_FILES['image_file'], __DIR__ . '/../uploads/gallery');
        if ($res['success']) {
            $image_url = $res['filename'];
        }
    }

    $is_slider = isset($_POST['is_slider']) ? 'slider' : 'image';

    if (!empty($image_title) && $pdo) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO gallery (image_title, image_date, image_description, status, image_by, video_url, image_url, file_type, created_by, created_at, updated_by, updated_at)
                VALUES (?, ?, ?, ?, 'Admin', ?, ?, ?, 1, NOW(), 1, NOW())
            ");
            $stmt->execute([$image_title, $image_date, $image_description, $status, $video_url, $image_url, $is_slider]);
            set_flash('success', 'Gallery item added successfully.');
        } catch (PDOException $e) {
            set_flash('danger', 'Failed to add gallery item: ' . $e->getMessage());
        }
    }
    header('Location: ' . url('/admin/gallery.php'));
    exit;
}

// Handle Toggle Slider
if (isset($_GET['action']) && $_GET['action'] === 'toggle_slider' && isset($_GET['id'])) {
    $t_id = (int)$_GET['id'];
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE gallery SET file_type = IF(file_type = 'slider', 'image', 'slider') WHERE id = ?");
            $stmt->execute([$t_id]);
            set_flash('success', 'Slider status updated.');
        } catch (PDOException $e) {
            set_flash('danger', 'Failed to update slider status.');
        }
    }
    header('Location: ' . url('/admin/gallery.php'));
    exit;
}

// Handle Delete Gallery Item
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $del_id = (int)$_GET['id'];
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->execute([$del_id]);
            set_flash('success', 'Gallery item deleted.');
        } catch (PDOException $e) {
            set_flash('danger', 'Failed to delete gallery item.');
        }
    }
    header('Location: ' . url('/admin/gallery.php'));
    exit;
}

$gallery_items = [];
$search = clean_input($_GET['search'] ?? '');
$status_filter = clean_input($_GET['status'] ?? '');
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
            $whereSql .= " AND (image_title LIKE :q OR image_description LIKE :q)";
            $params[':q'] = "%$search%";
        }

        if (!empty($status_filter)) {
            $whereSql .= " AND status = :st";
            $params[':st'] = $status_filter;
        }

        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM gallery $whereSql");
        $countStmt->execute($params);
        $total_records = (int)$countStmt->fetchColumn();

        $pagination = paginate($total_records, $current_page, $per_page);

        $stmt = $pdo->prepare("
            SELECT * FROM gallery 
            $whereSql 
            ORDER BY id DESC 
            LIMIT {$pagination['offset']}, {$pagination['per_page']}
        ");
        $stmt->execute($params);
        $gallery_items = $stmt->fetchAll();
    } catch (PDOException $e) {
        $gallery_items = [];
    }
}
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-images" style="color:var(--primary-blue);"></i> Media Gallery Management</h1>
    <p class="admin-page-subtitle">Upload photos and video links displayed on the public gallery page</p>
  </div>
</div>

<div style="display:grid; grid-template-columns: 340px 1fr; gap:1.5rem; align-items:start;">
  
  <!-- Add Photo Form -->
  <div class="card" style="border-top:3px solid var(--primary-blue);">
    <div class="card-header">
      <h3 class="card-title"><i class="fa-solid fa-upload"></i> Upload Media Item</h3>
    </div>
    <div class="card-body" style="padding:1.25rem;">
      <form action="<?= url('/admin/gallery.php') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="add_photo" value="1">
        
        <div class="form-group">
          <label class="form-label">Photo Title / Caption <span class="req">*</span></label>
          <input type="text" name="image_title" class="form-control" placeholder="e.g. Annual Convocation 2026" required>
        </div>

        <div class="form-group">
          <label class="form-label">Event Date</label>
          <input type="date" name="image_date" value="<?= date('Y-m-d') ?>" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label">Select Image File</label>
          <input type="file" name="image_file" class="form-control" accept="image/*">
        </div>

        <div class="form-group">
          <label class="form-label">Optional Video URL</label>
          <input type="text" name="video_url" class="form-control" placeholder="https://youtube.com/...">
        </div>

        <div class="form-group">
          <label class="form-label">Short Description</label>
          <textarea name="image_description" class="form-control" rows="2" placeholder="Event details..."></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="ACTIVE" selected>ACTIVE</option>
            <option value="INACTIVE">INACTIVE</option>
          </select>
        </div>

        <div class="form-group" style="display:flex; align-items:center; gap:0.5rem; margin-top:1rem;">
          <input type="checkbox" id="is_slider" name="is_slider" value="1" style="width:18px; height:18px; cursor:pointer;">
          <label class="form-label" for="is_slider" style="margin-bottom:0; cursor:pointer;">Set as Hero Slider</label>
        </div>

        <button type="submit" class="btn btn-primary btn-block">
          <i class="fa-solid fa-upload"></i> Upload to Gallery
        </button>
      </form>
    </div>
  </div>

  <!-- Gallery List -->
  <div>
    <!-- Search & Filter Bar -->
    <div class="toolbar-card" style="margin-bottom:1rem;">
      <form action="<?= url('/admin/gallery.php') ?>" method="GET" style="display:flex; gap:0.5rem; flex-wrap:wrap; flex:1; align-items:center;">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-sm" placeholder="Search Image Title, Description..." style="max-width:220px;">
        
        <select name="status" class="form-select form-control-sm" style="max-width:130px;">
          <option value="">All Statuses</option>
          <option value="ACTIVE" <?= ($status_filter === 'ACTIVE') ? 'selected' : '' ?>>Active</option>
          <option value="INACTIVE" <?= ($status_filter === 'INACTIVE') ? 'selected' : '' ?>>Inactive</option>
        </select>

        <select name="per_page" class="form-select form-control-sm" style="max-width:100px;" onchange="this.form.submit()">
          <option value="10" <?= ($per_page == 10) ? 'selected' : '' ?>>10 / page</option>
          <option value="15" <?= ($per_page == 15) ? 'selected' : '' ?>>15 / page</option>
          <option value="25" <?= ($per_page == 25) ? 'selected' : '' ?>>25 / page</option>
          <option value="50" <?= ($per_page == 50) ? 'selected' : '' ?>>50 / page</option>
        </select>

        <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-filter"></i></button>
        <?php if (!empty($search) || !empty($status_filter) || $per_page != 10): ?>
          <a href="<?= url('/admin/gallery.php') ?>" class="btn btn-sm btn-outline-danger">Reset</a>
        <?php endif; ?>
      </form>
    </div>

    <div class="admin-table-card">
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Preview</th>
              <th>Title</th>
              <th>Date</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($gallery_items)): ?>
              <?php foreach ($gallery_items as $gi): ?>
                <tr>
                  <td style="width:50px;">
                    <img src="<?= get_upload_image($gi['image_url'], 'gallery') ?>" alt="" style="width:40px; height:34px; object-fit:cover; border-radius:4px; border:1px solid var(--border-color); display:block;" onerror="this.onerror=null; this.src='<?= url('/assets/images/logo.png') ?>';">
                  </td>
                  <td>
                    <strong><?= htmlspecialchars($gi['image_title']) ?></strong>
                    <?php if ($gi['file_type'] === 'slider'): ?>
                      <span class="badge badge-primary" style="margin-left:0.5rem; font-size:0.7rem;"><i class="fa-solid fa-desktop"></i> Slider</span>
                    <?php endif; ?>
                    <?php if (!empty($gi['image_description'])): ?>
                      <div style="font-size:0.75rem; color:var(--text-muted);"><?= htmlspecialchars(substr($gi['image_description'], 0, 60)) ?></div>
                    <?php endif; ?>
                  </td>
                  <td><small><?= format_display_date($gi['image_date']) ?></small></td>
                  <td><span class="badge badge-active"><?= htmlspecialchars($gi['status']) ?></span></td>
                  <td style="white-space:nowrap;">
                    <a href="<?= url('/admin/gallery.php?action=toggle_slider&id=' . $gi['id']) ?>" class="btn btn-sm <?= $gi['file_type'] === 'slider' ? 'btn-primary' : 'btn-outline-primary' ?>" style="padding:0.2rem 0.45rem;" title="Toggle Slider">
                      <i class="fa-solid fa-desktop"></i>
                    </a>
                    <a href="<?= url('/admin/gallery.php?action=delete&id=' . $gi['id']) ?>" onclick="return confirmDelete()" class="btn btn-sm btn-outline-danger" style="padding:0.2rem 0.45rem;" title="Delete">
                      <i class="fa-solid fa-trash"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5" style="text-align:center; padding:1.5rem; color:var(--text-muted);">No gallery items found matching your criteria.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php render_pagination($pagination, url('/admin/gallery.php')); ?>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
