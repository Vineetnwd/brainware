<?php
/**
 * Authentication and Session Management
 * Brainware Computer Academy Management System
 */

require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged in user details
 */
function current_user() {
    if (!is_logged_in()) {
        return null;
    }
    return [
        'id'          => $_SESSION['user_id'] ?? null,
        'username'    => $_SESSION['user_name'] ?? '',
        'user_type'   => $_SESSION['user_type'] ?? '',
        'center_id'   => $_SESSION['center_id'] ?? null,
        'user_email'  => $_SESSION['user_email'] ?? '',
        'user_mobile' => $_SESSION['user_mobile'] ?? '',
        'center_name' => $_SESSION['center_name'] ?? '',
        'center_code' => $_SESSION['center_code'] ?? '',
    ];
}

/**
 * Check if user has ADMIN role
 */
function is_admin() {
    return is_logged_in() && strtoupper($_SESSION['user_type'] ?? '') === 'ADMIN';
}

/**
 * Check if user has CLIENT / CENTER role
 */
function is_center() {
    return is_logged_in() && (strtoupper($_SESSION['user_type'] ?? '') === 'CLIENT' || strtoupper($_SESSION['user_type'] ?? '') === 'CENTER');
}

/**
 * Require ADMIN access guard
 */
function require_admin($redirectPath = null) {
    if (!is_admin()) {
        set_flash('danger', 'Access denied. Please login as Administrator to continue.');
        header('Location: ' . ($redirectPath ?: url('/login.php')));
        exit;
    }
}

/**
 * Require CENTER access guard
 */
function require_center($redirectPath = null) {
    if (!is_center() && !is_admin()) {
        set_flash('danger', 'Access denied. Please login as Authorized Center to continue.');
        header('Location: ' . ($redirectPath ?: url('/login.php')));
        exit;
    }
}

/**
 * Verify user password (supports both plaintext legacy and password_hash)
 */
function verify_password($inputPassword, $storedPassword) {
    if ($inputPassword === $storedPassword) {
        return true;
    }
    if (password_verify($inputPassword, $storedPassword)) {
        return true;
    }
    return false;
}

/**
 * Retrieve center details by center_id
 */
function get_center_details($center_id) {
    global $pdo;
    if (!$pdo || !$center_id) return null;
    try {
        $stmt = $pdo->prepare("SELECT * FROM center_details WHERE id = ? LIMIT 1");
        $stmt->execute([$center_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}
