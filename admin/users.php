<?php
$page_title = "User Accounts & Credentials";
$active_menu = 'users';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

// Safe Database Deduplication: clean any duplicate user accounts in DB
if ($pdo) {
    try {
        // 1. Clean accidental leading/trailing whitespace
        $pdo->query("UPDATE user SET user_name = TRIM(user_name) WHERE user_name != TRIM(user_name)");

        // 2. Identify duplicate usernames
        $dup_query = $pdo->query("
            SELECT LOWER(TRIM(user_name)) AS uname, COUNT(*) AS cnt 
            FROM user 
            GROUP BY LOWER(TRIM(user_name)) 
            HAVING cnt > 1
        ");
        $duplicates = $dup_query->fetchAll();

        foreach ($duplicates as $dup) {
            $uname = $dup['uname'];
            // Prioritize rows with my_api key, valid updated_at, or highest id
            $d_stmt = $pdo->prepare("
                SELECT id, user_password, my_api, updated_at 
                FROM user 
                WHERE LOWER(TRIM(user_name)) = ? 
                ORDER BY (CASE WHEN my_api IS NOT NULL AND my_api != '' THEN 1 ELSE 0 END) DESC,
                         (CASE WHEN updated_at IS NOT NULL AND updated_at != '0000-00-00 00:00:00' THEN 1 ELSE 0 END) DESC,
                         id DESC
            ");
            $d_stmt->execute([$uname]);
            $all_rows = $d_stmt->fetchAll();

            if (count($all_rows) > 1) {
                $keep_id = (int)$all_rows[0]['id'];
                $delete_ids = [];
                for ($i = 1; $i < count($all_rows); $i++) {
                    $delete_ids[] = (int)$all_rows[$i]['id'];
                }
                if (!empty($delete_ids)) {
                    $del_sql = "DELETE FROM user WHERE id IN (" . implode(',', $delete_ids) . ")";
                    $pdo->query($del_sql);
                }
            }
        }
    } catch (Exception $e) {
        // Continue safely if already clean
    }
}

// Handle Delete User
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $del_id = (int)$_GET['id'];
    if ($del_id != ($admin_user['id'] ?? 0) && $pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM user WHERE id = ?");
            $stmt->execute([$del_id]);
            set_flash('success', 'User account deleted successfully.');
        } catch (PDOException $e) {
            set_flash('danger', 'Failed to delete user.');
        }
    }
    header('Location: ' . url('/admin/users.php'));
    exit;
}

// Handle Change Password
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $uid = (int)($_POST['user_id'] ?? 0);
    $new_pass = clean_input($_POST['new_password'] ?? '');
    
    if ($uid > 0 && !empty($new_pass) && $pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE user SET user_password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$new_pass, $uid]);
            set_flash('success', 'User password updated successfully.');
        } catch (PDOException $e) {
            set_flash('danger', 'Failed to update password.');
        }
    } else {
        set_flash('danger', 'Invalid input for changing password.');
    }
    header('Location: ' . url('/admin/users.php'));
    exit;
}

$search = clean_input($_GET['search'] ?? '');
$role_filter = clean_input($_GET['role'] ?? '');
$center_filter = (int)($_GET['center_id'] ?? 0);
$status_filter = clean_input($_GET['status'] ?? '');
$per_page = (int)($_GET['per_page'] ?? 15);
$page = (int)($_GET['page'] ?? 1);

$users = [];
$centers = [];
$pagination = null;

