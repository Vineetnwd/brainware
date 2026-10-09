<?php
/**
 * student_certificate root router / alias
 */
if (file_exists(__DIR__ . '/config/db.php')) {
    require_once __DIR__ . '/config/db.php';
    require_once __DIR__ . '/config/auth.php';
}
$student_id = (int)($_GET['student_id'] ?? $_GET['id'] ?? 0);
if ($student_id > 0) {
    header('Location: ' . url('/admin/student-certificate.php?student_id=' . $student_id));
    exit;
} else {
    header('Location: ' . url('/admin/students.php'));
    exit;
}
