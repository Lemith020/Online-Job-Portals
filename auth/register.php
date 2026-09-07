<?php
/**
 * JobPortal.lk - User Registration
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'seeker';
    $phone = trim($_POST['phone'] ?? '');

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill out all required fields.';
    } else {
        global $conn;
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        if ($conn) {
            $stmt = @mysqli_prepare($conn, "INSERT INTO users (name, email, password, role, phone, status) VALUES (?, ?, ?, ?, ?, 'Active')");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $hashed, $role, $phone);
                if (@mysqli_stmt_execute($stmt)) {
                    $new_id = mysqli_insert_id($conn);
                    if ($role === 'seeker') {
                        @mysqli_query($conn, "INSERT INTO job_seekers (user_id) VALUES ($new_id)");
                    } elseif ($role === 'company') {
                        @mysqli_query($conn, "INSERT INTO companies (user_id, company_name, owner_email, status) VALUES ($new_id, '$name', '$email', 'Pending Approval')");
                    }
                    $success = 'Account created successfully! You can now log in.';
                } else {
                    $error = 'Email address is already registered. Please try logging in.';
                }
            }
        } else {
            $success = 'Account registered successfully (Demo Mode)! You can now log in.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account | JobPortal.lk</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
  
  <style>
    body {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 24px;
    }
    .auth-card {
      background: #ffffff;
      width: 100%;
      max-width: 480px;
      border-radius: 16px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
      padding: 40px 36px;
    }
  </style>
</head>
<body>
  <div class="auth-card">
    <div class="text-center mb-4">
      <h1 class="page-title mb-1" style="font-size:24px;">Create an Account</h1>
      <p class="text-muted" style="font-size:14px;">Join Sri Lanka's leading job network today</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger mb-4">
        <span class="alert-icon">✕</span>
        <span class="alert-text"><?php echo htmlspecialchars($error); ?></span>
      </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success mb-4">
        <span class="alert-icon">✓</span>
        <span class="alert-text"><?php echo htmlspecialchars($success); ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" action="register.php">
      <div class="form-group mb-3">
        <label class="form-label">Account Type</label>
        <div class="grid-2 gap-2">
          <label class="radio-card">
            <input type="radio" name="role" value="seeker" checked>
            <span><i class="fa-solid fa-user-graduate"></i> Job Seeker</span>
          </label>
          <label class="radio-card">
            <input type="radio" name="role" value="company">
            <span><i class="fa-solid fa-building"></i> Employer</span>
          </label>
        </div>
      </div>

      <div class="form-group mb-3">
        <label class="form-label">Full Name or Company Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-input" required placeholder="e.g. Kasun Silva">
      </div>

      <div class="form-group mb-3">
        <label class="form-label">Email Address <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-input" required placeholder="kasun@example.com">
      </div>

      <div class="form-group mb-3">
        <label class="form-label">Phone Number</label>
        <input type="text" name="phone" class="form-input" placeholder="+94 77 123 4567">
      </div>

      <div class="form-group mb-4">
        <label class="form-label">Create Password <span class="text-danger">*</span></label>
        <input type="password" name="password" class="form-input" required placeholder="••••••••">
      </div>

      <button type="submit" class="btn btn-primary btn-block py-3">
        <i class="fa-solid fa-user-plus"></i> Register Account
      </button>
    </form>

    <div class="divider my-4"></div>

    <div class="text-center" style="font-size:14px;">
      <span class="text-muted">Already registered?</span>
      <a href="login.php" class="text-primary font-bold">Sign In</a>
    </div>
  </div>
</body>
</html>
