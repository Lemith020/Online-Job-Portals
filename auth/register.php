<?php
// -----------------------------------------------------------------
// Register
// Purpose : Show registration form (role toggle) and insert new
//           user + role-specific profile row into DB
// -----------------------------------------------------------------
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$error   = "";
$old     = $_POST ?? [];
$role    = $_POST['role'] ?? 'job_seeker'; // default tab

if (isset($_SESSION['user_id'])) {
    redirect('../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $role         = ($_POST['role'] ?? '') === 'company' ? 'company' : 'job_seeker';
    $first_name   = trim($_POST['first_name'] ?? '');
    $middle_name  = trim($_POST['middle_name'] ?? '');
    $last_name    = trim($_POST['last_name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $password     = $_POST['password'] ?? '';
    $phone        = trim($_POST['phone'] ?? '');

    // role-specific
    $birth_day    = trim($_POST['birth_day'] ?? '');       // job_seeker
    $company_name = trim($_POST['company_name'] ?? '');    // company
    $industry     = trim($_POST['industry_type'] ?? '');   // company
    $location     = trim($_POST['location'] ?? '');        // company
    $description  = trim($_POST['description'] ?? '');     // company

    // ---------------- Validation ----------------
    if ($first_name === '' || $last_name === '' || $email === '' || $password === '' || $phone === '') {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($role === 'job_seeker' && $birth_day === '') {
        $error = "Please enter your Date of Birth.";
    } elseif ($role === 'company' && ($company_name === '' || $industry === '' || $location === '')) {
        $error = "Please fill in all company fields.";
    } else {

        // Email already registered da kiyala check karanna
        $check = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "This email is already registered. Please login instead.";
        } else {

            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            mysqli_begin_transaction($conn);
            try {
                $stmt = mysqli_prepare($conn,
                    "INSERT INTO users (first_name, middle_name, last_name, email, password, phone, role)
                     VALUES (?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "sssssss",
                    $first_name, $middle_name, $last_name, $email, $hashed_password, $phone, $role);
                mysqli_stmt_execute($stmt);

                $user_id = mysqli_insert_id($conn);

                if ($role === 'job_seeker') {
                    $stmt2 = mysqli_prepare($conn,
                        "INSERT INTO job_seekers (user_id, birth_day, phone, bio, status)
                         VALUES (?, ?, ?, '', 'not_hired')");
                    mysqli_stmt_bind_param($stmt2, "iss", $user_id, $birth_day, $phone);
                    mysqli_stmt_execute($stmt2);
                } else {
                    $stmt2 = mysqli_prepare($conn,
                        "INSERT INTO company (user_id, company_name, industry_type, description, location)
                         VALUES (?, ?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt2, "issss", $user_id, $company_name, $industry, $description, $location);
                    mysqli_stmt_execute($stmt2);
                }

                mysqli_commit($conn);
                redirect('login.php?registered=1');

            } catch (mysqli_sql_exception $e) {
                mysqli_rollback($conn);
                $error = "Something went wrong while registering. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - JobPortal.lk</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../assets/css/public-auth/login.css">
<link rel="stylesheet" href="../assets/css/public-auth/register.css">
</head>
<body>

<div class="auth-body">
    <div class="auth-card auth-card-wide">

        <div class="role-toggle">
            <button type="button" class="role-btn <?= $role === 'job_seeker' ? 'active' : '' ?>" data-role="job_seeker">
                <i class="fa-solid fa-user"></i> Job Seeker
            </button>
            <button type="button" class="role-btn <?= $role === 'company' ? 'active' : '' ?>" data-role="company">
                <i class="fa-solid fa-building"></i> Company
            </button>
        </div>

        <h2 id="formTitle">Register as <?= $role === 'company' ? 'Company' : 'Job Seeker' ?></h2>

        <?php if ($error): ?>
            <div class="form-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php" novalidate>
            <input type="hidden" name="role" id="roleInput" value="<?= htmlspecialchars($role) ?>">

            <div class="form-group">
                <label for="first_name">First Name</label>
                <div class="input-icon-wrap">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" id="first_name" name="first_name" placeholder="First Name"
                           value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="middle_name">Middle Name (Optional)</label>
                <div class="input-icon-wrap">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" id="middle_name" name="middle_name" placeholder="Middle Name"
                           value="<?= htmlspecialchars($old['middle_name'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <div class="input-icon-wrap">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" id="last_name" name="last_name" placeholder="Last Name"
                           value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-icon-wrap">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="your-email@example.com"
                               value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon-wrap">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••" minlength="6" required>
                        <i class="fa-regular fa-eye toggle-eye" id="togglePassword"></i>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <div class="input-icon-wrap">
                        <i class="fa-solid fa-phone"></i>
                        <input type="text" id="phone" name="phone" placeholder="+94 7x xxx xxxx"
                               value="<?= htmlspecialchars($old['phone'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group role-field role-field-job_seeker">
                    <label for="birth_day">Date of Birth</label>
                    <div class="input-icon-wrap">
                        <i class="fa-regular fa-calendar"></i>
                        <input type="date" id="birth_day" name="birth_day"
                               value="<?= htmlspecialchars($old['birth_day'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Company-only fields -->
            <div class="role-field role-field-company">
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <div class="input-icon-wrap">
                        <i class="fa-solid fa-building"></i>
                        <input type="text" id="company_name" name="company_name" placeholder="Your Company Pvt Ltd"
                               value="<?= htmlspecialchars($old['company_name'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="industry_type">Industry Type</label>
                        <div class="input-icon-wrap">
                            <i class="fa-solid fa-industry"></i>
                            <input type="text" id="industry_type" name="industry_type" placeholder="e.g. Telecommunications"
                                   value="<?= htmlspecialchars($old['industry_type'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <div class="input-icon-wrap">
                            <i class="fa-solid fa-location-dot"></i>
                            <input type="text" id="location" name="location" placeholder="City, Country"
                                   value="<?= htmlspecialchars($old['location'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Company Description</label>
                    <div class="input-icon-wrap textarea-wrap">
                        <i class="fa-solid fa-align-left"></i>
                        <textarea id="description" name="description" rows="3" placeholder="Tell job seekers about your company..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-primary" id="submitBtn">Register as Job Seeker</button>
        </form>

        <div class="auth-footer-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
</div>

<script src="../assets/js/public-auth/login.js"></script>
<script src="../assets/js/public-auth/register.js"></script>
</body>
</html>
