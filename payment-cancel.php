<?php
// payment-cancel.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Cancelled - NqobileQ</title>
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
        h1 { font-size: 2rem; color: #ffc107; margin-bottom: 20px; }
        p { color: #fff; margin-bottom: 20px; }
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
        <div class="icon">❌</div>
        <h1>Payment Cancelled</h1>
        <p>You cancelled the payment. No charges were made to your account.</p>
        <p>You can try again whenever you're ready.</p>
        <a href="stripe-checkout.php?booking_id=TEST001&amount=500&service=Test+Service" class="btn">🔄 Try Again</a>
        <a href="index.php" class="btn" style="background: transparent; border: 1px solid #00b8a9;">🏠 Return Home</a>
    </div>
</body>
</html>