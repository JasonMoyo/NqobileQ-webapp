<?php
// stripe-config.php
require_once __DIR__ . '/vendor/autoload.php';

// Get keys from environment variables
$publishable_key = getenv('STRIPE_PUBLISHABLE_KEY');
$secret_key = getenv('STRIPE_SECRET_KEY');

if (empty($publishable_key) || empty($secret_key)) {
    die('Stripe keys not configured. Please set STRIPE_PUBLISHABLE_KEY and STRIPE_SECRET_KEY environment variables.');
}

define('STRIPE_PUBLISHABLE_KEY', $publishable_key);
define('STRIPE_SECRET_KEY', $secret_key);

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// ============================================
// FIX: Use auto-detection like config.php!
// ============================================
$domain = getenv('SITE_URL');
if (empty($domain)) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $domain = $protocol . $host;
}
$YOUR_DOMAIN = $domain;

// Enable error reporting for debugging
if (getenv('APP_ENV') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
?>