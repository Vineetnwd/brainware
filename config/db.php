<?php
/**
 * Database Configuration & Helper Utilities
 * Brainware Computer Academy Management System
 */

// Prevent direct script execution issues and set timezone
date_default_timezone_set('Asia/Kolkata');

// Detect environment (Local vs Production Hostinger)
$http_host = $_SERVER['HTTP_HOST'] ?? '';
$is_local_env = empty($http_host) || in_array($http_host, ['127.0.0.1', '127.0.0.1:8088', 'localhost', 'localhost:8088', 'localhost:8000', '127.0.0.1:8000']);

if ($is_local_env) {
    if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
    if (!defined('DB_PORT')) define('DB_PORT', getenv('DB_PORT') ?: '3306');
    if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
    if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
    if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'brain_db');
} else {
    // Production Hostinger credentials
    if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    if (!defined('DB_PORT')) define('DB_PORT', getenv('DB_PORT') ?: '3306');
    if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'u673864504_brain_user');
    if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '@User_2001');
    if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'u673864504_brain_db');
}

// Application Constants
if (!defined('APP_NAME')) define('APP_NAME', 'BRAINWARE');
if (!defined('APP_FULL_NAME')) define('APP_FULL_NAME', 'Brainware Computer Academy');
if (!defined('APP_TAGLINE')) define('APP_TAGLINE', 'Premier IT & Vocational Training Institute');

// Auto-detect base directory URL (works whether hosted in domain root or subfolder)
if (!defined('BASE_URL')) {
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $detected = preg_replace('#/(admin|center|config|includes)(/.*)?$#i', '', $script_dir);
    if ($detected === '/' || $detected === '.' || $detected === '\\') {
        $detected = '';
    }
    define('BASE_URL', rtrim((string)$detected, '/'));
}

// PHP 7.x backward compatibility polyfills
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos((string)$haystack, (string)$needle) !== false;
    }
}
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return (string)$needle !== '' && strncmp((string)$haystack, (string)$needle, strlen((string)$needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        return $needle !== '' && substr((string)$haystack, -strlen((string)$needle)) === (string)$needle;
    }
}

/**
 * Generate full relative URL with base path prefix
 */
function url($path = '') {
    $clean = '/' . ltrim((string)$path, '/');
    return BASE_URL . $clean;
}

// Ensure upload directories exist
$upload_dirs = [
    __DIR__ . '/../uploads',
    __DIR__ . '/../uploads/students',
    __DIR__ . '/../uploads/centers',
    __DIR__ . '/../uploads/gallery',
];
foreach ($upload_dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

/**
 * PDO Connection Singleton with multi-configuration failover
 */
function get_db_connection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $db_host = defined('DB_HOST') ? DB_HOST : 'localhost';
    $db_port = defined('DB_PORT') ? DB_PORT : '3306';
    $db_user = defined('DB_USER') ? DB_USER : 'u673864504_brain_user';
    $db_pass = defined('DB_PASS') ? DB_PASS : '@User_2001';
    $db_name = defined('DB_NAME') ? DB_NAME : 'u673864504_brain_db';

    $configs = [
        // Primary configured credentials
        [
            'host' => $db_host,
            'port' => $db_port,
            'user' => $db_user,
            'pass' => $db_pass,
            'name' => $db_name,
        ],
        // Production Hostinger with @User_2001
        [
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'u673864504_brain_user',
            'pass' => '@User_2001',
            'name' => 'u673864504_brain_db',
        ],
        // Production Hostinger with password
        [
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'u673864504_brain_user',
            'pass' => 'password',
            'name' => 'u673864504_brain_db',
        ],
        // Local XAMPP/MAMP Fallback
        [
            'host' => '127.0.0.1',
            'port' => '3306',
            'user' => 'root',
            'pass' => '',
            'name' => 'brain_db',
        ],
    ];

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    foreach ($configs as $cfg) {
        try {
            $dsn = "mysql:host=" . $cfg['host'] . ";port=" . $cfg['port'] . ";dbname=" . $cfg['name'] . ";charset=utf8mb4";
            $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], $options);
            return $pdo;
        } catch (PDOException $e) {
            // Try next candidate
        }
    }

    // Log connection failure
    error_log("Database connection failed for all candidate configurations.");
    return null;
}

// Global DB instance for direct queries
$pdo = get_db_connection();

/**
 * Helper: Sanitize string input
 */
