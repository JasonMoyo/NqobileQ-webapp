<?php
// api/create-checkout-session.php
require_once '../stripe-config.php';

header('Content-Type: application/json');

try {
    $booking_id = $_POST['booking_id'] ?? 'TEST_' . rand(10000, 99999);
    $amount = (int)($_POST['amount'] ?? 10000); // amount in cents
    $service_name = $_POST['service_name'] ?? 'NqobileQ Package';
    $customer_email = $_POST['email'] ?? '';

    // Validate email
    if (empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $customer_email = 'customer@nqobileq.com';
    }

    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',  // USD currency
                'product_data' => [
                    'name' => $service_name,
                    'description' => 'Booking Reference: #' . $booking_id,
                ],
                'unit_amount' => $amount,  // amount in cents ($100 = 10000 cents)
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/payment-success.php?session_id={CHECKOUT_SESSION_ID}&booking_id=' . $booking_id,
        'cancel_url' => $YOUR_DOMAIN . '/payment-cancel.php',
        'customer_email' => $customer_email,
    ]);

    echo json_encode([
        'id' => $checkout_session->id, 
        'url' => $checkout_session->url
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>