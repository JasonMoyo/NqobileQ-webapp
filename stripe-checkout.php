<?php
// stripe-checkout.php
session_start();
require_once 'stripe-config.php';

$booking_id = $_GET['booking_id'] ?? 'TEST_' . rand(1000, 9999);
$amount = (int)($_GET['amount'] ?? 500);
$service_name = $_GET['service'] ?? 'NqobileQ Service';

// FIXED: Use default email if none provided
$customer_email = $_GET['email'] ?? $_SESSION['user_email'] ?? 'customer@nqobileq.com';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Checkout - NqobileQ</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #000;
            font-family: -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .checkout-container {
            max-width: 500px;
            width: 100%;
            padding: 40px;
            background: #111;
            border-radius: 20px;
            border: 2px solid #00b8a9;
            text-align: center;
        }
        h1 { font-size: 2rem; color: #00b8a9; margin-bottom: 20px; }
        .amount { font-size: 3rem; color: #00b8a9; margin: 20px 0; font-weight: bold; }
        .test-badge { background: #ffc107; color: #000; padding: 5px 15px; border-radius: 20px; display: inline-block; margin-bottom: 20px; font-size: 12px; }
        .details { background: rgba(0, 184, 169, 0.1); padding: 20px; border-radius: 10px; margin: 20px 0; text-align: left; color: #fff; }
        .details p { margin: 8px 0; }
        #checkout-button {
            background: #00b8a9;
            color: #000;
            padding: 15px 40px;
            font-size: 1.2rem;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
        }
        #checkout-button:hover { background: #009688; }
        #checkout-button:disabled { opacity: 0.6; cursor: not-allowed; }
        .test-card { background: rgba(255,255,255,0.05); padding: 15px; border-radius: 10px; margin-top: 20px; font-size: 12px; color: #aaa; text-align: left; }
        .test-card strong { color: #00b8a9; }
        a { color: #00b8a9; text-decoration: none; display: inline-block; margin-top: 20px; font-size: 14px; }
        .manual-email { margin-top: 15px; }
        .manual-email input {
            width: 100%;
            padding: 10px;
            background: #222;
            border: 1px solid #00b8a9;
            border-radius: 5px;
            color: #fff;
            margin-top: 10px;
        }
        .manual-email label {
            font-size: 12px;
            color: #00b8a9;
        }
    </style>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <div class="checkout-container">
        <div class="test-badge">🔧 TEST MODE - No real charges</div>
        <h1>🔒 Secure Checkout</h1>
        <div class="amount">$<?php echo number_format($amount / 100, 2); ?> USD</div>
        
        <div class="details">
            <p><strong>Service:</strong> <?php echo htmlspecialchars($service_name); ?></p>
            <p><strong>Booking ID:</strong> #<?php echo htmlspecialchars($booking_id); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($customer_email); ?></p>
        </div>

        <div class="manual-email">
            <label>📧 Or enter a different email:</label>
            <input type="email" id="email-input" placeholder="your@email.com" value="<?php echo htmlspecialchars($customer_email); ?>">
        </div>
        
        <button id="checkout-button">Pay $<?php echo number_format($amount / 100, 2); ?></button>
        
        <div class="test-card">
            <strong>🔑 Test Card:</strong><br>
            4242 4242 4242 4242 | Exp: 12/28 | CVC: 123
        </div>
        <a href="index.php">← Back to Home</a>
    </div>

    <script>
    const stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');
    const button = document.getElementById('checkout-button');
    const emailInput = document.getElementById('email-input');

    button.addEventListener('click', async () => {
        button.disabled = true;
        button.textContent = 'Processing...';
        
        // Get email from input field
        const customerEmail = emailInput.value.trim();
        
        if (!customerEmail) {
            alert('Please enter your email address');
            button.disabled = false;
            button.textContent = 'Pay $<?php echo number_format($amount / 100, 2); ?>';
            return;
        }
        
        try {
            const response = await fetch('api/create-checkout-session.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    booking_id: '<?php echo $booking_id; ?>',
                    amount: '<?php echo $amount; ?>',
                    service_name: '<?php echo htmlspecialchars($service_name); ?>',
                    email: customerEmail
                })
            });
            
            const session = await response.json();
            
            if (session.error) {
                alert('Error: ' + session.error);
                button.disabled = false;
                button.textContent = 'Try Again';
                return;
            }
            
            window.location.href = session.url;
        } catch (error) {
            alert('Connection error: ' + error.message);
            button.disabled = false;
            button.textContent = 'Try Again';
        }
    });
    </script>
</body>
</html>