function clean_input($data) {
    if (is_array($data)) {
        return array_map('clean_input', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Helper: Format date nicely (e.g. 23 Sep 2026)
 */
function format_display_date($dateStr, $format = 'd M Y') {
    if (empty($dateStr) || $dateStr === '0000-00-00' || $dateStr === '0000-00-00 00:00:00') {
        return 'N/A';
    }
    $timestamp = strtotime($dateStr);
    return $timestamp ? date($format, $timestamp) : 'N/A';
}

/**
 * Flash message handling
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function set_flash($type, $message) {
    $_SESSION['flash_message'] = [
        'type'    => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

function get_flash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

function render_flash() {
    $flash = get_flash();
    if ($flash) {
        $type = htmlspecialchars($flash['type']);
        $icon = 'ℹ';
        $alertClass = 'alert-info';
        
        if ($type === 'success') {
            $icon = '✓';
            $alertClass = 'alert-success';
        } elseif ($type === 'danger') {
            $icon = '✕';
            $alertClass = 'alert-danger';
        } elseif ($type === 'warning') {
            $icon = '⚠';
            $alertClass = 'alert-warning';
        }

        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">
                <span class="alert-icon">' . $icon . '</span>
                <span class="alert-text">' . htmlspecialchars($flash['message']) . '</span>
                <button type="button" class="btn-close" onclick="this.parentElement.remove()" aria-label="Close">×</button>
              </div>';
    }
}

/**
 * Grade Calculator according to percentage
 */
function calculate_grade($percentage) {
    if ($percentage >= 85) return 'A+';
    if ($percentage >= 70) return 'A';
    if ($percentage >= 60) return 'B';
    if ($percentage >= 50) return 'C';
    if ($percentage >= 40) return 'D';
    return 'FAIL';
}

/**
 * Dynamic Institute Configuration Helper
 */
function config_val($name, $default = '') {
    $defaults = [
        'app_name'      => 'BRAINWARE',
        'sc_name'       => 'Brainware Computer Academy',
        'sc_fullname'   => 'Brainware Computer Academy Pvt. Ltd.',
        'sc_code'       => 'BR/CPR/01',
        'sc_address1'   => 'Dotnet Campus, Madho Bihari Lane, Salempur, Chapra',
        'sc_district'   => 'Saran',
        'sc_state'      => 'Bihar',
        'sc_pin'        => '841301',
        'sc_contact1'   => '+91 9835 4266 91',
        'sc_contact2'   => '+91 9030 9203 42',
        'sc_email'      => 'ajitkumar_16aug@rediffmail.com',
        'sc_email2'     => 'info@brainwareacademy.com',
        'sc_director'   => 'Er. Ajit Kumar',
        'sc_tagline'    => 'Premier IT & Vocational Training Institute',
        'sc_year'       => '2015',
    ];
    return $defaults[$name] ?? $default;
}

/**
 * Resolve upload image URL with smart fallbacks
 */
function get_upload_image($filename, $type = 'student') {
    if (empty($filename) || in_array($filename, ['no_photo.jpg', 'no_image.jpg', 'no_dir.jpg', 'no_center.jpg', 'no_sign.jpg'])) {
        return url('/assets/images/logo.png');
    }

    $clean = basename($filename);
    
    // Check direct uploads directory
    $directPath = __DIR__ . '/../uploads/' . $clean;
    if (file_exists($directPath)) {
        return url('/uploads/' . $clean);
    }

    // Check typed subfolder
    $folders = [$type . 's', $type];
    foreach ($folders as $folder) {
        $typedPath = __DIR__ . '/../uploads/' . $folder . '/' . $clean;
        if (file_exists($typedPath)) {
            return url('/uploads/' . $folder . '/' . $clean);
        }
    }

    // Check old uploads directory if present
    $oldPath = __DIR__ . '/../old/uploads/' . $clean;
    if (file_exists($oldPath)) {
        return url('/old/uploads/' . $clean);
    }

    return url('/uploads/' . $clean);
}

/**
 * File upload helper
 */
function handle_file_upload($fileArray, $targetDir, $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf']) {
    if (!isset($fileArray) || $fileArray['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error occurred.'];
    }

    $ext = strtolower(pathinfo($fileArray['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) {
        return ['success' => false, 'error' => 'Invalid file extension: .' . $ext];
    }

    if ($fileArray['size'] > 5 * 1024 * 1024) { // 5MB limit
        return ['success' => false, 'error' => 'File size exceeds maximum limit of 5MB.'];
    }

    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($fileArray['name']));
    $targetPath = rtrim($targetDir, '/') . '/' . $filename;

    if (move_uploaded_file($fileArray['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $filename];
    }

    return ['success' => false, 'error' => 'Failed to move uploaded file.'];
}

/**
 * Standard Pagination Calculation Helper
 */
function paginate($total_records, $current_page = 1, $per_page = 15) {
    $per_page = max(5, (int)$per_page);
    $total_pages = max(1, (int)ceil($total_records / $per_page));
    $current_page = max(1, min((int)$current_page, $total_pages));
    $offset = ($current_page - 1) * $per_page;

    return [
        'total_records' => (int)$total_records,
        'total_pages'   => $total_pages,
        'current_page'  => $current_page,
        'per_page'      => $per_page,
        'offset'        => $offset,
        'from'          => $total_records > 0 ? $offset + 1 : 0,
        'to'            => min($offset + $per_page, (int)$total_records),
    ];
}

/**
 * Render Universal Pagination Navigation Component
 */
function render_pagination($pagination, $base_url = '') {
    if (empty($pagination)) {
        return '';
    }
    if (empty($base_url)) {
        $base_url = $_SERVER['PHP_SELF'] ?? '';
    }

    $total_records = $pagination['total_records'] ?? 0;
    $current = $pagination['current_page'] ?? 1;
    $total = $pagination['total_pages'] ?? 1;
    $from = $pagination['from'] ?? 0;
    $to = $pagination['to'] ?? 0;

    $queryParams = $_GET;

    $out = '<div class="pagination-bar" style="display:flex; justify-content:space-between; align-items:center; padding:0.75rem 1rem; border-top:1px solid var(--border-color); flex-wrap:wrap; gap:0.75rem; font-size:0.8rem; background:#ffffff;">';
    
    $out .= '<div class="pagination-info" style="color:var(--text-muted);">';
    if ($total_records > 0) {
        $out .= 'Showing <strong>' . $from . '</strong> to <strong>' . $to . '</strong> of <strong>' . number_format($total_records) . '</strong> records';
        if ($total > 1) {
            $out .= ' (Page ' . $current . ' of ' . $total . ')';
        }
    } else {
        $out .= 'No records found';
    }
    $out .= '</div>';

    if ($total > 1) {
        $out .= '<ul class="pagination-list" style="display:flex; list-style:none; gap:0.25rem; align-items:center; margin:0; padding:0;">';

        // First & Prev buttons
        if ($current > 1) {
            $queryParams['page'] = 1;
            $out .= '<li><a href="' . htmlspecialchars($base_url . '?' . http_build_query($queryParams)) . '" class="pagination-btn" title="First Page"><i class="fa-solid fa-angles-left"></i></a></li>';
            
            $queryParams['page'] = $current - 1;
            $out .= '<li><a href="' . htmlspecialchars($base_url . '?' . http_build_query($queryParams)) . '" class="pagination-btn" title="Previous Page"><i class="fa-solid fa-angle-left"></i></a></li>';
        } else {
            $out .= '<li><span class="pagination-btn disabled"><i class="fa-solid fa-angles-left"></i></span></li>';
            $out .= '<li><span class="pagination-btn disabled"><i class="fa-solid fa-angle-left"></i></span></li>';
        }

        // Page Numbers Window
        $start = max(1, $current - 2);
        $end = min($total, $current + 2);

        if ($start > 1) {
            $queryParams['page'] = 1;
            $out .= '<li><a href="' . htmlspecialchars($base_url . '?' . http_build_query($queryParams)) . '" class="pagination-btn">1</a></li>';
            if ($start > 2) {
                $out .= '<li><span class="pagination-btn disabled" style="border:none;">...</span></li>';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            $queryParams['page'] = $i;
            $activeClass = ($i === $current) ? 'active' : '';
            $out .= '<li><a href="' . htmlspecialchars($base_url . '?' . http_build_query($queryParams)) . '" class="pagination-btn ' . $activeClass . '">' . $i . '</a></li>';
        }

        if ($end < $total) {
            if ($end < $total - 1) {
                $out .= '<li><span class="pagination-btn disabled" style="border:none;">...</span></li>';
            }
            $queryParams['page'] = $total;
            $out .= '<li><a href="' . htmlspecialchars($base_url . '?' . http_build_query($queryParams)) . '" class="pagination-btn">' . $total . '</a></li>';
        }

        // Next & Last buttons
        if ($current < $total) {
            $queryParams['page'] = $current + 1;
            $out .= '<li><a href="' . htmlspecialchars($base_url . '?' . http_build_query($queryParams)) . '" class="pagination-btn" title="Next Page"><i class="fa-solid fa-angle-right"></i></a></li>';

            $queryParams['page'] = $total;
            $out .= '<li><a href="' . htmlspecialchars($base_url . '?' . http_build_query($queryParams)) . '" class="pagination-btn" title="Last Page"><i class="fa-solid fa-angles-right"></i></a></li>';
        } else {
            $out .= '<li><span class="pagination-btn disabled"><i class="fa-solid fa-angle-right"></i></span></li>';
            $out .= '<li><span class="pagination-btn disabled"><i class="fa-solid fa-angles-right"></i></span></li>';
        }

        $out .= '</ul>';
    }

    $out .= '</div>';
    return $out;
}
