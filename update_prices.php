<?php
require 'config/database.php';

// Fix 6 Months plan (180 days) price to 2500
mysqli_query($conn, "UPDATE subscription_plans SET price = 2500.00 WHERE duration_days = 180");
// Fix 1 Year/Premium plan (365 days) price to 5000
mysqli_query($conn, "UPDATE subscription_plans SET price = 5000.00 WHERE duration_days = 365");

echo "Prices updated.\n";
?>
