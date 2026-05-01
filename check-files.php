<?php
// check-files.php
echo "<h1>File Check</h1>";

$files = [
    'stripe-config.php',
    'stripe-checkout.php',
    'payment-success.php',
    'payment-cancel.php',
    'api/create-checkout-session.php',
    'stripe-php/init.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file - FOUND<br>";
    } else {
        echo "❌ $file - MISSING<br>";
    }
}
?>