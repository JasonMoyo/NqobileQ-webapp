<?php
// payment-success.php
require_once 'stripe-config.php';

$session_id = $_GET['session_id'] ?? null;
$booking_id = $_GET['booking_id'] ?? null;
$amount = 0;

if ($session_id) {
    try {
        $session = \Stripe\Checkout\Session::retrieve($session_id);
        $amount = $session->amount_total / 100;
    } catch (Exception $e) {
        // Silent fail - just show the page anyway
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Success - NqobileQ</title>
    <style>
        body { 
            background: #000; 
            font-family: -apple-system, sans-serif; 
            min-height: 100vh; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            padding: 20px; 
        }
        .container { 
            max-width: 500px; 
            padding: 40px; 
            background: #111; 
            border-radius: 20px; 
            border: 2px solid #00b8a9; 
            text-align: center; 
        }
        .icon { font-size: 5rem; margin-bottom: 20px; }
        h1 { font-size: 2rem; color: #00b8a9; margin-bottom: 20px; }
        .details { 
            background: rgba(0,184,169,0.1); 
            padding: 20px; 
            border-radius: 10px; 
            margin: 20px 0; 
            text-align: left; 
            color: #fff; 
        }
        .btn { 
            display: inline-block; 
            padding: 12px 30px; 
            background: #00b8a9; 
            color: #000; 
            text-decoration: none; 
            border-radius: 10px; 
            margin: 10px; 
            font-weight: bold; 
        }
        .btn:hover { background: #009688; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">✅</div>
        <h1>Payment Successful!</h1>
        <div class="details">
            <p><strong>Booking ID:</strong> #<?php echo htmlspecialchars($booking_id); ?></p>
            <?php if ($amount > 0): ?>
            <p><strong>Amount Paid:</strong> $<?php echo number_format($amount, 2); ?> USD</p>
            <?php endif; ?>
            <p>A confirmation email will be sent to you.</p>
        </div>
        <a href="index.php" class="btn">🏠 Return Home</a>
        <a href="index.php#services" class="btn" style="background: transparent; border: 1px solid #00b8a9;">📋 View Services</a>
    </div>
</body>
</html>