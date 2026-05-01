<?php
// stripe-config.php - NO HARDCODED KEYS!

require_once __DIR__ . '/stripe-php/init.php';

// ============================================
// IMPORTANT: Keys come from ENVIRONMENT VARIABLES only!
// NEVER hardcode keys in files that go to GitHub
// ============================================

$publishable_key = getenv('STRIPE_PUBLISHABLE_KEY');
$secret_key = getenv('STRIPE_SECRET_KEY');

if (empty($publishable_key) || empty($secret_key)) {
    die('Stripe keys not configured. Please set STRIPE_PUBLISHABLE_KEY and STRIPE_SECRET_KEY environment variables.');
}

define('STRIPE_PUBLISHABLE_KEY', $publishable_key);
define('STRIPE_SECRET_KEY', $secret_key);

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// Auto-detect domain
$domain = getenv('SITE_URL');
if (empty($domain)) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $domain = $protocol . $host;
}
$YOUR_DOMAIN = $domain;

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>