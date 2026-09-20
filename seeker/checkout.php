<?php
// seeker/checkout.php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$user_id = $_SESSION['user_id'];
$plan_id = isset($_GET['plan_id']) ? (int)$_GET['plan_id'] : 0;
$error = '';

if ($plan_id <= 0) {
    redirect("my-cv.php");
}

// Fetch plan details
$sql = "SELECT * FROM subscription_plans WHERE plan_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $plan_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$plan = mysqli_fetch_assoc($res);

if (!$plan) {
    redirect("my-cv.php");
}

if (isset($_POST['process_payment'])) {
    // In a real application, you would integrate Stripe/PayPal here.
    // For now, we simulate a successful payment.
    
    // Process subscription
    subscribe_to_plan($conn, $user_id, $plan_id);
    
    // Redirect to my-cv.php with success
    redirect("my-cv.php?payment_success=1");
}

$page_title = "Checkout";
$page_css = "../assets/css/seeker_page_css/checkout.css";
require_once '../includes/seeker-header.php';
require_once '../includes/seeker-sidebar.php';
?>

<div class="checkout-container">
    <h1 class="page-title">Complete Your Subscription</h1>
    
    <div class="checkout-grid">
        <!-- Order Summary -->
        <div class="card order-summary">
            <h2 class="section-title">Order Summary</h2>
            <div class="summary-item">
                <span class="item-name"><?= clean($plan['plan_name']) ?> (<?= $plan['duration_days'] ?> days)</span>
                <span class="item-price">Rs. <?= number_format($plan['price'], 2) ?></span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-item total">
                <span>Total</span>
                <span>Rs. <?= number_format($plan['price'], 2) ?></span>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="card payment-details">
            <h2 class="section-title">Payment Details</h2>
            <p class="payment-note">This is a secure, 128-bit SSL encrypted payment.</p>
            
            <form method="POST" class="payment-form">
                <div class="form-group">
                    <label>Name on Card</label>
                    <input type="text" class="form-control" placeholder="John Doe" required>
                </div>
                
                <div class="form-group">
                    <label>Card Number</label>
                    <div class="input-icon">
                        <i class="fa-regular fa-credit-card"></i>
                        <input type="text" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group half">
                        <label>Expiry Date</label>
                        <input type="text" class="form-control" placeholder="MM/YY" maxlength="5" required>
                    </div>
                    <div class="form-group half">
                        <label>CVV</label>
                        <input type="password" class="form-control" placeholder="123" maxlength="4" required>
                    </div>
                </div>
                
                <button type="submit" name="process_payment" class="btn btn-primary btn-full pay-btn">
                    <i class="fa-solid fa-lock"></i> Pay Rs. <?= number_format($plan['price'], 2) ?>
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
