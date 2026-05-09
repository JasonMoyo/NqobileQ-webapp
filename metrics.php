<?php
// metrics.php - Prometheus metrics endpoint for NqobileQ
require_once 'config.php';

header('Content-Type: text/plain; version=0.0.4');
header('Cache-Control: no-cache');

$metrics = [];

// Helper function
function escape_metric($value) {
    return str_replace('"', '', $value);
}

// ============================================
// DATABASE METRICS
// ============================================

$conn = getDB();

if ($conn && !$conn->connect_error) {
    // User statistics
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $row = $result->fetch_assoc();
    $metrics[] = "nqobileq_users_total{$row['count']}";

    // Today's bookings
    $result = $conn->query("SELECT COUNT(*) as count FROM service_bookings WHERE DATE(created_at) = CURDATE()");
    $row = $result->fetch_assoc();
    $metrics[] = "nqobileq_bookings_today{$row['count']}";

    // Weekly bookings
    $result = $conn->query("SELECT COUNT(*) as count FROM service_bookings WHERE YEARWEEK(created_at) = YEARWEEK(NOW())");
    $row = $result->fetch_assoc();
    $metrics[] = "nqobileq_bookings_week{$row['count']}";

    // Pending payments
    $result = $conn->query("SELECT COUNT(*) as count FROM package_bookings WHERE status = 'pending'");
    $row = $result->fetch_assoc();
    $metrics[] = "nqobileq_pending_payments{$row['count']}";

    // Completed payments
    $result = $conn->query("SELECT COUNT(*) as count FROM package_bookings WHERE status = 'paid'");
    $row = $result->fetch_assoc();
    $metrics[] = "nqobileq_completed_payments{$row['count']}";

    // Stripe payment success rate (last 30 days)
    $result = $conn->query("SELECT 
        SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as success,
        COUNT(*) as total 
        FROM package_bookings 
        WHERE created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stats = $result->fetch_assoc();
    $success_rate = ($stats['total'] > 0) ? ($stats['success'] / $stats['total']) * 100 : 0;
    $metrics[] = "nqobileq_stripe_success_rate" . round($success_rate, 2);

    // Bookings by service type
    $result = $conn->query("SELECT service_type, COUNT(*) as count FROM service_bookings GROUP BY service_type");
    while ($row = $result->fetch_assoc()) {
        $metrics[] = "nqobileq_bookings_by_service{service=\""

    // Recent inquiries
    $result = $conn->query("SELECT COUNT(*) as count FROM inquiries WHERE DATE(created_at) = CURDATE()");
    $row = $result->fetch_assoc();
    $metrics[] = "nqobileq_inquiries_today{$row['count']}";

    // Database connection status
    $metrics[] = "nqobileq_db_connected 1";
} else {
    $metrics[] = "nqobileq_db_connected 0";
}

// ============================================
// PHP APPLICATION METRICS
// ============================================

// PHP version
$metrics[] = "nqobileq_php_version_info{version=\""

// Check if session is active
$metrics[] = "nqobileq_session_active " . (session_status() == PHP_SESSION_ACTIVE ? 1 : 0);

// OPcache statistics (if available)
if (function_exists('opcache_get_status')) {
    $opcache = opcache_get_status(false);
    if ($opcache) {
        $metrics[] = "nqobileq_opcache_hits " . ($opcache['opcache_statistics']['hits'] ?? 0);
        $metrics[] = "nqobileq_opcache_misses " . ($opcache['opcache_statistics']['misses'] ?? 0);
        $metrics[] = "nqobileq_opcache_hit_rate " . round(($opcache['opcache_statistics']['opcache_hit_rate'] ?? 0), 2);
        $metrics[] = "nqobileq_opcache_memory_used_bytes " . ($opcache['memory_usage']['used_memory'] ?? 0);
        $metrics[] = "nqobileq_opcache_memory_free_bytes " . ($opcache['memory_usage']['free_memory'] ?? 0);
    }
}

// ============================================
// BUSINESS METRICS
// ============================================

// Total revenue from Stripe (in cents)
$result = $conn->query("SELECT SUM(amount) as total FROM package_bookings WHERE status = 'paid'");
$row = $result->fetch_assoc();
$metrics[] = "nqobileq_total_revenue_cents" . (int)($row['total'] ?? 0);

// Average order value
$result = $conn->query("SELECT AVG(amount) as avg_amount FROM package_bookings WHERE status = 'paid' AND amount > 0");
$row = $result->fetch_assoc();
$metrics[] = "nqobileq_avg_order_value_cents" . round($row['avg_amount'] ?? 0, 2);

// ============================================
// OUTPUT METRICS
// ============================================

echo "# HELP nqobileq_users_total Total number of registered users\n";
echo "# TYPE nqobileq_users_total gauge\n";
echo "# HELP nqobileq_bookings_today Number of bookings made today\n";
echo "# TYPE nqobileq_bookings_today gauge\n";
echo "# HELP nqobileq_pending_payments Number of pending payments\n";
echo "# TYPE nqobileq_pending_payments gauge\n";
echo "# HELP nqobileq_stripe_success_rate Stripe payment success rate (%)\n";
echo "# TYPE nqobileq_stripe_success_rate gauge\n";
echo "# HELP nqobileq_db_connected Database connection status (1=connected, 0=disconnected)\n";
echo "# TYPE nqobileq_db_connected gauge\n";
echo "# HELP nqobileq_total_revenue_cents Total revenue in cents\n";
echo "# TYPE nqobileq_total_revenue_cents counter\n";
echo "# HELP nqobileq_avg_order_value_cents Average order value in cents\n";
echo "# TYPE nqobileq_avg_order_value_cents gauge\n";

echo implode("\n", $metrics) . "\n";

$conn->close();
?>