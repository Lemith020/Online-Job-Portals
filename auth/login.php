<?php
/**
 * JobPortal.lk - Shared Login
 * Member 1 - Core/Shared (Admin Login)
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// If already logged in as admin, go straight to dashboard
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Please enter both your email address and password.';
    } else {
        // Authenticate Admin
        $_SESSION['user'] = [
            'id' => 1,
            'name' => 'Admin Kamal',
            'email' => $email,
            'role' => 'admin'
        ];
        add_activity("Administrator logged in: $email", 'auth');
        set_flash("Welcome back, Admin Kamal!", 'success');
        header('Location: ' . BASE_URL . '/admin/dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - JobPortal.lk</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
  <style>
    body {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
    }
    .auth-card {
      background: #ffffff;
      width: 100%;
      max-width: 420px;
      border-radius: var(--radius-lg);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
      padding: 36px 32px;
    }
    .auth-header {
      text-align: center;
      margin-bottom: 24px;
    }
    .auth-title {
      font-size: 24px;
      font-weight: 800;
      color: var(--text-heading);
      letter-spacing: -0.5px;
    }
    .auth-subtitle {
      font-size: 13.5px;
      color: var(--text-muted);
      margin-top: 4px;
    }
  </style>
</head>
<body>
  <div class="auth-card">
    <div class="auth-header">
      <div style="display:inline-flex; align-items:center; gap:8px; margin-bottom:12px;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
          <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
        </svg>
        <span style="font-size:22px; font-weight:800; color:var(--text-heading);">JobPortal.lk</span>
      </div>
      <h1 class="auth-title">Welcome Back</h1>
      <p class="auth-subtitle">Sign in to your administrative account</p>
    </div>

    <?php if (!empty($error)): ?>
      <div style="background: #fee2e2; color: #991b1b; padding: 10px 14px; border-radius: var(--radius-md); font-size: 13px; margin-bottom: 18px;">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="form-field">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" id="loginEmail" class="form-input-text" value="admin@jobportal.lk" required>
      </div>

      <div class="form-field">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <label class="form-label">Password</label>
          <a href="#" style="font-size:12px; color:var(--primary-blue);">Forgot password?</a>
        </div>
        <input type="password" name="password" id="loginPass" class="form-input-text" value="password123" required>
      </div>

      <div style="margin: 22px 0 16px 0;">
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 14.5px;">
          Log In as Administrator
        </button>
      </div>

      <div style="text-align: center; margin-top: 16px;">
        <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="btn btn-secondary btn-sm" style="width: 100%;">
          ⚡ Quick Demo: Enter Admin Dashboard
        </a>
      </div>
    </form>
  </div>
</body>
</html>
