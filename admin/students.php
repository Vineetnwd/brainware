<?php
// Direct intercept for Marks AJAX requests if dispatched directly to students.php
if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], ['get_student_exams', 'save_marks'])) {
  require_once __DIR__ . '/ajax-marks.php';
  exit;
}

$page_title = "Student Directory";
$active_menu = 'students';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/topbar.php';

// Handle student delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
  $del_id = (int) $_GET['id'];
  if ($pdo) {
    try {
      // Delete associated results first
      $pdo->prepare("DELETE FROM result WHERE student_id = ?")->execute([$del_id]);
      // Delete student
      $stmt = $pdo->prepare("DELETE FROM student WHERE id = ?");
      $stmt->execute([$del_id]);
      set_flash('success', 'Student record and exam results deleted successfully.');
    } catch (PDOException $e) {
      set_flash('danger', 'Failed to delete student record.');
    }
  }
  header('Location: ' . url('/admin/students.php'));
  exit;
}

// Search and filter parameters
$search = clean_input($_GET['search'] ?? '');
$center_filter = (int) ($_GET['center_id'] ?? 0);
$course_filter = (int) ($_GET['course_id'] ?? 0);
$status_filter = clean_input($_GET['status'] ?? '');
$per_page = (int) ($_GET['per_page'] ?? 15);
if (!in_array($per_page, [10, 15, 25, 50, 100])) {
  $per_page = 15;
}
$current_page = max(1, (int) ($_GET['page'] ?? 1));

// Fetch dropdown options
$all_centers = [];
$all_courses = [];
$students = [];
$pagination = paginate(0, 1, $per_page);

if ($pdo) {
  try {
    $all_centers = $pdo->query("SELECT id, center_code, center_name FROM center_details WHERE status NOT IN ('removed', 'EMPTY') ORDER BY center_name ASC")->fetchAll();
    $all_courses = $pdo->query("SELECT id, course_code, course_name FROM course_details WHERE status NOT IN ('removed', 'EMPTY') ORDER BY course_code ASC")->fetchAll();

    // Build base condition
    $whereSql = " WHERE s.status NOT IN ('removed', 'EMPTY')";
    $params = [];

    if (!empty($search)) {
      $terms = array_filter(explode(' ', $search));
      if (!empty($terms)) {
        $termSql = [];
        foreach (array_values($terms) as $idx => $term) {
          $termSql[] = "(s.student_name LIKE :q_{$idx}_1 OR s.registration_no LIKE :q_{$idx}_2 OR s.student_roll LIKE :q_{$idx}_3 OR s.certificate_no LIKE :q_{$idx}_4 OR s.student_mobile LIKE :q_{$idx}_5 OR s.student_father LIKE :q_{$idx}_6)";
          $params[":q_{$idx}_1"] = "%$term%";
          $params[":q_{$idx}_2"] = "%$term%";
          $params[":q_{$idx}_3"] = "%$term%";
          $params[":q_{$idx}_4"] = "%$term%";
          $params[":q_{$idx}_5"] = "%$term%";
          $params[":q_{$idx}_6"] = "%$term%";
        }
        $whereSql .= " AND (" . implode(' OR ', $termSql) . ")";
      }
    }

    if ($center_filter > 0) {
      $whereSql .= " AND s.center_id = :cid";
      $params[':cid'] = $center_filter;
    }

    if ($course_filter > 0) {
      $whereSql .= " AND s.course_id = :coid";
      $params[':coid'] = $course_filter;
    }

    if (!empty($status_filter)) {
      $whereSql .= " AND s.status = :st";
      $params[':st'] = $status_filter;
    }

    // Count total matching records
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM student s $whereSql");
    $countStmt->execute($params);
    $total_records = (int) $countStmt->fetchColumn();

    $pagination = paginate($total_records, $current_page, $per_page);

    // Fetch paginated rows
    $sql = "
            SELECT s.*, c.center_name, c.center_code, co.course_code, co.course_name
            FROM student s
            LEFT JOIN center_details c ON s.center_id = c.id
            LEFT JOIN course_details co ON s.course_id = co.id
            $whereSql
            ORDER BY s.id DESC
            LIMIT {$pagination['offset']}, {$pagination['per_page']}
        ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll();
  } catch (PDOException $e) {
    $students = [];
  }
}
?>

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fa-solid fa-user-graduate" style="color:var(--primary-blue);"></i> Student
      Database</h1>
    <p class="admin-page-subtitle">Master enrollment records, certificates, marksheet access, and verification status
    </p>
  </div>
  <div>
    <a href="<?= url('/admin/student-add.php') ?>" class="btn btn-sm btn-danger">
      <i class="fa-solid fa-user-plus"></i> Register New Student
    </a>
  </div>
