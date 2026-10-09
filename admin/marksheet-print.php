<?php
if (file_exists(__DIR__ . '/../config/db.php')) {
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../config/auth.php';
} else {
    require_once __DIR__ . '/../../config/db.php';
    require_once __DIR__ . '/../../config/auth.php';
}

// Allow Admin or Center to print
if (!is_admin() && !is_center()) {
    header('Location: ' . url('/login.php'));
    exit;
}

$student_id = (int)($_GET['student_id'] ?? 0);
if ($student_id <= 0) {
    die("Invalid student ID.");
}

$student = null;
$results = [];

if ($pdo) {
    $stmt = $pdo->prepare("
        SELECT s.*, c.center_name, c.center_code, c.center_address, c.center_director,
               co.course_name, co.course_code, co.course_duration, co.no_of_semester
        FROM student s
        LEFT JOIN center_details c ON s.center_id = c.id
        LEFT JOIN course_details co ON s.course_id = co.id
        WHERE s.id = ? LIMIT 1
    ");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();

    if ($student) {
        // If center user, ensure student belongs to this center
        if (is_center() && $student['center_id'] != $_SESSION['center_id']) {
            die("Unauthorized access to this student marksheet.");
        }

        $r_stmt = $pdo->prepare("SELECT * FROM result WHERE student_id = ? ORDER BY id ASC");
        $r_stmt->execute([$student_id]);
        $results = $r_stmt->fetchAll();
    }
}

if (!$student) {
    die("Student record not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Official Marksheet - <?= htmlspecialchars($student['student_name']) ?> (<?= htmlspecialchars($student['registration_no']) ?>)</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Times New Roman', Times, serif, sans-serif;
      background: #525659;
      padding: 20px;
      display: flex;
      justify-content: center;
    }
    .marksheet-page {
      background: #fff;
      width: 210mm;
      min-height: 297mm;
      padding: 18mm 15mm;
      box-shadow: 0 0 15px rgba(0,0,0,0.4);
      position: relative;
      border: 4px double #1e3a8a;
      outline: 2px solid #dc2626;
      outline-offset: -8px;
    }
    .watermark {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%) rotate(-30deg);
      font-size: 5rem;
      font-weight: bold;
      color: rgba(30, 58, 138, 0.04);
      pointer-events: none;
      user-select: none;
      text-transform: uppercase;
    }
    .header-box {
      text-align: center;
      border-bottom: 2px solid #1e3a8a;
      padding-bottom: 12px;
      margin-bottom: 18px;
    }
    .cert-reg-info {
      font-size: 11pt;
      font-weight: bold;
      color: #dc2626;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .inst-name {
      font-size: 24pt;
      font-weight: 900;
      color: #0f172a;
      letter-spacing: -0.5px;
      margin: 4px 0;
    }
    .inst-address {
      font-size: 10pt;
      color: #475569;
    }
    .title-ribbon {
      display: inline-block;
      background: #1e3a8a;
      color: #ffffff;
      font-size: 13pt;
      font-weight: bold;
      padding: 4px 25px;
      border-radius: 20px;
      margin-top: 10px;
      letter-spacing: 1px;
    }
    .details-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 11pt;
      margin-bottom: 20px;
    }
    .details-table td {
      padding: 5px 4px;
      vertical-align: top;
    }
    .marks-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 10.5pt;
      text-align: center;
      margin-bottom: 25px;
    }
    .marks-table th, .marks-table td {
      border: 1px solid #1e3a8a;
      padding: 7px 6px;
    }
    .marks-table th {
      background: #f1f5f9;
      color: #0f172a;
      font-weight: bold;
    }
    .summary-box {
      border: 2px solid #1e3a8a;
      padding: 10px 15px;
      background: #f8fafc;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 40px;
      font-size: 11pt;
    }
    .signatures-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-top: 40px;
    }
    .sig-col {
      text-align: center;
      width: 160px;
      font-size: 10pt;
      font-weight: bold;
    }
    .sig-line {
      border-top: 1px solid #000;
      margin-bottom: 4px;
    }
    .qr-mock {
      border: 2px solid #000;
      padding: 6px;
      font-family: monospace;
      font-size: 8pt;
      text-align: center;
    }
    .no-print-bar {
      position: fixed;
      top: 15px;
      right: 15px;
      z-index: 9999;
      display: flex;
      gap: 10px;
    }
    .btn-print {
      background: #dc2626;
      color: #fff;
      border: none;
      padding: 10px 20px;
      font-size: 14px;
      font-weight: bold;
      border-radius: 4px;
      cursor: pointer;
      box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    }
    @media print {
      body { background: #fff; padding: 0; }
      .marksheet-page { box-shadow: none; margin: 0; }
      .no-print-bar { display: none; }
    }
  </style>
</head>
<body>

<div class="no-print-bar">
  <button onclick="window.print()" class="btn-print">🖨️ Print Marksheet</button>
</div>

<div class="marksheet-page">
  <div class="watermark">BRAINWARE</div>

  <!-- Header -->
  <div class="header-box">
    <div class="cert-reg-info">AN ISO 9001:2015 CERTIFIED IT INSTITUTION</div>
    <h1 class="inst-name">BRAINWARE COMPUTER ACADEMY</h1>
    <div class="inst-address">Head Office: Madho Bihari Lane, Salempur, Chapra (Saran) Bihar - 841301</div>
    <div class="title-ribbon">STATEMENT OF MARKS</div>
  </div>

  <!-- Candidate Details -->
  <table class="details-table">
    <tr>
      <td style="width:18%; font-weight:bold;">Student Name:</td>
      <td style="width:34%; font-weight:bold; font-size:12pt; color:#1e3a8a;"><?= strtoupper(htmlspecialchars($student['student_name'])) ?></td>
      <td style="width:18%; font-weight:bold;">Registration No:</td>
      <td style="width:30%; font-weight:bold; color:#dc2626;"><?= htmlspecialchars($student['registration_no']) ?></td>
    </tr>
    <tr>
      <td style="font-weight:bold;">Father's Name:</td>
      <td><?= strtoupper(htmlspecialchars($student['student_father'] ?: 'N/A')) ?></td>
      <td style="font-weight:bold;">Roll Number:</td>
      <td><strong><?= htmlspecialchars($student['student_roll']) ?></strong></td>
    </tr>
    <tr>
      <td style="font-weight:bold;">Mother's Name:</td>
      <td><?= strtoupper(htmlspecialchars($student['student_mother'] ?: 'N/A')) ?></td>
      <td style="font-weight:bold;">Certificate No:</td>
      <td><strong><?= htmlspecialchars($student['certificate_no']) ?></strong></td>
    </tr>
    <tr>
      <td style="font-weight:bold;">Course Name:</td>
      <td style="font-weight:bold; color:#0f172a;"><?= htmlspecialchars($student['course_name']) ?> (<?= htmlspecialchars($student['course_code']) ?>)</td>
      <td style="font-weight:bold;">Course Duration:</td>
      <td><?= htmlspecialchars($student['course_duration']) ?></td>
    </tr>
    <tr>
      <td style="font-weight:bold;">Study Center:</td>
      <td colspan="3"><?= htmlspecialchars($student['center_name']) ?> [Code: <?= htmlspecialchars($student['center_code']) ?>]</td>
    </tr>
  </table>

  <!-- Marks Table -->
  <table class="marks-table">
    <thead>
      <tr>
        <th style="text-align:left; width:25%;">Semester / Examination</th>
        <th>Theory Written<br><small>(Max: 100)</small></th>
        <th>Practical Lab<br><small>(Max: 100)</small></th>
        <th>Assignment<br><small>(Max: 100)</small></th>
        <th>Viva Voce<br><small>(Max: 100)</small></th>
        <th>Total Marks<br><small>(Max: 400)</small></th>
        <th>Remarks</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($results)): ?>
        <?php foreach ($results as $res): ?>
          <?php $t = $res['written'] + $res['practical'] + $res['assignment'] + $res['viva']; ?>
          <tr>
            <td style="text-align:left; font-weight:bold;"><?= strtoupper(htmlspecialchars($res['exam_name'])) ?></td>
            <td><?= $res['written'] ?></td>
            <td><?= $res['practical'] ?></td>
            <td><?= $res['assignment'] ?></td>
            <td><?= $res['viva'] ?></td>
            <td><strong><?= $t ?></strong></td>
            <td style="color:#16a34a; font-weight:bold;"><?= htmlspecialchars($res['remarks'] ?: 'Pass') ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="7" style="padding:15px;">Result records under processing.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Summary Box -->
  <div class="summary-box">
    <div>
      <span style="color:#64748b; font-size:9pt; font-weight:bold; display:block;">AGGREGATE PERCENTAGE</span>
      <strong style="font-size:15pt; color:#1e3a8a;"><?= number_format((float)$student['finalpercentage'], 2) ?>%</strong>
    </div>
    <div>
      <span style="color:#64748b; font-size:9pt; font-weight:bold; display:block;">AWARDED GRADE</span>
      <strong style="font-size:16pt; color:#dc2626;"><?= htmlspecialchars($student['grade']) ?></strong>
    </div>
    <div>
      <span style="color:#64748b; font-size:9pt; font-weight:bold; display:block;">FINAL RESULT</span>
      <strong style="font-size:14pt; color:#16a34a;"><?= strtoupper(htmlspecialchars($student['comment'] ?: 'PASSED')) ?></strong>
    </div>
    <div>
      <span style="color:#64748b; font-size:9pt; font-weight:bold; display:block;">DATE OF ISSUE</span>
      <strong style="font-size:11pt;"><?= format_display_date($student['date_of_issue']) ?></strong>
    </div>
  </div>

  <!-- Verification Note -->
  <div style="font-size:8.5pt; color:#475569; text-align:center; margin-bottom:30px; font-style:italic;">
    Verify authentic electronic record anytime at: <strong>https://brainwarecomputer.co.in/verification.php</strong>
  </div>

  <!-- Signatures -->
  <div class="signatures-row">
    <div class="sig-col">
      <div class="sig-line"></div>
      Center In-Charge
    </div>
    <div class="sig-col">
      <div class="qr-mock">
        <div>ONLINE VERIFIED</div>
        <div style="font-weight:bold; font-size:7pt; color:#1e3a8a;"><?= htmlspecialchars($student['registration_no']) ?></div>
      </div>
      <div style="margin-top:4px; font-size:8pt;">Academy Seal</div>
    </div>
    <div class="sig-col">
      <div class="sig-line"></div>
      Controller of Examinations
    </div>
  </div>

</div>

</body>
</html>