if ($pdo) {
    try {
        $centers = $pdo->query("SELECT id, center_code, center_name FROM center_details ORDER BY center_code ASC")->fetchAll();

        // Build base condition
        $where = ["1=1"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(u.user_name LIKE :q OR u.user_email LIKE :q OR u.user_mobile LIKE :q OR c.center_code LIKE :q OR c.center_name LIKE :q)";
            $params[':q'] = "%$search%";
        }

        if (!empty($role_filter)) {
            $where[] = "u.user_type = :role";
            $params[':role'] = $role_filter;
        }

        if ($center_filter > 0) {
            $where[] = "u.center_id = :cid";
            $params[':cid'] = $center_filter;
        }

        if (!empty($status_filter)) {
            $where[] = "u.status = :st";
            $params[':st'] = $status_filter;
        }

        $where_sql = implode(" AND ", $where);

        // Deduplicated subquery ensuring each username appears exactly once
        $dedup_join = "
            INNER JOIN (
                SELECT MAX(id) AS latest_id
                FROM user
                GROUP BY LOWER(TRIM(user_name))
            ) latest_u ON u.id = latest_u.latest_id
        ";

        // Count total unique users
        $cnt_stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM user u
            $dedup_join
            LEFT JOIN center_details c ON u.center_id = c.id
            WHERE $where_sql
        ");
        $cnt_stmt->execute($params);
        $total_records = (int)$cnt_stmt->fetchColumn();

        $pagination = paginate($total_records, $page, $per_page);

        // Fetch deduplicated users
        $stmt = $pdo->prepare("
            SELECT u.*, c.center_name, c.center_code
            FROM user u
            $dedup_join
            LEFT JOIN center_details c ON u.center_id = c.id
            WHERE $where_sql
            ORDER BY u.id DESC
            LIMIT :offset, :limit
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':offset', $pagination['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pagination['per_page'], PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll();
    } catch (PDOException $e) {}
}
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-users-gear" style="color:var(--primary-blue);"></i> User Accounts & Credentials</h1>
    <p class="admin-page-subtitle">Manage Admin logins, Study Center portal credentials, passwords, and access control</p>
  </div>
</div>

<!-- Full Width User Accounts Table -->
<div class="admin-table-card">
  <div class="card-header" style="flex-wrap:wrap; gap:0.5rem; display:flex; justify-content:space-between; align-items:center;">
    <h3 class="card-title"><i class="fa-solid fa-list-check"></i> System Users List</h3>
    <span style="font-size:0.82rem; color:var(--text-muted);">
      Showing <strong><?= count($users) ?></strong> of <strong><?= $pagination['total_records'] ?? count($users) ?></strong> unique accounts
    </span>
  </div>

  <!-- Filter Toolbar -->
  <div style="padding:0.75rem 1rem; background:var(--bg-light, #f8fafc); border-bottom:1px solid var(--border-color, #e2e8f0);">
    <form action="<?= url('/admin/users.php') ?>" method="GET" style="display:flex; gap:0.6rem; flex-wrap:wrap; align-items:center;">
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-sm" placeholder="Search user, mobile, email..." style="max-width:220px;">
      
      <select name="role" class="form-select form-control-sm" style="max-width:130px;">
        <option value="">All Roles</option>
        <option value="ADMIN" <?= ($role_filter === 'ADMIN') ? 'selected' : '' ?>>ADMIN</option>
        <option value="CLIENT" <?= ($role_filter === 'CLIENT') ? 'selected' : '' ?>>CLIENT</option>
      </select>

      <select name="center_id" class="form-select form-control-sm" style="max-width:180px;">
        <option value="0">All Centers</option>
        <?php foreach ($centers as $c): ?>
          <option value="<?= $c['id'] ?>" <?= ($center_filter == $c['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['center_code']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select name="status" class="form-select form-control-sm" style="max-width:130px;">
        <option value="">All Status</option>
        <option value="ACTIVE" <?= ($status_filter === 'ACTIVE') ? 'selected' : '' ?>>ACTIVE</option>
        <option value="INACTIVE" <?= ($status_filter === 'INACTIVE') ? 'selected' : '' ?>>INACTIVE</option>
      </select>

      <select name="per_page" class="form-select form-control-sm" style="max-width:95px;" onchange="this.form.submit()">
        <?php foreach ([10, 15, 25, 50, 100] as $opt): ?>
          <option value="<?= $opt ?>" <?= ($per_page == $opt) ? 'selected' : '' ?>><?= $opt ?>/pg</option>
        <?php endforeach; ?>
      </select>

      <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-filter"></i> Apply</button>
      <?php if (!empty($search) || !empty($role_filter) || $center_filter > 0 || !empty($status_filter)): ?>
        <a href="<?= url('/admin/users.php') ?>" class="btn btn-sm btn-outline-danger" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i> Reset</a>
      <?php endif; ?>
    </form>
  </div>

  <div class="table-responsive">
    <table class="admin-table" style="width:100%;">
      <thead>
        <tr>
          <th>Username</th>
          <th>Role</th>
          <th>Linked Center</th>
          <th style="color:var(--primary-blue);"><i class="fa-solid fa-key" style="margin-right:0.25rem;"></i> Password</th>
          <th>Contact</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($users)): ?>
          <?php foreach ($users as $u): ?>
            <tr>
              <td><strong><?= htmlspecialchars($u['user_name']) ?></strong></td>
              <td>
                <span class="badge <?= (strtoupper($u['user_type']) === 'ADMIN') ? 'badge-danger' : 'badge-primary' ?>">
                  <?= htmlspecialchars($u['user_type']) ?>
                </span>
              </td>
              <td><small><?= htmlspecialchars($u['center_code'] ? $u['center_code'].' - '.$u['center_name'] : 'Head Office') ?></small></td>
              <td>
                <!-- Password Display with 1-click Copy -->
                <div style="display:inline-flex; align-items:center; gap:0.45rem; background:#f1f5f9; padding:0.25rem 0.6rem; border-radius:6px; border:1px solid #e2e8f0;">
                  <code style="font-size:0.88rem; font-weight:700; color:#1e293b; font-family:'Courier New', monospace; letter-spacing:0.5px;"><?= htmlspecialchars($u['user_password']) ?></code>
                  <button type="button" onclick="copyPassword('<?= htmlspecialchars(addslashes($u['user_password'])) ?>', this)" title="Copy password" style="background:none; border:none; cursor:pointer; color:#64748b; padding:0; font-size:0.82rem;">
                    <i class="fa-regular fa-copy"></i>
                  </button>
                </div>
              </td>
              <td><small><?= htmlspecialchars($u['user_mobile'] ?: '—') ?></small></td>
              <td>
                <span class="badge <?= ($u['status'] === 'ACTIVE') ? 'badge-active' : 'badge-pending' ?>">
                  <?= htmlspecialchars($u['status']) ?>
                </span>
              </td>
              <td>
                <?php if ($u['id'] != ($admin_user['id'] ?? 0)): ?>
                  <button type="button" onclick="promptChangePassword(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['user_name'])) ?>')" class="btn btn-sm btn-outline-primary" style="padding:0.2rem 0.45rem;" title="Change Password">
                    <i class="fa-solid fa-key"></i>
                  </button>
                  <a href="<?= url('/admin/users.php?action=delete&id=' . $u['id']) ?>" onclick="return confirmDelete('Are you sure you want to delete user account <?= htmlspecialchars(addslashes($u['user_name'])) ?>?')" class="btn btn-sm btn-outline-danger" style="padding:0.2rem 0.45rem;" title="Delete User">
                    <i class="fa-solid fa-trash"></i>
                  </a>
                <?php else: ?>
                  <span style="font-size:0.75rem; color:var(--text-muted); font-weight:600;">Current</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" style="text-align:center; padding:1.5rem; color:var(--text-muted);">No user accounts match the criteria.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?= render_pagination($pagination, url('/admin/users.php')) ?>
</div>

<!-- Password Change Modal Overlay -->
<div class="marks-modal-overlay" id="passwordModalOverlay" onclick="closePasswordModalOnBackdrop(event)">
  <div class="marks-modal-dialog" onclick="event.stopPropagation()" style="max-width:400px; padding-bottom:0;">
    <div class="marks-modal-header" style="padding:1rem 1.5rem; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
      <h3 class="marks-modal-title" style="margin:0; font-size:1.1rem; color:#1e293b;"><i class="fa-solid fa-key" style="color:var(--primary-blue);"></i> Change Password</h3>
      <button type="button" class="marks-modal-close" onclick="closePasswordModal()" aria-label="Close" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#64748b;">&times;</button>
    </div>
    
    <form id="passwordModalForm" method="POST" action="<?= url('/admin/users.php') ?>">
      <input type="hidden" name="action" value="change_password">
      <input type="hidden" name="user_id" id="passwordModalUserId" value="">
      
      <div class="marks-modal-body" style="padding:1.5rem;">
        <p style="margin-top:0; margin-bottom:1rem; font-size:0.9rem; color:#64748b;">
          Set a new password for <strong id="passwordModalUserName" style="color:#0f172a;"></strong>.
        </p>
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.85rem; color:#475569;">New Password <span style="color:#ef4444;">*</span></label>
          <input type="text" name="new_password" id="passwordModalInput" class="form-control" required placeholder="Enter new password" style="width:100%; padding:0.6rem; border:1px solid #cbd5e1; border-radius:6px;">
        </div>
      </div>
      
      <div class="marks-modal-footer" style="padding:1rem 1.5rem; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:0.75rem; border-radius:0 0 10px 10px;">
        <button type="button" class="btn btn-outline-primary" style="padding:0.5rem 1rem; border:1px solid #cbd5e1; background:white; color:#475569; border-radius:6px; cursor:pointer;" onclick="closePasswordModal()">Cancel</button>
        <button type="submit" class="btn btn-primary" style="padding:0.5rem 1rem; background:var(--primary-blue, #2563eb); color:white; border:none; border-radius:6px; cursor:pointer;"><i class="fa-solid fa-save"></i> Save</button>
      </div>
    </form>
  </div>
</div>

<script>
function copyPassword(pwd, btn) {
  navigator.clipboard.writeText(pwd).then(() => {
    const icon = btn.querySelector('i');
    icon.className = 'fa-solid fa-check';
    icon.style.color = '#16a34a';
    setTimeout(() => {
      icon.className = 'fa-regular fa-copy';
      icon.style.color = '#64748b';
    }, 1500);
  });
}

function promptChangePassword(userId, userName) {
  document.getElementById('passwordModalUserId').value = userId;
  document.getElementById('passwordModalUserName').textContent = userName;
  document.getElementById('passwordModalInput').value = '';
  document.getElementById('passwordModalOverlay').classList.add('show');
  
  // Focus the input after the transition
  setTimeout(() => {
    document.getElementById('passwordModalInput').focus();
  }, 100);
}

function closePasswordModal() {
  document.getElementById('passwordModalOverlay').classList.remove('show');
}

function closePasswordModalOnBackdrop(e) {
  if (e.target === document.getElementById('passwordModalOverlay')) {
    closePasswordModal();
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
