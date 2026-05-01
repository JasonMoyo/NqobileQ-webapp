<?php
// test-stripe-debug.php
require_once 'stripe-config.php';

echo "<h1>Stripe Debug Info</h1>";

// Test if Stripe is working
try {
    // Just a simple API call to test connection
    $products = \Stripe\Product::all(['limit' => 1]);
    echo "✅ Stripe API connection successful!<br>";
} catch (Exception $e) {
    echo "❌ Stripe API error: " . $e->getMessage() . "<br>";
}

echo "<br><strong>Your configuration:</strong><br>";
echo "Base URL: " . $YOUR_DOMAIN . "<br>";
echo "Success URL would be: " . $YOUR_DOMAIN . "/payment-success.php?session_id=test123<br>";
echo "Cancel URL would be: " . $YOUR_DOMAIN . "/payment-cancel.php<br>";

echo "<br><strong>Test links:</strong><br>";
echo "<a href='stripe-checkout.php?booking_id=DEBUG001&amount=500&service=Debug+Test' target='_blank'>Click here to test checkout</a>";
?>