<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth.php';

// If already logged in, redirect to respective dashboard
if (is_logged_in()) {
  if (is_admin()) {
    header('Location: ' . url('/admin/'));
    exit;
  } else {
    header('Location: ' . url('/center/'));
    exit;
  }
}

$error_message = '';
$user_name = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
  $user_name = trim(clean_input($_POST['user_name'] ?? ''));
  $password = clean_input($_POST['password'] ?? '');

  if (empty($user_name) || empty($password)) {
    $error_message = 'Please enter Mobile Number and Password.';
  } else {
    if ($pdo) {
      try {
        // Fetch user by user_mobile or user_name (fallback for admin)
        $stmt = $pdo->prepare("SELECT * FROM user WHERE user_mobile = ? OR user_name = ? LIMIT 1");
        $stmt->execute([$user_name, $user_name]);
        $user = $stmt->fetch();

        if ($user && verify_password($password, $user['user_password'])) {
          // Check user status
          if ($user['status'] === 'INACTIVE' || $user['status'] === 'BLOCKED') {
            $error_message = 'Your account is currently disabled. Please contact Administrator.';
          } else {
            // Successful login: regenerate session
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['user_type'] = strtoupper($user['user_type']);
            $_SESSION['user_email'] = $user['user_email'] ?? '';
            $_SESSION['user_mobile'] = $user['user_mobile'] ?? '';
            $_SESSION['center_id'] = $user['center_id'] ?? null;

            // If Center/Client user, fetch center details
            if ($_SESSION['user_type'] === 'CLIENT' || $_SESSION['user_type'] === 'CENTER') {
              $center = null;
              if (!empty($user['center_id'])) {
                $c_stmt = $pdo->prepare("SELECT * FROM center_details WHERE id = ? LIMIT 1");
                $c_stmt->execute([$user['center_id']]);
                $center = $c_stmt->fetch();
              }
              if (!$center) {
                $c_stmt = $pdo->prepare("SELECT * FROM center_details WHERE LOWER(TRIM(center_code)) = LOWER(TRIM(?)) LIMIT 1");
                $c_stmt->execute([$user['user_name']]);
                $center = $c_stmt->fetch();
              }

              if ($center) {
                $_SESSION['center_id'] = (int) $center['id'];
                $_SESSION['center_name'] = $center['center_name'];
                $_SESSION['center_code'] = $center['center_code'];
              } else {
                $_SESSION['center_id'] = (int) ($user['center_id'] ?? 0);
                $_SESSION['center_name'] = 'Authorized Study Center';
                $_SESSION['center_code'] = $user['user_name'];
              }

              // Update last login / active status
              $upd = $pdo->prepare("UPDATE user SET status = 'ACTIVE', updated_at = NOW() WHERE id = ?");
              $upd->execute([$user['id']]);

              header('Location: ' . url('/center/'));
              exit;
            } else {
              // Admin user
              $_SESSION['center_name'] = 'Central Administration';
              $_SESSION['center_code'] = 'ADMIN';

              // Update last login
              $upd = $pdo->prepare("UPDATE user SET status = 'ACTIVE', updated_at = NOW() WHERE id = ?");
              $upd->execute([$user['id']]);

              header('Location: ' . url('/admin/'));
              exit;
            }
          }
        } else {
          $error_message = 'Invalid Mobile Number or Password. Please check and try again.';
        }
      } catch (PDOException $e) {
        $error_message = 'Database error during authentication: ' . $e->getMessage();
      }
    } else {
      $error_message = 'Database service unavailable. Please check configuration.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Login - <?= APP_FULL_NAME ?></title>

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="<?= url('/assets/css/components.css') ?>">
  <style>
    :root {
      --primary-blue: #1e3a8a;
      --primary-blue-dark: #0f172a;
      --primary-red: #dc2626;
      --primary-red-dark: #991b1b;
      --bg-slate: #0b1329;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: radial-gradient(circle at 10% 20%, #172554 0%, #0b1329 90%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      color: #334155;
    }

    .login-container {
      width: 100%;
      max-width: 460px;
    }

    .login-header-brand {
      text-align: center;
      margin-bottom: 2rem;
    }

    .login-logo-badge {
      width: 58px;
      height: 58px;
      background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-blue) 100%);
      color: #ffffff;
      border-radius: 14px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 1.75rem;
      box-shadow: 0 6px 18px rgba(220, 38, 38, 0.4);
      margin-bottom: 0.85rem;
    }

    .login-title {
      color: #ffffff;
      font-size: 1.65rem;
      font-weight: 800;
      letter-spacing: -0.5px;
    }

    .login-subtitle {
      color: #94a3b8;
      font-size: 0.88rem;
      margin-top: 0.35rem;
    }

    .login-card {
      background: #ffffff;
      border-radius: 14px;
      padding: 2.5rem 2.25rem;
      box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.08);
      border-top: 5px solid var(--primary-red);
      position: relative;
    }

    .login-card .form-group {
      margin-bottom: 1.35rem;
    }

    .login-card .form-label {
      font-size: 0.92rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .login-card .form-control {
      width: 100%;
      height: 48px;
      padding: 0.75rem 1rem;
      font-size: 0.95rem;
      font-weight: 500;
      color: #0f172a;
      background-color: #f8fafc;
      border: 2px solid #cbd5e1;
      border-radius: 8px;
      transition: all 0.2s ease-in-out;
    }

    .login-card .form-control::placeholder {
      color: #94a3b8;
      font-weight: 400;
    }

    .login-card .form-control:focus {
      background-color: #ffffff;
      border-color: #2563eb;
      box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
      outline: none;
    }

    .login-card .btn-submit {
      width: 100%;
      height: 48px;
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      color: #ffffff;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      cursor: pointer;
      margin-top: 1.5rem;
      box-shadow: 0 4px 14px rgba(220, 38, 38, 0.35);
      transition: all 0.2s ease-in-out;
    }

    .login-card .btn-submit:hover {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(220, 38, 38, 0.45);
    }

    .login-card .btn-submit:active {
      transform: translateY(0);
    }
  </style>
</head>

<body>

  <div class="login-container">


    <!-- Login Card Box -->
    <div class="login-card">

      <!-- Brand Logo & Header -->
      <div class="login-header-brand">
        <img src='assets/images/blogo.png?inline=true' style='max-width:400px'>
      </div>
      <?php render_flash(); ?>

      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"
          style="margin-bottom:1.35rem; font-size:0.86rem; padding:0.75rem 1rem; border-radius:8px;">
          <i class="fa-solid fa-circle-exclamation" style="margin-right:0.4rem;"></i>
          <?= htmlspecialchars($error_message) ?>
        </div>
      <?php endif; ?>

      <!-- Login Form -->
      <form action="<?= url('/login.php') ?>" method="POST" id="loginForm" autocomplete="off">
        <div class="form-group">
          <label class="form-label" for="user_name">
            <i class="fa-solid fa-mobile-screen-button" style="color:var(--primary-blue);"></i> Mobile Number
          </label>
          <input type="text" id="user_name" name="user_name" value="<?= htmlspecialchars($user_name) ?>"
            class="form-control" placeholder="Enter Registered Mobile Number" required autofocus
            autocomplete="username">
        </div>

        <div class="form-group">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.45rem;">
            <label class="form-label" for="password" style="margin-bottom:0;">
              <i class="fa-solid fa-lock" style="color:var(--primary-red);"></i> Password
            </label>
            <a href="<?= url('/contact.php') ?>"
              style="font-size:0.78rem; font-weight:600; color:var(--primary-blue);">Need Help?</a>
          </div>
          <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password"
            required autocomplete="current-password">
        </div>

        <button type="submit" class="btn-submit">
          <i class="fa-solid fa-right-to-bracket"></i> Sign In to Portal
        </button>
      </form>

      <div style="margin-top:2rem; text-align:center; font-size:0.84rem; color:#64748b;">
        <a href="<?= url('/') ?>" style="color:#64748b; font-weight:600; text-decoration:none;"><i
            class="fa-solid fa-arrow-left"></i> Back to
          Public Website</a>
      </div>

    </div>

    <div style="text-align:center; margin-top:1.75rem; font-size:0.78rem; color:#94a3b8;">
      &copy; <?= date('Y') ?> Brainware Computer Academy. Secured Portal.
    </div>

  </div>

</body>

</html>