</div>

<!-- Filters Toolbar -->
<div class="toolbar-card">
  <form id="filterForm" action="<?= url('/admin/students.php') ?>" method="GET"
    style="display:flex; gap:0.6rem; flex-wrap:wrap; flex:1; align-items:center;">

    <input type="text" id="searchInput" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-sm"
      placeholder="Search Name, Reg, Roll, Cert, Mobile..." style="max-width:240px;">

    <select name="center_id" class="form-select form-control-sm" style="max-width:180px;">
      <option value="0">All Centers</option>
      <?php foreach ($all_centers as $ac): ?>
        <option value="<?= $ac['id'] ?>" <?= ($center_filter == $ac['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($ac['center_code']) ?> - <?= htmlspecialchars($ac['center_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="course_id" class="form-select form-control-sm" style="max-width:160px;">
      <option value="0">All Courses</option>
      <?php foreach ($all_courses as $ac): ?>
        <option value="<?= $ac['id'] ?>" <?= ($course_filter == $ac['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($ac['course_code']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="per_page" class="form-select form-control-sm" style="max-width:110px;" onchange="this.form.submit()">
      <option value="10" <?= ($per_page == 10) ? 'selected' : '' ?>>10 / page</option>
      <option value="15" <?= ($per_page == 15) ? 'selected' : '' ?>>15 / page</option>
      <option value="25" <?= ($per_page == 25) ? 'selected' : '' ?>>25 / page</option>
      <option value="50" <?= ($per_page == 50) ? 'selected' : '' ?>>50 / page</option>
      <option value="100" <?= ($per_page == 100) ? 'selected' : '' ?>>100 / page</option>
    </select>

    <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-filter"></i> Apply</button>

    <?php if (!empty($search) || $center_filter > 0 || $course_filter > 0 || !empty($status_filter) || $per_page != 15): ?>
      <a href="<?= url('/admin/students.php') ?>" class="btn btn-sm btn-outline-danger">Reset</a>
    <?php endif; ?>

  </form>
</div>

<!-- Students Data Table -->
<div class="admin-table-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Reg No</th>
          <th>Student Name</th>
          <th>Father's Name</th>
          <th>Course</th>
          <th>Cert No</th>
          <th>Grade / %</th>
          <th>Issue Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($students)): ?>
          <?php foreach ($students as $st): ?>
            <tr>
              <td>
                <strong style="color:var(--primary-red);"><?= htmlspecialchars($st['registration_no'] ?: '—') ?></strong>
                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                  <?= htmlspecialchars($st['center_name'] ?: 'Center #' . $st['center_id']) ?>
                </div>
              </td>
              <td>
                <div style="display:flex; align-items:center; gap:0.5rem;">
                  <img src="<?= get_upload_image($st['student_photo'], 'student') ?>" alt=""
                    style="width:28px; height:28px; border-radius:50%; object-fit:cover; border:1px solid var(--border-color); flex-shrink:0;">
                  <div>
                    <strong><?= htmlspecialchars($st['student_name']) ?></strong>
                    <?php if (!empty($st['student_mobile'])): ?>
                      <div style="font-size:0.72rem; color:var(--text-muted);"><i class="fa-solid fa-phone"></i>
                        <?= htmlspecialchars($st['student_mobile']) ?></div>
                    <?php endif; ?>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($st['student_father'] ?: 'N/A') ?></td>
              <td><span
                  class="badge badge-primary"><?= htmlspecialchars($st['course_code'] ?: 'Course #' . $st['course_id']) ?></span>
              </td>
              <td><strong style="color:var(--text-muted);"><?= htmlspecialchars($st['certificate_no'] ?: 'Not Issued') ?></strong></td>
              <td>
                <?php if (!empty($st['certificate_no'])): ?>
                  <?php if (!empty($st['grade'])): ?>
                  <span class="badge badge-active"><?= htmlspecialchars($st['grade']) ?></span>
                  <?php endif; ?>
                  <?php if ((float) $st['finalpercentage'] > 0): ?>
                    <span
                      style="font-size:0.75rem; color:var(--text-muted);"><?= number_format((float) $st['finalpercentage'], 1) ?>%</span>
                  <?php endif; ?>
                <?php else: ?>
                  <span style="font-size:0.75rem; color:var(--text-muted);">—</span>
                <?php endif; ?>
              </td>
              <td><small><?= (!empty($st['certificate_no']) && !empty($st['date_of_issue']) && $st['date_of_issue'] != '0000-00-00') ? format_display_date($st['date_of_issue']) : '—' ?></small></td>
              <td>
                <div style="display:flex; gap:0.25rem;">
                  <a href="<?= url('/admin/student-view.php?id=' . $st['id']) ?>" class="btn btn-sm btn-outline-primary"
                    style="padding:0.2rem 0.4rem;" title="View Profile">
                    <i class="fa-solid fa-eye"></i>
                  </a>
                  <a href="<?= url('/admin/student-edit.php?id=' . $st['id']) ?>" class="btn btn-sm btn-outline-primary"
                    style="padding:0.2rem 0.4rem;" title="Edit Student">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                  <button type="button" class="btn btn-sm btn-outline-warning"
                    style="padding:0.2rem 0.45rem; font-weight:700; color:#d97706; border-color:#f59e0b;"
                    onclick="openMarksModal(<?= $st['id'] ?>, '<?= htmlspecialchars(addslashes($st['student_name'])) ?>')"
                    title="Enter / Update Semester Marks">
                    <i class="fa-solid fa-square-poll-vertical"></i> Marks
                  </button>
                  <a href="<?= url('/admin/student-certificate.php?student_id=' . $st['id']) ?>"
                    class="btn btn-sm btn-outline-danger" style="padding:0.2rem 0.45rem; font-weight:700;"
                    title="Certificate Entry & Details">
                    <i class="fa-solid fa-certificate"></i> Certificate
                  </a>
                  <a href="<?= url('/admin/students.php?action=delete&id=' . $st['id']) ?>"
                    onclick="return confirmDelete('Are you sure you want to delete this student and all their exam marks?')"
                    class="btn btn-sm btn-outline-danger" style="padding:0.2rem 0.4rem;" title="Delete">
                    <i class="fa-solid fa-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" style="text-align:center; padding:1.5rem; color:var(--text-muted);">No student records found
              matching the filter criteria.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php 
  $total_pages = $total_records > 0 ? ceil($total_records / $per_page) : 1;
  if ($total_pages > 1): 
    $qs = $_GET;
    unset($qs['page']);
    $qs_str = http_build_query($qs);
    $base_url = url('/admin/students.php') . '?' . ($qs_str ? $qs_str . '&' : '');
  ?>
  <div class="pagination-wrapper" style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
    <ul class="pagination" style="display: flex; list-style: none; padding: 0; gap: 0.35rem; margin: 0;">
      <?php if ($current_page > 1): ?>
        <li>
          <a href="<?= $base_url . 'page=' . ($current_page - 1) ?>" class="btn btn-sm btn-outline-primary" style="padding: 0.3rem 0.6rem;">&laquo; Prev</a>
        </li>
      <?php endif; ?>
      
      <?php 
      $window = 2; // Number of pages to show around current page
      for ($i = 1; $i <= $total_pages; $i++): 
        if ($i == 1 || $i == $total_pages || ($i >= $current_page - $window && $i <= $current_page + $window)):
      ?>
        <li>
          <a href="<?= $base_url . 'page=' . $i ?>" class="btn btn-sm <?= $i == $current_page ? 'btn-primary' : 'btn-outline-primary' ?>" style="padding: 0.3rem 0.6rem; min-width: 32px; text-align: center;">
            <?= $i ?>
          </a>
        </li>
      <?php 
        elseif ($i == $current_page - $window - 1 || $i == $current_page + $window + 1): 
      ?>
        <li>
          <span style="padding: 0.3rem 0.6rem; display: inline-block; color: var(--text-muted);">...</span>
        </li>
      <?php 
        endif;
      endfor; 
      ?>
      
      <?php if ($current_page < $total_pages): ?>
        <li>
          <a href="<?= $base_url . 'page=' . ($current_page + 1) ?>" class="btn btn-sm btn-outline-primary" style="padding: 0.3rem 0.6rem;">Next &raquo;</a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
  <?php endif; ?>
</div>

<!-- ==========================================
     Marks Details Modal Dialog (Exact Image Layout)
     ========================================== -->
<div class="marks-modal-overlay" id="marksModalOverlay" onclick="closeMarksModalOnBackdrop(event)">
  <div class="marks-modal-dialog" onclick="event.stopPropagation()">
    <div class="marks-modal-header">
      <h3 class="marks-modal-title" id="marksModalTitle">
        Marks details Of <span id="modalStudentName"></span>
      </h3>
      <button type="button" class="marks-modal-close" onclick="closeMarksModal()" aria-label="Close">&times;</button>
    </div>

    <div id="marksModalAlert"
      style="display:none; margin: 1.25rem 1.75rem 0; padding: 0.75rem 1rem; border-radius: 6px; font-size: 0.88rem;">
    </div>

    <form id="marksModalForm" onsubmit="submitMarksForm(event)">
      <input type="hidden" name="student_id" id="modalStudentId" value="">

      <div class="marks-modal-body">
        <div class="marks-grid-2col">

          <!-- Column 1 -->
          <div>
            <div class="form-group" style="margin-bottom:1.15rem;">
              <label class="form-label"
                style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
                Select Exam <span class="req" style="color:#dc2626;">*</span>
              </label>
              <select name="exam_name" id="modalExamSelect" class="form-select" onchange="onExamChange(this.value)"
                required>
                <option value="">Select Exam Name</option>
              </select>
            </div>

            <div class="form-group" style="margin-bottom:1.15rem;">
              <label class="form-label"
                style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
                Written
              </label>
              <input type="number" name="written" id="modalWritten" class="form-control" placeholder="" min="0"
                max="100" required>
            </div>

            <div class="form-group" style="margin-bottom:1.15rem;">
              <label class="form-label"
                style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
                Practical / Lab
              </label>
              <input type="number" name="practical" id="modalPractical" class="form-control" placeholder="" min="0"
                max="100" required>
            </div>

            <div class="form-group" style="margin-bottom:1.15rem;">
              <label class="form-label"
                style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
                Assignment / Project
              </label>
              <input type="number" name="assignment" id="modalAssignment" class="form-control" placeholder="" min="0"
                max="100" required>
            </div>
          </div>

          <!-- Column 2 -->
          <div>
            <div class="form-group" style="margin-bottom:1.15rem;">
              <label class="form-label"
                style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
                Viva / Interview
              </label>
              <input type="number" name="viva" id="modalViva" class="form-control" placeholder="" min="0" max="100"
                required>
            </div>

            <div class="form-group" style="margin-bottom:1.15rem;">
              <label class="form-label"
                style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
                Remarks (PASS/ FAIL)
              </label>
              <input type="text" name="remarks" id="modalRemarks" class="form-control" placeholder="e.g. Pass or Fail"
                value="Pass" required>
            </div>

            <div class="form-group" style="margin-bottom:1.15rem;">
              <label class="form-label"
                style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
                Serial Number
              </label>
              <input type="text" name="ms_no" id="modalMsNo" class="form-control" placeholder="Serial Number" required>
            </div>

            <div class="form-group" style="margin-bottom:1.15rem;">
              <label class="form-label"
                style="font-weight:700; font-size:0.88rem; color:#1e293b; margin-bottom:0.35rem;">
                Date Of Certificate Issue
              </label>
              <input type="date" name="date_of_issue" id="modalDateOfIssue" class="form-control"
                value="<?= date('Y-m-d') ?>">
            </div>
          </div>

        </div>
      </div>

      <div class="marks-modal-footer">
        <button type="submit" id="marksSubmitBtn" class="btn-save-marks">
          <i class="fa-solid fa-square-check"></i> SAVE NOW
        </button>
      </div>

    </form>
  </div>
</div>

<script>
  let currentStudentExamsData = null;
  const MARKS_PRIMARY_URL = '<?= url("/admin/ajax-marks.php") ?>';
  const MARKS_FALLBACK_URL = '<?= url("/admin/students.php") ?>';

  async function fetchMarksApi(urlParams, postData = null) {
    const isPost = !!postData;

    // Try primary endpoint first
    const primaryUrl = isPost ? MARKS_PRIMARY_URL : (MARKS_PRIMARY_URL + '?' + urlParams);
    const fallbackUrl = isPost ? MARKS_FALLBACK_URL : (MARKS_FALLBACK_URL + '?' + urlParams);

    async function executeRequest(reqUrl) {
      const opts = isPost ? { method: 'POST', body: postData } : { method: 'GET' };
      const res = await fetch(reqUrl, opts);
      const rawText = await res.text();
      let json;
      try {
        json = JSON.parse(rawText);
      } catch (parseErr) {
        const cleanErr = rawText.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim().substring(0, 140);
        throw new Error(cleanErr || 'Invalid server response (Status ' + res.status + ')');
      }
      return json;
    }

    try {
      return await executeRequest(primaryUrl);
    } catch (err1) {
      console.warn('Primary Marks API failed, trying fallback endpoint...', err1);
      try {
        return await executeRequest(fallbackUrl);
      } catch (err2) {
        throw (err1.message ? err1 : err2);
      }
    }
  }

  function openMarksModal(studentId, studentName) {
    const overlay = document.getElementById('marksModalOverlay');
    const nameSpan = document.getElementById('modalStudentName');
    const studentIdInput = document.getElementById('modalStudentId');
    const alertBox = document.getElementById('marksModalAlert');
    const examSelect = document.getElementById('modalExamSelect');

    alertBox.style.display = 'none';
    nameSpan.textContent = studentName || 'Student';
    studentIdInput.value = studentId;
    examSelect.innerHTML = '<option value="">Loading exams...</option>';
    resetMarksFormFields();

    overlay.classList.add('show');

    fetchMarksApi('action=get_student_exams&student_id=' + encodeURIComponent(studentId))
      .then(data => {
        if (data.status === 'success') {
          currentStudentExamsData = data;
          nameSpan.textContent = data.student_name + (data.course_code ? ' (' + data.course_code + ')' : '');

          // Populate dropdown with ONLY the semester(s) added to this student's course
          let optionsHtml = '<option value="">Select Exam Name</option>';
          if (data.semesters && data.semesters.length > 0) {
            data.semesters.forEach(sem => {
              optionsHtml += `<option value="${sem.value}">${sem.label}</option>`;
            });
          } else {
            optionsHtml += '<option value="semester 1">Semester 1</option>';
          }
          examSelect.innerHTML = optionsHtml;

          // Auto-select Semester 1 if available
          if (data.semesters && data.semesters.length > 0) {
            const firstSem = data.semesters[0].value;
            examSelect.value = firstSem;
            onExamChange(firstSem);
          } else {
            examSelect.value = 'semester 1';
            onExamChange('semester 1');
          }
        } else {
          examSelect.innerHTML = '<option value="">Error loading exams</option>';
          showModalAlert('danger', data.message || 'Could not load student course details.');
        }
      })
      .catch(err => {
        console.error(err);
        examSelect.innerHTML = '<option value="">Error loading</option>';
        showModalAlert('danger', err.message || 'Error connecting to server.');
      });
  }

  function onExamChange(examVal) {
    const normKey = examVal ? examVal.toLowerCase().trim() : '';

    if (!normKey || !currentStudentExamsData) {
      resetMarksFormFields();
      return;
    }

    const resultsMap = currentStudentExamsData.results || {};
    if (resultsMap[normKey]) {
      // Fill existing marks for this exam/semester
      const r = resultsMap[normKey];
      document.getElementById('modalWritten').value = r.written;
      document.getElementById('modalPractical').value = r.practical;
      document.getElementById('modalAssignment').value = r.assignment;
      document.getElementById('modalViva').value = r.viva;
      document.getElementById('modalRemarks').value = r.remarks || 'Pass';
      document.getElementById('modalMsNo').value = r.ms_no || '';
      document.getElementById('modalDateOfIssue').value = r.date_of_issue || '<?= date("Y-m-d") ?>';
    } else {
      // Fresh semester entry
      resetMarksFormFields();
      document.getElementById('modalMsNo').value = Math.floor(1000 + Math.random() * 9000);
    }
  }

  function resetMarksFormFields() {
    document.getElementById('modalWritten').value = '';
    document.getElementById('modalPractical').value = '';
    document.getElementById('modalAssignment').value = '';
    document.getElementById('modalViva').value = '';
    document.getElementById('modalRemarks').value = 'Pass';
    document.getElementById('modalMsNo').value = '';
    document.getElementById('modalDateOfIssue').value = '<?= date("Y-m-d") ?>';
  }

  function closeMarksModal() {
    document.getElementById('marksModalOverlay').classList.remove('show');
  }

  function closeMarksModalOnBackdrop(e) {
    if (e.target === document.getElementById('marksModalOverlay')) {
      closeMarksModal();
    }
  }

  function showModalAlert(type, message) {
    const alertBox = document.getElementById('marksModalAlert');
    alertBox.className = type === 'success' ? 'alert alert-success' : 'alert alert-danger';
    alertBox.style.backgroundColor = type === 'success' ? '#dcfce7' : '#fee2e2';
    alertBox.style.color = type === 'success' ? '#15803d' : '#991b1b';
    alertBox.style.border = type === 'success' ? '1px solid #bbf7d0' : '1px solid #fecaca';
    alertBox.innerHTML = message;
    alertBox.style.display = 'block';
  }

  function submitMarksForm(e) {
    e.preventDefault();
    const form = document.getElementById('marksModalForm');
    const submitBtn = document.getElementById('marksSubmitBtn');
    const originalText = submitBtn.innerHTML;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    const formData = new FormData(form);
    formData.append('action', 'save_marks');

    fetchMarksApi('', formData)
      .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;

        if (data.status === 'success') {
          showModalAlert('success', '<i class="fa-solid fa-circle-check"></i> ' + data.message + ' (Grade: <strong>' + data.grade + '</strong>, ' + data.finalpercentage + '%)');

          // Update local memory so switching exams retains saved values
          const examName = formData.get('exam_name').toLowerCase().trim();
          if (currentStudentExamsData) {
            if (!currentStudentExamsData.results) currentStudentExamsData.results = {};
            currentStudentExamsData.results[examName] = {
              written: parseInt(formData.get('written')),
              practical: parseInt(formData.get('practical')),
              assignment: parseInt(formData.get('assignment')),
              viva: parseInt(formData.get('viva')),
              remarks: formData.get('remarks'),
              ms_no: formData.get('ms_no'),
              date_of_issue: formData.get('date_of_issue')
            };
          }

          // Close after brief delay and refresh table
          setTimeout(() => {
            closeMarksModal();
            window.location.reload();
          }, 1200);
        } else {
          showModalAlert('danger', '<i class="fa-solid fa-triangle-exclamation"></i> ' + (data.message || 'Failed to save marks.'));
        }
      })
      .catch(err => {
        console.error(err);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        showModalAlert('danger', err.message || 'Error while saving marks.');
      });
  }
</script>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    let searchTimer;
    const searchInput = document.getElementById('searchInput');
    const filterForm = document.getElementById('filterForm');
    
    if(searchInput) {
      if (searchInput.value) {
        searchInput.focus();
        const val = searchInput.value;
        searchInput.value = '';
        searchInput.value = val;
      }
      
      searchInput.addEventListener('keyup', function(e) {
        if (['Tab', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Shift', 'Control', 'Alt', 'Meta'].includes(e.key)) {
          return;
        }
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
          filterForm.submit();
        }, 500);
      });
    }
  });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>