<?php
/**
 * JobPortal.lk - Password Recovery
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!empty($email)) {
        $msg = "If an account matches {$email}, a password reset link has been dispatched.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password | JobPortal.lk</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body class="auth-page-body">
  <div class="auth-card">
    <div class="text-center mb-4">
      <a href="<?php echo BASE_URL; ?>/index.php" class="auth-brand">
        <div class="auth-brand-icon"><i class="fa-solid fa-briefcase"></i></div>
        <span class="auth-brand-text">JobPortal<span>.lk</span></span>
      </a>
      <h1 class="page-title mb-1" style="font-size:22px;">Reset Password</h1>
      <p class="text-muted" style="font-size:13.5px;">Enter your email to receive recovery instructions</p>
    </div>

    <?php if (!empty($msg)): ?>
      <div class="alert alert-success mb-4">
        <i class="fa-solid fa-circle-check"></i>
        <span><?php echo htmlspecialchars($msg); ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" action="forgot-password.php">
      <div class="form-group mb-4">
        <label class="form-label">Email Address</label>
        <div class="input-with-icon">
          <i class="fa-regular fa-envelope"></i>
          <input type="email" name="email" class="form-input" required placeholder="your.email@example.com">
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-block py-3">
        Send Reset Link
      </button>
    </form>

    <div class="divider my-4"></div>

    <div class="text-center" style="font-size:14px;">
      <a href="login.php" class="text-primary font-semibold">&larr; Return to Sign In</a>
    </div>
  </div>
</body>
</html>
