<?php
// -----------------------------------------------------------------
// Login
// Purpose : Show login form and check email/password against DB
// -----------------------------------------------------------------
session_start();
session_unset();

require_once '../config/database.php';           // DB connection ($conn)
require_once '../includes/auth.php'; // login/session helpers
require_once '../includes/functions.php';  // helper functions

$error = "";

if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role'] ?? '') {
        case 'admin':
            redirect('../admin/dashboard.php');
            break;
        case 'company':
            redirect('../company/dashboard.php');
            break;
        case 'job_seeker':
            redirect('../seeker/dashboard.php');
            break;
        default:
            redirect('../index.php');
    }
}

// -----------------------------------------------------------------
// DB logic: Check email/password against DB
// -----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please fill in both Email and Password.";
    } else {

        $stmt = mysqli_prepare($conn, "SELECT user_id, first_name, password, role FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {

            // Session fixation vලක්වන්න session id eka regenerate karanna
            session_regenerate_id(true);

            $_SESSION['user_id']    = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['role']       = $user['role']; // admin / job_seeker / company

            // Role anuwa dashboard ekata redirect
            switch ($user['role']) {
                case 'admin':
                    redirect('../admin/dashboard.php');
                    break;
                case 'company':
                    redirect('../company/dashboard.php');
                    break;
                case 'job_seeker':
                    redirect('../seeker/dashboard.php');
                    break;
                default:
                    redirect('../index.php');
            }

        } else {
            // Email tiyenawada, password ekada wrong kiyala wenama kiyanne nathuwa
            // (user enumeration vලක්වන්න) generic error ekak dennawa
            $error = "Email or Password is incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - JobPortal.lk</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../assets/css/public-auth/login.css">
</head>
<body>

<div class="auth-body">
    <div class="auth-card">
        <h2>Login to JobPortal.lk</h2>

        <?php if ($error): ?>
            <div class="form-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
            <div class="form-success">Registration success! Please login.</div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-icon-wrap">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="your-email@example.com"
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <i class="fa-regular fa-eye toggle-eye" id="togglePassword"></i>
                </div>
            </div>

            <div class="auth-links-row">
                <a href="forgot-password.php">Forgot password?</a>
                <span>Don't have an account? <a href="register.php">Register</a></span>
            </div>

            <button type="submit" class="btn-primary">Login</button>
        </form>
    </div>
</div>

<script src="../assets/js/public-auth/login.js"></script>
</body>
</html>