<?php
require_once __DIR__ . '/config.php';

header('Content-Type: text/plain; version=0.0.4');

$conn = getDB();
$metrics = [];

if ($conn && !$conn->connect_error) {
    $metrics[] = "nqobileq_health 1";
    
    // Users
    $result = $conn->query("SELECT COUNT(*) as cnt FROM users");
    $row = $result->fetch_assoc();
    $metrics[] = "nqobileq_users_total " . ($row['cnt'] ?? 0);
    
    // Bookings
    $result = $conn->query("SELECT COUNT(*) as cnt FROM service_bookings");
    $row = $result->fetch_assoc();
    $metrics[] = "nqobileq_bookings_total " . ($row['cnt'] ?? 0);
    
    // Bookings today
    $result = $conn->query("SELECT COUNT(*) as cnt FROM service_bookings WHERE DATE(created_at) = CURDATE()");
    $row = $result->fetch_assoc();
    $metrics[] = "nqobileq_bookings_today " . ($row['cnt'] ?? 0);
    
    // Pending payments
    $result = $conn->query("SELECT COUNT(*) as cnt FROM package_bookings WHERE status = 'pending'");
    $row = $result->fetch_assoc();
    $metrics[] = "nqobileq_payments_pending " . ($row['cnt'] ?? 0);
    
    // Successful payments
    $result = $conn->query("SELECT COUNT(*) as cnt FROM package_bookings WHERE status = 'paid'");
    $row = $result->fetch_assoc();
    $metrics[] = "nqobileq_payments_successful " . ($row['cnt'] ?? 0);
    
    // Stripe success rate
    $result = $conn->query("SELECT 
        SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as sc,
        COUNT(*) as tc 
        FROM package_bookings");
    $stats = $result->fetch_assoc();
    $rate = ($stats['tc'] > 0) ? ($stats['sc'] / $stats['tc']) * 100 : 0;
    $metrics[] = "nqobileq_stripe_success_rate " . round($rate, 2);
    
    // Inquiries
    $result = $conn->query("SELECT COUNT(*) as cnt FROM inquiries");
    $row = $result->fetch_assoc();
    $metrics[] = "nqobileq_inquiries_total " . ($row['cnt'] ?? 0);
    
    $metrics[] = "nqobileq_db_connected 1";
    
} else {
    $metrics[] = "nqobileq_health 0";
    $metrics[] = "nqobileq_users_total 0";
    $metrics[] = "nqobileq_bookings_total 0";
    $metrics[] = "nqobileq_bookings_today 0";
    $metrics[] = "nqobileq_payments_pending 0";
    $metrics[] = "nqobileq_payments_successful 0";
    $metrics[] = "nqobileq_stripe_success_rate 0";
    $metrics[] = "nqobileq_inquiries_total 0";
    $metrics[] = "nqobileq_db_connected 0";
}

echo "# HELP nqobileq_health Health check metric\n";
echo "# TYPE nqobileq_health gauge\n";
echo "# HELP nqobileq_users_total Total registered users\n";
echo "# TYPE nqobileq_users_total gauge\n";
echo "# HELP nqobileq_bookings_total Total service bookings\n";
echo "# TYPE nqobileq_bookings_total gauge\n";
echo "# HELP nqobileq_bookings_today Bookings made today\n";
echo "# TYPE nqobileq_bookings_today gauge\n";
echo "# HELP nqobileq_payments_pending Pending payments\n";
echo "# TYPE nqobileq_payments_pending gauge\n";
echo "# HELP nqobileq_payments_successful Successful payments\n";
echo "# TYPE nqobileq_payments_successful gauge\n";
echo "# HELP nqobileq_stripe_success_rate Stripe payment success rate (%)\n";
echo "# TYPE nqobileq_stripe_success_rate gauge\n";
echo "# HELP nqobileq_inquiries_total Total inquiries received\n";
echo "# TYPE nqobileq_inquiries_total gauge\n";
echo "# HELP nqobileq_db_connected Database connection status\n";
echo "# TYPE nqobileq_db_connected gauge\n";

echo implode("\n", $metrics) . "\n";

$conn->close();
?>