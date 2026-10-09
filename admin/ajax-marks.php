<?php
if (file_exists(__DIR__ . '/../config/db.php')) {
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../config/auth.php';
} elseif (file_exists(__DIR__ . '/../../config/db.php')) {
    require_once __DIR__ . '/../../config/db.php';
    require_once __DIR__ . '/../../config/auth.php';
} else {
    require_once dirname(__DIR__) . '/config/db.php';
    require_once dirname(__DIR__) . '/config/auth.php';
}

header('Content-Type: application/json; charset=utf-8');

// Ensure only authorized admin can access
if (!is_admin()) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized or session expired. Please refresh and log in again.']);
    exit;
}

$admin_user = current_user();
$admin_id = $admin_user['id'] ?? 1;
$pdo = $pdo ?: get_db_connection();

$action = clean_input($_REQUEST['action'] ?? '');
$student_id = (int)($_REQUEST['student_id'] ?? 0);

if ($student_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid student ID.']);
    exit;
}

if ($action === 'get_student_exams') {
    if (!$pdo) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
        exit;
    }

    try {
        // Fetch student & course details
        $stmt = $pdo->prepare("
            SELECT s.id, s.student_name, s.registration_no, s.student_roll, s.course_id,
                   co.course_code, co.course_name, co.no_of_semester
            FROM student s
            LEFT JOIN course_details co ON s.course_id = co.id
            WHERE s.id = ?
            LIMIT 1
        ");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch();

        if (!$student) {
            echo json_encode(['status' => 'error', 'message' => 'Student record not found.']);
            exit;
        }

        // Generate semesters only for this specific course
        $num_semesters = max(1, (int)($student['no_of_semester'] ?: 1));
        $semesters = [];
        for ($i = 1; $i <= $num_semesters; $i++) {
            $semesters[] = [
                'value' => 'semester ' . $i,
                'label' => 'Semester ' . $i
            ];
        }

        // Fetch all recorded results for this student
        $res_stmt = $pdo->prepare("
            SELECT * FROM result 
            WHERE student_id = ? AND status != 'removed'
        ");
        $res_stmt->execute([$student_id]);
        $all_results = $res_stmt->fetchAll();

        $results_map = [];
        foreach ($all_results as $r) {
            $key = strtolower(trim($r['exam_name']));
            $results_map[$key] = [
                'id' => $r['id'],
                'written' => (int)$r['written'],
                'practical' => (int)$r['practical'],
                'assignment' => (int)$r['assignment'],
                'viva' => (int)$r['viva'],
                'ms_no' => $r['ms_no'],
                'date_of_issue' => $r['date_of_issue'] ?: date('Y-m-d'),
                'remarks' => $r['remarks'] ?: 'Pass',
                'status' => $r['status'] ?: 'ACTIVE'
            ];
        }

        echo json_encode([
            'status' => 'success',
            'student_id' => $student['id'],
            'student_name' => $student['student_name'],
            'registration_no' => $student['registration_no'],
            'course_code' => $student['course_code'],
            'course_name' => $student['course_name'],
            'no_of_semester' => $num_semesters,
            'semesters' => $semesters,
            'results' => $results_map
        ]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

if ($action === 'save_marks' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$pdo) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
        exit;
    }

    $exam_name = strtolower(clean_input($_POST['exam_name'] ?? ''));
    if (empty($exam_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Please select an exam name.']);
        exit;
    }

    $written = max(0, min(100, (int)($_POST['written'] ?? 0)));
    $practical = max(0, min(100, (int)($_POST['practical'] ?? 0)));
    $assignment = max(0, min(100, (int)($_POST['assignment'] ?? 0)));
    $viva = max(0, min(100, (int)($_POST['viva'] ?? 0)));
    $remarks = clean_input($_POST['remarks'] ?? 'Pass');
    if (empty($remarks)) $remarks = 'Pass';
    $ms_no = clean_input($_POST['ms_no'] ?? (string)rand(1000, 9999));
    $date_of_issue = clean_input($_POST['date_of_issue'] ?? date('Y-m-d'));
    if (empty($date_of_issue)) $date_of_issue = date('Y-m-d');
    $status = 'ACTIVE';

    try {
        // Check if an entry already exists for this student & exam_name
        $check_stmt = $pdo->prepare("SELECT id FROM result WHERE student_id = ? AND LOWER(TRIM(exam_name)) = ? LIMIT 1");
        $check_stmt->execute([$student_id, $exam_name]);
        $existing = $check_stmt->fetch();

        // Check for duplicate ms_no
        if (!empty($ms_no)) {
            $ms_check = $pdo->prepare("SELECT r.id, s.student_name, s.registration_no FROM result r JOIN student s ON r.student_id = s.id WHERE r.ms_no = ? AND r.id != ? LIMIT 1");
            $ignore_id = $existing ? $existing['id'] : 0;
            $ms_check->execute([$ms_no, $ignore_id]);
            $dup_ms = $ms_check->fetch();
            if ($dup_ms) {
                echo json_encode(['status' => 'error', 'message' => 'MS No. "' . $ms_no . '" is already assigned to ' . $dup_ms['student_name'] . ' (' . $dup_ms['registration_no'] . ').']);
                exit;
            }
        }

        if ($existing) {
            $stmt = $pdo->prepare("
                UPDATE result 
                SET written = ?, practical = ?, assignment = ?, viva = ?,
                    ms_no = ?, date_of_issue = ?, remarks = ?, status = ?,
                    updated_by = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $written, $practical, $assignment, $viva,
                $ms_no, $date_of_issue, $remarks, $status,
                $admin_id, $existing['id']
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO result 
                (student_id, exam_name, written, practical, assignment, viva, ms_no, date_of_issue, remarks, status, created_at, created_by, updated_by, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, NOW())
            ");
            $stmt->execute([
                $student_id, $exam_name, $written, $practical, $assignment, $viva,
                $ms_no, $date_of_issue, $remarks, $status,
                $admin_id, $admin_id
            ]);
        }

        // Recalculate aggregate percentage & grade for student
        $res_stmt = $pdo->prepare("SELECT written, practical, assignment, viva FROM result WHERE student_id = ? AND status = 'ACTIVE'");
        $res_stmt->execute([$student_id]);
        $all_results = $res_stmt->fetchAll();

        $final_pct = 0.00;
        $calc_grade = 'A';
        if (!empty($all_results)) {
            $total_obtained = 0;
            $total_max = count($all_results) * 400;
            foreach ($all_results as $ar) {
                $total_obtained += ((int)$ar['written'] + (int)$ar['practical'] + (int)$ar['assignment'] + (int)$ar['viva']);
            }
            $final_pct = round(($total_obtained / $total_max) * 100, 2);
            $calc_grade = calculate_grade($final_pct);

            $upd = $pdo->prepare("UPDATE student SET finalpercentage = ?, grade = ?, updated_at = NOW() WHERE id = ?");
            $upd->execute([$final_pct, $calc_grade, $student_id]);
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Marks details saved successfully for ' . ucwords($exam_name) . '!',
            'exam_name' => $exam_name,
            'finalpercentage' => $final_pct,
            'grade' => $calc_grade
        ]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save marks: ' . $e->getMessage()]);
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
exit;
