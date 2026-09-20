<?php
require_once __DIR__ . '/config/database.php';

// Check if 6 Months plan exists
$check6 = mysqli_query($conn, "SELECT * FROM subscription_plans WHERE duration_days = 180");
if (mysqli_num_rows($check6) == 0) {
    mysqli_query($conn, "INSERT INTO subscription_plans (plan_name, duration_days, price) VALUES ('6 Months Plan', 180, 5000.00)");
    echo "Added 6 Months Plan.\n";
}

// Check if 1 Year plan exists
$check12 = mysqli_query($conn, "SELECT * FROM subscription_plans WHERE duration_days = 365");
if (mysqli_num_rows($check12) == 0) {
    mysqli_query($conn, "INSERT INTO subscription_plans (plan_name, duration_days, price) VALUES ('1 Year Premium Plan', 365, 9000.00)");
    echo "Added 1 Year Premium Plan.\n";
}

echo "Done.\n";
?>
