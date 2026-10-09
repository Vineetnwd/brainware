<?php
$page_title = "Center Dashboard";
$active_menu = 'dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

$center_student_count = 0;
$center_results_count = 0;
$center_pass_count = 0;
$center_ongoing_count = 0;
$center_course_count = 0;
$course_stats = [];
$grade_stats = [];
$recent_students = [];
$all_centers_list = [];

if ($pdo && $center_id > 0) {
    try {
        // If admin is viewing, fetch list of all centers for switching
        if (is_admin()) {
            $all_centers_list = $pdo->query("SELECT id, center_code, center_name FROM center_details WHERE status NOT IN ('removed', 'EMPTY') ORDER BY center_code ASC")->fetchAll();
        }

        // 1. Total Student Admissions for this center
        $stmt1 = $pdo->prepare("SELECT COUNT(*) FROM student WHERE center_id = ? AND status NOT IN ('removed', 'EMPTY')");
        $stmt1->execute([$center_id]);
        $center_student_count = (int)$stmt1->fetchColumn();

        // 2. Total Results / Marksheets for this center
        $stmt2 = $pdo->prepare("
            SELECT COUNT(DISTINCT r.id) FROM result r 
            JOIN student s ON r.student_id = s.id 
            WHERE s.center_id = ? AND r.status NOT IN ('removed', 'EMPTY')
        ");
        $stmt2->execute([$center_id]);
        $center_results_count = (int)$stmt2->fetchColumn();

        // 3. Passed / Certified Students for this center
        $stmt3 = $pdo->prepare("
            SELECT COUNT(*) FROM student 
            WHERE center_id = ? AND status NOT IN ('removed', 'EMPTY') 
            AND (LOWER(comment) LIKE '%pass%' OR (finalpercentage > 0 AND grade != ''))
        ");
        $stmt3->execute([$center_id]);
        $center_pass_count = (int)$stmt3->fetchColumn();

        // 4. Ongoing / Continuing Students in this center
        $center_ongoing_count = max(0, $center_student_count - $center_pass_count);

        // 5. Total active courses with students in this center
        $stmt5 = $pdo->prepare("
            SELECT COUNT(DISTINCT course_id) FROM student 
            WHERE center_id = ? AND status NOT IN ('removed', 'EMPTY')
        ");
        $stmt5->execute([$center_id]);
        $center_course_count = (int)$stmt5->fetchColumn();

        // 6. Course-wise Student Distribution for this Center
        $cw_stmt = $pdo->prepare("
            SELECT 
                COALESCE(co.id, 0) AS course_id,
                COALESCE(co.course_code, 'UNASSIGNED') AS course_code,
                COALESCE(co.course_name, 'Unassigned Course') AS course_name,
                COALESCE(co.course_duration, '—') AS course_duration,
                COUNT(s.id) AS total_students,
                SUM(CASE WHEN LOWER(s.comment) LIKE '%pass%' OR (s.finalpercentage > 0 AND s.grade != '') THEN 1 ELSE 0 END) AS passed_count,
                SUM(CASE WHEN (s.comment = '' OR s.comment IS NULL OR LOWER(s.comment) NOT LIKE '%pass%') AND (s.finalpercentage = 0 OR s.grade = '') THEN 1 ELSE 0 END) AS ongoing_count
            FROM student s 
            LEFT JOIN course_details co ON s.course_id = co.id 
            WHERE s.center_id = ? AND s.status NOT IN ('removed', 'EMPTY')
            GROUP BY co.id, co.course_code, co.course_name, co.course_duration
            ORDER BY total_students DESC
        ");
        $cw_stmt->execute([$center_id]);
        $course_stats = $cw_stmt->fetchAll();

        // 7. Grade Performance Overview for this Center
        $gr_stmt = $pdo->prepare("
            SELECT 
                CASE 
                    WHEN grade IN ('A+', 'A', 'B+', 'B', 'C', 'D') THEN grade 
                    WHEN grade != '' THEN grade 
                    ELSE 'Under Evaluation' 
                END AS grade_label,
                COUNT(*) AS total
            FROM student
            WHERE center_id = ? AND status NOT IN ('removed', 'EMPTY')
            GROUP BY grade_label
            ORDER BY total DESC
        ");
        $gr_stmt->execute([$center_id]);
        $grade_stats = $gr_stmt->fetchAll();

        // 8. Recent admissions under this center
        $s_stmt = $pdo->prepare("
            SELECT s.*, co.course_code, co.course_name 
            FROM student s 
            LEFT JOIN course_details co ON s.course_id = co.id 
            WHERE s.center_id = ? AND s.status NOT IN ('removed', 'EMPTY')
            ORDER BY s.id DESC LIMIT 8
        ");
        $s_stmt->execute([$center_id]);
        $recent_students = $s_stmt->fetchAll();
    } catch (PDOException $e) {}
}
?>

<!-- Center Banner Info -->
<div class="center-banner" style="margin-bottom:1.5rem; background:linear-gradient(135deg, #0b1f48 0%, #1e3a8a 100%); border-radius:12px; padding:1.4rem 1.75rem; color:#fff; box-shadow:0 4px 15px rgba(11,31,72,0.15);">
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1.25rem;">
    <div>
      <div style="display:flex; align-items:center; gap:0.6rem; margin-bottom:0.4rem; flex-wrap:wrap;">
        <span class="badge badge-danger" style="background:#dc2626; color:#fff; font-size:0.75rem; padding:0.25rem 0.65rem; border-radius:4px; font-weight:700;">
          <i class="fa-solid fa-circle-check"></i> Authorized Center Portal
        </span>
        <span style="background:rgba(255,255,255,0.12); color:#93c5fd; font-size:0.75rem; padding:0.25rem 0.65rem; border-radius:4px; font-weight:600;">
          Center Code: <strong><?= htmlspecialchars($active_center['center_code'] ?? 'BR/CPR/01') ?></strong>
        </span>
      </div>

      <h1 style="font-size:1.6rem; font-weight:800; margin-bottom:0.35rem; color:#ffffff; letter-spacing:-0.02em;">
        <?= htmlspecialchars($active_center['center_name'] ?? 'Authorized Study Center') ?>
      </h1>

      <p style="color:#cbd5e1; font-size:0.85rem; margin:0; line-height:1.4;">
        <i class="fa-solid fa-user-tie" style="color:#60a5fa;"></i> Director: <strong><?= htmlspecialchars($active_center['center_director'] ?? 'N/A') ?></strong> 
        <?php if (!empty($active_center['center_mobile'])): ?>
          • <i class="fa-solid fa-phone" style="color:#60a5fa;"></i> <?= htmlspecialchars($active_center['center_mobile']) ?>
        <?php endif; ?>
        <?php if (!empty($active_center['center_address'])): ?>
          • <i class="fa-solid fa-location-dot" style="color:#60a5fa;"></i> <?= htmlspecialchars($active_center['center_address']) ?>
        <?php endif; ?>
      </p>
    </div>

    <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
      <?php if (is_admin() && !empty($all_centers_list)): ?>
        <div style="background:rgba(255,255,255,0.15); padding:0.35rem 0.65rem; border-radius:8px; display:flex; align-items:center; gap:0.5rem;">
          <label style="font-size:0.78rem; font-weight:600; color:#e2e8f0; margin:0; white-space:nowrap;">
            <i class="fa-solid fa-building-columns"></i> Switch Center:
          </label>
          <select class="form-select form-select-sm" style="background:#fff; color:#0f172a; font-weight:600; font-size:0.82rem; border-radius:6px; padding:0.25rem 0.6rem; cursor:pointer;" onchange="window.location.href='<?= url('/center/index.php?view_center_id=') ?>' + this.value">
            <?php foreach ($all_centers_list as $c_opt): ?>
              <option value="<?= $c_opt['id'] ?>" <?= ($center_id == $c_opt['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c_opt['center_code']) ?> - <?= htmlspecialchars($c_opt['center_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php endif; ?>

      <a href="<?= url('/center/student-add.php') ?>" class="btn btn-danger" style="font-weight:700; padding:0.55rem 1.15rem; border-radius:6px; display:inline-flex; align-items:center; gap:0.4rem; box-shadow:0 3px 10px rgba(220,38,38,0.35); text-decoration:none;">
        <i class="fa-solid fa-user-plus"></i> New Student Admission
      </a>
    </div>
  </div>
</div>

<!-- Center-Wise KPI Summary Statistics -->
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap:1.15rem; margin-bottom:1.5rem;">
  
  <!-- Total Center Admissions -->
  <div class="card" style="border:1px solid #e2e8f0; border-left:4px solid var(--primary-blue); border-radius:10px; box-shadow:0 2px 8px rgba(15,23,42,0.04); background:#fff; margin-bottom:0;">
    <div class="card-body" style="padding:1.15rem 1.25rem; display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.4px;">Total Admissions</div>
        <div style="font-size:1.85rem; font-weight:800; color:var(--primary-blue); line-height:1.2; margin-top:0.2rem;"><?= number_format($center_student_count) ?></div>
        <div style="font-size:0.74rem; color:#94a3b8; margin-top:0.15rem;">In <?= htmlspecialchars($active_center['center_code'] ?? 'Center') ?></div>
      </div>
      <div style="width:48px; height:48px; border-radius:10px; background:#eff6ff; color:var(--primary-blue); display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
        <i class="fa-solid fa-user-graduate"></i>
      </div>
    </div>
  </div>

  <!-- Active / Continuing Students -->
  <div class="card" style="border:1px solid #e2e8f0; border-left:4px solid #d97706; border-radius:10px; box-shadow:0 2px 8px rgba(15,23,42,0.04); background:#fff; margin-bottom:0;">
    <div class="card-body" style="padding:1.15rem 1.25rem; display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.4px;">Ongoing Students</div>
        <div style="font-size:1.85rem; font-weight:800; color:#d97706; line-height:1.2; margin-top:0.2rem;"><?= number_format($center_ongoing_count) ?></div>
        <div style="font-size:0.74rem; color:#94a3b8; margin-top:0.15rem;">Currently enrolled</div>
      </div>
      <div style="width:48px; height:48px; border-radius:10px; background:#fef3c7; color:#d97706; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
        <i class="fa-solid fa-user-clock"></i>
      </div>
    </div>
  </div>

  <!-- Passed / Certified Students -->
  <div class="card" style="border:1px solid #e2e8f0; border-left:4px solid #16a34a; border-radius:10px; box-shadow:0 2px 8px rgba(15,23,42,0.04); background:#fff; margin-bottom:0;">
    <div class="card-body" style="padding:1.15rem 1.25rem; display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.4px;">Certified / Passed</div>
        <div style="font-size:1.85rem; font-weight:800; color:#16a34a; line-height:1.2; margin-top:0.2rem;"><?= number_format($center_pass_count) ?></div>
        <div style="font-size:0.74rem; color:#94a3b8; margin-top:0.15rem;">Completed studies</div>
      </div>
      <div style="width:48px; height:48px; border-radius:10px; background:#f0fdf4; color:#16a34a; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
        <i class="fa-solid fa-award"></i>
      </div>
    </div>
  </div>

  <!-- Marksheets Issued -->
  <div class="card" style="border:1px solid #e2e8f0; border-left:4px solid var(--primary-red); border-radius:10px; box-shadow:0 2px 8px rgba(15,23,42,0.04); background:#fff; margin-bottom:0;">
    <div class="card-body" style="padding:1.15rem 1.25rem; display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.4px;">Results Issued</div>
        <div style="font-size:1.85rem; font-weight:800; color:var(--primary-red); line-height:1.2; margin-top:0.2rem;"><?= number_format($center_results_count) ?></div>
        <div style="font-size:0.74rem; color:#94a3b8; margin-top:0.15rem;">Verified marksheets</div>
      </div>
      <div style="width:48px; height:48px; border-radius:10px; background:#fef2f2; color:var(--primary-red); display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
        <i class="fa-solid fa-file-invoice"></i>
      </div>
    </div>
  </div>

  <!-- Active Center Courses -->
  <div class="card" style="border:1px solid #e2e8f0; border-left:4px solid #7c3aed; border-radius:10px; box-shadow:0 2px 8px rgba(15,23,42,0.04); background:#fff; margin-bottom:0;">
    <div class="card-body" style="padding:1.15rem 1.25rem; display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.4px;">Active Courses</div>
        <div style="font-size:1.85rem; font-weight:800; color:#7c3aed; line-height:1.2; margin-top:0.2rem;"><?= number_format($center_course_count) ?></div>
        <div style="font-size:0.74rem; color:#94a3b8; margin-top:0.15rem;">Under this branch</div>
      </div>
      <div style="width:48px; height:48px; border-radius:10px; background:#f5f3ff; color:#7c3aed; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
        <i class="fa-solid fa-book-open"></i>
      </div>
    </div>
  </div>

</div>

<!-- Center-Wise Course Breakdown & Grade Performance -->
<div style="display:grid; grid-template-columns: 2fr 1fr; gap:1.35rem; margin-bottom:1.5rem;">
  
  <!-- Left: Course-wise Student Distribution (Center Wise) -->
  <div class="admin-table-card" style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; box-shadow:0 2px 8px rgba(15,23,42,0.04); overflow:hidden;">
    <div style="padding:1.1rem 1.35rem; background:#ffffff; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
      <h3 style="margin:0; font-size:1.02rem; font-weight:800; color:#1e3a8a; display:flex; align-items:center; gap:0.5rem;">
        <i class="fa-solid fa-chart-column" style="color:var(--primary-blue);"></i> Course-Wise Student Statistics (<?= htmlspecialchars($active_center['center_code'] ?? 'Center') ?>)
      </h3>
      <span class="badge badge-primary" style="font-size:0.75rem; font-weight:700; padding:0.25rem 0.6rem;">
        <?= count($course_stats) ?> Courses Active
      </span>
    </div>
    
    <div class="table-responsive">
      <table class="admin-table" style="width:100%; border-collapse:collapse;">
        <thead>
          <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; font-size:0.76rem; text-transform:uppercase; color:#64748b;">
            <th style="padding:0.75rem 1rem;">Course</th>
            <th style="padding:0.75rem 1rem; text-align:center;">Duration</th>
            <th style="padding:0.75rem 1rem; text-align:center;">Total Students</th>
            <th style="padding:0.75rem 1rem; text-align:center;">Ongoing</th>
            <th style="padding:0.75rem 1rem; text-align:center;">Passed</th>
            <th style="padding:0.75rem 1rem; width:130px;">Center Share</th>
            <th style="padding:0.75rem 1rem; text-align:right;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($course_stats)): ?>
            <?php foreach ($course_stats as $cs): ?>
              <?php 
                $share_pct = ($center_student_count > 0) ? round(($cs['total_students'] / $center_student_count) * 100, 1) : 0;
              ?>
              <tr style="border-bottom:1px solid #f1f5f9; font-size:0.85rem;">
                <td style="padding:0.75rem 1rem;">
                  <strong style="color:#0f172a;"><?= htmlspecialchars($cs['course_code']) ?></strong>
                  <div style="font-size:0.76rem; color:#64748b;"><?= htmlspecialchars($cs['course_name']) ?></div>
                </td>
                <td style="padding:0.75rem 1rem; text-align:center;">
                  <span class="badge badge-secondary" style="font-size:0.75rem;"><?= htmlspecialchars($cs['course_duration']) ?></span>
                </td>
                <td style="padding:0.75rem 1rem; text-align:center;">
                  <strong style="color:var(--primary-blue); font-size:0.95rem;"><?= (int)$cs['total_students'] ?></strong>
                </td>
                <td style="padding:0.75rem 1rem; text-align:center;">
                  <span style="color:#d97706; font-weight:700;"><?= (int)$cs['ongoing_count'] ?></span>
                </td>
                <td style="padding:0.75rem 1rem; text-align:center;">
                  <span style="color:#16a34a; font-weight:700;"><?= (int)$cs['passed_count'] ?></span>
                </td>
                <td style="padding:0.75rem 1rem;">
                  <div style="display:flex; align-items:center; gap:0.45rem;">
                    <div style="flex:1; height:6px; background:#e2e8f0; border-radius:3px; overflow:hidden;">
                      <div style="width:<?= $share_pct ?>%; height:100%; background:var(--primary-blue); border-radius:3px;"></div>
                    </div>
                    <span style="font-size:0.72rem; color:#64748b; font-weight:600; min-width:32px; text-align:right;"><?= $share_pct ?>%</span>
                  </div>
                </td>
                <td style="padding:0.75rem 1rem; text-align:right;">
                  <a href="<?= url('/center/students.php?course_id=' . $cs['course_id']) ?>" class="btn btn-sm btn-outline-primary" style="padding:0.25rem 0.55rem; font-size:0.78rem; font-weight:600;" title="View Enrolled Students">
                    <i class="fa-solid fa-users"></i> View
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" style="text-align:center; padding:1.75rem; color:#94a3b8; font-size:0.85rem;">
                No course enrollment data found for this center yet.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Right: Center Performance & Quick Branch Summary -->
  <div style="display:flex; flex-direction:column; gap:1.25rem;">
    
    <!-- Grade Distribution Card -->
    <div class="card" style="border:1px solid #e2e8f0; border-radius:10px; box-shadow:0 2px 8px rgba(15,23,42,0.04); background:#fff; margin-bottom:0; overflow:hidden;">
      <div style="padding:0.9rem 1.15rem; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
        <h4 style="margin:0; font-size:0.92rem; font-weight:800; color:#1e3a8a; display:flex; align-items:center; gap:0.45rem;">
          <i class="fa-solid fa-graduation-cap" style="color:var(--primary-red);"></i> Academic Performance
        </h4>
      </div>
      <div style="padding:1.15rem;">
        <?php if (!empty($grade_stats)): ?>
          <div style="display:flex; flex-direction:column; gap:0.65rem;">
            <?php foreach ($grade_stats as $gr): ?>
              <?php 
                $gr_pct = ($center_student_count > 0) ? round(($gr['total'] / $center_student_count) * 100, 1) : 0;
                $badge_color = ($gr['grade_label'] === 'A+' || $gr['grade_label'] === 'A') ? '#16a34a' : (($gr['grade_label'] === 'B+' || $gr['grade_label'] === 'B') ? '#2563eb' : '#64748b');
              ?>
              <div>
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.82rem; margin-bottom:0.25rem;">
                  <span style="font-weight:700; color:#334155;">
                    Grade <?= htmlspecialchars($gr['grade_label']) ?>
                  </span>
                  <span style="font-weight:700; color:<?= $badge_color ?>;">
                    <?= (int)$gr['total'] ?> Students (<?= $gr_pct ?>%)
                  </span>
                </div>
                <div style="height:6px; background:#f1f5f9; border-radius:3px; overflow:hidden;">
                  <div style="width:<?= $gr_pct ?>%; height:100%; background:<?= $badge_color ?>; border-radius:3px;"></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p style="text-align:center; color:#94a3b8; font-size:0.82rem; margin:0.5rem 0;">No student performance records recorded yet.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Center Profile Quick Info Box -->
    <div class="card" style="border:1px solid #e2e8f0; border-radius:10px; box-shadow:0 2px 8px rgba(15,23,42,0.04); background:#fff; margin-bottom:0; overflow:hidden;">
      <div style="padding:0.9rem 1.15rem; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
        <h4 style="margin:0; font-size:0.92rem; font-weight:800; color:#1e3a8a; display:flex; align-items:center; gap:0.45rem;">
          <i class="fa-solid fa-id-badge" style="color:var(--primary-blue);"></i> Center Branch Details
        </h4>
      </div>
      <div style="padding:1rem 1.15rem; font-size:0.83rem; display:flex; flex-direction:column; gap:0.55rem;">
        <div style="display:flex; justify-content:space-between; border-bottom:1px dashed #f1f5f9; padding-bottom:0.4rem;">
          <span style="color:#64748b;">Center Code:</span>
          <strong style="color:var(--primary-red);"><?= htmlspecialchars($active_center['center_code'] ?? 'N/A') ?></strong>
        </div>
        <div style="display:flex; justify-content:space-between; border-bottom:1px dashed #f1f5f9; padding-bottom:0.4rem;">
          <span style="color:#64748b;">Center Status:</span>
          <span class="badge badge-active" style="padding:0.15rem 0.5rem; font-size:0.72rem;">ACTIVE</span>
        </div>
        <div style="display:flex; justify-content:space-between; border-bottom:1px dashed #f1f5f9; padding-bottom:0.4rem;">
          <span style="color:#64748b;">Established Date:</span>
          <strong><?= !empty($active_center['center_dos']) ? date('d M Y', strtotime($active_center['center_dos'])) : 'N/A' ?></strong>
        </div>
        <div style="display:flex; justify-content:space-between; padding-bottom:0.2rem;">
          <span style="color:#64748b;">Director:</span>
          <strong><?= htmlspecialchars($active_center['center_director'] ?? 'N/A') ?></strong>
        </div>
        <div style="margin-top:0.4rem;">
          <a href="<?= url('/center/profile.php') ?>" class="btn btn-sm btn-outline-primary" style="width:100%; text-align:center; font-weight:600; font-size:0.8rem; padding:0.35rem 0;">
            <i class="fa-solid fa-gear"></i> Manage Center Profile
          </a>
        </div>
      </div>
    </div>

  </div>

</div>

<!-- Recent Student Admissions Table (Center-Wise) -->
<div class="admin-table-card" style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; box-shadow:0 2px 8px rgba(15,23,42,0.04); overflow:hidden;">
  <div style="padding:1.15rem 1.5rem; background:#ffffff; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem;">
    <h3 style="margin:0; font-size:1.05rem; font-weight:800; color:#1e3a8a; display:flex; align-items:center; gap:0.5rem;">
      <i class="fa-solid fa-users" style="color:var(--primary-blue);"></i> Recent Admissions (<?= htmlspecialchars($active_center['center_code'] ?? 'Center') ?>)
    </h3>
    <a href="<?= url('/center/students.php') ?>" class="btn btn-sm btn-outline-primary" style="font-weight:600; font-size:0.82rem; padding:0.35rem 0.85rem;">
      <i class="fa-solid fa-list"></i> View All Center Students
    </a>
  </div>
  <div class="table-responsive">
    <table class="admin-table" style="width:100%; border-collapse:collapse;">
      <thead>
        <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; font-size:0.76rem; text-transform:uppercase; color:#64748b;">
          <th style="padding:0.75rem 1rem;">Reg No</th>
          <th style="padding:0.75rem 1rem;">Student Name</th>
          <th style="padding:0.75rem 1rem;">Course</th>
          <th style="padding:0.75rem 1rem;">Roll No</th>
          <th style="padding:0.75rem 1rem;">Certificate No</th>
          <th style="padding:0.75rem 1rem;">Grade / %</th>
          <th style="padding:0.75rem 1rem; text-align:center; width:80px;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($recent_students)): ?>
          <?php foreach ($recent_students as $st): ?>
            <tr style="border-bottom:1px solid #f1f5f9; font-size:0.85rem;">
              <td style="padding:0.75rem 1rem;">
                <strong style="color:var(--primary-red);"><?= htmlspecialchars($st['registration_no']) ?></strong>
              </td>
              <td style="padding:0.75rem 1rem;">
                <strong style="color:#0f172a;"><?= htmlspecialchars($st['student_name']) ?></strong>
              </td>
              <td style="padding:0.75rem 1rem;">
                <span class="badge badge-primary" style="font-size:0.75rem;"><?= htmlspecialchars($st['course_code']) ?></span>
              </td>
              <td style="padding:0.75rem 1rem; color:#475569;"><?= htmlspecialchars($st['student_roll']) ?></td>
              <td style="padding:0.75rem 1rem;">
                <strong><?= htmlspecialchars($st['certificate_no'] ?: '—') ?></strong>
              </td>
              <td style="padding:0.75rem 1rem;">
                <?php if (!empty($st['grade']) || (float)$st['finalpercentage'] > 0): ?>
                  <span class="badge badge-active" style="font-size:0.75rem;">
                    <?= htmlspecialchars($st['grade'] ?: 'A') ?> (<?= number_format((float)$st['finalpercentage'], 1) ?>%)
                  </span>
                <?php else: ?>
                  <span class="badge badge-secondary" style="font-size:0.75rem;">Enrolled</span>
                <?php endif; ?>
              </td>
              <td style="padding:0.75rem 1rem; text-align:center;">
                <a href="<?= url('/center/student-view.php?id=' . $st['id']) ?>" class="btn btn-sm btn-outline-primary" style="padding:0.25rem 0.55rem; font-size:0.8rem;" title="View Student Admission Card">
                  <i class="fa-solid fa-eye"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" style="text-align:center; padding:1.75rem; color:#94a3b8; font-size:0.85rem;">
              No student admissions in this center yet.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
