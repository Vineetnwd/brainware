<?php
if (file_exists(__DIR__ . '/../config/db.php')) {
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../config/auth.php';
} else {
    require_once __DIR__ . '/../../config/db.php';
    require_once __DIR__ . '/../../config/auth.php';
}

if (!is_admin() && !is_center()) {
    header('Location: ' . url('/login.php'));
    exit;
}

$student_id = (int)($_GET['student_id'] ?? 0);
if ($student_id <= 0) {
    die("Invalid student ID.");
}

$student = null;
if ($pdo) {
    $stmt = $pdo->prepare("
        SELECT s.*, c.center_name, c.center_code, c.center_address,
               co.course_name, co.course_code, co.course_duration
        FROM student s
        LEFT JOIN center_details c ON s.center_id = c.id
        LEFT JOIN course_details co ON s.course_id = co.id
        WHERE s.id = ? LIMIT 1
    ");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();

    if ($student && is_center() && $student['center_id'] != $_SESSION['center_id']) {
        die("Unauthorized access to certificate.");
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
  <title>Certificate - <?= htmlspecialchars($student['student_name']) ?></title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Georgia', 'Times New Roman', serif;
      background: #525659;
      padding: 20px;
      display: flex;
      justify-content: center;
    }
    .cert-page {
      background: #fff;
      width: 297mm; /* Landscape A4 */
      height: 210mm;
      padding: 12mm;
      position: relative;
      box-shadow: 0 0 15px rgba(0,0,0,0.4);
    }
    .cert-border {
      border: 4px solid #1e3a8a;
      height: 100%;
      padding: 15px 25px;
      position: relative;
      outline: 2px solid #dc2626;
      outline-offset: -8px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      text-align: center;
    }
    .cert-top-strip {
      display: flex;
      justify-content: space-between;
      font-size: 10pt;
      font-weight: bold;
      color: #dc2626;
    }
    .cert-header h1 {
      font-size: 28pt;
      color: #0f172a;
      letter-spacing: 1px;
      margin: 4px 0;
      font-family: 'Times New Roman', serif;
    }
    .cert-header h3 {
      font-size: 11pt;
      color: #475569;
      font-weight: normal;
    }
    .cert-title-badge {
      font-size: 20pt;
      color: #1e3a8a;
      font-weight: bold;
      font-style: italic;
      margin: 10px 0;
      text-transform: uppercase;
      letter-spacing: 2px;
      border-bottom: 2px solid #dc2626;
      display: inline-block;
      padding-bottom: 4px;
    }
    .cert-body-text {
      font-size: 13.5pt;
      line-height: 1.8;
      color: #1e293b;
      margin: 0 auto;
      max-width: 900px;
    }
    .cert-highlight {
      font-weight: bold;
      color: #1e3a8a;
      text-decoration: underline;
    }
    .cert-footer-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      padding: 0 20px 10px;
    }
    .cert-sig-box {
      text-align: center;
      width: 180px;
      font-size: 11pt;
      font-weight: bold;
    }
    .sig-line {
      border-top: 1px solid #000;
      margin-bottom: 5px;
    }
    .no-print-btn {
      position: fixed;
      top: 15px;
      right: 15px;
      z-index: 9999;
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
      .cert-page { box-shadow: none; margin: 0; }
      .no-print-btn { display: none; }
    }
  </style>
</head>
<body>

<button onclick="window.print()" class="no-print-btn">🖨️ Print Certificate</button>

<div class="cert-page">
  <div class="cert-border">
    
    <div class="cert-top-strip">
      <div>REG NO: <?= htmlspecialchars($student['registration_no']) ?></div>
      <div>ISO 9001:2015 CERTIFIED</div>
      <div>CERTIFICATE NO: <?= htmlspecialchars($student['certificate_no']) ?></div>
    </div>

    <div class="cert-header">
      <h3>AN AUTONOMOUS NATIONAL IT & VOCATIONAL TRAINING INSTITUTION</h3>
      <h1>BRAINWARE COMPUTER ACADEMY</h1>
      <h3>Central Office: Madho Bihari Lane, Salempur, Chapra, Saran (Bihar) - 841301</h3>
      <div class="cert-title-badge">Certificate of Excellence</div>
    </div>

    <div class="cert-body-text">
      This is to certify that Mr./Ms. <span class="cert-highlight"><?= strtoupper(htmlspecialchars($student['student_name'])) ?></span>, 
      Son/Daughter of <span class="cert-highlight"><?= strtoupper(htmlspecialchars($student['student_father'] ?: 'N/A')) ?></span>, 
      having Roll No. <strong><?= htmlspecialchars($student['student_roll']) ?></strong>, has successfully completed the prescribed course of study in 
      <span class="cert-highlight"><?= htmlspecialchars($student['course_name']) ?> (<?= htmlspecialchars($student['course_code']) ?>)</span> 
      of duration <strong><?= htmlspecialchars($student['course_duration']) ?></strong> conducted at 
      <strong><?= htmlspecialchars($student['center_name']) ?></strong> and has been awarded Grade 
      <span style="font-size:16pt; color:#dc2626; font-weight:bold;">'<?= htmlspecialchars($student['grade']) ?>'</span> 
      with <strong style="color:#1e3a8a;"><?= number_format((float)$student['finalpercentage'], 2) ?>%</strong> marks.
    </div>

    <div class="cert-footer-row">
      <div class="cert-sig-box">
        <div style="font-size:10pt; margin-bottom:5px;"><?= format_display_date($student['date_of_issue']) ?></div>
        <div class="sig-line"></div>
        Date of Issue
      </div>

      <div style="text-align:center;">
        <div style="width:75px; height:75px; border:2px dashed #1e3a8a; border-radius:50%; margin:0 auto 5px; display:flex; align-items:center; justify-content:center; color:#1e3a8a; font-weight:bold; font-size:9pt;">
          SEAL
        </div>
        <div style="font-size:9pt; font-weight:bold;">Academy Seal</div>
      </div>

      <div class="cert-sig-box">
        <div style="height:20px;"></div>
        <div class="sig-line"></div>
        Authorized Director
      </div>
    </div>

  </div>
</div>

</body>
</html>
