<?php
/**
 * Logout Handler
 * Brainware Computer Academy
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Update user status if logged in
if (isset($_SESSION['user_id']) && $pdo) {
    try {
        $stmt = $pdo->prepare("UPDATE user SET status = 'LOGOUT', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } catch (PDOException $e) {
        // Ignore on logout
    }
}

// Unset all session variables
$_SESSION = [];

// Destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Start fresh session for flash message
session_start();
set_flash('info', 'You have been successfully logged out.');
header('Location: ' . url('/login.php'));
exit;
