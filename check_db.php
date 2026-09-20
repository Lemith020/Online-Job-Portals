<?php
require 'config/database.php';
$res = mysqli_query($conn, 'DESCRIBE user_subscriptions');
while($r = mysqli_fetch_assoc($res)) {
    echo $r['Field'] . ' - ' . $r['Type'] . "\n";
}
?>
